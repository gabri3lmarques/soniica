<?php 

// Carregar css
function theme_styles() {
    wp_enqueue_style('sb-css',get_template_directory_uri() . '/assets/css/simplebar.css', array(), '1.0.0', 'all' );
    wp_enqueue_style('s-css',get_template_directory_uri() . '/assets/css/splide.min.css', array(), '1.0.0', 'all' );
    wp_enqueue_style('m-css',get_template_directory_uri() . '/assets/css/main.css', array(), '1.0.0', 'all' );
}

add_action('wp_enqueue_scripts', 'theme_styles');

// Carregar JS 
function enqueue_custom_menu_scripts() {
    wp_enqueue_script('s-js', get_template_directory_uri() . '/assets/js/splide.min.js', array('jquery'), null, true);
    wp_enqueue_script('m-js', get_template_directory_uri() . '/assets/js/main.min.js', array('jquery'), null, true);
    wp_enqueue_script('sb-js', get_template_directory_uri() . '/assets/js/simplebar.min.js', array('jquery'), null, true);
    wp_enqueue_script('soniica-playlist-js',get_template_directory_uri() . '/assets/js/playlist.api.js',['jquery'],'1.0',true);

    wp_localize_script('soniica-playlist-js', 'soniicaApi', [
        'apiBase' => rest_url('soniica/v1/playlists'),
        'nonce' => wp_create_nonce('wp_rest'),
    ]);
}

add_action('wp_enqueue_scripts', 'enqueue_custom_menu_scripts');

// Adiciona o campo de upload de imagem na criação de uma categoria
add_action('category_add_form_fields', 'add_category_image_field');
add_action('category_edit_form_fields', 'edit_category_image_field');

function add_category_image_field($taxonomy) {
    ?>
    <div class="form-field">
        <label for="category-image">Imagem da Categoria</label>
        <input type="hidden" id="category-image" name="category-image" value="" />
        <div id="category-image-preview" style="margin: 10px 0; max-width: 150px; max-height: 150px;">
            <img src="" style="width: 100%; height: auto; display: none;" />
        </div>
        <button type="button" class="button" id="upload-category-image">Escolher Imagem</button>
        <button type="button" class="button" id="remove-category-image" style="display: none;">Remover Imagem</button>
        <p>Selecione uma imagem para a categoria.</p>
    </div>
    <?php
}

function edit_category_image_field($term) {
    $image_id = get_term_meta($term->term_id, 'category-image', true);
    $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
    ?>
    <tr class="form-field">
        <th scope="row"><label for="category-image">Imagem da Categoria</label></th>
        <td>
            <input type="hidden" id="category-image" name="category-image" value="<?php echo esc_attr($image_id); ?>" />
            <div id="category-image-preview" style="margin: 10px 0; max-width: 150px; max-height: 150px;">
                <img src="<?php echo esc_url($image_url); ?>" style="width: 100%; height: auto; <?php echo $image_url ? '' : 'display: none;'; ?>" />
            </div>
            <button type="button" class="button" id="upload-category-image">Escolher Imagem</button>
            <button type="button" class="button" id="remove-category-image" style="<?php echo $image_url ? '' : 'display: none;'; ?>">Remover Imagem</button>
            <p>Selecione uma imagem para a categoria.</p>
        </td>
    </tr>
    <?php
}

// Salva o ID da imagem associada à categoria
add_action('edited_category', 'save_category_image');
add_action('create_category', 'save_category_image');

function save_category_image($term_id) {
    if (isset($_POST['category-image']) && !empty($_POST['category-image'])) {
        update_term_meta($term_id, 'category-image', sanitize_text_field($_POST['category-image']));
    } else {
        delete_term_meta($term_id, 'category-image');
    }
}

// Adiciona os scripts necessários para abrir a galeria de mídia
add_action('admin_enqueue_scripts', 'enqueue_category_image_scripts');

function enqueue_category_image_scripts($hook) {
    if ('edit-tags.php' === $hook || 'term.php' === $hook) {
        wp_enqueue_media();
        wp_enqueue_script('category-image-upload', get_template_directory_uri() . '/js/category-image-upload.js', ['jquery'], null, true);
    }
}

