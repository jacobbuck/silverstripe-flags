<?php

namespace JacobBuck\Flags;

use SilverStripe\Admin\ModelAdmin;

class FlagAdmin extends ModelAdmin
{
    private static $required_permission_codes = 'CMS_ACCESS_FlagAdmin';

    private static $managed_models = [
        Flag::class => ['title' => 'Flags'],
    ];

    private static $menu_icon = 'jacobbuck/silverstripe-flags:images/menu.svg';

    private static $menu_title = 'Flags';

    private static $url_segment = 'flags';
}
