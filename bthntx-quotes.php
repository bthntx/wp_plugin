<?php

/*
Plugin Name: Wp REST API Quotes plugin
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: Sergii Khudolii
Author URI: https://github.com/bthntx/
License: A "Slug" license name e.g. GPL2
*/



define( 'BTHNTX_QUOTES_VERSION', '0.1' );

define( 'BTHNTX_QUOTES_PLUGIN', __FILE__ );

define( 'BTHNTX_QUOTES_PLUGIN_BASENAME',
    plugin_basename( BTHNTX_QUOTES_PLUGIN ) );

define( 'BTHNTX_QUOTES_PLUGIN_NAME',
    trim( dirname( BTHNTX_QUOTES_PLUGIN_BASENAME ), '/' ) );

define( 'BTHNTX_QUOTES_PLUGIN_DIR',
    untrailingslashit( dirname( BTHNTX_QUOTES_PLUGIN ) ) );



if ( is_admin() ) {
    require_once BTHNTX_QUOTES_PLUGIN_DIR . '/admin/admin.php';
}

add_shortcode('bthntx_quotes', 'bthntx_quote_show');
add_filter( 'init', 'bthntx_quotes_by_author');

function bthntx_quotes_by_author()
{
    if (array_key_exists('quotes-by-author',$_GET)&&
        array_key_exists('quoteId',$_GET)&&(int)$_GET['quoteId']>0)
    {
        include  BTHNTX_QUOTES_PLUGIN_DIR . '/includes/quotes_by_author_page.php';
        die;

    }

}

function bthntx_quote_show()
{
    $url = get_option("bthquotes_settings_api_random",null);
    if (!$url) {
        if (!$url) {bthntx_quote_error('No valid url, check settings.');return;}
    }
    $response = wp_remote_get($url, ['headers' => bthquotes_auth_headers()]);
    $http_code = wp_remote_retrieve_response_code($response);
    if ($http_code !== 200) {
        bthntx_quote_error(print_r("Server error",true));
        return;
    }
    $data = wp_remote_retrieve_body($response);
    $data = json_decode($data, true);

    if ($data&&array_key_exists('content',$data)
        &&array_key_exists('id',$data)
        &&array_key_exists('author',$data)
        &&array_key_exists('name',$data['author'])) {

        echo "<div class='notice'><blockquote>{$data['content']}</blockquote> by <a href='?quotes-by-author&quoteId={$data['id']}'>{$data['author']['name']}</a> </div>";

    }

}

function bthquotes_auth_headers()
{
    $authToken =  get_option('bthquotes_settings_api_token',null);
    if (!$authToken)
    {
        wp_redirect(add_query_arg(['page' => 'quotes-list']));
    }
    return ['X-AUTH-TOKEN'=>$authToken,'Content-Type'=>'application/json'];
}

function bthntx_quote_error($message)
{
    echo "<div class='error'><pre>$message</pre></div>";

}
