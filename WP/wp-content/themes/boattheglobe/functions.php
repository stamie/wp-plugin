<?php
// Add custom Theme Functions here
/***************************************************/
//destinations parent

// kep feltoltes nagybetüre alakitas
include_once __DIR__."/functionsext.php";
add_action( 'add_attachment', 'my_set_image_meta_upon_image_upload' );
function my_set_image_meta_upon_image_upload( $post_ID ) {

	// Check if uploaded file is an image, else do nothing

	if ( wp_attachment_is_image( $post_ID ) ) {

		$my_image_title = get_post( $post_ID )->post_title;

		// Sanitize the title:  remove hyphens, underscores & extra spaces:
		$my_image_title = preg_replace( '%\s*[-_\s]+\s*%', ' ',  $my_image_title );

		// Sanitize the title:  capitalize first letter of every word (other letters lower case):
		$my_image_title = ucwords( strtolower( $my_image_title ) );

		// Create an array with the image meta (Title, Caption, Description) to be updated
		// Note:  comment out the Excerpt/Caption or Content/Description lines if not needed
		$my_image_meta = array(
			'ID'		=> $post_ID,			// Specify the image (ID) to be updated
			'post_title'	=> $my_image_title,		// Set image Title to sanitized title
		//	'post_excerpt'	=> $my_image_title,		// Set image Caption (Excerpt) to sanitized title
		//	'post_content'	=> $my_image_title,		// Set image Description (Content) to sanitized title
		);

		// Set the image Alt-Text
		update_post_meta( $post_ID, '_wp_attachment_image_alt', $my_image_title );

		// Set the image meta (e.g. Title, Excerpt, Content)
		wp_update_post( $my_image_meta );

	} 
}
// kep feltoltes nagybetüre alakitas END
/*
// admin bar kikapcs END
add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
}
}
// admin bar kikapcs END
*/
function get_destinations2( $args = null ) {
	$defaults = array(
		'category'         => 0,
		'include'          => array(),
		'exclude'          => array(),
		'meta_key'         => '',
		'meta_value'       => '',
		'post_type'        => 'destination',
		'suppress_filters' => true,
	);

	$parsed_args = wp_parse_args( $args, $defaults );

	if ( empty( $parsed_args['post_status'] ) ) {
		$parsed_args['post_status'] = ( 'attachment' == $parsed_args['post_type'] ) ? 'inherit' : 'publish';
	}

	if ( ! empty( $parsed_args['numberposts'] ) && empty( $parsed_args['posts_per_page'] ) ) {
		$parsed_args['posts_per_page'] = $parsed_args['numberposts'];
	}

	$parsed_args['posts_per_page']=1000000;

	if ( ! empty( $parsed_args['category'] ) ) {
		$parsed_args['cat'] = $parsed_args['category'];
	}
	if ( ! empty( $parsed_args['include'] ) ) {
		$incposts                      = wp_parse_id_list( $parsed_args['include'] );
		$parsed_args['posts_per_page'] = count( $incposts );  // only the number of posts included
		$parsed_args['post__in']       = $incposts;
	} elseif ( ! empty( $parsed_args['exclude'] ) ) {
		$parsed_args['post__not_in'] = wp_parse_id_list( $parsed_args['exclude'] );
	}


	$get_posts = new WP_Query;
	return $get_posts->query( $parsed_args );

}


