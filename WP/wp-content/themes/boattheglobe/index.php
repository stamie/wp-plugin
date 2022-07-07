<?php
/**
 * The blog template file.
 *
 * @package flatsome
 */

use Yoast\WP\SEO\Helpers\Current_Page_Helper;

//get_header();

?>
<!-- 
<div id="content" class="blog-wrapper blog-archive page-wrapper"> -->

<?php 
    
    if ( strpos( $_SERVER["REQUEST_URI"], "port") > 0 ){ 
       
        $pos = strpos( $_SERVER["REQUEST_URI"], "port") + 5;
        $name = substr($_SERVER["REQUEST_URI"], $pos);
        
        if ( strpos($name, "?") > 0 )
            $name = substr($name, 0, strpos($name, "?"));
        
        global $wpdb;

        $post = $wpdb->get_row("SELECT * from $wpdb->posts where post_name like '$name' and post_type like 'port'");
        
        if ($post) {
                
            get_template_part( '../boattheglobe/have-parent', 'port',  array("post" => $post));
        } 

       // get_footer(); 
    /* } else if (isset($_GET['post'])) {
        global $wpdb;

        $ID = $_GET['post'];

        $post = $wpdb->get_row("SELECT * from $wpdb->posts where ID like $ID");
        
        if ($post && $post->post_type === 'port' ) {
             //exit("hello")   ;
            get_template_part( '../boattheglobe/have-parent', 'port',  array("post" => $post));
        } 
 */
    } else {
        
        /**
         * The blog template file.
         *
         * @package flatsome
         */
        
        get_header();
        
        ?>
        
        <div id="content" class="blog-wrapper blog-archive page-wrapper">
                <?php get_template_part( 'template-parts/posts/layout', get_theme_mod('blog_layout','right-sidebar') ); ?>
        </div>
        
        <?php get_footer(); 
    }
?>
		
<!-- </div> -->

<?php //get_footer(); ?>