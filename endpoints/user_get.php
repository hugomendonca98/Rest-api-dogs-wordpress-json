<?php

    // Função para buscar as informações do usuário logado.
    function api_user_get($request) {

        // Função do wordpress para pegar o usuário.
        $user = wp_get_current_user();
        // Criando uma varíavel com o valor de id que tem dentro do objeto user. ou seja $user = user.id
        $user_id = $user->ID;

        // Caso o tente buscar sem um usuário logado.
        if($user_id === 0) {
            $response = new WP_Error('error', 'Usuário não possui permissão.', 'status' => 401);

            return rest_ensure_response($response);
        }

        // Monstando as informçaões que desejo retornar.
        $response = [
            'id' => $user_id,
            'username' => $user->user_login,
            'name' => $user->display_name,
            'email' => $user->user_email,
        ];

        return rest_ensure_response($response);
    }

    // Registrando a função de post na rota.
    function register_api_user_get() {
        register_rest_route('api', '/user', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'api_user_get',
        ]);
    }

    add_action('rest_api_init', 'register_api_user_get');

?>