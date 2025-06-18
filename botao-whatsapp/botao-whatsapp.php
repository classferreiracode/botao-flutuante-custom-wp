<?php
/*
Plugin Name: Botão WhatsApp com Formulário Avançado
Plugin URI: https://github.com/classferreiracode/botao-whatsapp
Description: Formulário flutuante customizável com envio externo ou WhatsApp, e atualizações via GitHub.
Version: 7.1
Author: classFerreiraCode
Author URI: https://github.com/classferreiracode/
*/

add_action('init', function() {
    if (is_admin()) {
        include_once plugin_dir_path(__FILE__) . 'update-checker.php';
    }
});

// O resto do código do plugin continua aqui normalmente (ex: carregar formulário, etc)
// Para este exemplo, vamos apenas manter o carregamento do update checker.
