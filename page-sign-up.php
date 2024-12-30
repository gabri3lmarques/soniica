
<?php include('components/song/song.php'); ?>

<?php get_header(); ?>
 
<div class="content">
    <aside class="sidebar">Sidebar</aside>
    <main class="main" data-simplebar data-simplebar-auto-hide="false">
        <?php include('components/top-banner/top-banner.php') ?>
        <div class="container p-t-0 m-t-0">
            <h2>Sign Up</h2>
            <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_register'])) {
                    // Processamento do cadastro
                    $username = sanitize_user($_POST['username']);
                    $email = sanitize_email($_POST['email']);
                    $password = sanitize_text_field($_POST['password']);
                    $is_premium = intval($_POST['is_premium']); // Inicialmente, não premium (0)

                    // Verifica se o nome de usuário já está registrado
                    if (username_exists($username)) {
                        echo '<p style="color: red;">O nome de usuário já está em uso.</p>';
                    } elseif (email_exists($email)) {
                        echo '<p style="color: red;">O e-mail já está cadastrado.</p>';
                    } else {
                        // Cria o novo usuário
                        $user_id = wp_create_user($username, $password, $email);
                        if (is_wp_error($user_id)) {
                            echo '<p style="color: red;">Erro ao criar o usuário: ' . $user_id->get_error_message() . '</p>';
                        } else {
                            // Define a role do usuário como subscriber
                            $user = new WP_User($user_id);
                            $user->set_role('subscriber');

                            // Adiciona o campo premium inicial como 0
                            update_user_meta($user_id, 'is_premium', $is_premium);

                            echo '<p style="color: green;">Cadastro realizado com sucesso! Faça login para acessar sua conta.</p>';
                        }
                    }
                }
                ?>
                
                <form class="form-ui id="custom-registration-form" method="POST" action="">
                    <p>
                        <input type="text" name="username" id="username" placeholder="Username" required>
                    </p>
                    <p>
                        <input type="email" name="email" id="email" placeholder="Email" required>
                    </p>
                    <p>
                        <input type="password" name="password" id="password" placeholder="Strong password" required>
                    </p>
                    <p>
                        <input class="button reverse" type="submit" name="custom_register" value="Cadastrar">
                    </p>
                    <input type="hidden" name="is_premium" value="0">
                </form>
        </div>
    </main>
 </div>

<?php include('components/player/player.php'); ?>

<?php get_footer(); ?>
