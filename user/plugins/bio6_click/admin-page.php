<?php
/*
Plugin Name: Bio6.click
Plugin URI: ../user/plugins/bio6_click/admin-page.php
Description: Tema personalizado Tailwind para YOURLS
Version: 1.0
Author: Bio6.click
Author URI: https://bio6.click
*/

define( 'YOURLS_ADMIN', true );

// Sobe 3 níveis até /includes/
require_once( dirname( __DIR__, 3 ) . '/includes/load-yourls.php' );

yourls_maybe_require_auth();

// Cabeçalho oficial do YOURLS
yourls_html_head( 'bio6_click', 'Configurações do Tema Bio6.click' );
yourls_html_logo();
yourls_html_menu();

echo '<main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">';
echo '<h1 class="text-3xl font-bold text-gray-800 mb-6">Configurações do Tema Bio6.click</h1>';
echo '<p class="text-gray-600">Aqui você poderá gerenciar opções futuras do tema.</p>';
echo '</main>';

// Rodapé oficial + customização
yourls_html_footer();
echo '<div class="text-center py-4 text-sm text-gray-500">
Bio6.click | Bio6.me - Powered by <a href="https://lgw.one" target="_blank" class="underline">Lgw.one</a>
</div>';
