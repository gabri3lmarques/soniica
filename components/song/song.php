
<?php 

function render_song_html($id, $url, $title, $artist,$image_utl, $artist_link, $download_link, $user_playlists, $tags) {
    ?>
    <div class="song" data-song-id="<?php echo esc_attr($id); ?>" data-src="<?php echo esc_url($url); ?>" data-duration="<?php echo esc_attr($time); ?>">
        <div class="thumb-container">
            <img class="thumb" src="<?php echo esc_url($image_utl); ?>" alt="Thumbnail">
            <div class="sound-wave-container">
                <div class="sound-wave">
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                </div>  
            </div>
            <div class="controls">
                <button class="play-button"></button>
            </div>
        </div>
        <div class="title-container">
            <span class="title">
                <?php echo esc_html($title); ?>
            </span>
        </div>
        <div class="artist-container">
            <a href="<?php echo $artist_link ?>"><span class="artist">
                <?php echo esc_html($artist); ?>
            </span></a>
        </div>
        <input id="download_link" type="hidden" value="<?php $download_link ?>">

        <!-- pega as playlists -->
        <?php 
            if (!empty($user_playlists)):
                $form_html = <<<HTML
                <form method="POST">
                    <input type="hidden" name="song_id" value="$id">
                    <fieldset data-simplebar data-simplebar-auto-hide="false">
                HTML;
                
                foreach ($user_playlists as $playlist) {
                    $form_html .= <<<HTML
                        <label class="playlist-radio">
                            <input type="radio" name="playlist_id" value="{$playlist->ID}" required>
                            {$playlist->post_title}
                        </label>
                HTML;
                }
                
                $form_html .= <<<HTML
                     </fieldset>
                    <button type="submit" class="add_to_playlist" name="add_to_playlist">Add to playlist</button>
                </form>
                HTML;
                // Escapa a string para ser usada dentro do atributo `data-content`
                $data_content = htmlspecialchars($form_html, ENT_QUOTES, 'UTF-8');
                ?>
                <div class="source" data-content="<?php echo $data_content; ?>"></div>
                <?php
            else:
        echo "";
        endif;
        ?>

        <!-- pega as tags -->
        <?php 
            $tags_list = '';

            if ($tags) {
                foreach ($tags as $tag) {
                    $tags_list .= '<span class="genre">' . esc_html($tag->name) . '</span>';
                }
            }
            // Renderiza o elemento HTML com o atributo data-tags
            echo '<div data-tags="' . esc_attr($tags_list) . '"></div>';
        ?>

    </div>
    <?php
}

?>