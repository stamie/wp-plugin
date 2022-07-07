<h2 class="reg-form-group-title <?php echo $value['field_name'] ?>"><?php echo self::removeslashes($value['field_label']); ?></h2>
<?php if ($value['field_desc']) {?>
<div class="reg-form-group-desc"><?php echo self::removeslashes($value['field_desc']); ?></div>
<?php }?>
