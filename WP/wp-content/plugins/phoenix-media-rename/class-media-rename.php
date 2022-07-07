<?php

#region constants

define("actionRename", "rename");
define("actionRenameRetitle", "rename_retitle");
define("actionRenameFromPostTitle", "rename_from_post_title");
define("actionRenameRetitleFromPostTitle", "rename_retitle_from_post_title");
define("success", "pmr_renamed");
define("pmrTableName", "pmr_status");
define("pluginArchivarixExternalImagesImporter", "archivarix-external-images-importer/archivarix-external-images-importer.php");
define("pluginAmazonS3AndCloudfront", "amazon-s3-and-cloudfront/wordpress-s3.php");
define("pluginSmartSlider3", "smart-slider-3/smart-slider-3.php");
define("pluginShortpixelImageOptimiser", "shortpixel-image-optimiser/wp-shortpixel.php");
define("pluginWPML", "sitepress-multilingual-cms/sitepress.php");
define("pluginRedirection", "redirection/redirection.php");

#endregion

class Phoenix_Media_Rename {

	private $is_media_rename_page;
	private $nonce_printed;

	/**
	 * Initializes the plugin
	 */
	function __construct() {
		$post = isset($_REQUEST['post']) ? get_post($_REQUEST['post']) : NULL;
		$is_media_edit_page = $post && $post->post_type == 'attachment' && $GLOBALS['pagenow'] == 'post.php';
		$is_media_listing_page = $GLOBALS['pagenow'] == 'upload.php';
		$this->is_media_rename_page = $is_media_edit_page || $is_media_listing_page;
		self::frontend_support();
	}

	/**
	 * Adds the "Filename" column at the media posts listing page
	 *
	 * @param [array] $columns
	 * @return void
	 */
	function add_filename_column($columns) {
		$columns['filename'] = 'Filename';
		return $columns;
	}

	/**
	 * Adds the "Filename" column content at the media posts listing page
	 *
	 * @param [string] $column_name
	 * @param [integer] $post_id
	 * @return void
	 */
	function add_filename_column_content($column_name, $post_id) {
		if ($column_name == 'filename') {

			//set bulk rename process as stopped
			$this->reset_bulk_rename();

			$file_parts = $this->get_file_parts($post_id);
			echo $this->get_filename_field($post_id, $file_parts['filename'], $file_parts['extension']);
		}
	}

	/**
	 * Add the "Filename" field to the Media form
	 *
	 * @param [type] $form_fields
	 * @param [type] $post
	 * @return array form fields
	 */
	function add_filename_field($form_fields, $post) {
		if (isset($GLOBALS['post']) && $GLOBALS['post']->post_type=='attachment') {
			$file_parts=$this->get_file_parts($GLOBALS['post']->ID);
			$form_fields['mr_filename']=array(
				'label' => __( 'Filename', constant( 'PHOENIX_MEDIA_RENAME_TEXT_DOMAIN' ) ),
				'input' => 'html',
				'html' => $this->get_filename_field($GLOBALS['post']->ID, $file_parts['filename'], $file_parts['extension'])
			);
		}
		return $form_fields;
	}

	/**
	 * Reset the bulk rename process
	 *
	 * @return void
	 */
	function reset_bulk_rename(){
		//set index for group rename
		$this->write_db_value('current_image_index', 0);
		//reset the bulk rename flag
		$this->write_db_value('bulk_rename_in_progress', false);
		//reset the bulk rename from post flag
		$this->write_db_value('bulk_rename_from_post_in_progress', false);
		//reset the bulk rename filename header
		$this->write_db_value('bulk_filename_header', '');
}

	/**
	 * Makes sure that the success message will be shown on bulk rename
	 *
	 * @return void
	 */
	function handle_bulk_pnx_rename_form_submit() {
		if ( array_search(constant("actionRename"), $_REQUEST, true) !== FALSE || array_search(constant("actionRenameRetitle"), $_REQUEST, true) !== FALSE ) {

			//set bulk rename process as stopped
			$this->reset_bulk_rename();

			wp_redirect( add_query_arg( array(constant("success") => 1), wp_get_referer() ) );
			exit;
		}
	}

	/**
	 * Shows bulk rename success notice
	 *
	 * @return void
	 */
	function show_bulk_pnx_rename_success_notice() {
		if( isset($_REQUEST[constant("success")]) ) {
			echo '<div class="updated"><p>'. __( 'Medias successfully renamed!', constant( 'PHOENIX_MEDIA_RENAME_TEXT_DOMAIN' ) ) .'</p></div>';
		}
	}

