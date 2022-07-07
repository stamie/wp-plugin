<style>
<?php include __DIR__ . '/admin.css';
    if ($_POST['yii_url']){
        if (!get_option('yii_url'))
            add_option('yii_url', $_POST['yii_url']);
        else
            update_option('yii_url', $_POST['yii_url']);
    }
    $yii = get_option('yii_url');
    $yii = $yii?'value="'.$yii.'"':'';
?>
</style>

<div class="wrap">

    <h2>Boat Shortcodes Beállítások</h2>

    <div class="satollo-box">
        <form action="#" method="post">
            <input type="text" name="yii_url" <?= $yii ?>/>
            <button type="submit">Mentés</button>
        </form>
    </div>
</div>
