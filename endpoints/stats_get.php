<?php

    // Função para buscar as informações do usuário logado.
    function api_stats_get($request) {
     $user = wp_get_current_user();
     $user_id = $user->ID;

     if($user_id === 0) {
         $response = new WP_Error('error', 'Usuário não possui permissão.', ['status' => 401]);
        return rest_ensure_response($response);
     }

     $args = [
        'post_type'=> 'post',
        'author'=> $user_id,
        'post_per_page'=> -1,
     ];
     
     $query = new WP_Query($args);
     $posts = $query->posts;

     $stats = [];
     if($posts){
         foreach($posts as $post){
             $stats[] = [
                'id'=> $post->ID,
                'title'=> $post->post_title,
                'access'=> get_post_meta($post->ID, 'access', true),
             ];
         }
     }
     
        return rest_ensure_response($stats);
        
    }

    // Registrando a função de stats na rota.
    function register_api_stats_get() {
        register_rest_route('api', '/stats', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_stats_get',
        ]);
    }

    add_action('rest_api_init', 'register_api_stats_get');

?>