	/**
	 * Print the JS code only on media.php and media-upload.php pages
	 *
	 * @return void
	 */
	function print_js() {
		if ($this->is_media_rename_page) {
			wp_enqueue_script( constant( 'PHOENIX_MEDIA_RENAME_TEXT_DOMAIN' ), plugins_url('js/scripts.min.js', __FILE__), array('jquery'), '3.1.0' );
			?>

			<script type="text/javascript">
				MRSettings = {
					'labels': {
						'<?php echo constant("actionRename") ?>': '<?php echo __( 'Rename', constant( 'PHOENIX_MEDIA_RENAME_TEXT_DOMAIN' ) ) ?>',
						'<?php echo constant("actionRenameRetitle") ?>': '<?php echo __('Rename & Retitle', constant( 'PHOENIX_MEDIA_RENAME_TEXT_DOMAIN' ) ) ?>',
						'<?php echo constant("actionRenameFromPostTitle") ?>': '<?php echo __('Rename from Post', constant( 'PHOENIX_MEDIA_RENAME_TEXT_DOMAIN' ) ) ?>',
						'<?php echo constant("actionRenameRetitleFromPostTitle") ?>': '<?php echo __('Rename & Retitle from Post', constant( 'PHOENIX_MEDIA_RENAME_TEXT_DOMAIN' ) ) ?>'
					}
				};
			</script>

			<?php
		}
	}

	/**
	 * Print the CSS styles only on media.php and media-upload.php pages
	 *
	 * @return void
	 */
	function print_css() {
		if ($this->is_media_rename_page) {
			wp_enqueue_style( 'phoenix-media-rename', plugins_url('css/style.css', __FILE__) );
		}
	}

	/**
	 * Prints the "Filename" textfield
	 *
	 * @param [integer] $post_id
	 * @param [string] $filename
	 * @param [string] $extension
	 * @return void
	 */
	function get_filename_field($post_id, $filename, $extension) {
		if (!isset($this->nonce_printed)) $this->nonce_printed=0;
		ob_start(); ?>

			<div class="phoenix-media-rename">
				<input type="text" class="text phoenix-media-rename-filename" autocomplete="post_title" value="<?php echo $filename ?>" title="<?php echo $filename ?>" data-post-id="<?php echo $post_id ?>" />
				<span class="file_ext">.<?php echo $extension ?></span>
				<span class="loader"></span>
				<span class="success"></span>
				<span class="error"></span>
				<?php if (!$this->nonce_printed) {
					wp_nonce_field('phoenix_media_rename', '_mr_wp_nonce');
					$this->nonce_printed++;
				} ?>
			</div>

		<?php return ob_get_clean();
	}

	/**
	 * Read a value from Phoenix Media Rename table
	 *
	 * @param [type] $field
	 * @return void
	 */
	function read_db_value($field){
		global $wpdb;

		//check if there are values in table
		$result = $wpdb->get_var( "SELECT " . $field . " FROM " . $wpdb->prefix . constant('pmrTableName') );

		return $result;
	}

	/**
	 * Insert a value in Phoenix Media Rename table
	 *
	 * @param [string] $field
	 * @param [any] $value
	 * @return void
	 */
	function write_db_value($field, $value){
		global $wpdb;

		//check if there are values in table
		$records = $wpdb->get_var( "SELECT IFNULL(COUNT(*), 0) FROM " . $wpdb->prefix . constant('pmrTableName') );

		if ($records > 1){
			//error in table content, truncate table to reset data
			$wpdb->query( 
				$wpdb->prepare( 
					"TRUNCATE TABLE " . $wpdb->prefix . constant('pmrTableName')
				)
			);
		}elseif ($records == 0){
			//table is empty, insert new row
			$wpdb->insert( 
				$wpdb->prefix . constant('pmrTableName'), 
				array( 
					$field => $value, 
				)
			);
		} else {
			//table contains a record, update data
			$wpdb->update( 
				$wpdb->prefix . constant('pmrTableName'), 
				array( 
					$field => $value, 
				),
				array( 
					'ID' => 1, 
				)
			);
		}
	}

