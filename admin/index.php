<?php
define('YOURLS_ADMIN', true);
require_once(dirname(__DIR__) . '/includes/load-yourls.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}





// ---------------------------------------------------
// LOGIN obrigatório
// ---------------------------------------------------
if (!isset($_SESSION['ua_user_id'])) {
    header("Location: " . YOURLS_SITE . "/?ua_action=login");
    exit;
}

// ---------------------------------------------------
// Banco e variáveis principais
// ---------------------------------------------------
$ydb       = yourls_get_db();
$table_url = YOURLS_DB_TABLE_URL;
$user_id   = $_SESSION['ua_user_id'] ?? null;
$user_role = $_SESSION['ua_role'] ?? 'free';

// ---------------------------------------------------
// Processar novo link
// ---------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
    $url     = trim($_POST['url']);
    $keyword = trim($_POST['keyword'] ?? '');
    $title   = trim($_POST['title'] ?? '');

    $return   = yourls_add_new_link($url, $keyword, $title);
    $status   = $return['status'] ?? '';
    $message  = $return['message'] ?? '';
    $shorturl = $return['shorturl'] ?? '';

    if ($status === 'success' && in_array($user_role, ['paid','vip','admin'])) {
        $utm_source   = trim($_POST['utm_source'] ?? '');
        $utm_medium   = trim($_POST['utm_medium'] ?? '');
        $utm_campaign = trim($_POST['utm_campaign'] ?? '');

        if ($utm_source || $utm_medium || $utm_campaign) {
            $ydb->fetchAffected(
                "INSERT INTO `yourls_link_campaigns`
                 (keyword, user_id, utm_source, utm_medium, utm_campaign, created_at)
                 VALUES (:keyword, :uid, :source, :medium, :campaign, NOW())",
                [
                    'keyword'  => $return['url']['keyword'],
                    'uid'      => $user_id,
                    'source'   => $utm_source,
                    'medium'   => $utm_medium,
                    'campaign' => $utm_campaign
                ]
            );
        }
    }
}

// ---------------------------------------------------
// Carregar header (menu e tema já inclusos)
// ---------------------------------------------------
include_once YOURLS_ABSPATH . '/includes/header.php';

// ---------------------------------------------------
// Formulário de novo link
// ---------------------------------------------------
?>
<div class="bg-slate-800/50 border border-slate-700 rounded-xl shadow-lg p-6 mb-8">
  <form method="post">
    <input type="hidden" name="nonce" value="<?php echo yourls_create_nonce('add-url'); ?>">
    <div class="flex flex-col gap-4">
      <div class="flex flex-col md:flex-row items-center gap-4">
        <input type="text" name="url" placeholder="Cole sua URL longa aqui..." required class="w-full h-12 bg-slate-900/50 border border-slate-600 rounded-lg px-4">
        <input type="text" name="keyword" placeholder="Apelido (opcional)" class="w-full md:w-56 h-12 bg-slate-900/50 border border-slate-600 rounded-lg px-4">
        <button type="submit" class="h-12 px-8 bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold text-white">Encurtar</button>
      </div>

      <?php if (in_array($user_role, ['paid','vip','admin'])): ?>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="text" name="utm_source" placeholder="UTM Source" class="w-full h-12 bg-slate-900/50 border border-slate-600 rounded-lg px-4">
        <input type="text" name="utm_medium" placeholder="UTM Medium" class="w-full h-12 bg-slate-900/50 border border-slate-600 rounded-lg px-4">
        <input type="text" name="utm_campaign" placeholder="UTM Campaign" class="w-full h-12 bg-slate-900/50 border border-slate-600 rounded-lg px-4">
      </div>
      <?php endif; ?>
    </div>
  </form>
</div>

<?php
// ---------------------------------------------------
// Listagem de links
// ---------------------------------------------------
$view_params = new YOURLS\Views\AdminParams();
$page        = $view_params->get_page();
$perpage     = $view_params->get_per_page(15);
$sort_by     = $view_params->get_sort_by();
$sort_order  = $view_params->get_sort_order();
$offset      = ($page - 1) * $perpage;

$where_sql   = '';
$where_binds = [];

if ($user_role !== 'admin' && $user_id) {
    $where_sql = "WHERE user_id = :uid";
    $where_binds['uid'] = $user_id;
}

$total_items = $ydb->fetchValue("SELECT COUNT(*) FROM `$table_url` $where_sql", $where_binds);
$total_pages = ceil($total_items / $perpage);

$query       = "SELECT * FROM `$table_url` $where_sql ORDER BY `$sort_by` $sort_order LIMIT $offset, $perpage";
$url_results = $ydb->fetchObjects($query, $where_binds);
?>

<table id="main_table" class="w-full border-collapse bg-slate-800/50 border border-slate-700 rounded-lg overflow-hidden">
  <thead>
    <tr>
      <th class="px-4 py-2">Keyword</th>
      <th class="px-4 py-2">URL</th>
      <th class="px-4 py-2">Título</th>
      <th class="px-4 py-2">Cliques</th>
      <th class="px-4 py-2">Criado em</th>
      <th class="px-4 py-2">Ações</th>
    </tr>
  </thead>
  <tbody>
<?php if ($url_results): ?>
  <?php foreach ($url_results as $url_result): ?>
    <?php
      $keyword   = yourls_sanitize_keyword($url_result->keyword);
      $shorturl  = yourls_site_url(false) . '/' . $keyword;
      $timestamp = strtotime($url_result->timestamp);
      $url       = stripslashes($url_result->url);
      $title     = $url_result->title ? $url_result->title : '';
      $clicks    = $url_result->clicks;
    ?>
    <tr class="border-b border-slate-700 hover:bg-slate-700/50">
      <td class="px-4 py-2"><a href="<?php echo $shorturl; ?>" target="_blank" class="text-blue-400 hover:underline"><?php echo $keyword; ?></a></td>
      <td class="px-4 py-2"><a href="<?php echo $url; ?>" target="_blank" class="text-gray-300 hover:underline"><?php echo htmlspecialchars($url); ?></a></td>
      <td class="px-4 py-2"><?php echo htmlspecialchars($title); ?></td>
      <td class="px-4 py-2">
        <?php echo $clicks; ?>
        <a href="<?php echo $shorturl; ?>+" target="_blank" title="Ver estatísticas" class="text-blue-400 hover:text-blue-300 ml-2">??</a>
      </td>
      <td class="px-4 py-2"><?php echo date('Y-m-d H:i', $timestamp); ?></td>
      <td class="px-4 py-2 space-x-2">
        <a href="edit-link.php?keyword=<?php echo $keyword; ?>" class="text-blue-400 hover:underline">Editar</a>
        <a href="delete-link.php?keyword=<?php echo $keyword; ?>" onclick="return confirm('Excluir este link?')" class="text-red-400 hover:underline">Excluir</a>
      </td>
    </tr>
  <?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="6" class="px-4 py-4 text-center">Nenhum link encontrado.</td></tr>
<?php endif; ?>
  </tbody>
</table>

<?php
// ---------------------------------------------------
// Rodapé
// ---------------------------------------------------
?>
</main>
<footer class="bg-slate-900 text-gray-500 py-6 text-center border-t border-slate-800 mt-8">
  <p>Powered by <a href="https://yourls.org/" class="text-blue-500 hover:underline">YOURLS</a></p>
</footer>
</div>
</body>
</html>
