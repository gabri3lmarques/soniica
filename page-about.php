<?php wp_head(); ?>

<?php
$args = [
    'post_type'   => 'song',
    'post_status' => 'publish',
    'posts_per_page' => 10,
];
$songs = new WP_Query($args);

if ($songs->have_posts()):
    while ($songs->have_posts()): $songs->the_post();
        ?>
        <h3><?php the_title(); ?></h3>
        <?php
        // Renderiza o formulário para cada música
        render_add_to_playlist_form(get_the_ID());
    endwhile;
    wp_reset_postdata();
else:
    echo '<p>Nenhuma música encontrada.</p>';
endif;
?>

<?php list_playlists_with_songs(); ?>

<?php wp_footer(); ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const buttons = document.querySelectorAll(".add-to-playlist-button");

    buttons.forEach((button) => {
        button.addEventListener("click", () => {
            const form = button.closest("form");
            const songId = form.querySelector("input[name='song_id']").value;
            const playlistId = form.querySelector("input[name='playlist_id']:checked")?.value;

            if (!playlistId) {
                alert("Por favor, selecione uma playlist.");
                return;
            }

            // Usa a função já existente
            addSongToPlaylist(playlistId, songId);
        });
    });
});

</script>