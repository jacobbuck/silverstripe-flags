<?php

namespace JacobBuck\Flags;

use SilverStripe\Forms\GridField\GridField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\View\TemplateGlobalProvider;

/**
 * @package flags
 */
class Flag extends DataObject implements PermissionProvider, TemplateGlobalProvider
{
    /**
     * @var array
     */
    private static $db = [
        'Name' => 'Varchar(255)',
        'Description' => 'Text',
        'Enabled' => 'Boolean',
    ];

    /**
     * @var string
     */
    private static $default_sort = '"Name" ASC';

    /**
     * @config
     *
     * @var array
     */
    private static $flags = [];

    /**
     * @var array
     */
    private static $indexes = [
        'Name' => true,
        'Enabled' => true,
    ];

    /**
     * @var array
     */
    private static $searchable_fields = [
        'Name',
        'Description',
        'Enabled',
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Name',
        'Description',
        'Enabled',
    ];

    /**
     * @var string
     */
    private static $table_name = 'Flag';

    /**
     * @param \SilverStripe\Security\Member $member
     * @param array $context
     * @return boolean
     */
    public function canCreate($member = null, $context = [])
    {
        return false;
    }

    /**
     * @param \SilverStripe\Security\Member $member
     * @return boolean
     */
    public function canDelete($member = null)
    {
        return false;
    }

    /**
     * @param \SilverStripe\Security\Member $member
     * @return boolean
     */
    public function canEdit($member = null)
    {
        return Permission::check('EDIT_FLAGS', 'any', $member);
    }

    /**
     * @param \SilverStripe\Security\Member $member
     * @return boolean
     */
    public function canView($member = null)
    {
        return true;
    }

    /**
     * @return \SilverStripe\Forms\FieldList
     */
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

    /**
     * @return void
     */
    public function onAfterWrite()
    {
        parent::onAfterWrite();

        $historyRecord = new FlagHistory();
        $historyRecord->Enabled = $this->Enabled;
        $historyRecord->AuthorID = Member::currentUserID();
        $historyRecord->FlagID = $this->ID;
        $historyRecord->write();
    }

    /**
     * @return array
     */
    public function providePermissions()
    {
        return [
            'EDIT_FLAGS' => [
                'name' => 'Modify Flags',
                'category' => 'Flags',
            ],
        ];
    }

    /**
     * @return void
     */
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

    /**
     * @return array
     */
    public static function get_template_global_variables()
    {
        return [
            'FlagEnabled' => 'isEnabled',
        ];
    }

    /**
     * @param string $flagName
     * @return boolean
     */
    public static function isEnabled($flagName)
    {
        $flag = Flag::get()->find('Name', $flagName);
        return $flag ? $flag->Enabled : false;
    }
}
