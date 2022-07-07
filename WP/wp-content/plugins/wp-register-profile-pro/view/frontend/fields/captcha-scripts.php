<script type="application/javascript">
  function refreshCaptcha(){ jQuery(".captcha").attr("src", '<?php echo plugins_url(CFWSP_DIR_NAME . '/captcha/captcha.php'); ?>?rand=' + Math.random() ); }
</script>
