<?php
/** Loads the WordPress Environment and Template */
define( 'WP_USE_THEMES', true );

/** Loads the WordPress Environment and Template */
//require_once __DIR__ . '/../../../../wp-blog-header.php';
require_once __DIR__.'/../../../../wp-config.php'; 
//require_once __DIR__ . '/boatslist.php';
$args = array(
            'dest_ids' => isset($_POST['dest_ids'])&&is_array($_POST['dest_ids'])?$_POST['dest_ids']:array(),
            'minLength' => isset($_POST['minLength'])?$_POST['minLength']:0,// /3.2808
            'maxLength' => isset($_POST['maxLength'])?$_POST['maxLength']:0,// /3.2808
            'minBerth' => isset($_POST['minBerth'])?$_POST['minBerth']:0,
            'maxBerth' => isset($_POST['maxBerth'])?$_POST['maxBerth']:0,
            'cabins'   => isset($_POST['cabins'])?$_POST['cabins']:array(),
            'distance' => isset($_POST['maxDistance'])?($_POST['maxDistance']):0,
            'selectedCategories'   => isset($_POST['selectedCategories'])?$_POST['selectedCategories']:array(),
            'selectedServiceNames' => (is_array($_POST['selectedServiceNames'])&&count($_POST['selectedServiceNames']))?$_POST['selectedServiceNames']:null,
            'selectedServiceTypes' => isset($_POST['selectedServiceTypes'])?$_POST['selectedServiceTypes']:0,
            'models' => isset($_POST['models'])?$_POST['models']:0,
            'have_skipper' => isset($_POST['have_skipper'])?$_POST['have_skipper']:0,
            'page_num'  => isset($_POST['page_num'])?$_POST['page_num']:1,
            'date_from'   => isset($_POST['date_from'])?$_POST['date_from']:null,
            'duration'    => isset($_POST['duration'])?intval($_POST['duration']):null,
            'flexibility' => isset($_POST['flexibility'])?$_POST['flexibility']:null,
            'feauteres'   => isset($_POST['feauteres'])?$_POST['feauteres']:array(),
            'order_by'    => isset($_POST['order_by'])?intval($_POST['order_by']):2,
            'desc'        => isset($_POST['desc'])?intval($_POST['desc']):0,
            'is_sale'     => isset($_POST['is_sale'])?intval($_POST['is_sale']):0,
            'ignoreOptions' => isset($_POST['ignoreOptions'])?intval($_POST['ignoreOptions']):0,
        );
    $return = only_boats_list($args);
    echo( $return['return'] );
    $script = loadMainPictures();
    echo $script;

?>