<?php

namespace JacobBuck\Flags;

use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;

class FlagHistory extends DataObject
{
    private static $table_name = 'FlagHistory';

    private static $db = [
        'Name' => 'Varchar(255)',
        'Enabled' => 'Boolean',
    ];

    private static $default_sort = '"LastEdited" DESC';

    private static $has_one = [
        'Author' => Member::class,
        'Flag' => Flag::class,
    ];

    private static $searchable_fields = [
        'Enabled',
        'LastEdited',
    ];

    private static $summary_fields = [
        'Enabled' => 'Enabled',
        'LastEdited' => 'Last edited',
        'Author.Name' => 'Author name',
    ];

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
        return false;
    }

    public function canView($member = null)
    {
        return $this->Flag()->canView($member);
    }
}
