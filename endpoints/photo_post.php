<?php

    // Função para buscar as informações do usuário logado.
    function api_photo_post($request) {

        $user = wp_get_current_user();
        $user_id = $user->ID;

        // Verifica se o usuário está logado.
        if($user_id === 0){
            $response = new WP_Error('error', 'Usuário não possui permissão.', ['status' => 401]);
            return rest_ensure_response($response);
        }

        $name = sanitize_text_field($request['name']); 
        $weight = sanitize_text_field($request['weight']); 
        $age = sanitize_text_field($request['age']);
        $files = $request->get_file_params();

        // Verificando se os campos estão preenchidos.
        if(empty($name) || empty($weight) || empty($age) || empty($files)){
            $response = new WP_Error('error', 'Dados imcompletos.', ['status' => 422]);
            return rest_ensure_response($response);
        }

        // Montando o array a ser enviado.
        $response = [
            'post_author' => $user_id,
            'post_type' => 'post',
            'post_status' => 'publish',
            'post_title' => $name,
            'post_content' => $name,
            'files' => $files,
            'meta_input' => [
                'weight' => $weight,
                'age' => $age,
                'access' => 0,
            ],
        ];

        $post_id = wp_insert_post($response);

        // Importando os arquivos com funções das imagems do core wordpress.
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        // Upload da foto relacionado ao post.
        $photo_id = media_handle_upload('img', $post_id);
        update_post_meta($post_id, 'img', $photo_id);
        
        return rest_ensure_response($response);
        
    }

    // Registrando a função de post na rota.
    function register_api_photo_post() {
        register_rest_route('api', '/photo', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => 'api_photo_post',
        ]);
    }

    add_action('rest_api_init', 'register_api_photo_post');

?>