// JavaScript para abrir a galeria de mídia
add_action('admin_footer', function () {
    ?>
    <script>
        jQuery(document).ready(function ($) {
            var mediaUploader;

            $('#upload-category-image').click(function (e) {
                e.preventDefault();

                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }

                mediaUploader = wp.media({
                    title: 'Escolha uma imagem',
                    button: {
                        text: 'Usar esta imagem'
                    },
                    multiple: false
                });

                mediaUploader.on('select', function () {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#category-image').val(attachment.id);
                    $('#category-image-preview img').attr('src', attachment.url).show();
                    $('#remove-category-image').show();
                });

                mediaUploader.open();
            });

            $('#remove-category-image').click(function (e) {
                e.preventDefault();
                $('#category-image').val('');
                $('#category-image-preview img').attr('src', '').hide();
                $(this).hide();
            });
        });
    </script>
    <?php
});

// função para criar tag new nos songs
function add_is_new_meta($post_id, $post, $update) {
    // Certifique-se de que é o post type 'song'
    if ($post->post_type !== 'song') {
        return;
    }

    // Certifique-se de que não está atualizando um post existente
    if ($update) {
        return;
    }

    // Adiciona o campo `is_new` com valor inicial `1`
    update_post_meta($post_id, 'is_new', 1);

    // Registra a data de criação para controlar os 5 dias
    update_post_meta($post_id, 'created_at', current_time('mysql'));
}
add_action('save_post', 'add_is_new_meta', 10, 3);

// hook para função para verificar se o post é new
function register_is_new_cron() {
    if (!wp_next_scheduled('update_is_new_posts')) {
        wp_schedule_event(time(), 'daily', 'update_is_new_posts');
    }
}
add_action('wp', 'register_is_new_cron');

function update_is_new_posts() {
    $args = [
        'post_type' => 'song',
        'meta_query' => [
            [
                'key' => 'is_new',
                'value' => 1,
                'compare' => '='
            ]
        ],
        'posts_per_page' => -1,
    ];

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $post_id = get_the_ID();
            $created_at = get_post_meta($post_id, 'created_at', true);

            if ($created_at) {
                $created_time = strtotime($created_at);
                $current_time = current_time('timestamp');

                // Se já passaram 5 dias, atualiza `is_new` para 0
                if (($current_time - $created_time) > (5 * DAY_IN_SECONDS)) {
                    update_post_meta($post_id, 'is_new', 0);
                }
            }
        }
        wp_reset_postdata();
    }
}
add_action('update_is_new_posts', 'update_is_new_posts');

//registrar user
function soniica_custom_registration_form() {
    ?>
    <form id="custom-registration-form" method="POST" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
        <p>
            <label for="username">Nome de Usuário</label>
            <input type="text" name="username" id="username" required>
        </p>
        <p>
            <label for="email">E-mail</label>
            <input type="email" name="email" id="email" required>
        </p>
        <p>
            <label for="password">Senha</label>
            <input type="password" name="password" id="password" required>
        </p>
        <input type="hidden" name="is_premium" value="0"> <!-- Campo premium inicial -->
        <p>
            <input type="submit" name="custom_register" value="Cadastrar">
        </p>
    </form>
    <?php
}

function soniica_process_registration() {
    if (isset($_POST['custom_register'])) {
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = sanitize_text_field($_POST['password']);
        $is_premium = intval($_POST['is_premium']); // Inicialmente, não premium (0)

        // Verifica se o nome de usuário já está registrado
        if (username_exists($username)) {
            echo '<p style="color: red;">O nome de usuário já está em uso.</p>';
            return;
        }

        // Verifica se o e-mail já está registrado
        if (email_exists($email)) {
            echo '<p style="color: red;">O e-mail já está cadastrado.</p>';
            return;
        }

        // Cria o novo usuário
        $user_id = wp_create_user($username, $password, $email);
        if (is_wp_error($user_id)) {
            echo '<p style="color: red;">Erro ao criar o usuário: ' . $user_id->get_error_message() . '</p>';
            return;
        }

        // Define a role do usuário como subscriber
        $user = new WP_User($user_id);
        $user->set_role('subscriber');

        // Adiciona o campo premium inicial como 0
        update_user_meta($user_id, 'is_premium', $is_premium);

        echo '<p style="color: green;">Cadastro realizado com sucesso! Faça login para acessar sua conta.</p>';
    }
}

function soniica_registration_shortcode() {
    ob_start();
    soniica_custom_registration_form();
    soniica_process_registration();
    return ob_get_clean();
}

add_shortcode('soniica_registration', 'soniica_registration_shortcode');

// Ocultar a barra de administração mesmo para usuários logados
add_filter('show_admin_bar', '__return_false');

