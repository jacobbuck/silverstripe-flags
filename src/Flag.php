<?php

namespace JacobBuck\Flags;

use SilverStripe\Forms\GridField\GridField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\View\TemplateGlobalProvider;

class Flag extends DataObject implements PermissionProvider, TemplateGlobalProvider
{
    private static $table_name = 'Flag';

    public static $flags = [];

    private static $db = [
        'Name' => 'Varchar(255)',
        'Description' => 'Text',
        'Enabled' => 'Boolean',
    ];

    private static $default_sort = '"Name" ASC';
    
    private static $indexes = [
        'Name' => true,
        'Enabled' => true,
    ];

    private static $searchable_fields = [
        'Name',
        'Description',
        'Enabled'
    ];

    private static $summary_fields = [
        'Name',
        'Description',
        'Enabled'
    ];

    public function canCreate($member = null, $context = [])
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
        return true;
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
        $fields->AddFieldToTab(
            'Root.History',
            GridField::create(
                'FlagHistory',
                'History',
                FlagHistory::get()->filter('FlagID', $this->ID)
            )
        );

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
        return [
            'EDIT_FLAGS' => [
                'name' => 'Modify Flags',
                'category' => 'Flags',
            ],
        ];
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
            } elseif (isset($flag['Description']) 
                && $record->Description != $flag['Description']
            ) {
                $alteration = 'changed';
                $record->Description = $flag['Description'];
            }

            if ($alteration) {
                $record->write();
                DB::alteration_message("Flag '$flag[Name]' $alteration", $alteration);
            }
        }

        $flagsConfigNames = array_map(
            function ($flag) {
                return $flag['Name'];
            }, $flagsConfig
        );

        $flagsToDelete = Flag::get()->exclude('Name', $flagsConfigNames);

        foreach ($flagsToDelete as $flag) {
            $flag->delete();
            DB::alteration_message("Flag '$flag->Name' deleted", 'deleted');
        }
    }
    
    public static function get_template_global_variables()
    {
        return [
            'FlagEnabled' => 'isEnabled',
        ];
    }

    public static function isEnabled($flagName)
    {
        $flag = Flag::get()->find('Name', $flagName);
        return $flag ? $flag->Enabled : false;
    }
}