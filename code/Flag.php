<?php

class Flag extends DataObject implements PermissionProvider
{
    public static $flags = array();

    private static $db = array(
        'Name' => 'Varchar(255)',
        'Description' => 'Text',
        'Enabled' => 'Boolean'
    );

    private static $default_sort = '"Name" ASC';

    private static $searchable_fields = array(
        'Name',
        'Description',
        'Enabled'
    );

    private static $summary_fields = array(
        'Name',
        'Description',
        'Enabled'
    );

    public function canCreate($member = null)
    {
        return false;
    }

    public function canDelete($member = null)
    {
        return false;
    }

    public function canEdit($member = null)
    {
        return Permission::check('EDIT_FLAGS', 'any', $member);
    }

    public function canView($member = null)
    {
        return (
            Permission::check('VIEW_FLAGS', 'any', $member) ||
            Permission::check('EDIT_FLAGS', 'any', $member)
        );
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->replaceField(
            'Name',
            $fields->dataFieldByName('Name')
                ->performReadonlyTransformation()
        );
        $fields->replaceField(
            'Description',
            $fields->dataFieldByName('Description')
                ->performReadonlyTransformation()
        );

        $historyGridField = GridField::create(
            'FlagHistory',
            'History',
            FlagHistory::get()->filter('FlagID', $this->ID)
        );

        $fields->AddFieldToTab("Root.History", $historyGridField);

        return $fields;
    }

    public function onAfterWrite()
    {
        parent::onAfterWrite();
        $historyRecord = new FlagHistory();
        $historyRecord->Enabled = $this->Enabled;
        $historyRecord->AuthorID = Member::currentUserID();
        $historyRecord->FlagID = $this->ID;
        $historyRecord->write();
    }

    public function providePermissions()
    {
        return array(
            'EDIT_FLAGS' => array(
                'name' => 'Modify Flags',
                'category' => 'Flags'
            ),
            'VIEW_FLAGS' => array(
                'name' => 'View Flags',
                'category' => 'Flags'
            )
        );
    }

    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();

        $flagsConfig = self::config()->flags;

        foreach ($flagsConfig as $flag) {
            $alteration = false;
            $record = Flag::get()->filter('Name', $flag['Name'])->first();

            if (!$record) {
                $alteration = 'created';
                $record = Flag::create($flag);
            } elseif (
                isset($flag['Description']) &&
                $record->Description != $flag['Description']
            ) {
                $alteration = 'changed';
                $record->Description = $flag['Description'];
            }

            if ($alteration) {
                $record->write();
                DB::alteration_message("Flag '$flag[Name]' $alteration", $alteration);
            }
        }

        $flagsConfigNames = array_map(function ($flag) {
            return $flag['Name'];
        }, $flagsConfig);

        $flagsToDelete = Flag::get()->exclude('Name', $flagsConfigNames);

        foreach ($flagsToDelete as $flag) {
            $flag->delete();
            DB::alteration_message("Flag '$flag->Name' deleted", 'deleted');
        }
    }

    public static function isEnabled($flagName)
    {
        $flag = Flag::get()->find('Name', $flagName);
        return $flag ? $flag->Enabled : false;
    }
}
