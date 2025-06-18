<?php
/*
Plugin Name: Botão WhatsApp com Formulário Avançado
Plugin URI: https://github.com/classferreiracode/botao-flutuante-custom-wp
Description: Formulário flutuante customizável com envio externo ou WhatsApp, e atualizações via GitHub.
Version: 1.4.2
Author: classFerreiraCode
Author URI: https://github.com/classferreiracode/
*/

add_action('init', function () {
    if (is_admin()) {
        include_once plugin_dir_path(__FILE__) . 'update-checker.php';
    }
});

// Admin menu
add_action('admin_menu', function () {
    add_options_page(
        'Botão WhatsApp Avançado',
        'Botão WhatsApp Avançado',
        'manage_options',
        'botao-whatsapp-avancado',
        'botao_whatsapp_v6_config_page'
    );
});

// Configurações
add_action('admin_init', function () {
    register_setting('botao_whatsapp_v6_options', 'botao_whatsapp_v6_action_url');
    register_setting('botao_whatsapp_v6_options', 'botao_whatsapp_v6_fields_json');
    register_setting('botao_whatsapp_v6_options', 'botao_whatsapp_v6_posicao');
    register_setting('botao_whatsapp_v6_options', 'botao_whatsapp_v6_imagem');
    register_setting('botao_whatsapp_v6_options', 'botao_whatsapp_v6_texto_msg');
});

