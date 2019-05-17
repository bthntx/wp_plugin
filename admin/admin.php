<?php

require_once BTHNTX_QUOTES_PLUGIN_DIR.'/admin/settings.php';
require_once BTHNTX_QUOTES_PLUGIN_DIR.'/admin/page_list.php';
require_once BTHNTX_QUOTES_PLUGIN_DIR.'/admin/page_edit.php';

add_action('admin_menu', 'bthntx_quotes_admin_menu', 8);

function bthntx_quotes_admin_menu()
{

    add_menu_page(
        'Quotes Plugin',
        'Quotes',
        'manage_options',
        'quotes-plugin',
        'bthtx_quotes_list'
    );
    add_submenu_page("quotes-plugin",
        "New quote",
        'New quote',
        'manage_options',
        'quotes-new',
        'bthtx_quotes_edit'
    );
    add_submenu_page(null,
        "Edit quote",
        'Edit quote',
        'manage_options',
        'quotes-edit',
        'bthtx_quotes_edit'
    );
    add_submenu_page(null,
        "Register blog",
        'Register blog',
        'manage_options',
        'quotes-register',
        'bthtx_quotes_register'
    );


    add_options_page('Settings',
        'Quotes Plugin Menu',
        'manage_options',
        'quotes-plugin-settings',
        'bthtx_quotes_options_page'
    );

}




