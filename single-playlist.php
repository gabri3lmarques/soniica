<?php get_header() ?>

<?php
// Certifique-se de que estamos dentro do loop do WordPress
if (have_posts()) : while (have_posts()) : the_post(); ?>

    <?php 
        is_current_user_post_author();
    ?>

    <h1><?php the_title(); ?></h1>

    <?php
    global $wpdb;
    $playlist_id = get_the_ID();

    // Buscar as músicas associadas à playlist atual
    $songs = $wpdb->get_results($wpdb->prepare(
        "SELECT s.ID, s.post_title FROM wp_playlist_songs ps
         INNER JOIN wp_posts s ON ps.song_id = s.ID
         WHERE ps.playlist_id = %d AND s.post_type = 'song' AND s.post_status = 'publish'",
        $playlist_id
    ));

    if (empty($songs)) {
        echo '<p>Não há músicas nesta playlist.</p>';
    } else {
        echo '<ul>';
        foreach ($songs as $song) {
            echo '<li>';
            echo esc_html($song->post_title);
            echo ' <button class="remove-song-button" data-song-id="' . esc_attr($song->ID) . '">Remover da Playlist</button>';
            echo '</li>';
        }
        echo '</ul>';
    }
    ?>

    <script>
document.addEventListener("DOMContentLoaded", () => {
    const playlistId = <?php echo json_encode($playlist_id); ?>;

    const buttons = document.querySelectorAll(".remove-song-button");

    buttons.forEach((button) => {
        button.addEventListener("click", () => {
            const songId = button.getAttribute("data-song-id");
            removeSongFromPlaylist(playlistId, songId, button);
        });
    });
});

    </script>

<?php endwhile; endif; ?>

<?php get_footer(); ?>
