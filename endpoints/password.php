<?php

    // Função para buscar as informações do usuário logado.
    function api_password_lost($request) {
        $login = $request['login'];
        $url = $request['url'];

        // Se o nome de usuário foi passado.
        if(empty($login)){
            $response = new WP_Error('error', 'Informe o email ou login.', ['status' => 406]);
            return rest_ensure_response($response);
        }

        // tenta pegar o nome de usuário via email que foi passado.
        $user = get_user_by('email', $login);
        // caso não consiga tenta por nome de usuário.
        if(empty($user)){
            $user = get_user_by('login', $login);
        }

        // Se mesmo apos essas duas tentativas não obtiver sucesso retorna um erro.
        if(empty($user)){
            $response = new WP_Error('error', 'Usuário não existe.', ['status' => 401]);
            return rest_ensure_response($response);
        }

        // Pegando o login do usuário e email.
        $user_login = $user->user_login;
        $user_email = $user->user_email;

        // Gera o hash do link da página para ser resetado o login.
        $key = get_password_reset_key($user);

        // Instruções e link.
        $message = "Ultilize o link abaixo para resetar a sua senha: \r\n";
        $url = esc_url_raw($url . "/?key=$key&login=" . rawurlencode($user_login) . "\r\n");
        $body = $message . $url;

        // Função do wordpress para enviar o email.
        wp_mail($user_email, 'Password reset', $body);
     
        return rest_ensure_response('Email enviado com sucesso!');
        
    }

    // Registrando a função de delete na rota.
    function register_api_password_lost() {
        register_rest_route('api', '/password/lost', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => 'api_password_lost',
        ]);
    }

    add_action('rest_api_init', 'register_api_password_lost');

?>