function ux_builder_get_destination_depth( $post ) {
  return $post->post_parent
    ? ux_builder_get_destination_depth( get_post( $post->post_parent ) ) + 1
    : 0;
}
function get_destinations( $args = array() ) {
	global $wpdb;

	$defaults = array(
		'child_of'     => 0,
		'sort_order'   => 'ASC',
		'sort_column'  => 'post_title',
		'hierarchical' => 1,
		'exclude'      => array(),
		'include'      => array(),
		'meta_key'     => '',
		'meta_value'   => '',
		'authors'      => '',
		'parent'       => -1,
		'exclude_tree' => array(),
		'number'       => '',
		'offset'       => 0,
		'post_type'    => 'destination',
		'post_status'  => 'publish',
	);

	$parsed_args = wp_parse_args( $args, $defaults );

	$number       = null; //(int) $parsed_args['number'];
	$offset       = (int) $parsed_args['offset'];
	$child_of     = (int) $parsed_args['child_of'];
	$hierarchical = $parsed_args['hierarchical'];
	$exclude      = $parsed_args['exclude'];
	$meta_key     = $parsed_args['meta_key'];
	$meta_value   = $parsed_args['meta_value'];
	$parent       = $parsed_args['parent'];
	$post_status  = $parsed_args['post_status'];


	// Make sure the post type is hierarchical.
	$hierarchical_post_types = get_post_types( array( 'hierarchical' => true ) );
	
	if ( ! in_array( $parsed_args['post_type'], $hierarchical_post_types ) ) {
		return false;
	}

	if ( $parent > 0 && ! $child_of ) {
		$hierarchical = false;
	}

	// Make sure we have a valid post status.
	if ( ! is_array( $post_status ) ) {
		$post_status = explode( ',', $post_status );
	}
	if ( array_diff( $post_status, get_post_stati() ) ) {
		return false;
	}

	// $args can be whatever, only use the args defined in defaults to compute the key.
	$key          = md5( serialize( wp_array_slice_assoc( $parsed_args, array_keys( $defaults ) ) ) );
	$last_changed = wp_cache_get_last_changed( 'posts' );

	$cache_key = "get_destinations:$key:$last_changed";
	$cache     = wp_cache_get( $cache_key, 'posts' );
	if ( false !== $cache ) {
		// Convert to WP_Post instances.
		$pages = array_map( 'get_post', $cache );
		/** This filter is documented in wp-includes/post.php */
		$pages = apply_filters( 'get_destinations', $pages, $parsed_args );

		return $pages;
	}

	$inclusions = '';
	if ( ! empty( $parsed_args['include'] ) ) {
		$child_of     = 0; //ignore child_of, parent, exclude, meta_key, and meta_value params if using include
		$parent       = -1;
		$exclude      = '';
		$meta_key     = '';
		$meta_value   = '';
		$hierarchical = false;
		$incpages     = wp_parse_id_list( $parsed_args['include'] );
		if ( ! empty( $incpages ) ) {
			$inclusions = ' AND ID IN (' . implode( ',', $incpages ) . ')';
		}
	}

	$exclusions = '';
	if ( ! empty( $exclude ) ) {
		$expages = wp_parse_id_list( $exclude );
		if ( ! empty( $expages ) ) {
			$exclusions = ' AND ID NOT IN (' . implode( ',', $expages ) . ')';
		}
	}

	$author_query = '';
	if ( ! empty( $parsed_args['authors'] ) ) {
		$post_authors = wp_parse_list( $parsed_args['authors'] );

		if ( ! empty( $post_authors ) ) {
			foreach ( $post_authors as $post_author ) {
				//Do we have an author id or an author login?
				if ( 0 == intval( $post_author ) ) {
					$post_author = get_user_by( 'login', $post_author );
					if ( empty( $post_author ) ) {
						continue;
					}
					if ( empty( $post_author->ID ) ) {
						continue;
					}
					$post_author = $post_author->ID;
				}

				if ( '' == $author_query ) {
					$author_query = $wpdb->prepare( ' post_author = %d ', $post_author );
				} else {
					$author_query .= $wpdb->prepare( ' OR post_author = %d ', $post_author );
				}
			}
			if ( '' != $author_query ) {
				$author_query = " AND ($author_query)";
			}
		}
	}

	$join  = '';
	$where = "$exclusions $inclusions ";
	if ( '' !== $meta_key || '' !== $meta_value ) {
		$join = " LEFT JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id )";

		// meta_key and meta_value might be slashed
		$meta_key   = wp_unslash( $meta_key );
		$meta_value = wp_unslash( $meta_value );
		if ( '' !== $meta_key ) {
			$where .= $wpdb->prepare( " AND $wpdb->postmeta.meta_key = %s", $meta_key );
		}
		if ( '' !== $meta_value ) {
			$where .= $wpdb->prepare( " AND $wpdb->postmeta.meta_value = %s", $meta_value );
		}
	}

	if ( is_array( $parent ) ) {
		$post_parent__in = implode( ',', array_map( 'absint', (array) $parent ) );
		if ( ! empty( $post_parent__in ) ) {
			$where .= " AND post_parent IN ($post_parent__in)";
		}
	} elseif ( $parent >= 0 ) {
		$where .= $wpdb->prepare( ' AND post_parent = %d ', $parent );
	}

	if ( 1 == count( $post_status ) ) {
		$where_post_type = $wpdb->prepare( 'post_type = %s AND post_status = %s', $parsed_args['post_type'], reset( $post_status ) );
	} else {
		$post_status     = implode( "', '", $post_status );
		$where_post_type = $wpdb->prepare( "post_type = %s AND post_status IN ('$post_status')", $parsed_args['post_type'] );
	}

	$orderby_array = array();
	$allowed_keys  = array(
		'author',
		'post_author',
		'date',
		'post_date',
		'title',
		'post_title',
		'name',
		'post_name',
		'modified',
		'post_modified',
		'modified_gmt',
		'post_modified_gmt',
		'menu_order',
		'parent',
		'post_parent',
		'ID',
		'rand',
		'comment_count',
	);

	foreach ( explode( ',', $parsed_args['sort_column'] ) as $orderby ) {
		$orderby = trim( $orderby );
		if ( ! in_array( $orderby, $allowed_keys ) ) {
			continue;
		}

		switch ( $orderby ) {
			case 'menu_order':
				break;
			case 'ID':
				$orderby = "$wpdb->posts.ID";
				break;
			case 'rand':
				$orderby = 'RAND()';
				break;
			case 'comment_count':
				$orderby = "$wpdb->posts.comment_count";
				break;
			default:
				if ( 0 === strpos( $orderby, 'post_' ) ) {
					$orderby = "$wpdb->posts." . $orderby;
				} else {
					$orderby = "$wpdb->posts.post_" . $orderby;
				}
		}

		$orderby_array[] = $orderby;

	}
	$sort_column = ! empty( $orderby_array ) ? implode( ',', $orderby_array ) : "$wpdb->posts.post_title";

	$sort_order = strtoupper( $parsed_args['sort_order'] );
	if ( '' !== $sort_order && ! in_array( $sort_order, array( 'ASC', 'DESC' ) ) ) {
		$sort_order = 'ASC';
	}

	$query  = "SELECT * FROM $wpdb->posts $join WHERE ($where_post_type) $where ";
	$query .= $author_query;
	$query .= ' ORDER BY ' . $sort_column . ' ' . $sort_order;


	if ( ! empty( $number ) ) {
		$query .= ' LIMIT ' . $offset . ',' . $number;
	}

	$pages = $wpdb->get_results( $query );

	if ( empty( $pages ) ) {
		wp_cache_set( $cache_key, array(), 'posts' );

		/** This filter is documented in wp-includes/post.php */
		$pages = apply_filters( 'get_pages', array(), $parsed_args );
		return $pages;
	}

	// Sanitize before caching so it'll only get done once.
	$num_pages = count( $pages );
	for ( $i = 0; $i < $num_pages; $i++ ) {
		$pages[ $i ] = sanitize_post( $pages[ $i ], 'raw' );
	}

	// Update cache.
	update_post_cache( $pages );

	if  ( $child_of || $hierarchical ) {
		$pages = get_page_children( $child_of, $pages );
	}

	if ( ! empty( $parsed_args['exclude_tree'] ) ) {

		$exclude = wp_parse_id_list( $parsed_args['exclude_tree'] );
		foreach ( $exclude as $id ) {
			$children = get_page_children( $id, $pages );
			foreach ( $children as $child ) {
				$exclude[] = $child->ID;
			}
		}

		$num_pages = count( $pages );
		for ( $i = 0; $i < $num_pages; $i++ ) {
			if ( in_array( $pages[ $i ]->ID, $exclude ) ) {
				unset( $pages[ $i ] );
			}
		}
	}

	$page_structure = array();
	foreach ( $pages as $page ) {
		
		$page_structure[] = $page->ID;
	}

	wp_cache_set( $cache_key, $page_structure, 'posts' );

	// Convert to WP_Post instances
	$pages = array_map( 'get_post', $pages );

	/**
	 * Filters the retrieved list of pages.
	 *
	 * @since 2.1.0
	 *
	 * @param array $pages List of pages to retrieve.
	 * @param array $parsed_args     Array of get_pages() arguments.
	 */

	return apply_filters( 'get_destinations', $pages, $parsed_args );
}

