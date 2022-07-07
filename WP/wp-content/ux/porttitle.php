<?php 
global $wp_query, $wpdb;
//var_dump($wp_query);
$post = null;

if (isset($wp_query->query['name']) && $wp_query->query['post_type']) {
    $name = $wp_query->query['name'];
    $type = $wp_query->query['post_type'];
    $post = $wpdb->get_row("SELECT * from $wpdb->posts where post_name like '$name' and post_type like '$type'");
} else if ( count($wp_query->query) > 0 ){
    foreach ( $wp_query->query as $key => $value ){
        $post = $wpdb->get_row("SELECT * from $wpdb->posts where ID like '$key'");
    }
}

$title = "";
if ($post) {
    $title = $post->post_title;
}

echo '<h1 class="port-title">' . $title . '</h1>';  ?>