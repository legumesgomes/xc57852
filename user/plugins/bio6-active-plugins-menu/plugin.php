<?php
/*
Plugin Name: Menu Plugins Ativos
Plugin URI: https://bio6.click
Description: Adiciona um menu no header listando apenas os plugins ativos, cada um como link para sua página.
Version: 1.0
Author: bio6.click
Author URI: https://bio6.click
*/

// Hook para injetar o menu no header
yourls_add_action( 'admin_notices', 'bio6_render_active_plugins_menu' );

/**
 * Renderiza o menu com links para plugins ativos
 */
function bio6_render_active_plugins_menu() {
    $active_plugins = (array) yourls_get_option( 'active_plugins' );

    if( empty( $active_plugins ) ) {
        return;
    }

    echo '<nav class="bio6-plugins-menu bg-slate-800/70 backdrop-blur-sm border border-slate-700 rounded-lg px-4 py-2 my-4">';
    echo '<span class="text-gray-300 font-semibold mr-4">Plugins Ativos:</span>';

    foreach( $active_plugins as $plugin_file ) {
        $plugindir   = trim( dirname( $plugin_file ), '/' );
        $plugin_data = yourls_get_plugin_data( YOURLS_PLUGINDIR . '/' . $plugin_file );
        $plugin_name = $plugin_data['Plugin Name'] ?? $plugindir;

        $url = yourls_admin_url( 'plugins.php?page=' . $plugindir );

        echo '<a href="'. $url .'" class="text-blue-400 hover:text-blue-300 mr-4">'. htmlspecialchars( $plugin_name ) .'</a>';
    }

    echo '</nav>';
}