function get_ports( $args = array() ) {
	global $wpdb;

	$defaults = array(
		'child_of'     => 0,
		'sort_order'   => 'ASC',
		'sort_column'  => 'post_title',
		'hierarchical' => 1,
		'exclude'      => array(),
		'include'      => array(),
		'meta_key'     => '',
		'meta_value'   => '',
		'authors'      => '',
		'parent'       => -1,
		'exclude_tree' => array(),
		'number'       => '',
		'offset'       => 0,
		'post_type'    => 'port',
		'post_status'  => 'publish',
	);

	$parsed_args = wp_parse_args( $args, $defaults );

	$number       = null; //(int) $parsed_args['number'];
	$offset       = (int) $parsed_args['offset'];
	$child_of     = (int) $parsed_args['child_of'];
	$hierarchical = $parsed_args['hierarchical'];
	$exclude      = $parsed_args['exclude'];
	$meta_key     = $parsed_args['meta_key'];
	$meta_value   = $parsed_args['meta_value'];
	$parent       = $parsed_args['parent'];
	$post_status  = $parsed_args['post_status'];


	// Make sure the post type is hierarchical.
	$hierarchical_post_types = get_post_types( array( 'hierarchical' => true ) );
	
	if ( ! in_array( $parsed_args['post_type'], $hierarchical_post_types ) ) {
		return false;
	}

	if ( $parent > 0 && ! $child_of ) {
		$hierarchical = false;
	}

	// Make sure we have a valid post status.
	if ( ! is_array( $post_status ) ) {
		$post_status = explode( ',', $post_status );
	}
	if ( array_diff( $post_status, get_post_stati() ) ) {
		return false;
	}

	// $args can be whatever, only use the args defined in defaults to compute the key.
	$key          = md5( serialize( wp_array_slice_assoc( $parsed_args, array_keys( $defaults ) ) ) );
	$last_changed = wp_cache_get_last_changed( 'posts' );

	$cache_key = "get_destinations:$key:$last_changed";
	$cache     = wp_cache_get( $cache_key, 'posts' );
	if ( false !== $cache ) {
		// Convert to WP_Post instances.
		$pages = array_map( 'get_post', $cache );
		/** This filter is documented in wp-includes/post.php */
		$pages = apply_filters( 'get_destinations', $pages, $parsed_args );

		return $pages;
	}

	$inclusions = '';
	if ( ! empty( $parsed_args['include'] ) ) {
		$child_of     = 0; //ignore child_of, parent, exclude, meta_key, and meta_value params if using include
		$parent       = -1;
		$exclude      = '';
		$meta_key     = '';
		$meta_value   = '';
		$hierarchical = false;
		$incpages     = wp_parse_id_list( $parsed_args['include'] );
		if ( ! empty( $incpages ) ) {
			$inclusions = ' AND ID IN (' . implode( ',', $incpages ) . ')';
		}
	}

	$exclusions = '';
	if ( ! empty( $exclude ) ) {
		$expages = wp_parse_id_list( $exclude );
		if ( ! empty( $expages ) ) {
			$exclusions = ' AND ID NOT IN (' . implode( ',', $expages ) . ')';
		}
	}

	$author_query = '';
	if ( ! empty( $parsed_args['authors'] ) ) {
		$post_authors = wp_parse_list( $parsed_args['authors'] );

		if ( ! empty( $post_authors ) ) {
			foreach ( $post_authors as $post_author ) {
				//Do we have an author id or an author login?
				if ( 0 == intval( $post_author ) ) {
					$post_author = get_user_by( 'login', $post_author );
					if ( empty( $post_author ) ) {
						continue;
					}
					if ( empty( $post_author->ID ) ) {
						continue;
					}
					$post_author = $post_author->ID;
				}

				if ( '' == $author_query ) {
					$author_query = $wpdb->prepare( ' post_author = %d ', $post_author );
				} else {
					$author_query .= $wpdb->prepare( ' OR post_author = %d ', $post_author );
				}
			}
			if ( '' != $author_query ) {
				$author_query = " AND ($author_query)";
			}
		}
	}

	$join  = '';
	$where = "$exclusions $inclusions ";
	if ( '' !== $meta_key || '' !== $meta_value ) {
		$join = " LEFT JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id )";

		// meta_key and meta_value might be slashed
		$meta_key   = wp_unslash( $meta_key );
		$meta_value = wp_unslash( $meta_value );
		if ( '' !== $meta_key ) {
			$where .= $wpdb->prepare( " AND $wpdb->postmeta.meta_key = %s", $meta_key );
		}
		if ( '' !== $meta_value ) {
			$where .= $wpdb->prepare( " AND $wpdb->postmeta.meta_value = %s", $meta_value );
		}
	}

	if ( is_array( $parent ) ) {
		$post_parent__in = implode( ',', array_map( 'absint', (array) $parent ) );
		if ( ! empty( $post_parent__in ) ) {
			$where .= " AND post_parent IN ($post_parent__in)";
		}
	} elseif ( $parent >= 0 ) {
		$where .= $wpdb->prepare( ' AND post_parent = %d ', $parent );
	}

	if ( 1 == count( $post_status ) ) {
		$where_post_type = $wpdb->prepare( 'post_type = %s AND post_status = %s', $parsed_args['post_type'], reset( $post_status ) );
	} else {
		$post_status     = implode( "', '", $post_status );
		$where_post_type = $wpdb->prepare( "post_type = %s AND post_status IN ('$post_status')", $parsed_args['post_type'] );
	}

	$orderby_array = array();
	$allowed_keys  = array(
		'author',
		'post_author',
		'date',
		'post_date',
		'title',
		'post_title',
		'name',
		'post_name',
		'modified',
		'post_modified',
		'modified_gmt',
		'post_modified_gmt',
		'menu_order',
		'parent',
		'post_parent',
		'ID',
		'rand',
		'comment_count',
	);

	foreach ( explode( ',', $parsed_args['sort_column'] ) as $orderby ) {
		$orderby = trim( $orderby );
		if ( ! in_array( $orderby, $allowed_keys ) ) {
			continue;
		}

		switch ( $orderby ) {
			case 'menu_order':
				break;
			case 'ID':
				$orderby = "$wpdb->posts.ID";
				break;
			case 'rand':
				$orderby = 'RAND()';
				break;
			case 'comment_count':
				$orderby = "$wpdb->posts.comment_count";
				break;
			default:
				if ( 0 === strpos( $orderby, 'post_' ) ) {
					$orderby = "$wpdb->posts." . $orderby;
				} else {
					$orderby = "$wpdb->posts.post_" . $orderby;
				}
		}

		$orderby_array[] = $orderby;

	}
	$sort_column = ! empty( $orderby_array ) ? implode( ',', $orderby_array ) : "$wpdb->posts.post_title";

	$sort_order = strtoupper( $parsed_args['sort_order'] );
	if ( '' !== $sort_order && ! in_array( $sort_order, array( 'ASC', 'DESC' ) ) ) {
		$sort_order = 'ASC';
	}

	$query  = "SELECT * FROM $wpdb->posts $join WHERE ($where_post_type) $where ";
	$query .= $author_query;
	$query .= ' ORDER BY ' . $sort_column . ' ' . $sort_order;


	if ( ! empty( $number ) ) {
		$query .= ' LIMIT ' . $offset . ',' . $number;
	}

	$pages = $wpdb->get_results( $query );

	if ( empty( $pages ) ) {
		wp_cache_set( $cache_key, array(), 'posts' );

		/** This filter is documented in wp-includes/post.php */
		$pages = apply_filters( 'get_pages', array(), $parsed_args );
		return $pages;
	}

	// Sanitize before caching so it'll only get done once.
	$num_pages = count( $pages );
	for ( $i = 0; $i < $num_pages; $i++ ) {
		$pages[ $i ] = sanitize_post( $pages[ $i ], 'raw' );
	}

	// Update cache.
	update_post_cache( $pages );

	if  ( $child_of || $hierarchical ) {
		$pages = get_page_children( $child_of, $pages );
	}

	if ( ! empty( $parsed_args['exclude_tree'] ) ) {

		$exclude = wp_parse_id_list( $parsed_args['exclude_tree'] );
		foreach ( $exclude as $id ) {
			$children = get_page_children( $id, $pages );
			foreach ( $children as $child ) {
				$exclude[] = $child->ID;
			}
		}

		$num_pages = count( $pages );
		for ( $i = 0; $i < $num_pages; $i++ ) {
			if ( in_array( $pages[ $i ]->ID, $exclude ) ) {
				unset( $pages[ $i ] );
			}
		}
	}

	$page_structure = array();
	foreach ( $pages as $page ) {
		
		$page_structure[] = $page->ID;
	}

	wp_cache_set( $cache_key, $page_structure, 'posts' );

	// Convert to WP_Post instances
	$pages = array_map( 'get_post', $pages );

	/**
	 * Filters the retrieved list of pages.
	 *
	 * @since 2.1.0
	 *
	 * @param array $pages List of pages to retrieve.
	 * @param array $parsed_args     Array of get_pages() arguments.
	 */

	return apply_filters( 'get_destinations', $pages, $parsed_args );
}