//impedir que subs vejam o painel admin
// function redirect_admin_area() {
//     if (is_admin() && !defined('DOING_AJAX') && !current_user_can('administrator')) {
//         wp_redirect(home_url());
//         exit;
//     }
// }
// add_action('admin_init', 'redirect_admin_area');


// Adicionar a coluna 'Premium' na lista de usuários
function soniica_add_premium_column($columns) {
    $columns['is_premium'] = 'Premium';
    return $columns;
}
add_filter('manage_users_columns', 'soniica_add_premium_column');

// Preencher a coluna com o valor (Sim/Não) e botão de edição
function soniica_show_premium_column($value, $column_name, $user_id) {
    if ('is_premium' === $column_name) {
        $is_premium = get_user_meta($user_id, 'is_premium', true);
        $status = $is_premium ? 'Sim' : 'Não';

        // Botão para alternar o status
        $toggle_action = $is_premium ? 'remover' : 'adicionar';
        $nonce = wp_create_nonce('soniica_toggle_premium_' . $user_id);

        return sprintf(
            '%s <br> <a href="%s" class="button">Tornar %s</a>',
            esc_html($status),
            esc_url(admin_url("users.php?action=toggle_premium&user_id=$user_id&_wpnonce=$nonce")),
            $toggle_action === 'adicionar' ? 'Premium' : 'Não Premium'
        );
    }
    return $value;
}
add_action('manage_users_custom_column', 'soniica_show_premium_column', 10, 3);

// Processar a ação de alternar o status 'Premium'
function soniica_process_toggle_premium() {
    if (!current_user_can('edit_users')) {
        wp_die('Você não tem permissão para realizar esta ação.');
    }

    if (isset($_GET['action'], $_GET['user_id'], $_GET['_wpnonce']) && $_GET['action'] === 'toggle_premium') {
        $user_id = intval($_GET['user_id']);

        // Verificar nonce para segurança
        if (!wp_verify_nonce($_GET['_wpnonce'], 'soniica_toggle_premium_' . $user_id)) {
            wp_die('Nonce inválido.');
        }

        $is_premium = get_user_meta($user_id, 'is_premium', true);

        // Alternar o status
        $new_status = $is_premium ? 0 : 1;
        update_user_meta($user_id, 'is_premium', $new_status);

        // Mensagem de sucesso
        wp_redirect(admin_url('users.php?premium_updated=1'));
        exit;
    }
}
add_action('admin_init', 'soniica_process_toggle_premium');

function soniica_premium_updated_notice() {
    if (isset($_GET['premium_updated']) && $_GET['premium_updated'] == 1) {
        echo '<div class="notice notice-success is-dismissible"><p>Status premium atualizado com sucesso!</p></div>';
    }
}
add_action('admin_notices', 'soniica_premium_updated_notice');

