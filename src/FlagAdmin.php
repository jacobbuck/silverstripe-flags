<?php

namespace JacobBuck\Flags;

use SilverStripe\Admin\ModelAdmin;

/**
 * @package flags
 */
class FlagAdmin extends ModelAdmin
{
    /**
     * @var string
     */
    private static $required_permission_codes = 'CMS_ACCESS_FlagAdmin';

    /**
     * @var array
     */
    private static $managed_models = [
        Flag::class => ['title' => 'Flags'],
    ];

    /**
     * @var string
     */
    private static $menu_icon = 'jacobbuck/silverstripe-flags:images/menu.svg';

    /**
     * @var string
     */
    private static $menu_title = 'Flags';

    /**
     * @var string
     */
    private static $url_segment = 'flags';
}