function ux_builder_get_destination_parents( $post = null ) {
  $defaults = array(
    'depth' => 0,
    'child_of' => 0,
    'selected' => 0,
    'echo' => 0,
    'name' => 'destination_id',
    'id' => '',
    'class' => '',
    'show_option_none' => '',
    'show_option_no_change' => '',
    'option_none_value' => '',
    'value_field' => 'ID',
  );

  $args = apply_filters( 'destination_attributes_dropdown_destinations_args', array(
    'name' => 'parent_id',
    'show_option_none' => __( '(no parent)' ),
    'sort_column' => 'menu_order, post_title',
    'hierarchical' => 1,
    'echo' => 0,
  ), $post );

  if ( $post ) {
    $args['post_type'] = $post->post_type;
    $args['exclude_tree'] = $post->ID;
    $args['selected'] = $post->post_parent;
	}

   	$posts = null;
	   
	if ($post && $post->post_type === 'port')
		$posts = get_ports( wp_parse_args( $args, $defaults ) );
	else
		$posts = get_destinations( wp_parse_args( $args, $defaults ) );

  	$parents = array();

  // Add blank
  $parents[''] = __( 'None' );
  
  if ( $posts ) {
    foreach ( $posts as $key => &$post ) {
      $depth = ux_builder_get_destination_depth( $post );
      $parents[$post->ID] = str_repeat( '— ', $depth ) . $post->post_title;
    }
  }

  return $parents;
}

