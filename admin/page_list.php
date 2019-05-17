<?php
function bthtx_quotes_list()
{
    //Handle POST
    //Detect Current page
    //Request new data;
    $cur_page = (array_key_exists('pagenum', $_GET) && (int)$_GET['pagenum'] > 1) ? ((int)$_GET['pagenum']) : 1;
    $url = get_option('bthquotes_settings_api_listing');

    echo "<p><strong>Debug url:</strong>$url</p>";

    $response = wp_remote_get($url, ['headers' => bthquotes_auth_headers()]);
    $http_code = wp_remote_retrieve_response_code($response);


    if ($http_code !== 200) {
        bthntx_quote_error(print_r($response,true));
        return;
    }
    $body = wp_remote_retrieve_body($response);
    $listing = json_decode($body, true);

    echo "<h2>List of quotes:</h2>";
    foreach ($listing['quotes'] as $item) {
        ?>
        <div style="">
            <div class="notice">
                <p><strong><?php echo $item['content'] ?></strong></p>
                <p style="text-align: right">by <?= $item['author']['name'] ?></p>
            </div>
            <div style="margin: 1em 1em;display: flex;justify-content: flex-end">
                <a href="<?= add_query_arg(['page' => 'quotes-edit', 'quoteId' => $item['id']]) ?>"
                   class="button button-primary">Edit</a>
                <form method="post" onsubmit="return confirm('Are you sure want delete message?');"  action="<?= add_query_arg(['page' => 'quotes-edit']) ?>">
                    <button type="submit" name="deleteAction" value=<?=$item['id'] ?>
                       class="button button-error">Delete</button>
                </form>


            </div>
        </div>
        <?php
    }

    $page_links = paginate_links([
        'base' => add_query_arg('pagenum', '%#%'),
        'format' => '',
        'prev_text' => __('&laquo;', 'aag'),
        'next_text' => __('&raquo;', 'aag'),
        'total' => 25,
        'current' => $cur_page,
        'type' => 'array',
    ]);

    if (count($page_links)) {
        array_map(function ($el) {
            echo '<span class="tablenav-pages-navspan button" style="margin: 1em 0">'.
                $el.'</span>';
        }, $page_links);
    }

}
