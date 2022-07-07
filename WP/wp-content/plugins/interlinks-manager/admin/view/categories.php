<?php

if ( ! current_user_can(get_option($this->shared->get('slug') . '_categories_menu_required_capability'))) {
    wp_die(esc_attr__('You do not have sufficient permissions to access this page.', 'daim'));
}

?>

<!-- process data -->

<?php

if (isset($_POST['update_id']) or isset($_POST['form_submitted'])) {

    extract($_POST);

    //prepare data -----------------------------------------------------------------------------------------------------
    if (isset($update_id)) {
        $update_id = intval($update_id, 10);
    }

    $name        = trim($name);
    $description = trim($description);

    //validation -------------------------------------------------------------------------------------------------------

    $invalid_data_message = '';

    //validation on "name"
    if (mb_strlen(trim($name)) === 0 or mb_strlen(trim($name)) > 100) {
        $invalid_data_message .= '<div class="error settings-error notice is-dismissible below-h2"><p>' . esc_attr__('Please enter a valid value in the "Name" field.',
                'daim') . '</p></div>';
        $invalid_data         = true;
    }

    //validation on "description"
    if (mb_strlen(trim($description)) === 0 or mb_strlen(trim($description)) > 255) {
        $invalid_data_message .= '<div class="error settings-error notice is-dismissible below-h2"><p>' . esc_attr__('Please enter a valid value in the "Description" field.',
                'daim') . '</p></div>';
        $invalid_data         = true;
    }

}

//update ---------------------------------------------------------------
if (isset($_POST['update_id']) and ! isset($invalid_data)) {

    //update the database
    global $wpdb;
    $table_name = $wpdb->prefix . $this->shared->get('slug') . "_category";
    $safe_sql   = $wpdb->prepare("UPDATE $table_name SET 
                name = %s,
                description = %s
                WHERE category_id = %d",
        $name,
        $description,
        $update_id);

    $query_result = $wpdb->query($safe_sql);

    if ($query_result !== false) {
        $process_data_message = '<div class="updated settings-error notice is-dismissible below-h2"><p>' . esc_attr__('The category has been successfully updated.',
                'daim') . '</p></div>';
    }

} else {

    //add ------------------------------------------------------------------
    if (isset($_POST['form_submitted']) and ! isset($invalid_data)) {

        //insert into the database
        global $wpdb;
        $table_name = $wpdb->prefix . $this->shared->get('slug') . "_category";
        $safe_sql   = $wpdb->prepare("INSERT INTO $table_name SET 
                name = %s,
                description = %s",
            $name,
            $description
        );

        $query_result = $wpdb->query($safe_sql);

        if ($query_result !== false) {
            $process_data_message = '<div class="updated settings-error notice is-dismissible below-h2"><p>' . esc_attr__('The category has been successfully added.',
                    'daim') . '</p></div>';
        }

    }

}

//delete a category
if (isset($_POST['delete_id'])) {

    global $wpdb;
    $delete_id = intval($_POST['delete_id'], 10);

    //prevent deletion if the category is associated with an AIL
    if ($this->shared->category_is_used($delete_id)) {

        $process_data_message = '<div class="error settings-error notice is-dismissible below-h2"><p>' . esc_attr__("This category is associated with one or more AIL and can't be deleted.",
                'daim') . '</p></div>';

    } else {

        $table_name   = $wpdb->prefix . $this->shared->get('slug') . "_category";
        $safe_sql     = $wpdb->prepare("DELETE FROM $table_name WHERE category_id = %d ", $delete_id);
        $query_result = $wpdb->query($safe_sql);

        if ($query_result !== false) {
            $process_data_message = '<div class="updated settings-error notice is-dismissible below-h2"><p>' . esc_attr__('The category has been successfully deleted.',
                    'daim') . '</p></div>';
        }

    }

}

//get the category data
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id'], 10);
    global $wpdb;
    $table_name   = $wpdb->prefix . $this->shared->get('slug') . "_category";
    $safe_sql     = $wpdb->prepare("SELECT * FROM $table_name WHERE category_id = %d ", $edit_id);
    $category_obj = $wpdb->get_row($safe_sql);
}

