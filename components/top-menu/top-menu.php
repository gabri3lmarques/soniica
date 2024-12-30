<header class="top-menu">
    <?php 
        if(is_user_logged_in()){ ?>
        <div class="login">
            <svg class="icon user-icon" version="1.2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20"><path fill-rule="evenodd" d="m10 20c-5.5 0-10-4.5-10-10 0-5.5 4.5-10 10-10 5.5 0 10 4.5 10 10 0 5.5-4.5 10-10 10zm3-13c0-1.7-1.3-3-3-3-1.7 0-3 1.3-3 3 0 1.7 1.3 3 3 3 1.7 0 3-1.3 3-3zm-3 11.5c1.8 0 3.4-0.5 4.8-1.5 0.6-0.4 0.9-1.2 0.5-1.8-0.7-1.4-2.2-2.2-5.3-2.2-3.1 0-4.6 0.8-5.3 2.2-0.4 0.6-0.1 1.4 0.5 1.8 1.4 1 3 1.5 4.8 1.5z"/></svg>
            <a class="login_link" href="<?php echo wp_logout_url(home_url()); ?>">Log Out</a>
        </div>
       <?php } else { ?>
            <div class="login">
                <svg class="icon user-icon" version="1.2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20"><path fill-rule="evenodd" d="m10 20c-5.5 0-10-4.5-10-10 0-5.5 4.5-10 10-10 5.5 0 10 4.5 10 10 0 5.5-4.5 10-10 10zm3-13c0-1.7-1.3-3-3-3-1.7 0-3 1.3-3 3 0 1.7 1.3 3 3 3 1.7 0 3-1.3 3-3zm-3 11.5c1.8 0 3.4-0.5 4.8-1.5 0.6-0.4 0.9-1.2 0.5-1.8-0.7-1.4-2.2-2.2-5.3-2.2-3.1 0-4.6 0.8-5.3 2.2-0.4 0.6-0.1 1.4 0.5 1.8 1.4 1 3 1.5 4.8 1.5z"/></svg>
                <a class="login_link" href="/login">Log In</a>
            </div>
        <?php }
    ?>
    <div class="nav">
        <a class="button" href="/">Subscribe</a>
        <ul>
            <li>
                <a href="/">Lorem</a>
            </li>
            <li>
                <a href="/sign-up">Sign Up</a>
            </li>
        </ul>
    </div>
    <form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url(home_url('/')); ?>">
        <label for="s" class="screen-reader-text">Pesquisar:</label>
        <input type="text" value="" name="s" id="s" autocomplete="off" placeholder="Search..." />
        <button type="submit" id="searchsubmit"></button>
    </form>
    <a class="link-logo" href="/"><img src="<?php echo get_theme_file_uri(); ?>/assets/images/logo.svg"></a>
</header>