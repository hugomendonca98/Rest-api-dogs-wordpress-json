<?php

function photo_data($post) {
    // Função para buscar os dados a serem retornados.
    $post_meta = get_post_meta($post->ID);
    $src = wp_get_attachment_image_src($post_meta['img'][0], 'large')[0];
    $user = get_userdata($post->post_author);
    $total_comments = get_comments_number($post->ID);

    return [
        'id'=> $post->ID,
        'author'=> $user->user_login,
        'title'=> $post->post_title,
        'data'=> $post->post_date,
        'src'=> $src,
        'weight'=> $post_meta['weight'][0],
        'age'=> $post_meta['age'][0],
        'access'=> $post_meta['access'][0],
        'total_comments'=> $total_comments,
    ];
}

    function api_photo_get($request) {
        $post_id = $request['id'];
        $post = get_post($post_id);

        // Se o post não existir da um erro.
        if(!isset($post) || empty($post_id)){
            $response = new WP_Error('error', 'Post não encontrado.', ['status' => 404]);
            return rest_ensure_response($response);
        }

        // Executando a função criada no top do codigo.
        $photo = photo_data($post);

        // Pegando o total de acessos atual, somando +1 e depois fazendo o update.
        $photo['access'] = (int) $photo['access'] + 1;
        update_post_meta($post_id, 'access', $photo['access']);

        // pegando todos os comentarios de um post.
        $comments = get_comments([
            'post_id'=> $post_id,
            'order'=> 'ASC',
        ]);

        // Montando a resposta.
        $response = [
            'photo'=> $photo,
            'comments'=> $comments,
        ];
        
        return rest_ensure_response($response);
        
    }

    // Registrando a função de delete na rota.
    function register_api_photo_get() {
        register_rest_route('api', '/photo/(?P<id>[0-9]+)', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_photo_get',
        ]);
    }

    add_action('rest_api_init', 'register_api_photo_get');


    function api_photos_get($request) {
        // Operador ternario php, se o primeiro valor for verdadeiro deixa ele mesmo se não usa o outro (não me diga).
       $_total = sanitize_text_field($request['_total']) ?: 6;
       $_page = sanitize_text_field($request['_page']) ?: 1;
       
       $_user = sanitize_text_field($request['_user']) ?: 0;

       // Caso o usuário da api passe no lugar do user id o nome de usuário ele tenta puxar pelo nome, caso não consiga retornar um erro 404.
       if(!is_numeric($_user)){
           $user = get_user_by('login', $_user);
           if(!$user){
               $response = new WP_Error('error', 'Usuário não encontrado.', ['status' => 404]);
            return rest_ensure_response($response);
           }
           $_user = $user->ID;
       }

       // Argumentos para pesquisar os posts no query do wordpress.
       $args = [
            'post_type' => 'post',
            'author' => $_user,
            'post_per_page' => $_total,
            'paged' => $_page,
       ];

       $query = new WP_Query($args);
       // Pegando os posts da resposta do query.
       $posts = $query->posts;

       // Criando um array para armazenar apenas as informações dos posts que desejo ultilizar, lembrando que estou usando a função criada no topo do código.
       $photos = [];
       if($posts){
           // faz o laço nos posts e adiciona cada um com as informações desejadas ao array criado.
           foreach ($posts as $post){
            $photos[] = photo_data($post);
           };
       }
        
        return rest_ensure_response($photos);
        
    }

    // Registrando a função de delete na rota.
    function register_api_photos_get() {
        register_rest_route('api', '/photo', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_photos_get',
        ]);
    }

    add_action('rest_api_init', 'register_api_photos_get');

?>