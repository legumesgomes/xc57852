<?php
function bio6_html_head($user_role = 'free') {
    $brand_name = yourls_get_option('wl_brand_name', 'bio6.click');
    $user = $_SESSION['ua_username'] ?? 'Usuário';
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Painel de Administração - <?php echo htmlspecialchars($brand_name); ?></title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
            body { font-family: 'Inter', sans-serif; }
            #main_table { width: 100%; border-collapse: collapse; background: #1e293b; border-radius: 0.75rem; overflow: hidden; }
            #main_table th, #main_table td { padding: 12px 16px; border-bottom: 1px solid #334155; }
            #main_table th { text-align: left; font-weight: 600; color: #cbd5e1; background: rgba(30, 41, 59, 0.7); }
            #main_table td { color: #94a3b8; }
            #main_table tbody tr:hover { background: #334155; }
            #main_table td.actions a { color: #60a5fa; text-decoration: none; margin-right: 1rem; }
            #main_table td.actions a:hover { text-decoration: underline; }
            .stats-icon { display:inline-block; vertical-align:middle; margin-left:6px; color:#60a5fa; }
            .stats-icon:hover { color:#3b82f6; }
        </style>
    </head>
    <body class="bg-slate-900 text-gray-200 antialiased">
    <div class="min-h-screen">

    <header class="bg-slate-800/50 border-b border-slate-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
            <a href="<?php echo yourls_admin_url(); ?>" class="flex items-center">
                <img src="https://bio6.click/user/plugins/yourls-white-label-main/uploads/1758760162_68d48ce252c74.png" style="max-height:30px;">
            </a>
            <nav class="flex items-center space-x-4">
                <a href="<?php echo yourls_admin_url(); ?>" class="text-gray-300 hover:text-white">Painel</a>
                <?php if ($user_role === 'admin') { ?>
                    <a href="<?php echo yourls_admin_url('tools.php'); ?>" class="text-gray-300 hover:text-white">Ferramentas</a>
                    <a href="<?php echo yourls_admin_url('plugins.php'); ?>" class="text-gray-300 hover:text-white">Plugins</a>
                    <a href="<?php echo yourls_admin_url('plugins.php?page=a-plugins'); ?>" class="text-gray-300 hover:text-white">A-Plugins</a>
                <?php } ?>
                <span class="text-gray-400">|</span>
                <span class="text-gray-300 text-sm">Olá, <?php echo htmlspecialchars($user); ?></span>
                <a href="?action=logout" class="text-gray-300 hover:text-white text-sm">[Sair]</a>
            </nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <?php
}