function ux_builder_get_port_parents( $post = null ) {
	$defaults = array(
	  'depth' => 0,
	  'child_of' => 0,
	  'selected' => 0,
	  'echo' => 0,
	  'name' => 'destination_id',
	  'id' => '',
	  'class' => '',
	  'show_option_none' => '',
	  'show_option_no_change' => '',
	  'option_none_value' => '',
	  'value_field' => 'ID',
	);
	if ($post && $post->post_type == 'port'){
		$post->post_type = 'destination';
		$args = apply_filters( 'destination_attributes_dropdown_destinations_args', array(
		'name' => 'parent_id',
		'show_option_none' => __( '(no parent)' ),
		'sort_column' => 'menu_order, post_title',
		'hierarchical' => 1,
		'echo' => 0,
		), $post );
		$post->post_type = 'port';
	} else {

		$args = apply_filters( 'destination_attributes_dropdown_destinations_args', array(
			'name' => 'parent_id',
			'show_option_none' => __( '(no parent)' ),
			'sort_column' => 'menu_order, post_title',
			'hierarchical' => 1,
			'echo' => 0,
			), $post );
		
	}
  	
	$posts = null;
	
	if ( $post && $post->post_type != 'port' ) {
		
		$args['post_type'] = $post->post_type;
		$args['exclude_tree'] = $post->ID;
		$args['selected'] = $post->post_parent;

		$posts = get_pages( wp_parse_args( $args, $defaults ) );
	
	} else if ( $post ) {
		if (!$post->post_parent){
			$args['post_type'] = 'destination';
			$args['exclude_tree'] = $post->ID;
			$args['selected'] = 0;
			$posts = get_pages( wp_parse_args( $args, $defaults ) );
		
		} else {
			$args['post_type'] = 'destination';
//			$args['exclude_tree'] = $post->ID;
			$args['selected'] = $post->post_parent;
			$posts = get_pages( wp_parse_args( $args, $defaults ) );
		}
	}
	
  
	$parents = array();
  
	// Add blank
	$parents[''] = __( 'None' );
	
	if ( $posts ) {
	  foreach ( $posts as $key => &$post2 ) {
		$depth = ux_builder_get_destination_depth( $post2 );
		$parents[$post2->ID] = str_repeat( '— ', $depth ) . $post2->post_title;
	  }
	}
  
	return $parents;
  }


/******************************************************/
// tabs modifications

add_action("init", "remove_parent_theme_ux_tabgroup");


function remove_parent_theme_ux_tabgroup() {


	// remove shortcode from parent theme

	// shortcode_name should be the name of the shortcode you want to modify

	remove_shortcode( "ux_tabgroup");

	// add the same shortcode in child theme with our own function

	// note to self: think of a better example function name

	//add_shortcode( "tabgroup", "wdm_boattheglobe_tabgroup" );
	//add_shortcode('tabgroup_vertical', 'wdm_boattheglobe_tabgroup');

}

// write your shortcode function here.

//require_once __DIR__ . '/inc/shortcodes/tabs.php';


/***************************************/
// Register Custom Post Type

function custom_post_type() {
	
	$args = array(
			"post"=>'Post',
			"destination"=>'Destination',
			"port"=>'Port',
			"boat"=> 'Boat',
			
			);
	return $args;

}
// Register Custom Post Type - Destinations END

/***************************************/


