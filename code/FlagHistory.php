<?php

class FlagHistory extends DataObject
{
    private static $db = array(
        'Name' => 'Varchar(255)',
        'Enabled' => 'Boolean'
    );

    private static $default_sort = '"LastEdited" DESC';

    private static $has_one = array(
        'Author' => 'Member',
        'Flag' => 'Flag'
    );

    private static $searchable_fields = array(
        'Enabled',
        'LastEdited'
    );

    private static $summary_fields = array(
        'Enabled',
        'LastEdited',
        'Author.Name'
    );

    public function canView($member = null)
    {
        return (
            Permission::check('VIEW_FLAGS', 'any', $member) ||
            Permission::check('EDIT_FLAGS', 'any', $member)
        );
    }

    public function canCreate($member = null)
    {
        return false;
    }

    public function canEdit($member = null)
    {
        return false;
    }

    public function canDelete($member = null)
    {
        return false;
    }
}
