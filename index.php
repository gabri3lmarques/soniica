
<?php include('components/song/song.php'); ?>

<?php get_header(); ?>
 
<div class="content">
    <?php include('components/sidebar/sidebar.php'); ?>
    <main class="main" data-simplebar data-simplebar-auto-hide="false">
        <?php include('components/top-banner/top-banner.php') ?>
        <div class="container p-t-0 m-t-0">
            <h2>Last Releases</h2>
            <div class="playlist" data-playlist-id="1">
            <?php
                // Loop personalizado para exibir posts do tipo 'song'
                $args = [
                    'post_type' => 'song',
                    'posts_per_page' => -1, // Exibe todos os posts (ou ajuste conforme necessário)
                ];

                $query = new WP_Query($args);

                if ($query->have_posts()) :
                    while ($query->have_posts()) : $query->the_post();

                        $post_id = get_the_ID();
                        $url = get_field('url');
                        $title = get_field('title');
                        $duration = get_field('duration');
                        $post_id = get_the_ID();
                        $categories = get_the_terms(get_the_ID(), 'category');
                        
                        $category = $categories[0];
                        $image_id = get_term_meta($category->term_id, 'category-image', true);

                        // pega a categoria pai
                        if ($category->parent) {
                            $parent_id = $category->parent; // ID da categoria pai
                            $parent_category = get_term($parent_id, 'category'); // Objeto da categoria pai
                            $artist = $parent_category->name;
                            $artist_link = get_category_link($parent_id); 
                        }

                        //pega a imagem da categoria
                        if ($image_id) {
                            $image_url = wp_get_attachment_url($image_id);
                        }

                        //verifica se o post é novo
                        $is_new = get_post_meta($post_id, 'is_new', true);
                        //if ($is_new) {echo "<p>is_new = $is_new</p>";}

            
                        // pega as playlists do user 
                        $current_user_id = get_current_user_id();
                        $user_playlists = get_posts([
                            'post_type'      => 'playlist',
                            'author'         => $current_user_id,
                            'posts_per_page' => -1,
                        ]);

                        //pega as tags do user
                        $tags = wp_get_post_tags(get_the_ID());

                        render_song_html($post_id, $url, $title, $artist, $image_url, $artist_link, 'lol', $user_playlists, $tags);

                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<p>No song found.</p>';
                endif;
                ?>
            </div>
        </div>
    </main>
 </div>

<?php include('components/player/player.php'); ?>

<?php get_footer(); ?>
