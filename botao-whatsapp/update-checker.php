<?php
add_filter('site_transient_update_plugins', function ($transient) {
    if (empty($transient->checked)) return $transient;

    $remote = wp_remote_get('https://raw.githubusercontent.com/classferreiracode/botao-flutuante-custom-wp/master/update.json');
    if (is_wp_error($remote) || wp_remote_retrieve_response_code($remote) !== 200) return $transient;

    $remote = json_decode(wp_remote_retrieve_body($remote));
    if (
        version_compare($remote->version, get_plugin_data(__FILE__)['Version'], '>') &&
        isset($remote->download_url)
    ) {
        $plugin_slug = plugin_basename(__DIR__ . '/botao-whatsapp.php');
        $transient->response[$plugin_slug] = (object) [
            'slug' => $plugin_slug,
            'new_version' => $remote->version,
            'url' => $remote->homepage ?? '',
            'package' => $remote->download_url,
        ];
    }

    return $transient;
});
