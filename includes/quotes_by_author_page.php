<?php get_header();get_index_template(); ?>


<div class="site-main col-md-6">
    <div class="blog-post">
        <?php
        $url = get_option('bthquotes_settings_api_author');
        if (array_key_exists('quoteId', $_GET)) {
            $url = str_replace("%#%", $_GET['quoteId'], $url);
            $response = wp_remote_get($url, ['headers' => bthquotes_auth_headers()]);
            $http_code = wp_remote_retrieve_response_code($response);


            if ($http_code !== 200) {
                bthntx_quote_error('Server error');
                return;
            }
            $data = wp_remote_retrieve_body($response);
            $data = json_decode($data, true);
            if ($data
                && array_key_exists('quotes', $data)) {
                foreach ($data['quotes'] as $quote) {
                    if (array_key_exists('content', $quote)
                        && array_key_exists('id', $quote)
                        && array_key_exists('author', $quote)
                        && array_key_exists('name', $quote['author'])) {
                        echo "<div class='notice'>
            <blockquote>
            <p>{$quote['content']}</p>
            <p>{$quote['author']['name']}</p> 
            </blockquote> <hr>
                </div>";
                    }
                }
            }

        }
        ?>
    </div>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
