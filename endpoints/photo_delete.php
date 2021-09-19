<?php

    // Função para buscar as informações do usuário logado.
    function api_photo_delete($request) {

        $post_id = $request['id'];
        $user = wp_get_current_user();
        $post = get_post($post_id);
        // int para o id sempre vim em tipo númerico.
        $author_id = (int)$post->post_author;
        $user_id = (int)$user->ID;

        // Verifica se o usuário logado é o criador do post ou se o post existe.
        if($user_id !== $author_id || !isset($post)){
            $response = new WP_Error('error', 'Sem permissão.', ['status' => 401]);
            return rest_ensure_response($response);
        }

        // Pega o id da imagem relacionado ao post.
        $attachement_id = get_post_meta($post_id, 'img', true);
        // deleta a imagem relacionada ao post.
        wp_delete_attachment($attachement_id, true);
        // Deleta o post.
        wp_delete_post($post_id, true);
        
        return rest_ensure_response('Post Deletado com sucesso!');
        
    }

    // Registrando a função de delete na rota.
    function register_api_photo_delete() {
        register_rest_route('api', '/photo/(?P<id>[0-9]+)', [
            'methods' => WP_REST_Server::DELETABLE,
            'callback' => 'api_photo_delete',
        ]);
    }

    add_action('rest_api_init', 'register_api_photo_delete');

?>