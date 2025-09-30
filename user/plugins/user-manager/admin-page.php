<?php

define('YOURLS_ADMIN', true);

require_once(dirname(__DIR__, 3) . '/includes/load-yourls.php');

yourls_maybe_require_auth();

// Bloqueia acesso para não-admins
session_start();
if (!isset($_SESSION['ua_role']) || $_SESSION['ua_role'] !== 'admin') {
    die('<div style="color:red;text-align:center;margin-top:50px;font-family:sans-serif">
            🚫 Acesso restrito aos administradores.
         </div>');
}

// **********************************************************************************
// * INÍCIO: FUNÇÕES PARA GERAR O LAYOUT MODERNO
// **********************************************************************************

function bio6_html_head($page_title) {
    $brand_name = yourls_get_option('wl_brand_name', 'bio6.click');
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($page_title); ?> - <?php echo htmlspecialchars($brand_name); ?></title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
            body { font-family: 'Inter', sans-serif; }
            #main_table { width: 100%; border-collapse: collapse; background-color: #1e293b; border-radius: 0.75rem; overflow: hidden; }
            #main_table thead { background-color: rgba(30, 41, 59, 0.5); }
            #main_table th, #main_table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #334155; }
            #main_table th { font-weight: 600; color: #cbd5e1; }
            #main_table tbody tr:hover { background-color: #334155; }
            #main_table tbody tr:last-child td { border-bottom: none; }
            #main_table td { color: #94a3b8; vertical-align: middle; }
            #main_table td a { color: #60a5fa; text-decoration: none; }
            #main_table td a:hover { text-decoration: underline; }
        </style>
    </head>
    <body class="bg-slate-900 text-gray-200 antialiased">
    <div class="min-h-screen">
    <?php
}

function bio6_html_menu() {
    $brand_name = yourls_get_option('wl_brand_name', 'bio6.click');
    $user = YOURLS_USER;
    ?>
    <header class="bg-slate-800/50 backdrop-blur-sm border-b border-slate-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="<?php echo yourls_admin_url(); ?>" class="text-2xl font-bold text-white"><?php echo htmlspecialchars($brand_name); ?></a>
                </div>
                <nav class="flex items-center space-x-4">
                    <a href="<?php echo yourls_admin_url(); ?>" class="text-gray-300 hover:bg-slate-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Painel</a>
                    <a href="<?php echo yourls_admin_url('tools.php'); ?>" class="text-gray-300 hover:bg-slate-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Ferramentas</a>
                    <a href="<?php echo yourls_admin_url('plugins.php'); ?>" class="text-gray-300 hover:bg-slate-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Plugins</a>
                    <span class="text-gray-400">|</span>
                    <span class="text-gray-300 text-sm">Olá, <?php echo $user; ?> (admin)</span>
                    <a href="?action=logout" class="text-gray-300 hover:text-white text-sm font-medium" title="Sair">[Sair]</a>
                </nav>
            </div>
        </div>
    </header>
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <?php
}

function bio6_html_footer() {
    ?>
    </main>
    <footer class="bg-slate-900 text-gray-500 py-6 text-center border-t border-slate-800 mt-8">
        <p>Powered by <a href="http://yourls.org/" class="text-blue-500 hover:underline">YOURLS</a></p>
    </footer>
    </div>
    <?php
}

// **********************************************************************************
// * FIM: FUNÇÕES DE LAYOUT
// **********************************************************************************

global $ydb;
$table = YOURLS_DB_PREFIX . "users_custom"; 

// Alterar role
if (isset($_POST['update_role'])) {
    $id   = intval($_POST['id']);
    $role = trim($_POST['role']);

    $ydb->fetchAffected(
        "UPDATE `$table` SET role = :role WHERE id = :id",
        ['role' => $role, 'id' => $id]
    );

    echo "<div class='mb-4 p-4 bg-blue-500/10 border border-blue-500 text-blue-300 text-sm rounded-lg'>
            Role do usuário #$id atualizado para <b>" . htmlspecialchars($role) . "</b>.
          </div>";
}

// Exclusão
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $ydb->fetchAffected("DELETE FROM `$table` WHERE id = :id", ['id' => $id]);
    echo "<div class='mb-4 p-4 bg-red-500/10 border border-red-500 text-red-300 text-sm rounded-lg'>Usuário #$id removido com sucesso!</div>";
}

// Listagem
$users = $ydb->fetchObjects("SELECT * FROM `$table` ORDER BY id DESC");

// Renderização
bio6_html_head('Gestão de Usuários');
bio6_html_menu();
?>

<h1 class="text-3xl font-bold text-white mb-6">👤 Gestão de Usuários</h1>

<div>
    <h2 class="text-xl font-bold text-white mb-4">Lista de Usuários</h2>
    <table id="main_table">
        <thead>
            <tr>
                <th>ID</th><th>Usuário</th><th>Email</th><th>Role</th><th>Criado em</th><th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $u): ?>
            <tr>
                <td><?php echo $u->id; ?></td>
                <td><?php echo htmlspecialchars($u->username); ?></td>
                <td><?php echo htmlspecialchars($u->email); ?></td>
                <td>
                    <form method="post" class="flex items-center space-x-2">
                        <input type="hidden" name="id" value="<?php echo $u->id; ?>">
                        <select name="role" class="bg-slate-900/50 border border-slate-600 rounded px-2 py-1 text-sm text-white">
                            <option value="free"  <?php if($u->role==='free')  echo 'selected'; ?>>Free</option>
                            <option value="paid"  <?php if($u->role==='paid')  echo 'selected'; ?>>Paid</option>
                            <option value="vip"   <?php if($u->role==='vip')   echo 'selected'; ?>>VIP</option>
                            <option value="admin" <?php if($u->role==='admin') echo 'selected'; ?>>Admin</option>
                        </select>
                        <button type="submit" name="update_role" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 rounded text-white text-sm">Salvar</button>
                    </form>
                </td>
                <td><?php echo $u->created_at; ?></td>
                <td class="actions">
                    <a href="?delete=<?php echo $u->id; ?>" onclick="return confirm('Tem certeza que deseja excluir este usuário?')" class="text-red-400 hover:text-red-300 hover:underline">Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="6" class="text-center py-8">Nenhum usuário encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
bio6_html_footer();
?>