	/**
	 * Handles AJAX rename queries
	 *
	 * @return void
	 */
	function ajax_pnx_rename() {
		if (check_ajax_referer('phoenix_media_rename', '_wpnonce', 0)) {
			$retitle = $_REQUEST['type'] == constant("actionRenameRetitle");
			$new_filename = $_REQUEST['new_filename'];
			$bulk_rename_in_progress = $this->read_db_value('bulk_rename_in_progress');
			$bulk_rename_from_post_in_progress = $this->read_db_value('bulk_rename_from_post_in_progress');
			$attachment_id = $_REQUEST['post_id'];

			//if action is "actionRenameFromPostTitle" or "actionRenameRetitleFromPostTitle" retrieve title for post related to media file to generate filename
			if ( ( $_REQUEST['type'] == constant("actionRenameFromPostTitle") ) || ( $_REQUEST['type'] == constant("actionRenameRetitleFromPostTitle") ) ){
				$name_from_post = 1;
			}else{
				$name_from_post = 0;
			}

			//if action is "actionRenameFromPostTitle" or "actionRenameRetitleFromPostTitle" retrieve title for post related to media file to generate attachment title
			if ( $_REQUEST['type'] == constant("actionRenameRetitleFromPostTitle") ) {
				$title_from_post = 1;
			}else{
				$title_from_post = 0;
			}

			if ( $name_from_post ){
				//if filename has to be generated from parent post the do_rename function will get the filename
				// $new_filename = '';
				$post = get_post($attachment_id);

				if ( ! $this->check_post_parent( $post ) ){
					//media is not attached to a post, or post has no title
					return;
				}else{
					$new_filename = $this->get_filename_from_post_parent($post, true, $post_parent);
					$bulk_filename_header = $this->read_db_value('bulk_filename_header');

					if ( $bulk_rename_from_post_in_progress && $new_filename == $bulk_filename_header ){
						//the process is renaming a file from his post and is not the first file
						$current_image_index = $this->read_db_value('current_image_index');
		
						$this->write_db_value('current_image_index', ++$current_image_index);
		
						//create filename
						$new_filename .= '-' . $current_image_index;
					} else{
						//the file is the first one in the post
						$this->write_db_value('bulk_rename_from_post_in_progress', true);

						$this->write_db_value('bulk_filename_header', $new_filename);

						$this->write_db_value('current_image_index', 0);
					}

				};

			}elseif ( $bulk_rename_in_progress ){
				//bulk rename in progress: build filename
				//increment image name index
				$current_image_index = $this->read_db_value('current_image_index');
				$bulk_filename_header = $this->read_db_value('bulk_filename_header');

				$this->write_db_value('current_image_index', ++$current_image_index);

				//create filename
				$new_filename = $this->build_filename($bulk_filename_header, $current_image_index);
			}else{
				//bulk rename not in progress: check if filename contains {}
				//search pattern {number}
				$re = '/[{][0-9]{1,10}[}]/m';

				preg_match($re, $new_filename, $matches);

				//if new filename contains {number}, serialize following file names
				if ($matches){
					//notify the start of bulk rename process
					$this->write_db_value('bulk_rename_in_progress', true);

					//extract file header
					$bulk_filename_header = preg_replace($re, '', $new_filename);


					//if this is the first iteration, extract the number from filename
					$re = '/[0-9]{1,10}/m';

					preg_match($re, $matches[0], $matches);

					$current_image_index = $matches[0];

					//check if image index start with '0'
					$zeroes = self::starts_with( $current_image_index, '0');

					if ( $zeroes != -1 ){
						//image index start with one or more '0'
						//add zeroes to header
						$bulk_filename_header .= $zeroes;

						//remove zeroes from image index
						$current_image_index = intval($current_image_index);
					}

					$this->write_db_value('bulk_filename_header', $bulk_filename_header);

					$this->write_db_value('current_image_index', $current_image_index);

					//create filename
					$new_filename = $this->build_filename($bulk_filename_header, $current_image_index);
				}	
			}

			echo $this->do_rename($attachment_id, $new_filename, $retitle, $title_from_post, $name_from_post, false);
		}
		exit;
	}

	/**
	 * build a filename from filename parts
	 *
	 * @param [string] $header
	 * @param [string] $trailer
	 * @return void
	 */
	function build_filename($header, $trailer){
		return $header . $trailer;
	}

	/**
	 * Retrive boolean option value
	 *
	 * @param [array] $options
	 * @param [string] $name
	 * @param [boolean] $default
	 * @return void
	 */
	static function get_option_boolean($options, $name, $default = true){
		if ( isset($options[$name]) ){
			if ( $options[$name] ) {
				return true;
			}else{
				return false;
			}
		} else {
			// default
			return $default;
		}
	}

	static function get_filename_from_post_parent( $post, $name_from_post, &$post_parent ){
		//retrive post_parent
		$post_parent = get_post($post->post_parent);

		if ( ( $name_from_post ) ){
			//generate filename from post_parent title
			$new_filename = $post_parent->post_title;
		} else {
			$new_filename = '';
		}

		return $new_filename;
	}

	/**
	 * Check if media is attached to a post and if the post have a title
	 *
	 * @param [integer] $post
	 * @return void
	 */
	static function check_post_parent( $post ){
		$post_parent = $post->post_parent;

		if ( ! $post_parent ){
			//no post found
			echo __( 'The media is not attached to a post!', constant( 'PHOENIX_MEDIA_RENAME_TEXT_DOMAIN' ) );
			return false;
		}

		$new_filename = self::get_filename_from_post_parent($post, true, $post_parent);

		if ( ! $new_filename ){
			//no title set
			echo __( 'The post has no title!', constant( 'PHOENIX_MEDIA_RENAME_TEXT_DOMAIN' ) );
			return false;
		}

		//everything is ok
		return true;
	}