//PLAYLISTS
// Registrar Custom Post Type para Playlists
function soniica_register_playlist_cpt() {
    register_post_type('playlist', [
        'labels' => [
            'name' => __('Playlists'),
            'singular_name' => __('Playlist'),
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title'],
    ]);
}
add_action('init', 'soniica_register_playlist_cpt');

// Resgistrar rotas das playlists
function soniica_register_rest_routes() {
    // Criar Playlist
    register_rest_route('soniica/v1', '/playlists', [
        'methods' => 'POST',
        'callback' => 'soniica_create_playlist',
        'permission_callback' => 'is_user_logged_in',
    ]);

    // Deletar Playlist
    register_rest_route('soniica/v1', '/playlists/(?P<id>\\d+)', [
        'methods' => 'DELETE',
        'callback' => 'soniica_delete_playlist',
        'permission_callback' => 'soniica_check_ownership',
    ]);

    // Atualizar Nome da Playlist
    register_rest_route('soniica/v1', '/playlists/(?P<id>\\d+)', [
        'methods' => 'PATCH',
        'callback' => 'soniica_update_playlist',
        'permission_callback' => 'soniica_check_ownership',
    ]);

    // Adicionar Música à Playlist
    register_rest_route('soniica/v1', '/playlists/(?P<id>\\d+)/add-song', [
        'methods' => 'POST',
        'callback' => 'soniica_add_song_to_playlist',
        'permission_callback' => 'soniica_check_ownership',
    ]);

    // Remover Música da Playlist
    // register_rest_route('soniica/v1', '/playlists/(?P<id>\\d+)/remove-song', [
    //     'methods' => 'DELETE',
    //     'callback' => 'soniica_remove_song_from_playlist',
    //     'permission_callback' => 'soniica_check_ownership',
    // ]);

    register_rest_route('soniica/v1', '/playlists/(?P<id>\\d+)/remove-song', [
        'methods' => 'DELETE', // Temporariamente usar POST em vez de DELETE
        'callback' => 'soniica_remove_song_from_playlist',
        'permission_callback' => 'soniica_check_ownership',
    ]);
}
add_action('rest_api_init', 'soniica_register_rest_routes');

//callbacks para cada rota
// Criar Playlist
function soniica_create_playlist($request) {
    $name = sanitize_text_field($request['name']);
    $user_id = get_current_user_id();

    // Prevenção adicional de duplicação
    $lock_key = 'creating_playlist_' . $user_id;
    if (get_transient($lock_key)) {
        return new WP_Error('duplicate_request', __('Uma criação já está em andamento. Tente novamente.'), ['status' => 400]);
    }

    // Definir um bloqueio temporário
    set_transient($lock_key, true, 5); // Bloqueio de 5 segundos

    // Verificar duplicados pelo nome
    $existing_playlists = get_posts([
        'post_type' => 'playlist',
        'author' => $user_id,
        'title' => $name,
        'post_status' => 'publish',
    ]);

    if (!empty($existing_playlists)) {
        delete_transient($lock_key); // Remove o bloqueio
        return new WP_Error('duplicate_playlist', __('Você já tem uma playlist com esse nome.'), ['status' => 400]);
    }

    // Criar a playlist
    $playlist_id = wp_insert_post([
        'post_type' => 'playlist',
        'post_title' => $name,
        'post_status' => 'publish',
        'post_author' => $user_id,
    ]);

    // Remover o bloqueio
    delete_transient($lock_key);

    if (is_wp_error($playlist_id)) {
        return $playlist_id;
    }

    return rest_ensure_response(['id' => $playlist_id, 'message' => __('Playlist criada.')]);
}

// Deletar Playlist
function soniica_delete_playlist($request) {
    $playlist_id = $request['id'];
    wp_delete_post($playlist_id, true);
    return rest_ensure_response(['message' => __('Playlist deleted.')]);
}

// Atualizar Nome da Playlist
function soniica_update_playlist($request) {
    $playlist_id = $request['id'];
    $new_name = sanitize_text_field($request['name']);

    wp_update_post([
        'ID' => $playlist_id,
        'post_title' => $new_name,
    ]);

    return rest_ensure_response(['message' => __('Playlist updated.')]);
}

// Adicionar Música à Playlist
function soniica_add_song_to_playlist($request) {
    global $wpdb;
    $playlist_id = $request['id'];
    $song_id = intval($request['song_id']);

    // Verifica se a música já está na playlist
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM wp_playlist_songs WHERE playlist_id = %d AND song_id = %d",
        $playlist_id,
        $song_id
    ));

    if ($exists) {
        return rest_ensure_response(['message' => __('This song is already in the playlist.')], 400);
    }

    // Insere a música na playlist
    $wpdb->insert('wp_playlist_songs', [
        'playlist_id' => $playlist_id,
        'song_id' => $song_id,
    ]);

    return rest_ensure_response(['message' => __('Song added to playlist.')]);
}

// Remover Música da Playlist
function soniica_remove_song_from_playlist($request) {
    global $wpdb;
    $playlist_id = $request['id'];
    $song_id = intval($request['song_id']);

    $wpdb->delete('wp_playlist_songs', [
        'playlist_id' => $playlist_id,
        'song_id' => $song_id,
    ]);

    return rest_ensure_response(['message' => __('Song removed from playlist.')]);
}

// Verificar Propriedade da Playlist
function soniica_check_ownership($request) {
    $playlist_id = $request['id'];
    $playlist = get_post($playlist_id);
    return $playlist && $playlist->post_author == get_current_user_id();
}

// render form add song to playlist 
function render_add_to_playlist_form($song_id) {

    if(is_user_logged_in()){
        $user_id = get_current_user_id();
    // Recuperar as playlists do usuário
        $args = [
            'post_type'   => 'playlist',
            'author'      => $user_id,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ];

        $playlists = get_posts($args);

        // Renderiza o formulário se houver playlists
        if (!empty($playlists)) {
            ?>
            <form id="add-to-playlist-form-<?php echo esc_attr($song_id); ?>" class="add-to-playlist-form">
                <p>Escolha uma playlist:</p>
                <?php foreach ($playlists as $playlist): ?>
                    <label>
                        <input type="radio" name="playlist_id" value="<?php echo esc_attr($playlist->ID); ?>">
                        <?php echo esc_html($playlist->post_title); ?>
                    </label><br>
                <?php endforeach; ?>
                <input type="hidden" name="song_id" value="<?php echo esc_attr($song_id); ?>">
                <button type="button" class="add-to-playlist-button" data-song-id="<?php echo esc_attr($song_id); ?>">Adicionar à Playlist</button>
            </form>
            <?php
        } else {
            echo '<p>Você ainda não possui playlists. <a href="/criar-playlist">Crie uma agora!</a></p>';
        }        
    }
}


