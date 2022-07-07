<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/* Variables */
/* @var $validationManager DUPX_Validation_manager */
?>
<div class="clearfix" >
    <?php DUPX_Params_Manager::getInstance()->getHtmlFormParam(DUPX_Params_Manager::PARAM_VALIDATION_SHOW_ALL); ?>
</div>
<?php
dupxTplRender('parts/validation/validation-category', array(
    'title'    => 'General',
    'category' => DUPX_Validation_manager::CAT_GENERAL));
dupxTplRender('parts/validation/validation-category', array(
    'title'    => 'File System',
    'category' => DUPX_Validation_manager::CAT_FILESYSTEM));
dupxTplRender('parts/validation/validation-category', array(
    'title'    => 'PHP config',
    'category' => DUPX_Validation_manager::CAT_PHP));
dupxTplRender('parts/validation/validation-category', array(
    'title'    => 'Database',
    'category' => DUPX_Validation_manager::CAT_DATABASE));
