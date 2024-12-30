<div id="player-main" class="player">
    <div class="info">
        <!-- Informações da música -->
        <img class="current-thumb" src="" alt="Thumbnail">
        
        <div class="info-group">

            <div class="current-title-artist">
                <div class="sound-wave-player">
                    <div class="bar-player"></div>
                    <div class="bar-player"></div>
                    <div class="bar-player"></div>
                    <div class="bar-player"></div>
                </div>  
                <span class="current-title">Título da música</span> by <span class="current-artist">Artista</span></div>
            <ul class="current-genres"></ul>
        </div>
    </div>

    <div class="controls-and-time">
        <div class="controls">
             <div class="player_btn">
                <button class="add-to-playlist"></button>
                <div class="player_btn_hidden">
                    <?php 
                        if (is_user_logged_in()) {
                            $current_user_id = get_current_user_id();
                            $user_playlists = get_posts([
                                'post_type'      => 'playlist',
                                'author'         => $current_user_id,
                                'posts_per_page' => -1,
                            ]);

                            if (!empty($user_playlists)) {
                                echo '<div class="player_btn_hidden_content add-to-playlist-form-hidden"></div>';
                            } else {
                                echo '<div class="ui-tooltip">No playlists</div>';
                            }
                        } else {
                            echo '<div class="ui-tooltip">Login required</div>';
                        }
                    ?>
                </div>
            </div>
            <div class="player_btn">
                <button class="random"></button>
                <div class="player_btn_hidden">
                    <div class="ui-tooltip">Random</div>
                </div>
            </div>
            <div class="player_btn">
                <button class="previous"></button>
                <div class="player_btn_hidden"><div class="ui-tooltip">Back</div></div>
            </div>
            <div class="player_btn">
                <button class="play-pause"></button>
                <div class="player_btn_hidden"><div class="ui-tooltip">Play/Pause</div></div>
            </div>
            <div class="player_btn">
                <button class="next"></button>
                <div class="player_btn_hidden"><div class="ui-tooltip">Next</div></div>
            </div>
            <div class="player_btn">
                <button class="loop"></button>
                <div class="player_btn_hidden"><div class="ui-tooltip">Loop</div></div>
            </div>
            <div class="player_btn">
                <button class="download"></button>
                <div class="player_btn_hidden"><div class="ui-tooltip">Download</div></div>
            </div>
        </div>

        <div class="time-progress-group">
            <span class="current-time">00:00</span>
            <div class="progress-bar-container">
                <div class="progress-bar"></div>
            </div>
            <span class="total-time">00:00</span>
        </div>
    </div>

    <div class="volume-group">
        <div class="volume-icon"></div>
        <input id="volume-slider" type="range" min="0" max="1" step="0.01" value="1">
    </div>
   
</div>