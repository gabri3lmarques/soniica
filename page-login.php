<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_login'])) {
    ob_start(); // Garante que não haverá saída antes do redirecionamento

    // Processamento do login
    $username = sanitize_user($_POST['username']);
    $password = sanitize_text_field($_POST['password']);

    $credentials = [
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => isset($_POST['remember']),
    ];

    $user = wp_signon($credentials, false);

    if (is_wp_error($user)) {
        $login_error = '<p style="color: red;">Erro: ' . esc_html($user->get_error_message()) . '</p>';
    } else {
        // Redireciona para o dashboard ou outra página após o login
        wp_safe_redirect(home_url()); // Substitua '/dashboard' pela página desejada
        exit;
    }

    ob_end_flush(); // Limpa o buffer e envia o conteúdo
}
?>

<?php include('components/song/song.php'); ?>

<?php get_header(); ?>
 
<div class="content">
    <aside class="sidebar">Sidebar</aside>
    <main class="main" data-simplebar data-simplebar-auto-hide="false">
        <?php include('components/top-banner/top-banner.php') ?>
        <div class="container p-t-0 m-t-0">
            <h2>Login</h2>

            <?php
            // Exibir erros, se houver
            if (!empty($login_error)) {
                echo $login_error;
            }
            ?>

            <!-- Formulário de Login -->
            <form class="form-ui" id="custom-login-form" method="POST" action="">
                <p>
                    <input placeholder="Username or Email" type="text" name="username" id="username" required>
                </p>
                <p>
                    <input placeholder="Password" type="password" name="password" id="password" required>
                </p>
                <p>
                    <input class="button reverse" type="submit" name="custom_login" value="Submit">
                </p>
            </form>

        </div>
    </main>
 </div>

<?php include('components/player/player.php'); ?>

<?php get_footer(); ?>
