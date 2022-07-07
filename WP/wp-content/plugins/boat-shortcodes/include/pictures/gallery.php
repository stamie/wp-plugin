<?php if ($gallery != ''): 
    $gallery = '

    

    <div id="gallery" class="single-boat-gallery">

        <div id="slide" class="single-boat-slide">
            <div class="counter hidden"></div>
            <a class="prev">❮</a>
            <a class="next">❯</a>
            <img class="toggleDiapo" src="../wp-content/plugins/boat-shortcodes/include/pictures/photo-viewr-dark/icons/pause_diapo.png" width="40" />
            <img class="fullscreen" src="../wp-content/plugins/boat-shortcodes/include/pictures/photo-viewr-dark/icons/fullscreen.png" width="30" />
            <img id="preview" class="single-boat-image" />
        </div>

        <div id="thumbnails" class="single-boat-thumbnails">
            <div class="wrapper">
                '.$gallery.'

            </div>
        </div>
    </div>
        <script src="../wp-content/plugins/boat-shortcodes/include/pictures/photo-viewr-dark/js/slider.js"></script>';
 else: 
    $gallery = '<h1>'.__("Haven't got pictures", "boat-shortcodes").'</h1>';
endif; ?>