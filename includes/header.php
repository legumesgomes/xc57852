<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$brand_name = yourls_get_option('wl_brand_name', 'bio6.click');
$user       = $_SESSION['ua_username'] ?? 'Usuário';
$user_role  = $_SESSION['ua_role'] ?? 'free';

// ----------------------------
// Forçar Logout
// ----------------------------
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Limpa sessão
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();

    // Redireciona para a home
    header("Location: " . YOURLS_SITE);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Administração - <?php echo htmlspecialchars($brand_name ?? ''); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-gray-200 antialiased">
<div class="min-h-screen">

<?php
// ----------------------------
// Função do menu
// ----------------------------
function bio6_html_menu($brand_name, $user, $user_role) {
?>
<header class="bg-slate-800/50 border-b border-slate-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        <!-- LOGO -->
        <a href="<?php echo yourls_admin_url(); ?>" class="flex items-center">
            <img src="https://bio6.click/user/plugins/yourls-white-label-main/uploads/1758760162_68d48ce252c74.png"
                 alt="Logo" class="h-8">
        </a>

        <!-- MENU DESKTOP -->
        <nav class="hidden md:flex items-center space-x-4">
            <a href="<?php echo yourls_admin_url(); ?>" class="text-gray-300 hover:text-white">Painel</a>

            <!-- Dropdown -->
            <div class="relative dropdown">
                <button class="text-gray-300 hover:text-white focus:outline-none">Suas Ferramentas ▾</button>
                <div class="absolute hidden dropdown-menu bg-slate-800 border border-slate-700 rounded-lg mt-2 py-2 w-56 shadow-lg z-50">
                    <a href="<?php echo yourls_admin_url('index.php'); ?>" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Links</a>

                    <?php if ($user_role === 'vip'): ?>
                         <a href="https://bio6.click/user/plugins/fb-pixel/admin-page.php" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Pixel Meta</a>
            <a href="https://bio6.click/admin/" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Estatísticas</a>
		    <a href="#" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">UTM - Campanhas</a>
			<a href="#" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Ads Page</a>
			<a href="#" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Open App</a>
			<a href="#" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Esconder URL</a>
			<a href="#" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Link com Senha</a>
			<a href="#" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">A/B Test</a>
			<a href="#" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Link Rastreável</a>
			<a href="#" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Seu Dominio</a>
			<a href="#" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Seu Bio6.me</a>
			<a href="#" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Seu Plano</a>
                    <?php endif; ?>

                    <?php if ($user_role === 'paid'): ?>
                        <a href="#" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Plugin Paid 1</a>
                        <a href="#" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Plugin Paid 2</a>
                    <?php endif; ?>

                    <?php if ($user_role === 'admin'): ?>
                        <a href="<?php echo yourls_admin_url('tools.php'); ?>" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Ferramentas</a>
                        <a href="<?php echo yourls_admin_url('plugins.php'); ?>" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Plugins</a>
                        <a href="<?php echo yourls_admin_url('a-plugins.php'); ?>" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">A-Plugins</a>
                    <?php endif; ?>
                </div>
            </div>

            <span class="text-gray-400">|</span>
            <span class="text-gray-300 text-sm">Olá, <?php echo htmlspecialchars($user ?? ''); ?></span>
            <a href="?action=logout" class="text-gray-300 hover:text-white text-sm">[Sair]</a>
        </nav>

        <!-- MENU MOBILE -->
        <div class="md:hidden flex items-center">
            <button id="mobile-menu-btn" class="text-gray-300 hover:text-white focus:outline-none">
                ☰
            </button>
        </div>
    </div>

    <!-- Dropdown Mobile -->
    <div id="mobile-menu" class="hidden md:hidden bg-slate-800 border-t border-slate-700">
        <a href="<?php echo yourls_admin_url(); ?>" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Painel</a>
        <a href="<?php echo yourls_admin_url('index.php'); ?>" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Links</a>

        <?php if ($user_role === 'vip'): ?>
            <a href="#" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Plugin VIP 1</a>
            <a href="#" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Plugin VIP 2</a>
        <?php endif; ?>

        <?php if ($user_role === 'paid'): ?>
            <a href="#" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Plugin Paid 1</a>
            <a href="#" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Plugin Paid 2</a>
        <?php endif; ?>

        <?php if ($user_role === 'admin'): ?>
            <a href="<?php echo yourls_admin_url('tools.php'); ?>" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Ferramentas</a>
            <a href="<?php echo yourls_admin_url('plugins.php'); ?>" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Plugins</a>
            <a href="<?php echo yourls_admin_url('a-plugins.php'); ?>" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">A-Plugins</a>
        <?php endif; ?>

        <a href="?action=logout" class="block px-4 py-2 text-gray-300 hover:bg-slate-700 hover:text-white">Sair</a>
    </div>
</header>

<main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
<?php
}
bio6_html_menu($brand_name, $user, $user_role);
?>

<!-- Scripts -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Dropdown desktop
    const dropdownBtn  = document.querySelector(".dropdown > button");
    const dropdownMenu = document.querySelector(".dropdown-menu");
    if (dropdownBtn && dropdownMenu) {
        dropdownBtn.addEventListener("click", function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle("hidden");
        });
        document.addEventListener("click", function(e) {
            if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.add("hidden");
            }
        });
    }

    // Mobile menu
    const mobileBtn  = document.getElementById("mobile-menu-btn");
    const mobileMenu = document.getElementById("mobile-menu");
    if (mobileBtn && mobileMenu) {
        mobileBtn.addEventListener("click", () => {
            mobileMenu.classList.toggle("hidden");
        });
    }
});
</script>