?>

<!-- output -->

<div class="wrap">

    <div id="daext-header-wrapper" class="daext-clearfix">

        <h2><?php esc_attr_e('Interlinks Manager - Categories', 'daim'); ?></h2>

        <form action="admin.php" method="get" id="daext-search-form">

            <input type="hidden" name="page" value="daim-categories">

            <p><?php esc_attr_e('Perform your Search', 'daim'); ?></p>

			<?php
			if (isset($_GET['s']) and mb_strlen(trim($_GET['s'])) > 0) {
				$search_string = $_GET['s'];
			} else {
				$search_string = '';
			}
			?>

            <input type="text" name="s"
                   value="<?php echo esc_attr(stripslashes($search_string)); ?>" autocomplete="off" maxlength="255">
            <input type="submit" value="">

        </form>

    </div>

    <div id="daext-menu-wrapper">

        <?php if (isset($invalid_data_message)) {
            echo $invalid_data_message;
        } ?>
        <?php if (isset($process_data_message)) {
            echo $process_data_message;
        } ?>

        <!-- table -->

        <?php

        $filter = '';

        //create the query part used to filter the results when a search is performed
        if (mb_strlen(trim($filter)) === 0 and isset($_GET['s']) and mb_strlen(trim($_GET['s'])) > 0) {
            $search_string = $_GET['s'];
            global $wpdb;
            $filter = $wpdb->prepare('WHERE (name LIKE %s OR description LIKE %s)',
                '%' . $search_string . '%',
                '%' . $search_string . '%');
        }

        //retrieve the total number of categories
        global $wpdb;
        $table_name  = $wpdb->prefix . $this->shared->get('slug') . "_category";
        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name $filter");

        //Initialize the pagination class
        require_once($this->shared->get('dir') . '/admin/inc/class-daim-pagination.php');
        $pag = new daim_pagination();
        $pag->set_total_items($total_items);//Set the total number of items
        $pag->set_record_per_page(intval(get_option($this->shared->get('slug') . '_pagination_categories_menu'),
            10)); //Set records per page
        $pag->set_target_page("admin.php?page=" . $this->shared->get('slug') . "-categories");//Set target page
        $pag->set_current_page();//set the current page number from $_GET

        ?>

        <!-- Query the database -->
        <?php
        $query_limit = $pag->query_limit();
        $results     = $wpdb->get_results("SELECT * FROM $table_name $filter ORDER BY category_id DESC $query_limit",
            ARRAY_A); ?>

        <?php if (count($results) > 0) : ?>

            <div class="daext-items-container">

                <!-- list of tables -->
                <table class="daext-items">
                    <thead>
                    <tr>
                        <th>
                            <div><?php esc_attr_e('Category ID', 'daim'); ?></div>
                            <div class="help-icon"
                                 title="<?php esc_attr_e('The ID of the category.', 'daim'); ?>"></div>
                        </th>
                        <th>
                            <div><?php esc_attr_e('Name', 'daim'); ?></div>
                            <div class="help-icon"
                                 title="<?php esc_attr_e('The name of the category.', 'daim'); ?>"></div>
                        </th>
                        <th>
                            <div><?php esc_attr_e('Description', 'daim'); ?></div>
                            <div class="help-icon"
                                 title="<?php esc_attr_e('The description of the category.', 'daim'); ?>"></div>
                        </th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php foreach ($results as $result) : ?>
                        <tr>
                            <td><?php echo intval($result['category_id'], 10); ?></td>
                            <td><?php echo esc_attr(stripslashes($result['name'])); ?></td>
                            <td><?php echo esc_attr(stripslashes($result['description'])); ?></td>
                            <td class="icons-container">
                                <form method="POST"
                                      action="admin.php?page=<?php echo $this->shared->get('slug'); ?>-categories">
                                    <input type="hidden" name="clone_id" value="<?php echo $result['category_id']; ?>">
                                </form>
                                <a class="menu-icon edit"
                                   href="admin.php?page=<?php echo $this->shared->get('slug'); ?>-categories&edit_id=<?php echo $result['category_id']; ?>"></a>
                                <form id="form-delete-<?php echo $result['category_id']; ?>" method="POST"
                                      action="admin.php?page=<?php echo $this->shared->get('slug'); ?>-categories">
                                    <input type="hidden" value="<?php echo $result['category_id']; ?>"
                                           name="delete_id">
                                    <input class="menu-icon delete" type="submit" value="">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <!-- Display the pagination -->
            <?php if ($pag->total_items > 0) : ?>
                <div class="daext-tablenav daext-clearfix">
                    <div class="daext-tablenav-pages">
                        <span class="daext-displaying-num"><?php echo $pag->total_items; ?>
                            &nbsp<?php esc_attr_e('items',
                                'daim'); ?></span>
                        <?php $pag->show(); ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php else : ?>

            <?php

	        if (mb_strlen(trim($filter)) > 0) {
		        echo '<div class="error settings-error notice is-dismissible below-h2"><p>' . esc_attr__('There are no results that match your filter.',
				        'daim') . '</p></div>';
	        }

            ?>

        <?php endif; ?>

        <div>

            <form method="POST" action="admin.php?page=<?php echo $this->shared->get('slug'); ?>-categories"
                  autocomplete="off">

                <input type="hidden" value="1" name="form_submitted">

                <?php if (isset($_GET['edit_id'])) : ?>

                <!-- Edit a Category -->

                <div class="daext-form-container">

                    <h3 class="daext-form-title"><?php esc_attr_e('Edit Category',
                            'daim'); ?>&nbsp<?php echo $category_obj->category_id; ?></h3>

                    <table class="daext-form daext-form-table">

                        <input type="hidden" name="update_id"
                               value="<?php echo $category_obj->category_id; ?>"/>

                        <!-- Name -->
                        <tr valign="top">
                            <th><label for="title"><?php esc_attr_e('Name', 'daim'); ?></label></th>
                            <td>
                                <input value="<?php echo esc_attr(stripslashes($category_obj->name)); ?>" type="text"
                                       id="name" maxlength="100" size="30" name="name"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('The name of the category.', 'daim'); ?>"></div>
                            </td>
                        </tr>

                        <!-- Description -->
                        <tr valign="top">
                            <th><label for="title"><?php esc_attr_e('Description', 'daim'); ?></label></th>
                            <td>
                                <input value="<?php echo esc_attr(stripslashes($category_obj->description)); ?>"
                                       type="text" id="description" maxlength="255" size="30" name="description"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('The description of the category.', 'daim'); ?>"></div>
                            </td>
                        </tr>

                    </table>

                    <!-- submit button -->
                    <div class="daext-form-action">
                        <input class="button" type="submit"
                               value="<?php esc_attr_e('Update Category', 'daim'); ?>">
                        <input id="cancel" class="button" type="submit"
                               value="<?php esc_attr_e('Cancel', 'daim'); ?>">
                    </div>

                    <?php else : ?>

                    <!-- Create a Category -->

                    <div class="daext-form-container">

                        <div class="daext-form-title"><?php esc_attr_e('Create New Category', 'daim'); ?></div>

                        <table class="daext-form daext-form-table">

                            <!-- Name -->
                            <tr valign="top">
                                <th><label for="title"><?php esc_attr_e('Name', 'daim'); ?></label></th>
                                <td>
                                    <input type="text" id="name" maxlength="100" size="30" name="name"/>
                                    <div class="help-icon"
                                         title="<?php esc_attr_e('The name of the category.', 'daim'); ?>"></div>
                                </td>
                            </tr>

                            <!-- Description -->
                            <tr valign="top">
                                <th><label for="title"><?php esc_attr_e('Description', 'daim'); ?></label></th>
                                <td>
                                    <input type="text" id="description" maxlength="255" size="30" name="description"/>
                                    <div class="help-icon"
                                         title="<?php esc_attr_e('The description of the category.', 'daim'); ?>"></div>
                                </td>
                            </tr>

                        </table>

                        <!-- submit button -->
                        <div class="daext-form-action">
                            <input class="button" type="submit"
                                   value="<?php esc_attr_e('Add Category', 'daim'); ?>">
                        </div>

                        <?php endif; ?>

                    </div>

            </form>

        </div>

    </div>

</div>