	/**
	 * Handles the actual rename process
	 *
	 * @param [integer] $attachment_id
	 * @param [string] $new_filename
	 * @param [boolean] $retitle
	 * @param [boolean] $title_from_post
	 * @param [boolean] $name_from_post
	 * @param [boolean] $check_post_parent
	 * @return void
	 */
	static function do_rename($attachment_id, $new_filename, $retitle = 0, $title_from_post = 0, $name_from_post = 0, $check_post_parent = true ) {
		//Variables
		$options = get_option( 'pmr_options' );
		$post = get_post($attachment_id);
		$file_parts = self::get_file_parts($attachment_id);
		$file_path = $file_parts['filepath'];
		$file_subfolder = $file_parts['subfolder'];
		$file_old_filename = $file_parts['filename'];
		$file_original_filename = $file_parts['originalfilename'];
		$file_filename_ends_with = $file_parts['endswith'];
		$file_extension = $file_parts['extension'];
		$file_edited = $file_parts['edited'];

		global $wpdb;

		if ( ( $title_from_post ) || ( $name_from_post ) ){
			if ( $check_post_parent ){
				if (! self::check_post_parent( $post ) ){
					//the media is not attached to a post or the post has no title
					//this check is needed to avoid issues with third party code that calls directly pmr->do_rename
					return;
				}else{
					$post_parent = get_post($post->post_parent);
				}
			} else {
				$post_parent = get_post($post->post_parent);
			}
		}

		$option_update_revisions = self::get_option_boolean( $options, 'pmr_update_revisions', true );
		$option_remove_accents = self::get_option_boolean( $options, 'pmr_remove_accents', true );
		$option_debug_mode = self::get_option_boolean( $options, 'pmr_debug_mode', false );
		$option_create_redirection = self::get_option_boolean( $options, 'pmr_create_redirection', false );

		//restore '-scaled' filename part if user removed it (due to poor code implementation in WordPress core)
		if ( ( $file_edited ) && ( $file_filename_ends_with == '-scaled' ) && ! (self::ends_with($new_filename, '-scaled' )) ){
			$new_filename = $new_filename . $file_filename_ends_with;
		}

		//sanitizing file name (using sanitize_title because sanitize_file_name doesn't remove accents)
		if ($option_remove_accents){
			$new_filename = sanitize_file_name( remove_accents( $new_filename ) );
		} else{
			$new_filename = sanitize_file_name( $new_filename );
		}

		try{
			if ( self::is_plugin_active( constant("pluginArchivarixExternalImagesImporter") ) ) {
				//plugin is active, remove last . added by archivarix
				$new_filename = rtrim( $new_filename, '.' );
			}
		}catch(exception $e){
		}

		$file_abs_path = $file_path . $file_old_filename . '.' .$file_extension;
		$file_abs_dir = $file_path;
		
		$file_rel_path = $file_subfolder . $file_old_filename . '.' .$file_extension;

		$new_filename_unsanitized = $new_filename;
		$new_file_rel_path = preg_replace('~[^/]+$~', $new_filename . '.' . $file_extension, $file_rel_path);
		$new_file_abs_path = preg_replace('~[^/]+$~', $new_filename . '.' . $file_extension, $file_abs_path);

		if ( self::is_plugin_active( constant("pluginAmazonS3AndCloudfront") ) ) {
			//plugin is active
			add_filter( 'as3cf_get_attached_file_copy_back_to_local', '__return_true' );
		}

		//attachment miniatures
		$searches = self::get_attachment_urls($attachment_id, '', $file_edited, Operation::search);

		//Validations

		//check if old file still exists
		if ( ! file_exists($file_abs_path) ) return __( 'Can\'t find original file in the folder. Tried to rename ' . $file_abs_path, constant( 'PHOENIX_MEDIA_RENAME_TEXT_DOMAIN' ) );

		//check if post containing media file exists
		if (!$post) return __('Post with ID ' . $attachment_id . ' does not exist!');

		//check if type of post containing media file is "attachment"
		if ($post && $post->post_type != 'attachment') return __( 'Post with ID ' . $attachment_id . ' is not an attachment!', constant( 'PHOENIX_MEDIA_RENAME_TEXT_DOMAIN' ) );

		//check if new filename has been compiled
		if (!$new_filename) return __( 'The field is empty!', constant( 'PHOENIX_MEDIA_RENAME_TEXT_DOMAIN' ) );

		//check if new filename contains bad characters
		if ($option_remove_accents){
			if ($new_filename != sanitize_file_name( remove_accents( $new_filename ) )) return __( 'Bad characters or invalid filename!', constant( 'PHOENIX_MEDIA_RENAME_TEXT_DOMAIN' ) );
		}else{
			if ($new_filename != sanitize_file_name( $new_filename ) ) return __( 'Bad characters or invalid filename!', constant( 'PHOENIX_MEDIA_RENAME_TEXT_DOMAIN' ) );
		}

		//check if destination folder already contains a file with the target filename
		if ( file_exists( $new_file_abs_path ) ) return __( 'A file with that name already exists in the containing folder!', constant( 'PHOENIX_MEDIA_RENAME_TEXT_DOMAIN' ) );

		//check if destination folder is writable
		if ( !is_writable( realpath( $file_abs_dir ) ) ) return __( 'The media containing directory is not writable!', constant( 'PHOENIX_MEDIA_RENAME_TEXT_DOMAIN' ) );

		//Change the attachment post
		$post_changes['ID'] = $post->ID;
		$post_changes['guid'] = preg_replace('~[^/]+$~', $new_filename . '.' . $file_extension, $post->guid);

		//Change post title
		//if action is "actionRenameFromPostTitle" retrieve title for post related to media file
		if ( $retitle ){
			$post_changes['post_title'] = self::filename_to_title($new_filename_unsanitized);
		}elseif ( $title_from_post ){
			$post_changes['post_title'] = $post_parent->post_title;
		}else{
			$post_changes['post_title'] = $post->post_title;
		}

		// $post_changes['post_title'] = ($retitle) ? self::filename_to_title($new_filename_unsanitized) : $post->post_title;

		$post_changes['post_name'] = wp_unique_post_slug($new_filename, $post->ID, $post->post_status, $post->post_type, $post->post_parent);
		wp_update_post($post_changes);

		self::delete_files($attachment_id, $file_old_filename, $file_extension, $options);

		// Change attachment post metas & rename files
		if ( $option_debug_mode ){
			//execute rename showing errors (if present)
			//read error reporting settings
			$error_level = error_reporting();
			$display_errors = ini_get( 'display_errors' );

			//enable errors display
			error_reporting(E_ALL); 
			ini_set( 'display_errors', 1 );

			try{
				// if ( !rename($file_abs_path, $new_file_abs_path) ) return __('File renaming error! Tried to rename ' . $file_abs_path . ' in ' . $new_file_abs_path);
				if ( !copy($file_abs_path, $new_file_abs_path) ) return __('File renaming error! Tried to copy ' . $file_abs_path . ' to ' . $new_file_abs_path);
				if ( !unlink($file_abs_path) ) return __('File renaming error! Tried to delete ' . $file_abs_path);
			}catch(exception $e){
				//reset error reporting settings
				error_reporting($error_level);
				ini_set( 'display_errors', $display_errors );

				//avoid to update posts due to renaming failure
				return;
			}

			//reset error reporting settings
			error_reporting($error_level);
			ini_set( 'display_errors', $display_errors );
		} else {
			//execute rename hiding errors (if present)
			if ( !@copy($file_abs_path, $new_file_abs_path) ) return __('File renaming error! Tried to copy ' . $file_abs_path . ' to ' . $new_file_abs_path);
			if ( !@unlink($file_abs_path) ) return __('File renaming error! Tried to delete ' . $file_abs_path);
		}

		update_post_meta($attachment_id, '_wp_attached_file', $new_file_rel_path);

		$metas = self::update_metadata(wp_get_attachment_metadata($attachment_id), wp_generate_attachment_metadata($attachment_id, $new_file_abs_path), $new_filename, $file_old_filename);

		wp_update_attachment_metadata($attachment_id, $metas);

		// Replace the old with the new media link in the content of all posts and metas
		$replaces = self::get_attachment_urls($attachment_id, $file_filename_ends_with, $file_edited, Operation::replace);

		$i = 0;
		$post_types = get_post_types();

		if ( !$option_update_revisions ) {
			unset( $post_types ['revision']);
		}

		unset( $post_types['attachment'] );
		
		while ( $posts = get_posts(array( 'post_type' => $post_types, 'post_status' => 'any', 'numberposts' => 100, 'offset' => $i * 100 )) ) {
			foreach ($posts as $post) {
				// Updating post content if necessary
				$new_post = array( 'ID' => $post->ID );
				$new_post['post_content'] = str_replace('\\', '\\\\', $post->post_content);
				$new_post['post_content'] = str_replace($searches, $replaces, $new_post['post_content']);
				if ($new_post['post_content'] != $post->post_content) wp_update_post($new_post);

				// Updating post metas if necessary
				$metas = get_post_meta($post->ID);
				foreach ($metas as $key => $meta) {
					switch ($key){
						case '_elementor_css':
							//delete elementor css, it will be generated at first page visit
							//don't need to check if plugin is active because the query is safe
							$table_name = $wpdb->prefix . 'postmeta';
							$wpdb->query( 
								$wpdb->prepare( 'DELETE FROM ' . $table_name . '
									WHERE post_id = %d
									AND meta_key = %s',
									array($post->ID, '_elementor_css')
								)
							);
						break;
						case '_elementor_data':
							//update elementor data, it will be used to regenerate css file
							//don't need to check if plugin is active because the query is safe
							$table_name = $wpdb->prefix . 'postmeta';

							for ( $i = 0; $i < sizeof ($searches); $i++ ){
								$wpdb->query( 
									$wpdb->prepare( 'UPDATE ' . $table_name . '
										SET meta_value = REPLACE (meta_value, %s, %s)
										WHERE post_id = %d
										AND meta_key = %s',
										array(str_replace( '/', '\/', $searches[$i] ) , str_replace( '/', '\/', $replaces[$i]), $post->ID, $key)
									)
								);
							}
						break;
						default:
							//update wp_postmeta
							$meta[0] = self::unserialize_deep($meta[0]);
							$new_meta = self::replace_media_urls($meta[0], $searches, $replaces);
							if ($new_meta != $meta[0]) update_post_meta($post->ID, $key, $new_meta, $meta[0]);
					}
				}
			}

			$i++;
		}

		// Updating options if necessary
		$options = self::get_all_options();
		foreach ($options as $option) {
			$option['value'] = self::unserialize_deep($option['value']);
			$new_option = self::replace_media_urls($option['value'], $searches, $replaces);
			if ($new_option != $option['value']) update_option($option['name'], $new_option);
		}

		do_action('pmr_renaming_successful', $file_old_filename, $new_filename);

		if ( self::is_plugin_active( constant( "pluginWPML" ) ) ) {
			//plugin is active
			//Updating WPML tables
			self::update_wpml( $attachment_id );
		}

		if ( self::is_plugin_active( constant( "pluginSmartSlider3" ) ) ) {
			//plugin is active
			//Updating SmartSlider 3 tables
			self::update_smartslider( $file_old_filename, $new_filename, $file_extension );
		}

		if ( self::is_plugin_active( constant( "pluginRedirection" ) ) ) {
			//plugin is active
			//Adding Redirection from old ORL to the new one
			self::add_redirection( $file_old_filename, $new_filename, $file_extension, $file_subfolder, $option_create_redirection );
		}

		return 1;
	}

