<?php

namespace JacobBuck\Flags;

use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;

/**
 * @package flags
 */
class FlagHistory extends DataObject
{
    /**
     * @var array
     */
    private static $db = [
        'Enabled' => 'Boolean',
    ];

    /**
     * @var string
     */
    private static $default_sort = '"LastEdited" DESC';

    /**
     * @var array
     */
    private static $has_one = [
        'Author' => Member::class,
        'Flag' => Flag::class,
    ];

    /**
     * @var array
     */
    private static $searchable_fields = [
        'Enabled',
        'LastEdited',
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Enabled' => 'Enabled',
        'LastEdited' => 'Last edited',
        'Author.Name' => 'Author name',
    ];

    /**
     * @var string
     */
    private static $table_name = 'FlagHistory';

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
        return false;
    }

    /**
     * @param \SilverStripe\Security\Member $member
     * @return boolean
     */
    public function canView($member = null)
    {
        return $this->Flag()->canView($member);
    }
}
