<?php
function bthtx_quotes_edit()
{
    $data = ['content' => '', 'author' => ['name' => '']];
    if ($_POST) {
        if (!array_key_exists('deleteAction', $_POST)) {
            $data = ['content' => $_POST['content'], 'author' => ['name' => $_POST['name']]];
        }
        if (array_key_exists('deleteAction', $_POST) && $_POST['deleteAction'] > 0) {
            $url = get_option('bthquotes_settings_api_item');
            $url .= "/".((int)$_POST['deleteAction']);
            $response = wp_remote_request($url, ['method' => 'DELETE', 'headers' => bthquotes_auth_headers()]);
            echo "DELETE $url <br>";
        } else {
            if (array_key_exists('quoteId',$_REQUEST) && $_REQUEST['quoteId'] > 0) {
                $url = get_option('bthquotes_settings_api_item');
                $url .= "/".((int)$_REQUEST['quoteId']);
                $response = wp_remote_request($url, [
                    'method' => 'PUT',
                    'headers' => bthquotes_auth_headers(),
                    'body' => json_encode($data),
                ]);
                echo "PUT $url <br>";
            } else {
                $url = get_option('bthquotes_settings_api_listing');
                $response = wp_remote_post($url, ['headers' => bthquotes_auth_headers(), 'body' => json_encode($data)]);
                echo "POST $url <br>";
            }
        }

        $http_code = wp_remote_retrieve_response_code($response);
        if ($http_code !== 201 && $http_code !== 204) {
            echo "<pre>";
            print_r($response);
            echo "</pre>";
            bthtx_showForm($data);

            return;

        }
        echo "<div class='wpbody-content'>Looks like ok</div> ";
        $link = add_query_arg(['page' => 'quotes-plugin']);
        echo "<a href='{$link}' class='button button-primary'>Back to quotes list</a>";
        return;

    } elseif (array_key_exists("quoteId", $_GET) && (int)$_GET['quoteId'] > 0) {
        $url = get_option('bthquotes_settings_api_item');
        $url .= "/".((int)$_GET['quoteId']);
        $response = wp_remote_get($url, ['headers' => bthquotes_auth_headers()]);
        $http_code = wp_remote_retrieve_response_code($response);
        if ($http_code !== 200) {
            bthntx_quote_error('Server error.');
            return;
        }
        $data = wp_remote_retrieve_body($response);
        $data = json_decode($data, true);

    }


    bthtx_showForm($data);
}

function bthtx_showForm($data)
{
    ?>
    <form method="post">
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">Quote text:</th>
                <td>
                    <textarea class="regular-text" rows="10"
                              name="content"><?= esc_textarea($data['content']) ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">Author of the quote</th>
                <td><input class="regular-text" type="text" name="name"
                           value="<?= esc_attr($data['author']['name']) ?>">
                </td>
            </tr>
            </tbody>
        </table>
        <?php submit_button(); ?>
    </form>

    <?php

}