	/**
	 * Updates metadata array
	 *
	 * @param [array] $old_meta
	 * @param [array] $new_meta
	 * @param [string] $new_filename
	 * @param [string] $old_filename
	 * @return void
	 */
	static function update_metadata($old_meta, $new_meta, $new_filename, $old_filename){
		$result = $old_meta;

		//update ShortPixel thumbnails data
		if ( self::is_plugin_active( constant("pluginShortpixelImageOptimiser") ) ) {
			for ( $i = 0; $i < count( $result['ShortPixel']['thumbsOptList'] ); $i++ ){
				$shortpixel_meta = $result['ShortPixel']['thumbsOptList'][$i];

				$new_shortpixel_meta = str_replace( $old_filename, $new_filename, $shortpixel_meta );

				if ( $shortpixel_meta != $new_shortpixel_meta ){
					$result['ShortPixel']['thumbsOptList'][$i] = $new_shortpixel_meta;
				}
			}
		}

		foreach ($new_meta as $key => $value) {
			switch ( $key ){
				case 'file':
					//change the file name in meta
					$result[$key] = $value;
					break;
				case 'sizes':
					//change the file name in miniatures
					$result[$key] = $value;
					break;
				default:
					if ( ! array_key_exists($key, $result) ){
						//add missing keys (if needed)
						array_push($result[$key], $value);
						$result[$key] = $value;
					}
			}
		}

		return $result;
	}

