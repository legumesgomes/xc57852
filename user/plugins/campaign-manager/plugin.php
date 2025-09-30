<?php
/*
Plugin Name: Facebook Pixel per User
Plugin URI: https://bio6.click/user/plugins/fb-pixel/admin-page.php
Description: Injeta o Pixel do Facebook apenas se o usuário tiver configurado um pixel_id.
Version: 1.2
Author: bio6.click
Author URI: https://bio6.click/
*/

if (!defined('YOURLS_ABSPATH')) {
    die('No direct call!');
}

// Garante que não quebre em páginas sem sessão
yourls_add_action('html_head', 'fb_pixel_inject');

function fb_pixel_inject() {
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }

    // Só roda se usuário estiver logado no sistema de user-auth
    if (!isset($_SESSION['ua_user_id'])) {
        return;
    }

    $uid = (int) $_SESSION['ua_user_id'];
    global $ydb;
    $table = YOURLS_DB_PREFIX . "users_custom";

    try {
        $pixel_id = $ydb->fetchValue(
            "SELECT pixel_id FROM `$table` WHERE id = :id",
            ['id' => $uid]
        );
    } catch (Exception $e) {
        error_log("FB Pixel plugin error: " . $e->getMessage());
        return;
    }

    if (empty($pixel_id)) {
        return; // sem pixel cadastrado
    }

    ?>
    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s){
            if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?php echo htmlspecialchars($pixel_id); ?>');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
             src="https://www.facebook.com/tr?id=<?php echo htmlspecialchars($pixel_id); ?>&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->
    <?php
}
