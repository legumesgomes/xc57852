<?php
/*
Plugin Name:  User Auth
Plugin URI:   https://your-site.example/
Description:  Registro/Login simples para YOURLS (sem dependências externas).
Version:      1.9
Author:       Você
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hook principal para interceptar ações
yourls_add_action('pre_load_template', 'ua_router');
yourls_add_action('loader_failed', 'ua_router');

function ua_router($args = null) {
    if (!isset($_GET['ua_action'])) {
        return;
    }

    $action = $_GET['ua_action'];

    if (isset($_SESSION['ua_user_id']) && in_array($action, ['register', 'login'], true)) {
        header('Location: ' . YOURLS_SITE . '/admin/');
        exit;
    }

    if ($action === 'register') {
        ua_display_register_page();
        exit;
    } elseif ($action === 'login') {
        ua_display_login_page();
        exit;
    } elseif ($action === 'logout') {
        session_destroy();
        header('Location: ' . YOURLS_SITE . '/index.php');
        exit;
    }
}

/**
 * Página de Registro
 */
function ua_display_register_page() {
    $errors = [];
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $email    = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if (!$username || !$email || !$password) {
            $errors[] = 'Todos os campos são obrigatórios.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido.';
        } else {
            $ydb = yourls_get_db();
            $prefix = defined('YOURLS_DB_PREFIX') ? YOURLS_DB_PREFIX : 'yourls_';
            $users_table = $prefix . 'users_custom';
            $hash = password_hash($password, PASSWORD_DEFAULT);

            try {
                $exists = $ydb->fetchOne(
                    "SELECT id FROM `{$users_table}` WHERE username = :u OR email = :e LIMIT 1",
                    ['u' => $username, 'e' => $email]
                );

                if ($exists && isset($exists['id'])) {
                    $errors[] = 'Usuário ou e-mail já cadastrado.';
                } else {
                    $ydb->perform(
                        "INSERT INTO `{$users_table}` (username,email,password_hash,role,created_at) 
                         VALUES (:u,:e,:p,'free',NOW())",
                        ['u' => $username, 'e' => $email, 'p' => $hash]
                    );
                    $success = 'Cadastro realizado com sucesso. Você pode <a href="?ua_action=login" class="font-bold text-blue-400 hover:underline">entrar</a>.';
                }
            } catch (Exception $e) {
                $errors[] = 'Erro no banco de dados: ' . $e->getMessage();
            }
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Criar Conta - <?php echo yourls_get_option('wl_brand_name', 'bio6.click'); ?></title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
            body { font-family: 'Inter', sans-serif; }
            .aurora-bg {
                position: fixed; inset: 0; z-index: -1;
                background: radial-gradient(ellipse 50% 50% at 20% 20%, rgba(59,130,246,0.3), transparent),
                            radial-gradient(ellipse 50% 50% at 80% 90%, rgba(139,92,246,0.2), transparent);
                animation: aurora 15s ease-in-out infinite;
            }
            @keyframes aurora {
                0%,100% { background-position: 0% 50%, 0% 50%; }
                50% { background-position: 100% 50%, 100% 50%; }
            }
        </style>
    </head>
    <body class="bg-slate-900 text-gray-200 antialiased">
        <div class="aurora-bg"></div>
        <div class="min-h-screen flex flex-col items-center justify-center p-4">
            <div class="w-full max-w-md bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl shadow-lg p-8">
                <h1 class="text-3xl font-bold text-center text-white mb-2">Criar Nova Conta</h1>
                <p class="text-center text-gray-400 mb-6">Junte-se a nós e comece a encurtar seus links.</p>

                <?php if ($success): ?>
                    <div class="bg-green-500/10 border border-green-500 text-green-300 text-sm rounded-lg p-4 mb-4"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="bg-red-500/10 border border-red-500 text-red-300 text-sm rounded-lg p-4 mb-4">
                        <?php foreach ($errors as $err) echo "<p>" . htmlspecialchars($err) . "</p>"; ?>
                    </div>
                <?php endif; ?>

                <?php if (!$success): ?>
                <form method="post" class="space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-300 mb-1">Nome de Usuário</label>
                        <input type="text" name="username" id="username" required class="w-full h-12 bg-slate-900/50 border border-slate-600 rounded-lg px-4 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-1">E-mail</label>
                        <input type="email" name="email" id="email" required class="w-full h-12 bg-slate-900/50 border border-slate-600 rounded-lg px-4 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Senha</label>
                        <input type="password" name="password" id="password" required class="w-full h-12 bg-slate-900/50 border border-slate-600 rounded-lg px-4 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                    <button type="submit" class="w-full h-12 px-8 bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold text-white transition-transform duration-300 hover:scale-105">Cadastrar</button>
                </form>
                <?php endif; ?>

                <p class="text-center text-sm text-gray-400 mt-6">
                    Já tem uma conta? <a href="?ua_action=login" class="font-semibold text-blue-400 hover:underline">Entrar</a>
                </p>
            </div>
        </div>
    </body>
    </html>
    <?php
}

/**
 * Página de Login
 */
function ua_display_login_page() {
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if (!$username || !$password) {
            $error = 'Preencha usuário e senha.';
        } else {
            $ydb = yourls_get_db();
            $prefix = defined('YOURLS_DB_PREFIX') ? YOURLS_DB_PREFIX : 'yourls_';
            $users_table = $prefix . 'users_custom';

            $row = $ydb->fetchOne(
                "SELECT * FROM `{$users_table}` WHERE username = :u LIMIT 1",
                ['u' => $username]
            );

            if ($row && password_verify($password, $row['password_hash'])) {
                $_SESSION['ua_user_id']  = $row['id'];
                $_SESSION['ua_username'] = $row['username'];
                $_SESSION['ua_role']     = $row['role'];
                header('Location: ' . YOURLS_SITE . '/admin/');
                exit;
            } else {
                $error = 'Usuário ou senha incorretos.';
            }
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Acessar Conta - <?php echo yourls_get_option('wl_brand_name', 'bio6.click'); ?></title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    </head>
    <body class="bg-slate-900 text-gray-200 antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center p-4">
            <div class="w-full max-w-md bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl shadow-lg p-8">
                <h1 class="text-3xl font-bold text-center text-white mb-2">Acessar sua Conta</h1>
                <p class="text-center text-gray-400 mb-6">Bem-vindo de volta!</p>

                <?php if ($error): ?>
                    <div class="bg-red-500/10 border border-red-500 text-red-300 text-sm rounded-lg p-4 mb-4"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="post" class="space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-300 mb-1">Nome de Usuário</label>
                        <input type="text" name="username" id="username" required class="w-full h-12 bg-slate-900/50 border border-slate-600 rounded-lg px-4 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Senha</label>
                        <input type="password" name="password" id="password" required class="w-full h-12 bg-slate-900/50 border border-slate-600 rounded-lg px-4 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                    <button type="submit" class="w-full h-12 px-8 bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold text-white transition-transform duration-300 hover:scale-105">Entrar</button>
                </form>

                <p class="text-center text-sm text-gray-400 mt-6">
                    Não tem uma conta? <a href="?ua_action=register" class="font-semibold text-blue-400 hover:underline">Cadastre-se</a>
                </p>
            </div>
        </div>
    </body>
    </html>
    <?php
}

/**
 * Vincular links ao usuário logado
 */
yourls_add_action('post_add_new_link', 'ua_attach_user_to_link');

function ua_attach_user_to_link($args) {
    $keyword = '';

    if (is_array($args) && !empty($args['keyword'])) {
        $keyword = $args['keyword'];
    } elseif (is_array($args) && isset($args[1])) {
        $keyword = $args[1];
    } elseif (isset($_POST['keyword'])) {
        $keyword = $_POST['keyword'];
    }

    if ($keyword && isset($_SESSION['ua_user_id'])) {
        $ydb = yourls_get_db();
        $url_table = defined('YOURLS_DB_TABLE_URL') ? YOURLS_DB_TABLE_URL : (YOURLS_DB_PREFIX . 'url');

        // Correção: usar perform() para bind de parâmetros
        $ydb->perform(
            "UPDATE `{$url_table}` SET user_id = :uid WHERE keyword = :kw",
            ['uid' => (int)$_SESSION['ua_user_id'], 'kw' => $keyword]
        );
    }
}