	/**
	 * Delete thumbnail files from upload folder
	 *
	 * @param [integer] $attachment_id
	 * @param [string] $original_filename
	 * @param [string] $extension
	 * @param [array] $$option_debug_mode
	 * @return void
	 */
	static function delete_files($attachment_id, $original_filename, $extension, $option_debug_mode){
		$uploads_path = wp_upload_dir();
		$uploads_path = $uploads_path['path'];

		foreach (get_intermediate_image_sizes() as $size) {
			$size_data = image_get_intermediate_size($attachment_id, $size);
			if ( is_bool( $size_data ) ){
				//image intermediate sizes not found
			} else {
				if ( ! array_key_exists( 'file', $size_data ) ){
					//array key is missing
					if ( $option_debug_mode ){
						echo 'array key is missing';
					}
				} else{
					if ( $size_data['file'] == '' ){
						//filename is missing
						if ( $option_debug_mode ){
							echo 'filename is missing';
						}
					} else {
						//delete the file
						@unlink ( realpath( $uploads_path . DIRECTORY_SEPARATOR . $size_data['file'] ) );
					}
				}
			}
		}
	}

#region Redirection compatibility

	/**
	 * Add a redirection from the old URL to the NEW one using Redirection plugin
	 *
	 * @param [string] $old_filename
	 * @param [string] $new_filename
	 * @param [string] $extension
	 * @param [boolean] $option_create_redirection
	 * @return void
	 */
	static function add_redirection( $old_filename, $new_filename, $extension, $file_subfolder, $option_create_redirection ){
		if ( $option_create_redirection ){
			//option is active
			if ( class_exists( 'Red_Item' ) ){

				//include Redirection code
				require_once WP_PLUGIN_DIR . '/redirection/models/group.php';

				//fix file name
				if ( $file_subfolder ){
					$old_filename = $file_subfolder . $old_filename . '.' . $extension;
					$new_filename = $file_subfolder . $new_filename .'.' . $extension;
				} else {
					$old_filename = $old_filename . '.' . $extension;
					$new_filename = $new_filename .'.' . $extension;
				}

				//add upload folder
				if ( defined( 'UPLOADS' ) ) {
					$upload_folder = UPLOADS;

					$old_filename = get_site_url() . '/' . $upload_folder . '/' . $old_filename;
					$new_filename = get_site_url() . '/' . $upload_folder . '/' . $new_filename;
				} else {
					$upload_folder = wp_upload_dir()['baseurl'] . '/';

					$old_filename = $upload_folder . $old_filename;
					$new_filename = $upload_folder . $new_filename;
				}

				$old_filename = str_replace( '\\', '/' , $old_filename );
				$new_filename = str_replace( '\\', '/' , $new_filename );

				$details = [
					'url'			=> $old_filename,
					'action_data'	=> [ 'url' => $new_filename ],
					'action_type'	=> 'url',
					'title'			=> 'Phoenix Media Rename',
					'status'		=> 'enabled',
					'regex'			=> false,
					'group_id'		=> 2, //set group to "updated posts"
					'match_type'	=> 'url',
				];

				//add redirection via Redirection's functions
				$result = Red_Item::create( $details );
			}
		}
	}

#endregion

#region Smart Slider compatibility
	/**
	 * Update Smart Slider 3 custom table
	 *
	 * @param [string] $old_filename
	 * @param [string] $new_filename
	 * @param [string] $extension
	 * @return void
	 */
	static function update_smartslider($old_filename, $new_filename, $extension){
		global $wpdb;

		//compose file names
		$old_filename = $old_filename . '.' . $extension;
		$new_filename = $new_filename . '.' . $extension;

		if(empty($old_filename) || empty($new_filename))
		{
			return false;
		}
		if ($old_filename == ''){
			return false;
		}

		//escape filename for use in LIKE statement
		$old_filename = $wpdb->esc_like( $old_filename );

		$filter = '%/'. $old_filename;

		//compose Smart Slider table name
		$tablename = $wpdb->prefix . 'nextend2_smartslider3_slides';

		if (!self::TableExist($tablename)){
			//if table does not exist, exit and return false
			return false;
		}else{
			//if table exist, change file name
			$sqlQuery = "UPDATE ". $tablename ." SET thumbnail = REPLACE(thumbnail, %s, %s), params = REPLACE(params, %s, %s) WHERE thumbnail LIKE %s";

			$updated = $wpdb->query(
				$wpdb->prepare( 
					$sqlQuery, $old_filename, $new_filename, $old_filename, $new_filename, $filter
				));
		}

		$tablename = $wpdb->prefix . 'nextend2_image_storage';

		if (self::TableExist($tablename)){
			//if table exist, change file name (unnecessary table, does not exit if table is missing)
			$sqlQuery = "UPDATE ". $tablename ." SET image = REPLACE(image, %s, %s) WHERE image LIKE %s";

			$updated = $wpdb->query(
				$wpdb->prepare( 
					$sqlQuery, $old_filename, $new_filename, $filter
				));
		}

		return true;
	}
#endregion

#region WPML compatibility
	/**
	 * Update Smart WPML custom table
	 *
	 * @param [string] $extension
	 * @return void
	 */
	static function update_wpml($post_id){
		// Get "trid" of the file
		$trid = apply_filters( 'wpml_element_trid', NULL, $post_id, 'post_attachment' );

		if ( empty( $trid ) ) {
			//translation not found
		} else {
			//get all translations
			$translations = apply_filters( 'wpml_get_element_translations', NULL, $trid );

			//iterates through translations to update attachment metadata
			foreach ( $translations as $translation ) {
				if ( $post_id == $translation->element_id ) {
					//update filename
					update_post_meta( $translation->element_id, '_wp_attached_file', get_post_meta($translation->element_id, '_wp_attached_file', true) );

					//update metadata
					update_post_meta( $translation->element_id, '_wp_attachment_metadata', get_post_meta( $translation->element_id, '_wp_attachment_metadata', true ) );
				}
			}
		}
	}

#endregion

#region support functions

