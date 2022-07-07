<?php
add_action('admin_menu', function () {

    add_menu_page('Boat Shortcodes', 'Boat Shortcodes', 'administrator', 'boat-shortcodes/admin/options.php');

    add_submenu_page('boat-shortcodes/admin/options.php', __('Beállítások', 'boat-shortcodes'), __('Beállítások', 'boat-shortcodes'), 'administrator', 'boat-shortcodes/admin/settings.php' );

    add_submenu_page('boat-shortcodes/admin/options.php', __('Szinkronizáció', 'boat-shortcodes'), __('Szinkronizáció', 'boat-shortcodes'), 'administrator', 'boat-shortcodes/admin/syncron.php' );

    add_submenu_page('boat-shortcodes/admin/options.php', __('Kikötő/Város Szinkronizáció', 'boat-shortcodes'), __('Kikötő/Város Szinkronizáció', 'boat-shortcodes'), 'administrator', 'boat-shortcodes/admin/portCitySyncron.php' );

    add_submenu_page('boat-shortcodes/admin/options.php', __('Opciózás logolás', 'boat-shortcodes'), __('Opciózás logolás', 'boat-shortcodes'), 'administrator', 'boat-shortcodes/admin/optionsLog.php' );
    add_submenu_page('boat-shortcodes/admin/options.php', __('Városok csoportos betöltése', 'boat-shortcodes'), __('Városok csoportos betöltése', 'boat-shortcodes'), 'administrator', 'boat-shortcodes/admin/loadCitiesForDestionations.php' );
    add_submenu_page('boat-shortcodes/admin/options.php', __('Városok csoportos betöltése', 'boat-shortcodes'), __('Keresők frissítése', 'boat-shortcodes'), 'administrator', 'boat-shortcodes/admin/refreshSearcher.php' );

});