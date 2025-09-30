<?php
/*
Plugin Name: Menu Head
Plugin URI: ../user/plugins/menu-head/admin-page.php
Description: Substitui o menu padrão do YOURLS por um menu customizado com controle de permissões por role.
Version: 1.0
Author: Você
*/

if (!defined('YOURLS_ABSPATH')) {
    die('No direct access allowed');
}

// Função principal que substitui o menu
function bio6_html_menu() {
    $brand_name = yourls_get_option('wl_brand_name', 'bio6.click');
    $user = YOURLS_USER;
    $role = $_SESSION['ua_role'] ?? 'free';

    $ydb = yourls_get_db();
    $access_table = YOURLS_DB_PREFIX . 'plugin_access';

    // Buscar permissões da tabela customizada
    $permissions = [];
    try {
        $rows = $ydb->fetchAll("SELECT plugin, role FROM `{$access_table}`");
        foreach ($rows as $row) {
            $permissions[$row['plugin']][] = $row['role'];
        }
    } catch (Exception $e) {
        $permissions = [];
    }

    ?>
    <header class="bg-slate-800/50 backdrop-blur-sm border-b border-slate-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <a href="<?php echo yourls_admin_url(); ?>" class="flex items-center space-x-2">
                        <img src="https://bio6.click/user/plugins/yourls-white-label-main/uploads/1758761163_68d490cb530b3.png"
                             alt="Logo"
                             class="h-8 w-auto">
                        <span class="text-2xl font-bold text-white"><?php echo htmlspecialchars($brand_name); ?></span>
                    </a>
                </div>
                <nav class="flex items-center space-x-4">
                    <a href="<?php echo yourls_admin_url(); ?>" class="text-gray-300 hover:bg-slate-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Painel</a>
                    
                    <?php
                    // Plugins ativos
                    $plugins = (array) yourls_get_plugins();
                    foreach ($plugins as $file => $plugin) {
                        if (!yourls_is_active_plugin($file)) {
                            continue;
                        }

                        $plugindir = trim(dirname($file), '/');
                        $plugin_name = $plugin['Plugin Name'] ?? $plugindir;

                        // Verificar permissões
                        $allowed_roles = $permissions[$file] ?? [];

                        if (in_array($role, $allowed_roles) || $role === 'admin') {
                            echo '<a href="' . yourls_admin_url('?page=' . $plugindir) . '" class="text-gray-300 hover:bg-slate-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">' . htmlspecialchars($plugin_name) . '</a>';
                        }
                    }
                    ?>

                    <span class="text-gray-400">|</span>
                    <span class="text-gray-300 text-sm">Olá, <?php echo $user; ?></span>
                    <a href="?action=logout" class="text-gray-300 hover:text-white text-sm font-medium" title="Sair">[Sair]</a>
                </nav>
            </div>
        </div>
    </header>
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <?php
}

// Fechar o <main> no rodapé
function bio6_html_footer() {
    ?>
    </main>
    <footer class="bg-slate-900 text-gray-500 py-6 text-center border-t border-slate-800 mt-8">
        <p>Powered by <a href="http://yourls.org/" class="text-blue-500 hover:underline">YOURLS</a></p>
    </footer>
    </div>
    <?php
}