	/**
	 * Get attachment filename
	 *
	 * @param [integer] $post_id
	 * @return void
	 */
	static function get_filename($post_id) {
		$filename = get_attached_file($post_id);

		return $filename;
	}

	/**
	 * Get attachment filename
	 *
	 * @param [integer] $post_id
	 * @return void
	 */
	static function get_file_parts($post_id) {
		$filename = self::get_filename($post_id);

		return self::file_parts($filename, $post_id);
	}

	/**
	 * Extract filename and extension
	 *
	 * @param [string] $filename
	 * @param [integer] $post_id
	 * @return void
	 */
	static function file_parts($filename, $post_id){
		//read post meta to check if image has been edited
		$post_meta = get_post_meta($post_id, '_wp_attachment_metadata', 1);
		$file_path = wp_upload_dir();

		if ( isset( $post_meta['original_image'] ) ){
			$edited = true;
			$original_filename = $post_meta['original_image'];
		} else {
			$edited = false;
			$original_filename = "";
		}

		//separate filename and extension
		preg_match('~([^/]+)\.([^\.]+)$~', basename($filename), $file_parts);

		// $filepath = $file_path['path'] . '/';
		// $subfolder = $file_path['subdir'] . '/';
		$filepath = str_replace(basename($filename), '', $filename);
		$subfolder = str_replace($file_path['basedir'], '', $filepath);

		//remove first slash from subfolder (it breaks image metadata)
		if ( strlen( $subfolder ) > 0 ){
			if ( substr( $subfolder, 0, 1 ) == '/' ) {
				$subfolder = substr( $subfolder, 1, strlen( $subfolder ) -1 );
			}
		}

		if ( ( ! is_array( $file_parts ) ) || ( sizeof( $file_parts ) < 2  ) ){
			//file name or extension is missing
			echo "file name or extension is missing";
			$result = array(
				'filepath'			=> $filepath,
				'subfolder'			=> $subfolder,
				'filename'			=> "",
				'extension'			=> "",
				'endswith'			=> "",
				'edited'			=> $edited,
				'originalfilename'	=> $original_filename
			);
		} else {
			$filename = $file_parts[1];

			//check if filename ends with "-scaled"
			if ( ( $edited ) && ( self::ends_with($file_parts[1], '-scaled' ) ) ) {
				$endsWith = '-scaled';
			} else {
				$endsWith = '';
			}

			$result = array(
				'filepath'			=> $filepath,
				'subfolder'			=> $subfolder,
				'filename'			=> $filename,
				'extension'			=> $file_parts[2],
				'endswith'			=> $endsWith,
				'edited'			=> $edited,
				'originalfilename'	=> $original_filename
			);
		}

		return $result;
	}

