<?php
require_once __DIR__.'/../../../../wp-config.php'; 
global $wpdb;

// The Query
$the_query = new WP_Query( array('post_type' => 'destination', "post_status" => "publish") );
// The Loop
if ( $the_query->have_posts() ) {
    while ( $the_query->have_posts() ) {
        $the_query->the_post();
        $childrenPosts = get_posts(['post_parent' => $the_query->post->ID ,'post_type' => 'destination', "post_status" => "publish"]);
        if (!is_array($childrenPosts) || count($childrenPosts) == 0) {
            $args = array('dest_id' => $the_query->post->ID);
            boat_search($args);
        }
    }
} else {
    // no posts found
}
/* Restore original Post Data */
wp_reset_postdata();
boat_search($args);
$types = $wpdb->get_results("SELECT distinct name as name_ from yacht_category", OBJECT);
foreach ($types as $type){
    $args['selectedCategory']= $type->name_;
    boat_search($args);
}