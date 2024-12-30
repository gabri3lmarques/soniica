<?php get_header(); ?>

<main>
    <?php
    if (have_posts()) :
        while (have_posts()) : the_post();
            ?>
            <h1><?php the_title(); ?></h1>
            <div><?php the_content(); ?></div>

            <h2>Músicas na Playlist</h2>
            <?php
            // Recupera o ID da playlist atual
            $playlist_id = get_the_ID();

            // Recupera os IDs das músicas associadas à playlist
            $song_ids = get_post_meta($playlist_id, 'playlist_songs', true);

            if (!is_array($song_ids) || empty($song_ids)) {
                echo '<p>Não há músicas nesta playlist.</p>';
            } else {
                // Configura o loop para exibir as músicas
                $args = [
                    'post_type' => 'song',         // Tipo de post das músicas
                    'post__in'  => $song_ids,      // Filtra pelos IDs armazenados no metadado
                    'orderby'   => 'post__in',     // Mantém a ordem dos IDs no array
                    'posts_per_page' => -1,        // Exibe todas as músicas
                ];

                $query = new WP_Query($args);

                if ($query->have_posts()) {
                    echo '<ul>';
                    while ($query->have_posts()) {
                        $query->the_post();
                        ?>
                        <li>
                            <h3><?php the_title(); ?></h3>
                            <p><?php the_excerpt(); ?></p>
                            <a href="<?php the_permalink(); ?>">Ouvir Música</a>
                        </li>
                        <?php
                    }
                    echo '</ul>';
                    wp_reset_postdata(); // Restaura o contexto global do WordPress
                } else {
                    echo '<p>Nenhuma música encontrada.</p>';
                }
            }
            ?>
            <?php
        endwhile;
    else :
        echo '<p>Playlist não encontrada.</p>';
    endif;
    ?>
</main>

<?php get_footer(); ?>