	/**
	 * add support for calling Phoenix Media Rename from frontend
	 *
	 * @return boolean
	 */
	static function frontend_support(  ){
		if ( ! function_exists( 'wp_crop_image' ) ) {
			include( ABSPATH . 'wp-admin/includes/image.php' );
		}
	}

	/**
	 * check if plugin is active
	 *
	 * @param [string] $plugin_name
	 * @return boolean
	 */
	static function is_plugin_active( $plugin_name ){
		if(in_array($plugin_name, apply_filters('active_plugins', get_option('active_plugins')))){ 
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check if strings ends with a sequence of characters
	 *
	 * @param [string] $haystack
	 * @param [string] $needle
	 * @return void
	 */
	static function ends_with( $haystack, $needle ) {
		$length = strlen( $needle );
		if( !$length ) {
			return true;
		}
		return substr( $haystack, -$length ) === $needle;
	}

	/**
	 * Search a substring at the start of a string
	 *
	 * @param [string] $haystack
	 * @param [string] $needle
	 * @return the match if found (i.e. $haystack = '0001523', $needle = '0', returns '000' ), -1 otherwise
	 */
	function starts_with($haystack, $needle) {
		$re = '/^[' . $needle . ']+/';

		if ( preg_match( $re, $haystack, $matches, PREG_OFFSET_CAPTURE ) ){
			return $matches[0][0];
		} else {
			return -1;
		}
	}

	/**
	 * Adds more problematic characters to the "sanitize_file_name_chars" filter
	 *
	 * @param [string] $special_chars
	 * @return void
	 */
	static function add_special_chars($special_chars) {
		return array_merge($special_chars, array('%', '^'));
	}

	/**
	 * Returns the attachment URL and sizes URLs, in case of an image
	 *
	 * @param [integer] $attachment_id
	 * @param [string] $filename_ends_with
	 * @param [boolean] $remove_suffix
	 * @param [Operation] $operation
	 * @return void
	 */
	static function get_attachment_urls($attachment_id, $filename_ends_with, $remove_suffix, $operation) {
		$urls = array( wp_get_attachment_url($attachment_id) );
		// $filename = '';

		if ( wp_attachment_is_image($attachment_id) ) {
			foreach (get_intermediate_image_sizes() as $size) {
				$image = wp_get_attachment_image_src($attachment_id, $size);

				// if ( ( $operation == Operation::replace ) && (remove_suffix) ) {
				// 	// get filename
				// 	preg_match('~([^/]+)(-scaled-)(.)+\.([^\.]+)$~', $image[0], $file_parts);

				// 	if ( $file_parts[2] = '-scaled-' ){
				// 		//image is a miniature, remove -scaled to obtain original filename
				// 		$image[0] = preg_replace('~([^/]+)(-scaled-)(.+)\.([^\.]+)$~', '\1-\3', $image[0]);

				// 		// //get file path
				// 		// $filepath = substr($image[0], 0, strrpos($image[0], '/')) . '/';

				// 		//scaled image, add scaled filename
				// 		$image[0] = $image[0] . '.' . end($file_parts);
				// 	}
				// }

				$urls[] = $image[0];
			}

		}

		return array_unique($urls);
	}

	/**
	 * Convert filename to post title
	 *
	 * @param [string] $filename
	 * @return void
	 */
	static function filename_to_title($filename) {
		return $filename;
	}

	/**
	 * Get all options
	 *
	 * @return void
	 */
	static function get_all_options() {
		return $GLOBALS['wpdb']->get_results("SELECT option_name as name, option_value as value FROM {$GLOBALS['wpdb']->options}", ARRAY_A);
	}

	/**
	 * Replace the media url and fix serialization if necessary
	 *
	 * @param [string] $subj
	 * @param [string] $searches
	 * @param [string] $replaces
	 * @return void
	 */
	static function replace_media_urls($subj, &$searches, &$replaces) {
		$subj = is_object($subj) ? clone $subj : $subj;

		if (!is_scalar($subj) && is_countable($subj) && count($subj)) {
			foreach($subj as &$item) {
				$item = self::replace_media_urls($item, $searches, $replaces);
			}
		} else {
			$subj = is_string($subj) ? str_replace($searches, $replaces, $subj) : $subj;
		}
		
		return $subj;
	}

	/**
	 * Unserializes a variable until reaching a non-serialized value
	 *
	 * @param [type] $var
	 * @return void
	 */
	static function unserialize_deep($var) {
		while ( is_serialized($var) ) {
			$var = @unserialize($var);
		}

		return $var;
	}
		
	/**
	 * Check if table exists
	 *
	 * @param [type] $tablename
	 * @return boolean
	 */
	static function TableExist($tablename){
		global $wpdb;

		if($wpdb->get_var("SHOW TABLES LIKE '$tablename'") == $tablename){
			//table is not present
			return true;
		}else{
			return false;
		}
	}

#endregion
}

/**
 * Polyfill for compatibility with old PHP versions (less than 7)
 */
if (!function_exists('is_countable')) {
	function is_countable($var) {
		return (is_array($var) || $var instanceof Countable);
	}
}

abstract class Operation
{
	const search = 0;
	const replace = 1;
}
