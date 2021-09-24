<?php 

// Esse action é para remover todas as rotas de api padrão do wordpress, porem se for ultilizar a parte administrativa do wordpress não usar.

/*remove_action('rest_api_init', 'create_initial_rest_routes', 99);*/

// Para remover apenas endpoints especificos.
add_filter('rest_endpoints', function($endpoints){
    unset($endpoints['/wp/v2/users']);
    unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
    return $endpoints;
});



$dirbase = get_template_directory();

require_once $dirbase . '/endpoints/user_post.php';
require_once $dirbase . '/endpoints/user_get.php';

require_once $dirbase . '/endpoints/photo_post.php';
require_once $dirbase . '/endpoints/photo_get.php';
require_once $dirbase . '/endpoints/photo_delete.php';

require_once $dirbase . '/endpoints/comment_post.php';
require_once $dirbase . '/endpoints/comment_get.php';

// Mudando o tamanho da imgem larga.
update_option('large_size_w', 1000); // Largura de 1000px.
update_option('large_size_h', 1000); // Altura de 1000px.
update_option('large_crop', 1); // Se a imagem não for quadrada deve cortar.

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