function botao_whatsapp_v6_config_page()
{
?>
    <div class="wrap">
        <h1>Configurações do Formulário Flutuante</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('botao_whatsapp_v6_options');
            do_settings_sections('botao_whatsapp_v6_options');
            ?>
            <table class="form-table">
                <tr>
                    <th><label for="botao_whatsapp_v6_action_url">URL de Envio do Formulário (action)</label></th>
                    <td><input type="text" name="botao_whatsapp_v6_action_url" value="<?php echo esc_attr(get_option('botao_whatsapp_v6_action_url')); ?>" class="large-text" /></td>
                </tr>
                <tr>
                    <th><label for="botao_whatsapp_v6_imagem">URL da Imagem do Botão</label></th>
                    <td><input type="text" name="botao_whatsapp_v6_imagem" value="<?php echo esc_attr(get_option('botao_whatsapp_v6_imagem')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="botao_whatsapp_v6_texto_msg">Texto da Mensagem no WhatsApp</label></th>
                    <td><input type="text" name="botao_whatsapp_v6_texto_msg" value="<?php echo esc_attr(get_option('botao_whatsapp_v6_texto_msg')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="botao_whatsapp_v6_posicao">Posição do Botão</label></th>
                    <td>
                        <select name="botao_whatsapp_v6_posicao">
                            <?php $selected = get_option('botao_whatsapp_v6_posicao', 'bottom-right'); ?>
                            <option value="bottom-right" <?php selected($selected, 'bottom-right'); ?>>Inferior direita</option>
                            <option value="bottom-left" <?php selected($selected, 'bottom-left'); ?>>Inferior esquerda</option>
                            <option value="top-right" <?php selected($selected, 'top-right'); ?>>Superior direita</option>
                            <option value="top-left" <?php selected($selected, 'top-left'); ?>>Superior esquerda</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="botao_whatsapp_v6_fields_json">Campos do Formulário (JSON)</label></th>
                    <td>
                        <textarea name="botao_whatsapp_v6_fields_json" rows="12" class="large-text code"><?php echo esc_textarea(get_option('botao_whatsapp_v6_fields_json', '[
  {"id": "first_name", "label": "Primeiro Nome", "type": "text", "required": true},
  {"id": "last_name", "label": "Sobrenome", "type": "text", "required": true},
  {"id": "phone_mobile", "label": "Telefone", "type": "text", "required": true},
  {"id": "email1", "label": "Email", "type": "email", "required": true},
  {"id": "campaign_id", "type": "hidden", "value": "4d0109a5-fdc3-14c0-4672-6842e46e0ed8"},
  {"id": "assigned_user_id", "type": "hidden", "value": "1"},
  {"id": "moduleDir", "type": "hidden", "value": "Leads"}
]')); ?></textarea>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

// Exibição no site
add_action('wp_footer', function () {
    $action = esc_url(get_option('botao_whatsapp_v6_action_url', '#'));
    $posicao = get_option('botao_whatsapp_v6_posicao', 'bottom-right');
    $imagem = esc_url(get_option('botao_whatsapp_v6_imagem', ''));
    $msg = urlencode(get_option('botao_whatsapp_v6_texto_msg', 'Olá, gostaria de mais informações!'));
    $fields_json = get_option('botao_whatsapp_v6_fields_json', '[]');
    $fields = json_decode($fields_json, true);
    if (!is_array($fields)) return;

    $posicoes = [
        'bottom-right' => 'bottom:20px;right:20px;',
        'bottom-left' => 'bottom:20px;left:20px;',
        'top-right' => 'top:20px;right:20px;',
        'top-left' => 'top:20px;left:20px;',
    ];
    $style_pos = $posicoes[$posicao] ?? $posicoes['bottom-right'];

    echo "<div id='whatsapp-float' style='{$style_pos}'>";
    echo "<a onclick='openForm()' style='cursor:pointer;'>";
    if ($imagem) {
        echo "<img src='{$imagem}' alt='Ícone' style='height:72px;width:72px;'>";
    } else {
        echo "<img src='https://analiza.amzmp.com.br/icones/icons8-whatsapp-96.png' alt='Ícone' style='height:72px;width:72px;'>";
    }
    echo "</a></div>";

    echo "<div id='form-modal' class='hidden'><div class='form-content'>";
    echo "<form id='WebToLeadForm' action='{$action}' method='POST' target='_blank'>";
    echo "<h3>Fale conosco</h3>";


    foreach ($fields as $field) {
        $id = esc_attr($field['id']);
        $label = $field['label'] ?? '';
        $type = esc_attr($field['type']);
        $value = $field['value'] ?? '';
        $required = !empty($field['required']) ? 'required' : '';

        if ($type === 'hidden') {
            echo "<input type='hidden' name='{$id}' id='{$id}' value='{$value}'>";
        } else {
            if ($label) echo "<label for='{$id}'>{$label}:</label>";
            if ($type === 'textarea') {
                echo "<textarea name='{$id}' id='{$id}' {$required}></textarea>";
            } else {
                echo "<input type='{$type}' name='{$id}' id='{$id}' value='' {$required}>";
            }
        }
    }

    echo "<input type='hidden' name='redirect_url' id='redirect_url' value='https://api.whatsapp.com/send/?phone=551935145050&text={$msg}&type=phone_number&app_absent=0'>";

    echo "<button type='submit'>Enviar</button>
          <button type='button' onclick='closeForm()' style='background:#ccc;'>Fechar</button>
          </form></div></div>";
?>
    <style>
        #whatsapp-float {
            position: fixed;
            z-index: 9999;
        }

        #whatsapp-float button {
            background: transparent !important;
            border: none;
            padding: 0;
            cursor: pointer;
            box-shadow: none;
        }

        #whatsapp-float img {
            vertical-align: middle;
        }

        #form-modal.hidden {
            display: none;
        }

        #form-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 10000;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-content {
            background: white;
            padding: 20px;
            width: 90%;
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0 0 10px black;
        }

        .form-content input,
        .form-content textarea {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-content button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
    <script>
        function openForm() {
            document.getElementById('form-modal').classList.remove('hidden');
        }

        function closeForm() {
            document.getElementById('form-modal').classList.add('hidden');
        }

        document.getElementById('WebToLeadForm').addEventListener('submit', function(event) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.border = '2px solid red';
                } else {
                    field.style.border = '';
                }
            });
            if (!isValid) {
                event.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios.');
            }
        });
    </script>
<?php
});
