<?php
/*
Plugin Name: Bio6.click
Plugin URI: https://bio6.click
Description: Tema personalizado Tailwind para o YOURLS
Version: 1.0
Author: Bio6.click
Author URI: https://bio6.click
*/

// Bloqueia acesso direto
if( !defined( 'YOURLS_ABSPATH' ) ) die();

// Carregar CSS e JS do plugin
yourls_add_action( 'html_head', 'bio6_enqueue_assets' );
function bio6_enqueue_assets() {
    $plugin_url = yourls_plugin_url( dirname( __FILE__ ) );
    echo '<link rel="stylesheet" href="'. $plugin_url . '/assets/style.css?v=1.0">'."\n";
    echo '<script src="'. $plugin_url . '/assets/script.js?v=1.0" defer></script>'."\n";
}

// Substituir o cabeçalho
yourls_add_action( 'html_head', 'bio6_custom_header', 1 );
function bio6_custom_header() {
    ?>
    <header class="bg-slate-800 text-white py-3 px-6 flex justify-between items-center shadow-md">
        <div class="flex items-center space-x-3">
            <img src="https://bio6.click/user/plugins/yourls-white-label-main/logo.png" alt="Logo" class="h-8">
            <span class="font-bold text-lg">Bio6.click</span>
        </div>
        <nav id="bio6-menu" class="space-x-4">
            <!-- Links do menu virão depois -->
        </nav>
    </header>
    <?php
}

// Substituir o rodapé
yourls_add_action( 'html_footer', 'bio6_custom_footer' );
function bio6_custom_footer() {
    ?>
    <footer class="bg-slate-900 text-gray-400 text-center py-4 mt-12 border-t border-slate-800">
        Bio6.click | Bio6.me - Powered by <a href="https://lgw.one" target="_blank" class="text-blue-400 hover:underline">Lgw.one</a>
    </footer>
    <?php
}