function list_playlists_with_songs() {
    global $wpdb;

    // Obter o ID do usuário logado
    $user_id = get_current_user_id();

    if (!$user_id) {
        echo '<p>Você precisa estar logado para ver suas playlists.</p>';
        return;
    }

    // Recuperar playlists do usuário
    $playlists = get_posts([
        'post_type'   => 'playlist',
        'author'      => $user_id,
        'post_status' => 'publish',
        'posts_per_page' => -1, // Todas as playlists do usuário
    ]);

    if (empty($playlists)) {
        echo '<p>Você ainda não possui playlists.</p>';
        return;
    }

    // Listar cada playlist e as músicas dentro dela
    echo '<ul class="playlists">';
    foreach ($playlists as $playlist) {
        echo '<li class="playlist">';
        echo '<h3>' . esc_html($playlist->post_title) . '</h3>';

        // Recuperar músicas associadas à playlist
        $playlist_id = $playlist->ID;
        $songs = $wpdb->get_results($wpdb->prepare(
            "SELECT s.* 
            FROM wp_posts s
            INNER JOIN wp_playlist_songs ps ON ps.song_id = s.ID
            WHERE ps.playlist_id = %d AND s.post_type = 'song' AND s.post_status = 'publish'",
            $playlist_id
        ));

        if (!empty($songs)) {
            echo '<ul class="songs">';
            foreach ($songs as $song) {
                echo '<li>' . esc_html($song->post_title) . '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>Sem músicas nesta playlist.</p>';
        }

        echo '</li>';
    }
    echo '</ul>';
}

// Adicionar a coluna 'Criador' na listagem do CPT 'playlist'
add_filter('manage_playlist_posts_columns', function($columns) {
    $columns['creator'] = 'Criador';
    return $columns;
});

// Preencher a coluna com o e-mail do criador
add_action('manage_playlist_posts_custom_column', function($column, $post_id) {
    if ($column === 'creator') {
        $author_id = get_post_field('post_author', $post_id);
        $author_email = get_the_author_meta('user_email', $author_id);
        echo esc_html($author_email);
    }
}, 10, 2);

// Adicionar uma meta box na página de edição da playlist
add_action('add_meta_boxes', function() {
    add_meta_box(
        'playlist_songs_box',
        'Músicas na Playlist',
        'render_playlist_songs_box',
        'playlist',
        'normal',
        'high'
    );
});

// Renderizar a meta box com a lista de músicas
function render_playlist_songs_box($post) {
    global $wpdb;
    $playlist_id = $post->ID;

    // Buscar as músicas associadas a essa playlist
    $songs = $wpdb->get_results($wpdb->prepare(
        "SELECT s.ID, s.post_title FROM wp_playlist_songs ps
         INNER JOIN wp_posts s ON ps.song_id = s.ID
         WHERE ps.playlist_id = %d AND s.post_type = 'song' AND s.post_status = 'publish'",
        $playlist_id
    ));

    if (empty($songs)) {
        echo '<p>Não há músicas nesta playlist.</p>';
        return;
    }

    echo '<ul>';
    foreach ($songs as $song) {
        echo '<li>' . esc_html($song->post_title) . ' (ID: ' . esc_html($song->ID) . ')</li>';
    }
    echo '</ul>';
}

//

function is_current_user_post_author(){
    if (is_user_logged_in()) {
        // Obtém o ID do usuário atual
        $current_user_id = get_current_user_id();

        // Obtém o ID do autor do post atual
        $post_author_id = get_the_author_meta('ID');

        // Compara se o usuário atual é o autor do post
        if ($current_user_id === $post_author_id) {
            ?>
                <script>
                    window.location(console.log('é o dono do post'))
                </script>
            <?php
        } else {
            ?>
                <script>
                    window.location="<?php echo(home_url()); ?>";
                </script>
            <?php
        }
    } else {
        ?>
        <script>console.log("não está logado")</script>
        <?php
    }
}
?>