// Register Custom Post Type - Destinations
function destination_post_type() {

	$labels = array(
		'name'                  => _x( 'Destinations', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Destination', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Destinations', 'text_domain' ),
		'name_admin_bar'        => __( 'Destination', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All Items', 'text_domain' ),
		'add_new_item'          => __( 'Add New Item', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Item', 'text_domain' ),
		'edit_item'             => __( 'Edit Item', 'text_domain' ),
		'update_item'           => __( 'Update Item', 'text_domain' ),
		'view_item'             => __( 'View Item', 'text_domain' ),
		'view_items'            => __( 'View Items', 'text_domain' ),
		'search_items'          => __( 'Search Item', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	);
	
		$rewrite = array(
		'slug'                 => '',
		'with_front'           => true,
		'pages'                => true,
		'feeds'                => true,
	);
	
	$args = array(
		'label'                 => __( 'Destination', 'text_domain' ),
		'description'           => __( 'Site destination.', 'text_domain' ),
		'labels'                => $labels,
		'menu_icon'				=> 'dashicons-flag',
		'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'page-attributes', 'post-formats', 'custom-fields' ),
		'taxonomies'            => array( 'post_tag' ),
		'hierarchical'          => true,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
		'rewrite' 				=> $rewrite,
	);
	register_post_type( 'destination', $args );

}
add_action( 'init', 'destination_post_type', 0 );

// Register Custom Post Type - Destinations END

/***************************************/

// Register Custom Post Type - Port
function port_post_type() {

	$labels = array(
		'name'                  => _x( 'Ports', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Port', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Ports', 'text_domain' ),
		'name_admin_bar'        => __( 'Port', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All Items', 'text_domain' ),
		'add_new_item'          => __( 'Add New Item', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Item', 'text_domain' ),
		'edit_item'             => __( 'Edit Item', 'text_domain' ),
		'update_item'           => __( 'Update Item', 'text_domain' ),
		'view_item'             => __( 'View Item', 'text_domain' ),
		'view_items'            => __( 'View Items', 'text_domain' ),
		'search_items'          => __( 'Search Item', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	
	);
	
		$rewrite = array(
		'slug'                 => 'port',
		'with_front'           => true,
		'pages'                => true,
		'feeds'                => true,
	);
	
	$args = array(
		'label'                 => __( 'Port', 'text_domain' ),
		'description'           => __( 'Site port.', 'text_domain' ),
		'labels'                => $labels,
		'menu_icon'				=> 'dashicons-location-alt',
		'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'page-attributes', 'post-formats', 'custom-fields' ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => true,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
		'rewrite' 				=> $rewrite,
	);
	register_post_type( 'port', $args );

}
add_action( 'init', 'port_post_type', 0 );
// Register Custom Post Type - Destinations END

/***************************************/

// Register Custom Boat Type - Boat
function boat_post_type() {

	$labels = array(
		'name'                  => _x( 'Boats', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Boat', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Boats', 'text_domain' ),
		'name_admin_bar'        => __( 'Boat', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All Items', 'text_domain' ),
		'add_new_item'          => __( 'Add New Item', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Item', 'text_domain' ),
		'edit_item'             => __( 'Edit Item', 'text_domain' ),
		'update_item'           => __( 'Update Item', 'text_domain' ),
		'view_item'             => __( 'View Item', 'text_domain' ),
		'view_items'            => __( 'View Items', 'text_domain' ),
		'search_items'          => __( 'Search Item', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
		//'sample'				=> __( 'Sample', 'text_domain' ),
	);
	
		$rewrite = array(
		'slug'                 => 'boat',
		'with_front'           => true,
		'pages'                => true,
		'feeds'                => true,
		//'sample'			   => true,
	);
	
	$args = array(
		'label'                 => __( 'Boat', 'text_domain' ),
		'description'           => __( 'Site boat.', 'text_domain' ),
		'labels'                => $labels,
		'menu_icon'				=> 'dashicons-location-alt',
		'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'page-attributes', 'post-formats', 'custom-fields', 'sample' ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => true,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
		'rewrite' 				=> $rewrite,
	);
	register_post_type( 'boat', $args );

}


add_action( 'init', 'boat_post_type', 0 );



// Register Custom Post Type - Destinations END

/***************************************/


/*Hajó template PAGE*/
add_action( 'init', 'boattemplate_custom_post_types' );
function boattemplate_custom_post_types() {
  register_post_type( 'boat-template',
    array(
      'labels' => array(
        'name' => __( 'Hajó elrendezés' ),
        'singular_name' => __( 'Elrendezés' )
      ),
      'public' => true,
      'has_archive' => true,
      'supports' => array( 'title', 'editor', 'thumbnail', 'attributes' ),
      'menu_icon' => 'dashicons-laptop'
    )
  );
}
/*Hajó template PAGE END*/
// Register Custom Post Type - Destinations END

/***************************************/

// Register Custom Boat Type - Boat
function boat_mail_post_type() {

	$labels = array(
		'name'                  => _x( 'Boats mail', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Boat mail', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Boats mail', 'text_domain' ),
		'name_admin_bar'        => __( 'Boat mail', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All Items', 'text_domain' ),
		'add_new_item'          => __( 'Add New Item', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Item', 'text_domain' ),
		'edit_item'             => __( 'Edit Item', 'text_domain' ),
		'update_item'           => __( 'Update Item', 'text_domain' ),
		'view_item'             => __( 'View Item', 'text_domain' ),
		'view_items'            => __( 'View Items', 'text_domain' ),
		'search_items'          => __( 'Search Item', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
		//'sample'				=> __( 'Sample', 'text_domain' ),
	);
	
		$rewrite = array(
		'slug'                 => 'boat_mail',
		'with_front'           => true,
		'pages'                => true,
		'feeds'                => true,
		//'sample'			   => true,
	);
	
	$args = array(
		'label'                 => __( 'Boat mail', 'text_domain' ),
		'description'           => __( 'Site boat mail.', 'text_domain' ),
		'labels'                => $labels,
		'menu_icon'				=> 'dashicons-location-alt',
		'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'page-attributes', 'post-formats', 'custom-fields', 'sample' ),
		'taxonomies'            => array(  ),
		'hierarchical'          => true,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
		'rewrite' 				=> $rewrite,
	);
	register_post_type( 'boat_mail', $args );

}


add_action( 'init', 'boat_mail_post_type', 0 );
// add_action( 'init', 'boatmailtemplate_custom_post_types' );



if ( is_admin() ) {
	
    add_action( 'admin_menu', 'add_boat_menu_sample', 100 );
}

function add_boat_menu_sample() {

	global $wpdb;

	$query = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = 'boat-template' AND post_title = 'draft'";

	$returns = $wpdb->get_results($query, OBJECT);

	foreach ($returns as $return){

		$id = $return->ID;
		add_submenu_page('edit.php?post_type=boat',__('Sample Boat', 'text_domain'), __('Sample Boat', 'text_domain'), 'administrator', "post.php?post=$id&action=edit");
		break;
	}
} 
function namespace_add_custom_types( $query ) {
	if( (is_category() || is_tag()) && $query->is_archive() && empty( $query->query_vars['suppress_filters'] ) ) {
	  $query->set( 'post_type', array(
	   'post', 'port', //'boat'
		  ));
	  }
	  return $query;
  }
add_filter( 'pre_get_posts', 'namespace_add_custom_types' );


// Register Custom Post Type - Boat END

/***************************************/

// UX Builder to destinations_post_type

add_action( 'init', function () {
	if( function_exists( 'add_ux_builder_post_type' ) ) {
		add_ux_builder_post_type( 'destination' );
	}
} );

// UX Builder to destinations_post_type END

/***************************************/

// UX Builder to port_post_type

add_action( 'init', function () {
	if( function_exists( 'add_ux_builder_post_type' ) ) {
		add_ux_builder_post_type( 'port' );
	}
} );

// UX Builder to port_post_type END
add_action( 'init', function () {
	if( function_exists( 'add_ux_builder_post_type' ) ) {
		add_ux_builder_post_type( 'boat' );
	}
} );

// UX Builder to port_post_type END



// UX Builder to port_post_type END
add_action( 'init', function () {
	if( function_exists( 'add_ux_builder_post_type' ) ) {
		add_ux_builder_post_type( 'boat-template' );
	}
} );

// UX Builder to port_post_type END

// permalink manager
add_filter('permalink_manager_fix_uri_duplicates', '__return_true');
// permalink manager END

// Footer menu widget
add_action( 'widgets_init', 'footer_menu_sidebar' );
function footer_menu_sidebar() {
  $args = array(
    'name'          => 'Footer Menu',
    'id'            => 'footer-menu',
    'description'   => 'Ez az alsó menü widget',
    'class'         => 'widget-footer-menu-part',
    'before_widget' => '<div class="widget-footer-menu">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3 class="footer-widget-title">',
    'after_title'   => '</h3>' 
  );
  register_sidebar( $args );
}
// Footer menu widget END

// Change sidebar parameters in child theme widget title
add_filter('dynamic_sidebar_params', 'boattheglobe_edit_widget_func');

function boattheglobe_edit_widget_func($params) {
    $params[0]['before_title'] =  '<h4 class="widget-title subheading heading-size">' ;
    $params[0]['after_title'] =  '</h4>' ;
    return $params;
}
// Change sidebar parameters in child theme widget title END

add_filter( 'generate_google_font_display', function() {
    return 'swap';
} );

// [post_title] shortcode
function post_title_shortcode(){
    return get_the_title('');
}
add_shortcode('post_title','get_the_title');
// [post_title] shortcode END


// custom post type excerpt leght

function custom_short_excerpt($excerpt){
	return substr($excerpt, 0, 400);
}
add_filter('the_excerpt', 'custom_short_excerpt');

// custom post type excerpt leght END

/**
 * Font Awesome Kit Setup
 * 
 * This will add your Font Awesome Kit to the front-end, the admin back-end,
 * and the login screen area.
 */
if (! function_exists('fa_custom_setup_kit') ) {
  function fa_custom_setup_kit($kit_url = '') {
    foreach ( [ 'wp_enqueue_scripts', 'admin_enqueue_scripts', 'login_enqueue_scripts' ] as $action ) {
      add_action(
        $action,
        function () use ( $kit_url ) {
          wp_enqueue_script( 'font-awesome-kit', $kit_url, [], null );
        }
      );
    }
  }
}


//add class to yoast breadcrumb link
add_filter( 'wpseo_breadcrumb_single_link', 'change_breadcrumb_link_class'); 
function change_breadcrumb_link_class($link) {  
    return str_replace('<a', '<a class="utvonal"', $link); 
}




/****************************************************************/

/**
 * Generate breadcrumbs
 * @author CodexWorld
 * @authorURL www.codexworld.com
 */
function get_hansel_and_gretel_breadcrumbs()
{
    // Set variables for later use
//    $here_text        = __( 'You are currently here!' );
    $home_link        = home_url('/');
    $home_text        = __( 'Home' );
    $link_before      = '<span class="destinations-back">';
    $link_after       = '</span>';
    $link_attr        = ' rel="v:url" property="v:title"';
    $link             = $link_before . '<a' . $link_attr . ' href="%1$s">Boat rental %2$s</a>' . $link_after;
    $delimiter        = ' ';//' &raquo; ';              // Delimiter between crumbs
    $before           = '<span class="current">'; // Tag before the current crumb
    $after            = '</span>';                // Tag after the current crumb
    $page_addon       = '';                       // Adds the page number if the query is paged
    $breadcrumb_trail = '';
    $category_links   = '';

    /** 
     * Set our own $wp_the_query variable. Do not use the global variable version due to 
     * reliability
     */
    $wp_the_query   = $GLOBALS['wp_the_query'];
    $queried_object = $wp_the_query->get_queried_object();

    // Handle single post requests which includes single pages, posts and attatchments
    if ( is_singular() ) 
    {
        /** 
         * Set our own $post variable. Do not use the global variable version due to 
         * reliability. We will set $post_object variable to $GLOBALS['wp_the_query']
         */
        $post_object = sanitize_post( $queried_object );

        // Set variables 
        $title          = apply_filters( 'the_title', $post_object->post_title );
        $parent         = $post_object->post_parent;
        $post_type      = $post_object->post_type;
        $post_id        = $post_object->ID;
        $post_link      = $before . $title . $after;
        $parent_string  = '';
        $post_type_link = '';

        if ( 'post' === $post_type ) 
        {
            // Get the post categories
            $categories = get_the_category( $post_id );
            if ( $categories ) {
                // Lets grab the first category
                $category  = $categories[0];

                $category_links = get_category_parents( $category, true, $delimiter );
                $category_links = str_replace( '<a',   $link_before . '<a' . $link_attr, $category_links );
                $category_links = str_replace( '</a>', '</a>' . $link_after,             $category_links );
            }
        }

        if ( !in_array( $post_type, ['post', 'page', 'attachment'] ) )
        {
            $post_type_object = get_post_type_object( $post_type );
            $archive_link     = esc_url( get_post_type_archive_link( $post_type ) );

            $post_type_link   = sprintf( $link, $archive_link, $post_type_object->labels->singular_name );
        }

        // Get post parents if $parent !== 0
        if ( 0 !== $parent ) 
        {
            $parent_links = [];
            while ( $parent ) {
                $post_parent = get_post( $parent );

                $parent_links[] = sprintf( $link, esc_url( get_permalink( $post_parent->ID ) ), get_the_title( $post_parent->ID ) );

                $parent = $post_parent->post_parent;
            }

            $parent_links = array_reverse( $parent_links );

            $parent_string = implode( $delimiter, $parent_links );
        }

        // Lets build the breadcrumb trail
        if ( $parent_string ) {
            $breadcrumb_trail = $parent_string . $delimiter . $post_link;
        } else {
            $breadcrumb_trail = $post_link;
        }

        if ( $post_type_link )
            $breadcrumb_trail = $post_type_link . $delimiter . $breadcrumb_trail;

        if ( $category_links )
            $breadcrumb_trail = $category_links . $breadcrumb_trail;
    }

    // Handle archives which includes category-, tag-, taxonomy-, date-, custom post type archives and author archives
    if( is_archive() )
    {
        if (    is_category()
             || is_tag()
             || is_tax()
        ) {
            // Set the variables for this section
            $term_object        = get_term( $queried_object );
            $taxonomy           = $term_object->taxonomy;
            $term_id            = $term_object->term_id;
            $term_name          = $term_object->name;
            $term_parent        = $term_object->parent;
            $taxonomy_object    = get_taxonomy( $taxonomy );
//            $current_term_link  = $before . $taxonomy_object->labels->singular_name . ': ' . $term_name . $after;
            $parent_term_string = '';

            if ( 0 !== $term_parent )
            {
                // Get all the current term ancestors
                $parent_term_links = [];
                while ( $term_parent ) {
                    $term = get_term( $term_parent, $taxonomy );

                    $parent_term_links[] = sprintf( $link, esc_url( get_term_link( $term ) ), $term->name );

                    $term_parent = $term->parent;
                }

                $parent_term_links  = array_reverse( $parent_term_links );
                $parent_term_string = implode( $delimiter, $parent_term_links );
            }

//            if ( $parent_term_string ) {
//                $breadcrumb_trail = $parent_term_string . $delimiter . $current_term_link;
//            } else {
//                $breadcrumb_trail = $current_term_link;
//            }

        } elseif ( is_author() ) {

            $breadcrumb_trail = __( 'Author archive for ') .  $before . $queried_object->data->display_name . $after;

        } elseif ( is_post_type_archive() ) {

            $post_type        = $wp_the_query->query_vars['post_type'];
            $post_type_object = get_post_type_object( $post_type );

            $breadcrumb_trail = $before . $post_type_object->labels->singular_name . $after;

        }
    }   

    // Handle the search page
    if ( is_search() ) {
        $breadcrumb_trail = __( 'Search query for: ' ) . $before . get_search_query() . $after;
    }

    // Handle 404's
    if ( is_404() ) {
        $breadcrumb_trail = $before . __( 'Error 404' ) . $after;
    }

    // Handle paged pages
//    if ( is_paged() ) {
//        $current_page = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : get_query_var( 'page' );
//        $page_addon   = $before . sprintf( __( ' ( Page %s )' ), number_format_i18n( $current_page ) ) . $after;
//    }

    $breadcrumb_output_link  = '';
    $breadcrumb_output_link .= '<div class="breadcrumb">';
    if (    is_home()
         || is_front_page()
    ) {
        // Do not show breadcrumbs on page one of home and frontpage
        if ( is_paged() ) {
//            $breadcrumb_output_link .= $here_text . $delimiter;
//            $breadcrumb_output_link .= '<a href="' . $home_link . '">' . $home_text . '</a>';
            $breadcrumb_output_link .= $page_addon;
        }
    } else {
//        $breadcrumb_output_link .= $here_text . $delimiter;
//        $breadcrumb_output_link .= '<a href="' . $home_link . '" rel="v:url" property="v:title">' . $home_text . '</a>';
//        $breadcrumb_output_link .= $delimiter;
        $breadcrumb_output_link .= $breadcrumb_trail;
//        $breadcrumb_output_link .= $page_addon;
    }
    $breadcrumb_output_link .= '</div><!-- .breadcrumbs -->';

    return $breadcrumb_output_link;
}

function my_ux_builder_get_page_parents( $post = null ) {
	$defaults = array(
	  'depth' => 0,
	  'child_of' => 0,
	  'selected' => 0,
	  'echo' => 0,
	  'name' => 'page_id',
	  'id' => '',
	  'class' => '',
	  'show_option_none' => '',
	  'show_option_no_change' => '',
	  'option_none_value' => '',
	  'value_field' => 'ID',
	);
  
	$args = apply_filters( 'page_attributes_dropdown_pages_args', array(
	  'name' => 'parent_id',
	  'show_option_none' => __( '(no parent)' ),
	  'sort_column' => 'menu_order, post_title',
	  'hierarchical' => 1,
	  'echo' => 0,
	), $post );
  
	if ( $post ) {
	  $args['post_type'] = $post->post_type=='port'?'destination':$post->post_type;

	  $args['exclude_tree'] = $post->ID;
	  $args['selected'] = $post->post_parent;
	}
  
	$posts = get_pages( wp_parse_args( $args, $defaults ) );
	$parents = array();
  
	// Add blank
	$parents[''] = __( 'None' );
	
	if ( $posts ) {
	  foreach ( $posts as $key => &$post ) {
		$depth = ux_builder_get_page_depth( $post );
		$parents[$post->ID] = str_repeat( '— ', $depth ) . $post->post_title;
	  }
	}
  
	return $parents;
  }
  //add_action('ux_builder_get_page_parents', 'my_ux_builder_get_page_parents');
  
// admin css
add_action('admin_head', 'my_custom_fonts'); // admin_head is a hook my_custom_fonts is a function we are adding it to the hook

function my_custom_fonts() {
  echo '<style>
    .notice-error.permalink-manager-notice.notice.is-dismissible {
        display:none;   
    }
  </style>';
}
// end  