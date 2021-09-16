<?php 
remove_action('rest_api_init', 'create_initial_rest_routes', 99);

$dirbase = get_template_directory();

require_once $dirbase . '/endpoints/user_post.php';
require_once $dirbase . '/endpoints/user_get.php';

function change_api() {
    return 'json';
}

add_filter('rest_url_prefix', 'change_api');

// Função para definir o tempo de expiração do token, no caso 24hrs.
function experi_token() {
    return time() + (60 * 60 * 24);
}

add_action('jwr_auth_expire', 'experi_token');

?>