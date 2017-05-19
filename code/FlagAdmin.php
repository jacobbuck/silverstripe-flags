<?php

class FlagAdmin extends ModelAdmin
{
    private static $managed_models = array(
        'Flag' => array('title' => 'Flags'),
    );

    private static $menu_icon = 'flags/images/menu.svg';

    private static $menu_title = 'Flags';

    private static $url_segment = 'flags';
}
