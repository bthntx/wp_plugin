<?php

add_action('admin_init', 'bthquotes_settings_init');

function bthtx_quotes_options_page()
{
    ?>
    <div>
        <form method="post" action="options.php">
            <?php
            settings_fields('bthntx_quotes_options_group');
            do_settings_sections('quotes');
            ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php

}

function bthtx_quotes_register()
{
    $url =  get_option('bthquotes_settings_api_register',null);
    if (!$url) {bthntx_quote_error('No valid url, check settings.');return;}
    $response = wp_remote_post($url);
    $http_code = wp_remote_retrieve_response_code($response);
    if ($http_code !== 200) {
        if (!$url) {bthntx_quote_error('Server error');return;}
        return;

    }
    $data = wp_remote_retrieve_body($response);
    $data = json_decode($data, true);
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    if (array_key_exists('api_token',$data)&& array_key_exists('refresh_token',$data)) {
        update_option('bthquotes_settings_api_token',$data['api_token']);
        update_option('bthquotes_settings_api_refresh',$data['refresh_token']);
        $link = add_query_arg(['page' => 'quotes-plugin']);
        echo "<div>Blog Registration is successfull </div><a href='{$link}' class='button button-primary'>To quotes list</a>";
    }



}


function bthquotes_settings_init()
{

    register_setting('bthntx_quotes_options_group', 'bthquotes_settings_api_register');
    register_setting('bthntx_quotes_options_group', 'bthquotes_settings_api_token');
    register_setting('bthntx_quotes_options_group', 'bthquotes_settings_api_refresh');
    register_setting('bthntx_quotes_options_group', 'bthquotes_settings_api_listing');
    register_setting('bthntx_quotes_options_group', 'bthquotes_settings_api_random');
    register_setting('bthntx_quotes_options_group', 'bthquotes_settings_api_author');
    register_setting('bthntx_quotes_options_group', 'bthquotes_settings_api_item');
    register_setting('bthntx_quotes_options_group', 'bthquotes_settings_quotes_per_page');
    register_setting('bthntx_quotes_options_group', 'bthquotes_settings_allow_public');
    add_settings_section(
        'bthquotes_settings_section',
        'Quotes API  Settings Section',
        'bthquotes_settings_section_cb',
        'quotes'
    );
    add_settings_field(
        'bthquotes_settings_api_listing',
        'Quotes API listing url',
        'bthquotes_settings_listing_cb',
        'quotes',
        'bthquotes_settings_section'
    );
    add_settings_field(
        'bthquotes_settings_api_random',
        'Quotes API random fetch url',
        'bthquotes_settings_random_cb',
        'quotes',
        'bthquotes_settings_section'
    );
    add_settings_field(
        'bthquotes_settings_api_author',
        'Quotes API by author fetch url',
        'bthquotes_settings_author_cb',
        'quotes',
        'bthquotes_settings_section'
    );
    add_settings_field(
        'bthquotes_settings_api_item',
        'Quotes API item url',
        'bthquotes_settings_item_cb',
        'quotes',
        'bthquotes_settings_section'
    );
    add_settings_field(
        'bthquotes_settings_api_refresh',
        'Last refresh token ',
        'bthquotes_settings_api_refresh_cb',
        'quotes',
        'bthquotes_settings_section'
    );
    add_settings_field(
        'bthquotes_settings_api_token',
        'Last api access token ',
        'bthquotes_settings_api_token_cb',
        'quotes',
        'bthquotes_settings_section'
    );
    add_settings_field(
        'bthquotes_settings_api_register',
        'Register URL ',
        'bthquotes_settings_api_register_cb',
        'quotes',
        'bthquotes_settings_section'
    );
    add_settings_field(
        'bthquotes_settings_quotes_per_page',
        'Quotes per page in CRUD page ',
        'bthquotes_settings_quotes_per_page_cb',
        'quotes',
        'bthquotes_settings_section'
    );
    add_settings_field(
        'bthquotes_settings_allow_public',
        'Allow to show public quotes along with own',
        'bthquotes_settings_allow_public_cb',
        'quotes',
        'bthquotes_settings_section'
    );
}

function bthquotes_settings_section_cb()
{
    echo '<p>Quotes Section Settings.</p>';
}


function bthquotes_settings_listing_cb()
{
    $setting = get_option('bthquotes_settings_api_listing');
    ?>
    <input class="regular-text" type="text" name="bthquotes_settings_api_listing"
           value="<?php echo isset($setting) ? esc_attr($setting) : ''; ?>">
    <?php
}


function bthquotes_settings_random_cb()
{
    $setting = get_option('bthquotes_settings_api_random');
    ?>
    <input class="regular-text" type="text" name="bthquotes_settings_api_random"
           value="<?php echo isset($setting) ? esc_attr($setting) : ''; ?>">
    <?php
}

function bthquotes_settings_author_cb()
{
    $setting = get_option('bthquotes_settings_api_author');
    ?>
    <input class="regular-text" type="text" name="bthquotes_settings_api_author"
           value="<?php echo isset($setting) ? esc_attr($setting) : ''; ?>">
    <?php
}


function bthquotes_settings_item_cb()
{
    $setting = get_option('bthquotes_settings_api_item');
    ?>
    <input class="regular-text" type="text" name="bthquotes_settings_api_item"
           value="<?php echo isset($setting) ? esc_attr($setting) : ''; ?>">
    <?php
}


function bthquotes_settings_quotes_per_page_cb()
{
    $setting = get_option('bthquotes_settings_quotes_per_page', 25);
    ?>
    <input class="regular-text" type="number" name="bthquotes_settings_quotes_per_page"
           value="<?php echo isset($setting) ? esc_attr($setting) : ''; ?>">
    <?php
}

function bthquotes_settings_allow_public_cb()
{
    $setting = get_option('bthquotes_settings_allow_public');
    ?>
    <input type="checkbox" name="bthquotes_settings_allow_public"
           value="1" <?php echo $setting == 1 ? 'checked' : ''; ?>>
    <?php
}

function bthquotes_settings_api_token_cb()
{
    $setting = get_option('bthquotes_settings_api_token');
    if (!isset($setting)||$setting=='') {
        ?>
        <a href="<?= add_query_arg(['page' => 'quotes-register']) ?>"
           class="button button-primary">Request Tokens</a>
        <?php
    }
    ?>
    <input type="text" class="regular-text" name="bthquotes_settings_api_token"
           value="<?php echo isset($setting) ? esc_attr($setting) : ''; ?>">
    <?php
}

function bthquotes_settings_api_refresh_cb()
{
    $setting = get_option('bthquotes_settings_api_refresh');
    ?>
    <input type="text" class="regular-text" name="bthquotes_settings_api_refresh"
           value="<?php echo isset($setting) ? esc_attr($setting) : ''; ?>">
    <?php
}

function bthquotes_settings_api_register_cb()
{
    $setting = get_option('bthquotes_settings_api_register');
    ?>
    <input type="text" class="regular-text" name="bthquotes_settings_api_register"
           value="<?php echo isset($setting) ? esc_attr($setting) : ''; ?>">
    <?php
}
