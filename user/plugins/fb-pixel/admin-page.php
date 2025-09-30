<?php
/**
 * Admin page: Facebook Pixel per User
 * Local path (when plugin stored in user/plugins/fb-pixel/admin-page.php)
 */

define('YOURLS_ADMIN', true);

// Carrega o engine YOURLS
require_once( dirname(__DIR__, 3) . '/includes/load-yourls.php' );

// Inicia sessão customizada (se ainda não iniciada)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -----------------------------
// Tratamento de logout aqui (se o link for ?action=logout)
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Limpa sessão customizada
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    // Redireciona para domínio principal
    header("Location: " . YOURLS_SITE);
    exit;
}

// -----------------------------
// Login obrigatório (usa sessão customizada 'ua_user_id')
if (!isset($_SESSION['ua_user_id'])) {
    header("Location: " . YOURLS_SITE . "/?ua_action=login");
    exit;
}

// Variáveis de usuário e DB
$ydb      = yourls_get_db();
$user_id  = (int) ($_SESSION['ua_user_id'] ?? 0);
$username = $_SESSION['ua_username'] ?? '';
$user_role = $_SESSION['ua_role'] ?? 'free';

// Mensagem de feedback
$message = '';

// -----------------------------
// Salvar Pixel (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pixel_id'])) {
    $pixel_id = trim($_POST['pixel_id']);

    // Permitir pixel vazio (remove) ou valor
    $ydb->fetchAffected(
        "UPDATE yourls_users_custom SET pixel_id = :pixel WHERE id = :id",
        [ 'pixel' => $pixel_id, 'id' => $user_id ]
    );

    $message = "Pixel atualizado com sucesso!";
}

// -----------------------------
// Recupera pixel atual (se existir)
$current_pixel = $ydb->fetchValue(
    "SELECT pixel_id FROM yourls_users_custom WHERE id = :id",
    [ 'id' => $user_id ]
);

// -----------------------------
// Inclui header (que imprime o header/menu e abre <main>)
// IMPORTANT: header.php já imprime o menu — NÃO chamar bio6_html_menu() depois para evitar duplicação.
@include_once YOURLS_ABSPATH . '/includes/header.php';

// Se por algum motivo o include falhar, garantimos abrir estrutura básica
if (!function_exists('yourls_admin_url')) {
    // fallback mínimo (evita white page). Na prática, load-yourls.php existe sempre.
    echo "<div style='color:yellow;background:#111;padding:20px;'>Erro: ambiente YOURLS não carregado corretamente.</div>";
}

// -----------------------------
// Conteúdo da página
?>
<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold text-white mb-6">?? Configuração do Pixel do Facebook</h1>

    <?php if (!empty($message)): ?>
        <div class="mb-6 p-4 bg-green-600/10 border border-green-500 text-green-300 rounded">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="bg-slate-800/50 border border-slate-700 rounded-xl shadow-lg p-6">
        <form method="post" class="space-y-4" autocomplete="off">
            <div>
                <label for="pixel_id" class="block mb-2 text-sm font-medium text-gray-300">Pixel ID</label>
                <input type="text" id="pixel_id" name="pixel_id"
                       value="<?php echo htmlspecialchars($current_pixel ?? ''); ?>"
                       placeholder="Ex: 123456789012345"
                       class="w-full p-3 rounded bg-slate-900 border border-slate-600 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-400 mt-2">
                    Deixe em branco para remover o pixel. O pixel é injetado apenas nas páginas do seu usuário.
                </p>
            </div>

            <button type="submit"
                    class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold text-white">
                Salvar Pixel
            </button>
        </form>
    </div>
</div>

<?php
// -----------------------------
// Fecha estrutura (footer).
// O includes/header.php abre <main>. Aqui fechamos e imprimimos footer.
?>
    </main>
    <footer class="bg-slate-900 text-gray-500 py-6 text-center border-t border-slate-800 mt-8">
        <p>Powered by <a href="https://bio6.click" target="_blank" class="text-blue-400 hover:underline">bio6.click</a></p>
    </footer>
</div>
</body>
</html>
