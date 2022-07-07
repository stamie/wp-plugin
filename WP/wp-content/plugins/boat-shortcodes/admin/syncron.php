<style>
    <?php
        use function PHPSTORM_META\type;
use function YoastSEO_Vendor\GuzzleHttp\json_decode;

include __DIR__ . '/admin.css' ;
    ?>
</style>
<?php
    require_once __DIR__ . '/functions.php';
    global $wpdb;
    $query = "SELECT * FROM xml";
    $tabs = $wpdb->get_results($query, OBJECT);
    $query = "SELECT id FROM user WHERE username like 'wpuser'";
    $id = $wpdb->get_results($query, OBJECT);
    // var_dump($id); exit;
    $id = isset($id[0])?$id[0]->id:null;
    $randomString = '';
    if ($id){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $length = 24;
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        $wpdb->update('user', array('password' => $randomString), array('id' => $id) );
    }
    $queryLastSync = "SELECT parent_string, date_start, date_end from syncron_log ORDER BY date_end desc limit 1";
    $lastSyncRows = $wpdb->get_results($queryLastSync, OBJECT);
    $lastSyncRow = is_array($lastSyncRows)&&isset($lastSyncRows[0])?$lastSyncRows[0]:null;

?>
<div class="wrap">

    <h2>Boat Shortcodes</h2>

    <div class="satollo-box">
       <h1><?= __('Szinkronizáció', 'boat-shortcodes') ?></h1>
    </div>
    <?php if ($id): ?>
    <div class="tabs">
        <?php if(is_array($tabs)): ?> 
        
            <?php $active = 0; ?>
            <?php foreach($tabs as $tab): ?>
                <div class="tab_header<?=(++$active==1)?' active':' inactive'; ?>">
                    <?=$tab->class_name ?>
                </div>
            <?php endforeach; ?>
            <?php $active = 0; ?>
            <?php foreach($tabs as $tab): ?>
                <div class="tab_body<?=(++$active==1)?' active':' hidden'; ?>">
                    <ul>
                        <li><a href="#" class="table_" attr="<?=$tab->class_name ?>"><?=__('All little syncrons', 'boat-shortcodes') ?></a></li>
                        <li class="activate"><a href="#" class="report_" attr="<?=$tab->class_name ?>"><?=__('Reports', 'boat-shortcodes') ?></a></li></ul>
                    <div class="table <?=$tab->class_name ?> hidden">
                        <h1><?=__('All little syncrons', 'boat-shortcodes') ?></h1>

                        <button class="sync-button daily-sync" attr-id="<?=$tab->id ?>"><?=__('Napi kis szinkon', 'boat-shortcodes')?></button>
                        <div class="runer-sync">                            
                        </div>
                        <div class="last-sync">
                        <?php if ($lastSyncRow): ?>
                            <?php
                                $date_end = new DateTime($lastSyncRow->date_end); 
                                $date_start = new DateTime($lastSyncRow->date_start);
                                $diff = $date_end->diff($date_start);
                                $echo = $diff->format("%h óra %i perc %s másodperc %f századmásodperc");
                            ?> 
                            <?=__('Utolsó szinkron: ', 'boat-shortcodes').$lastSyncRow->parent_string ?> | <?=__('Kezdete: ', 'boat-shortcodes').$lastSyncRow->date_start ?> | <?=__('Vége: ', 'boat-shortcodes').$lastSyncRow->date_end ?> | <?=__('Össz futási idő: ', 'boat-shortcodes') ?> <?= $echo ?>

                        <?php endif; ?>
                        </div>
                        <div class="next-sync">
                        <?=__('Következő szinkron: ', 'boat-shortcodes')?> 
                        <?php
                            $dateNow = date('Y-m-d H:i:s');
                            $dateNow = new DateTime($dateNow);
                            $dateNextDay00 = date('Y-m-d 00:00:00', strtotime('next day'));
                            $dateThisDay12 = date('Y-m-d 12:00:00');
                            $dateThisDay12_ = new DateTime($dateThisDay12);

                            $dateNextDay = date('Y-m-d 00:00:00', strtotime('next day'));
                            $dateNextMonthSync = date('Y-m-01 00:00:00', strtotime('next month'));

                            if ($dateNow->diff($dateThisDay12_) < 0){
                                if ($dateNextDay == $dateNextMonthSync) {
                                    echo __('Havi szinkron ', 'boat-shortcodes');
                                    echo $dateNextMonthSync;
                                
                                } else {
                                    echo __('Napi 2X-i szinkron ', 'boat-shortcodes');
                                    echo $dateThisDay12;
                                

                                }
                            } else {

                                echo __('Napi 2X-i szinkron ', 'boat-shortcodes');
                                echo $dateNextDay00;
                            }


                            

                        ?>
                        
                        </div>
                        <div class="waitContainer" style="height:50px"></div>
                        <table class="little-sync">
                            <tr><th><?=__('Szinkron neve', 'boat-shortcodes') ?></th><th><?=__('Action', 'boat-shortcodes') ?></th><th><?=__('Last synronise', 'boat-shortcodes') ?></th></tr>
                            <tr><td class="little"><?=__('Yachtok szinkronja', 'boat-shortcodes') ?> (YachtSync)</td><td><button class="sync sync-button button" attr-sync="yacht" attr-id="<?=$tab->id ?>"><?=__('Start syncron', 'boat-shortcodes') ?></button></td></td><td><?php
    $querySync = "SELECT max(date_end) _date from syncron_log where parent_string like 'Yacht Syncron'";
    $date = $wpdb->get_results($querySync, OBJECT);
    echo is_array($date)&&count($date)>0?$date[0]->_date:'';
?>
              </td></tr>
                        <tr><td class="little"><?=__('Wordpress kinézet szinkronja', 'boat-shortcodes') ?> (WPSync)</td><td><button class="sync sync-button button" attr-sync="wpsync" attr-id="<?=$tab->id ?>"><?=__('Start syncron', 'boat-shortcodes') ?></button></td></td><td><?php
    $querySync = "SELECT max(date_end) _date from syncron_log where parent_string like 'WordPress Syncron'";
    $date = $wpdb->get_results($querySync, OBJECT);
    echo is_array($date)&&count($date)>0?$date[0]->_date:'';
?>
              </td></tr>                            
                            <tr><td><?=__('Országok szinkronja', 'boat-shortcodes') ?> (CountrySync)</td><td><button class="sync sync-button button" attr-sync="country" attr-id="<?=$tab->id ?>"><?=__('Start syncron', 'boat-shortcodes') ?></button></td><td>
<?php
    $querySync = "SELECT max(date_end) _date from syncron_log where parent_string like 'Country Syncron'";
    $date = $wpdb->get_results($querySync, OBJECT);
    echo is_array($date)&&count($date)>0?$date[0]->_date:'';
?>
                            </td></tr>
                            <tr><td><?=__('Berendezés Kategóriáinak Szinkronja', 'boat-shortcodes') ?> (EquipmentCategorySync)</td><td><button class="sync sync-button button" attr-sync="equipmentcategory" attr-id="<?=$tab->id ?>"><?=__('Start syncron', 'boat-shortcodes') ?></button></td></td><td>
<?php
    $querySync = "SELECT max(date_end) _date from syncron_log where parent_string like 'Equipment Category Syncron'";
    $date = $wpdb->get_results($querySync, OBJECT);
    echo is_array($date)&&count($date)>0?$date[0]->_date:'';
?>

                            </td></tr>
                            <tr><td><?=__('Felszereltség szinkronja', 'boat-shortcodes') ?> (EquipmentSync)</td><td><button class="sync sync-button button" attr-sync="equipment" attr-id="<?=$tab->id ?>"><?=__('Start syncron', 'boat-shortcodes') ?></button></td></td><td>
<?php
    $querySync = "SELECT max(date_end) _date from syncron_log where parent_string like 'Equipment Syncron'";
    $date = $wpdb->get_results($querySync, OBJECT);
    echo is_array($date)&&count($date)>0?$date[0]->_date:'';
?>                                
                            </td></tr>
                            <tr><td><?=__('Yacht építők szinkronja', 'boat-shortcodes') ?> (YachtBuilderSync)</td><td><button class="sync sync-button button" attr-sync="yachtbuilder" attr-id="<?=$tab->id ?>"><?=__('Start syncron', 'boat-shortcodes') ?></button></td></td><td>
<?php
    $querySync = "SELECT max(date_end) _date from syncron_log where parent_string like 'Yacht Builder Syncron'";
    $date = $wpdb->get_results($querySync, OBJECT);
    echo is_array($date)&&count($date)>0?$date[0]->_date:'';
?>                                
                            </td></tr>
                            <tr><td><?=__('Yachtmotor építők szinkronja', 'boat-shortcodes') ?> (EngineBuilderSync)</td><td><button class="sync sync-button button" attr-sync="enginebuilder" attr-id="<?=$tab->id ?>"><?=__('Start syncron', 'boat-shortcodes') ?></button></td></td><td>
<?php
    $querySync = "SELECT max(date_end) _date from syncron_log where parent_string like 'Engine Builder Syncron'";
    $date = $wpdb->get_results($querySync, OBJECT);
    echo is_array($date)&&count($date)>0?$date[0]->_date:'';
?>                                

                            </td></tr>
                            <tr><td><?=__('Yacht kategóriájinak szinkronja', 'boat-shortcodes') ?> (YachtCategorySync)</td><td><button class="sync sync-button button" attr-sync="yachtcategory" attr-id="<?=$tab->id ?>"><?=__('Start syncron', 'boat-shortcodes') ?></button></td></td><td>
<?php
    $querySync = "SELECT max(date_end) _date from syncron_log where parent_string like 'Yacht Category Syncron'";
    $date = $wpdb->get_results($querySync, OBJECT);
    echo is_array($date)&&count($date)>0?$date[0]->_date:'';
?>                                

                            </td></tr>
                            <tr><td><?=__('Yacht modellek szinkronja', 'boat-shortcodes') ?> (YachtModelSync)</td><td><button class="sync sync-button button" attr-sync="yachtmodel" attr-id="<?=$tab->id ?>"><?=__('Start syncron', 'boat-shortcodes') ?></button></td></td><td>
<?php
    $querySync = "SELECT max(date_end) _date from syncron_log where parent_string like 'Yacht Model Syncron'";
    $date = $wpdb->get_results($querySync, OBJECT);
    echo is_array($date)&&count($date)>0?$date[0]->_date:'';
?>                                 
                            </td></tr>
                            <tr><td><?=__('Kedvezmények típusainka szinkronja', 'boat-shortcodes') ?> (DiscountItemSync)</td><td><button class="sync sync-button button" attr-sync="discountitem" attr-id="<?=$tab->id ?>"><?=__('Start syncron', 'boat-shortcodes') ?></button></td></td><td>
<?php
    $querySync = "SELECT max(date_end) _date from syncron_log where parent_string like 'Sail Type Syncron'";
    $date = $wpdb->get_results($querySync, OBJECT);
    echo is_array($date)&&count($date)>0?$date[0]->_date:'';
?>
                            </td></tr>
                            <tr><td><?=__('Szezonok szikronja', 'boat-shortcodes') ?> (SeasonSync)</td><td><button class="sync sync-button button" attr-sync="season" attr-id="<?=$tab->id ?>"><?=__('Start syncron', 'boat-shortcodes') ?></button></td></td><td>
                            <?php
    $querySync = "SELECT max(date_end) _date from syncron_log where parent_string like 'Session Syncron'";
    $date = $wpdb->get_results($querySync, OBJECT);
    echo is_array($date)&&count($date)>0?$date[0]->_date:'';
?>
                            </td></tr>
                            <tr><td><?=__('Régiók szikronja', 'boat-shortcodes') ?> (RegionSync)</td><td><button class="sync sync-button button" attr-sync="region" attr-id="<?=$tab->id ?>"><?=__('Start syncron', 'boat-shortcodes') ?></button></td></td><td>
                            <?php
    $querySync = "SELECT max(date_end) _date from syncron_log where parent_string like 'Region Syncron'";
    $date = $wpdb->get_results($querySync, OBJECT);
    echo is_array($date)&&count($date)>0?$date[0]->_date:'';
?>
                            </td></tr>
                            <tr><td><?=__('Bázisok szikronja', 'boat-shortcodes') ?> (BaseSync)</td><td><button class="sync sync-button button" attr-sync="base" attr-id="<?=$tab->id ?>"><?=__('Start syncron', 'boat-shortcodes') ?></button></td></td><td><?php
    $querySync = "SELECT max(date_end) _date from syncron_log where parent_string like 'Base Syncron'";
    $date = $wpdb->get_results($querySync, OBJECT);
    echo is_array($date)&&count($date)>0?$date[0]->_date:'';
?>
              </td></tr>
                            <tr><td><?=__('Kikötők szikronja', 'boat-shortcodes') ?> (PortSync)</td><td><button class="sync sync-button button" attr-sync="port" attr-id="<?=$tab->id ?>"><?=__('Start syncron', 'boat-shortcodes') ?></button></td></td><td><?php
    $querySync = "SELECT max(date_end) _date from syncron_log where parent_string like 'Port Syncron'";
    $date = $wpdb->get_results($querySync, OBJECT);
    echo is_array($date)&&count($date)>0?$date[0]->_date:'';
?>
              </td></tr>
                            <tr><td><?=__('Kormány típus szinkronja', 'boat-shortcodes') ?> (SteeringTypeSync)</td><td><button class="sync sync-button button" attr-sync="steeringtype" attr-id="<?=$tab->id ?>"><?=__('Start syncron', 'boat-shortcodes') ?></button></td></td><td><?php
    $querySync = "SELECT max(date_end) _date from syncron_log where parent_string like 'Steering Type Syncron'";
    $date = $wpdb->get_results($querySync, OBJECT);
    echo is_array($date)&&count($date)>0?$date[0]->_date:'';
?>
              </td></tr>
                            <tr><td><?=__('Vitorla típusainak szinkronja', 'boat-shortcodes') ?> (SailTypeSync)</td><td><button class="sync sync-button button" attr-sync="sailtype" attr-id="<?=$tab->id ?>"><?=__('Start syncron', 'boat-shortcodes') ?></button></td></td><td><?php
    $querySync = "SELECT max(date_end) _date from syncron_log where parent_string like 'Sail Type Syncron'";
    $date = $wpdb->get_results($querySync, OBJECT);
    echo is_array($date)&&count($date)>0?$date[0]->_date:'';
?>
              </td></tr>
              <tr><td><?=__('Price Measure szinkronja', 'boat-shortcodes') ?> (PriceMeasureSync)</td><td><button class="sync sync-button button" attr-sync="pricemeasure" attr-id="<?=$tab->id ?>"><?=__('Start syncron', 'boat-shortcodes') ?></button></td></td><td><?php
    $querySync = "SELECT max(date_end) _date from syncron_log where parent_string like 'Price Measure Syncron'";
    $date = $wpdb->get_results($querySync, OBJECT);
    echo is_array($date)&&count($date)>0?$date[0]->_date:'';
?>
              </td></tr>
                            <tr><td><?=__('Szolgáltatás szinkronja', 'boat-shortcodes') ?> (ServiceSync)</td><td><button class="sync sync-button button" attr-sync="service" attr-id="<?=$tab->id ?>"><?=__('Start syncron', 'boat-shortcodes') ?></button></td></td><td><?php
    $querySync = "SELECT max(date_end) _date from syncron_log where parent_string like 'Service Syncron'";
    $date = $wpdb->get_results($querySync, OBJECT);
    echo is_array($date)&&count($date)>0?$date[0]->_date:'';
?>
              </td></tr>
                            <tr><td><?=__('Cégek szinkronja', 'boat-shortcodes') ?> (CompanySync)</td><td><button class="sync sync-button button" attr-sync="company" attr-id="<?=$tab->id ?>"><?=__('Start syncron', 'boat-shortcodes') ?></button></td></td><td><?php
    $querySync = "SELECT max(date_end) _date from syncron_log where parent_string like 'Company Syncron'";
    $date = $wpdb->get_results($querySync, OBJECT);
    echo is_array($date)&&count($date)>0?$date[0]->_date:'';
?>
              </td></tr>

              
            </table>
                    </div>
                    <div class="report <?=$tab->class_name ?>">
                    <?php
                        $reportQuery = "SELECT sl.date_start date_start, sl.date_end date_end, sl.parent_string string, subsl.parent_string parent_string, sl.errors errors, sl.is_automate is_automate FROM syncron_log sl LEFT JOIN syncron_log subsl ON subsl.id = sl.parent_id ORDER BY sl.id DESC limit 50";
                        $report = $wpdb->get_results($reportQuery, OBJECT);
                        $searchTypeQuery1 = "SELECT distinct parent_string parent_string FROM syncron_log";
                        $resultSearch1 = $wpdb->get_results($searchTypeQuery1, OBJECT);

                        $searchTypeQuery2 = "SELECT distinct parent_string FROM syncron_log WHERE id in (SELECT paretnt_id FROM syncron_log)";
                        $resultSearch2 = $wpdb->get_results($searchTypeQuery2, OBJECT);

                    ?>
                        <h1>Report</h1>
                        <button class="sync-button daily-sync" attr-id="<?=$tab->id ?>"><?=__('Napi kis szinkon', 'boat-shortcodes')?></button>
                        <div class="runer-sync"></div>
                        <div class="last-sync">
                        <?php if ($lastSyncRow): ?>
                            <?php
                                $date_end = new DateTime($lastSyncRow->date_end); 
                                $date_start = new DateTime($lastSyncRow->date_start);
                                $diff = $date_end->diff($date_start);
                                $echo = $diff->format("%h óra %i perc %s másodperc %f századmásodperc");
                            ?> 
                            <?=__('Utolsó szinkron: ', 'boat-shortcodes').$lastSyncRow->parent_string ?> | <?=__('Kezdete: ', 'boat-shortcodes').$lastSyncRow->date_start ?> | <?=__('Vége: ', 'boat-shortcodes').$lastSyncRow->date_end ?> | <?=__('Össz futási idő: ', 'boat-shortcodes') ?> <?php echo $echo ?>


                        <?php endif; ?>

                        </div>
                        <div class="next-sync">
                            <?=__('Következő szinkron: ', 'boat-shortcodes')?> 
                        <?php

                            if ($dateNow->diff($dateThisDay12_) < 0){
                                if ($dateNextDay == $dateNextMonthSync) {
                                    echo __('Havi szinkron ', 'boat-shortcodes');
                                    echo $dateNextMonthSync;
                                
                                } else {
                                    echo __('Napi 2X-i szinkron ', 'boat-shortcodes');
                                    echo $dateThisDay12;
                                

                                }
                            } else {

                                echo __('Napi 2X-i szinkron ', 'boat-shortcodes');
                                echo $dateNextDay00;
                            }


                            

                        ?>
                        </div>
                        <div class="waitContainer" style="height:50px"></div>
                        <div class="search-box">
                            <label for="type-1">
                                <?=__('Felirat', 'boat-shortcodes')?>
                            <select name="type-1" class="type-1 <?= $tab->id ?>">
                                <option value = "false"><?=__('Összes', 'boat-shortcodes') ?></option>
                                <?php foreach ($resultSearch1 as $type): ?>
                                    <option value="<?=$type->parent_string?>"><?=__($type->parent_string, 'boat-shortcodes') ?></option>
                                <?php endforeach; ?> 
                            </select></label>
                            <label for="type-2">
                                <?=__('Szülő Felirata', 'boat-shortcodes')?>
                            <select name="type-2" class="type-2 <?= $tab->id ?>">
                                <option value = "-1"><?=__('Összes', 'boat-shortcodes') ?></option>
                                <option value = "0"><?=__('Haven\'t parent', 'boat-shortcodes') ?></option>
                                <?php foreach ($resultSearch2 as $type): ?>
                                    <option value="<?=$type->parent_string?>"><?=__($type->parent_string, 'boat-shortcodes') ?></option>
                                <?php endforeach; ?> 
                            </select></label>

                            <button type="button" class="button search-in-report" tab-id="<?= $tab->id ?>"><?= __('Keresés', 'boat-shortcodes' ) ?></button>
                        </div>
                        <div class="result-box">
                            <table class="syncron_datas">
                                <thead>
                                <tr>
                                    <th><?=__('Kezdés Időpontja', 'boat-shortcodes') ?></th>
                                    <th><?=__('Végzés Időpontja', 'boat-shortcodes') ?></th>
                                    <th><?=__('Felirat', 'boat-shortcodes') ?></th>
                                    <th><?=__('Szülő Felirata', 'boat-shortcodes') ?></th>
                                    <th><?=__('Kézi/Automatikus frissítés', 'boat-shortcodes') ?></th>
                                    <th><?=__('Hibák', 'boat-shortcodes') ?></th>
                                </tr>
                                </thead>
                                <tbody id="report_table">
                                <?php foreach ($report as $row): ?>
                                    <tr>
                                        <td><?= $row->date_start ?></td>
                                        <td><?= $row->date_end ?></td>
                                        <td><?=__($row->string, 'boat-shortcodes') ?></td>
                                        <td><?=__(isset($row->parent_string)?$row->parent_string:'Haven\'t parent', 'boat-shortcodes') ?></td>
                                        <td><?= $row->is_automate?'<p class="automata">'.__('Autómatikus', 'boat-shortcodes').'</p>':'<p class="manual">'.__('Kézi', 'boat-shortcodes').'</p>' ?></td>
                                        <td><?php $ret = writeErrorsForSyncron( json_decode($row->errors), $tab->id, $row->string ); echo $ret;?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

        
        <?php endif; ?>
    </div>
    <?php else: ?>
        <h2><?=__('Don\'t have wpuser', 'bnoat_shortcodes' ) ?></h2>
    <?php endif; ?>
</div>
<?php if($id): ?>
    <script src="/wp-content/plugins/boat-shortcodes/include/refreshlastsync/lastSync.js"></script>
    <script>
    
    var tabs = ['table', 'report'];
    jQuery('.sync').on('click', function(){
        var sync = jQuery(this);
        var attr_id = sync.attr('attr-id');
        var name = sync.attr('attr-sync');
        var send_url = '<?=get_option('yii_url', '/')?>inwpsync/'+name+'?id='+attr_id+'&p=<?= $randomString ?>';
       // alert (send_url);
        jQuery.ajax({
            url: send_url,
            method: "GET",
            cache:false,
            dataType: "json",

            beforeSend :function(){
                run_waitMe(jQuery('.waitContainer'), 1, 'progressBar');
                jQuery('.sync-button').prop('disabled', true);
                switch (name){
                    case "country":
                        jQuery('.runer-sync').html('<p class="bold-sync">Futó szinkron: Országok szinkronja</p>');
                        lastSyncModfy('Országok szinkronja');
                        break;
                    case 'equipmentcategory':
                        jQuery('.runer-sync').html('<p class="bold-sync">Futó szinkron: Berendezés Kategóriáinak Szinkronja</p>');
                        lastSyncModfy('Berendezés Kategóriáinak Szinkronja');
                        break;
                    case 'equipment':
                        jQuery('.runer-sync').html('<p class="bold-sync">Futó szinkron: Felszereltség Szinkronja</p>');
                        lastSyncModfy('Felszereltség Szinkronja');
                        break;
                    case 'yachtbuilder':
                        jQuery('.runer-sync').html('<p class="bold-sync">Futó szinkron: Yacht építők Szinkronja</p>');
                        lastSyncModfy('Yacht építők Szinkronja');
                        break;
                    case 'enginebuilder':
                        jQuery('.runer-sync').html('<p class="bold-sync">Futó szinkron: Yachtmotor építők Szinkronja</p>');
                        lastSyncModfy('Yachtmotor építők Szinkronja');
                        break;
                    case 'yachtcategory':
                        jQuery('.runer-sync').html('<p class="bold-sync">Futó szinkron: Yacht kategóriájinak Szinkronja</p>');
                        lastSyncModfy('Yacht kategóriájinak Szinkronja');
                        break;
                    case 'yachtmodel':
                        jQuery('.runer-sync').html('<p class="bold-sync">Futó szinkron: Yacht modellek Szinkronja</p>');
                        lastSyncModfy('Yacht modellek Szinkronja');
                        break;
                    case 'discountitem':
                        jQuery('.runer-sync').html('<p class="bold-sync">Futó szinkron: Kedvezmények típusainka Szinkronja</p>');
                        lastSyncModfy('Kedvezmények típusainka Szinkronja');
                        break;
                    case 'season':
                        jQuery('.runer-sync').html('<p class="bold-sync">Futó szinkron: Szezonok Szinkronja</p>');
                        lastSyncModfy('Szezonok Szinkronja');
                        break;
                    case 'region':
                        jQuery('.runer-sync').html('<p class="bold-sync">Futó szinkron: Régiók Szinkronja</p>');
                        lastSyncModfy('Régiók Szinkronja');
                        break;
                    case 'base':
                        jQuery('.runer-sync').html('<p class="bold-sync">Futó szinkron: Bázisok Szinkronja</p>');
                        lastSyncModfy('Bázisok Szinkronja');
                        break;
                    case 'port':
                        jQuery('.runer-sync').html('<p class="bold-sync">Futó szinkron: Kikötők Szinkronja</p>');
                        lastSyncModfy('Kikötők Szinkronja');
                        break;
                    case 'steeringtype':
                        jQuery('.runer-sync').html('<p class="bold-sync">Futó szinkron: Kormány típus Szinkronja</p>');
                        lastSyncModfy('Kormány típus Szinkronja');
                        break;
                    case 'sailtype':
                        jQuery('.runer-sync').html('<p class="bold-sync">Futó szinkron: Vitorla típusainak Szinkronja</p>');
                        lastSyncModfy('Vitorla típusainak Szinkronja');
                        break;
                    case 'service':
                        jQuery('.runer-sync').html('<p class="bold-sync">Futó szinkron: Szolgáltatások Szinkronja</p>');
                        lastSyncModfy('Szolgáltatások Szinkronja');
                        break;
                    case 'company':
                        jQuery('.runer-sync').html('<p class="bold-sync">Futó szinkron: Cégek Szinkronja</p>');
                        lastSyncModfy('Cégek Szinkronja');
                        break;
                    case 'yacht':
                        jQuery('.runer-sync').html('<p class="bold-sync">Futó szinkron: Yachtok Szinkronja</p>');
                        lastSyncModfy('Yachtok Szinkronja');
                        break;
                        case 'wpsync':
                        jQuery('.runer-sync').html('<p class="bold-sync">Futó szinkron: WP Kinézet Szinkronja</p>');
                        lastSyncModfy('WP Kinézet Szinkronja');
                        break;
                    
                    case 'pricemeasure':
                        jQuery('.runer-sync').html('<p class="bold-sync">Futó szinkron: Price Measure Szinkronja</p>');
                        lastSyncModfy('Price Measure Szinkronja');
                        break;
                    
                    default:
                        jQuery('.runer-sync').html('<p class="bold-sync">Futó szinkron: '+name+' Szinkronja</p>');
                        break;

                }
            },
            success: function( data ) {
            jQuery('.waitContainer').waitMe("hide");
            jQuery('.runer-sync').html('');
            if (data.return){
                alert( '<?= __('The synron is succesful', 'boat-shortcodes') ?>' );
                jQuery('.sync-button').prop('disabled', false);
            } else
                alert( '<?= __("Sikertelen Szinkronizáció", 'boat-shortcodes') ?>' );
            endSyncLastSync();
        }
        }).always(function(){
            run_waitMe(jQuery('.waitContainer'), 1, 'progressBar');

        })/*.done(function( data ) {
            jQuery('.waitContainer').waitMe("hide");
            jQuery('.runer-sync').html('');
            if (data.return){
                alert( '<?= __('The synron is succesful', 'boat-shortcodes') ?>' );
                jQuery('.sync-button').prop('disabled', false);
            } else
                alert( '<?= __("Sikertelen Szinkronizáció", 'boat-shortcodes') ?>' );
            endSyncLastSync();
        })*/.fail(function() {
            jQuery('.waitContainer').waitMe("hide");
            jQuery('.runer-sync').html('');
                alert( "<?=__('Frissítsd az oldalt!', 'boat-shortcodes') ?>" );
            endSyncLastSync();
        });
    });
    
    jQuery('.daily-sync').on('click', function(){
        var sync = jQuery(this);
        var attr_id = sync.attr('attr-id');
        var name = 'littlesync';
        var send_url = '<?=get_option('yii_url', '/')?>inwpsync/'+name+'?id='+attr_id+'&p=<?= $randomString ?>';
        jQuery.ajax({
            url: send_url,
            method: "GET",
            cache:false,
            dataType: "json",

            beforeSend :function(){
                jQuery('.sync-button').prop('disabled', true);
                run_waitMe(jQuery('.waitContainer'), 1, 'progressBar');
             
            }
        }).always(function(){
            run_waitMe(jQuery('.waitContainer'), 1, 'progressBar');
            jQuery('.runer-sync').html('<p class="bold-sync">Futó szinkron: Napi kis szinkron</p>');

        }).done(function( data ) {
            jQuery('.waitContainer').waitMe("hide");
            jQuery('.runer-sync').html('');
            if (data.return){
                alert( '<?= __('The synron is succesful', 'boat-shortcodes') ?>' );
                jQuery('.sync-button').prop('disabled', false);
            } else
                alert( '<?= __("Sikertelen Szinkronizáció", 'boat-shortcodes') ?>' );
            endSyncLastSync();
        }).fail(function() {
            jQuery('.waitContainer').waitMe("hide");
            jQuery('.runer-sync').html('');
            alert( "<?=__('Frissítsd az oldalt!', 'boat-shortcodes') ?>" );
            endSyncLastSync();
        });
    });

    jQuery('.search-in-report').on('click', function(){

        var tab_id = jQuery(this).attr('tab-id');
        var type1 = jQuery('.type-1>'+tab_id).val();
        var type2 = jQuery('.type-2>'+tab_id).val();
        
        
        var send_url = '/wp-content/plugins/boat-shortcodes/admin/ajax_search.php';
        jQuery.ajax({
            url: send_url,
            method: "POST",
            cache:false,
            data: { 'type_1': type1, 'type_2': type2, 'tab_id': tab_id },
           // dataType: "html",

        }).done(function( msg ) {
                jQuery('#report_table').html(msg);
                jQuery('.waitContainer').waitMe("hide");
            
        }).fail(function() {
            jQuery('.waitContainer').waitMe("hide");
            alert( send_url );
        });
    });


    jQuery('.table_').on('click', function(){
       
        var val = jQuery(this);
        var findClass = val.attr('attr');

        var li = val.parent('li');
        var ul = li.parent('ul');
        
        ul.children('li').each(function(){
            jQuery(this).removeClass('activate');
        });

        li.addClass('activate');

        tabs.forEach(function(tab){
            if (tab == 'table'){
                jQuery('.table.'+findClass).removeClass('hidden');

            } else {
                jQuery('.'+tab).addClass('hidden');
            }
        });
    });

    jQuery('.report_').on('click', function(){
        var val = jQuery(this);
        var findClass = val.attr('attr');

        var li = val.parent('li');
        var ul = li.parent('ul');
        ul.children('li').each(function(){
            jQuery(this).removeClass('activate');
        });

        li.addClass('activate');
        tabs.forEach(function(tab){
            if (tab == 'report'){
                jQuery('.report.'+findClass).removeClass('hidden');
            } else {
                jQuery('.'+tab).addClass('hidden');
            }
        });


    });

	function run_waitMe(el, num, effect){
		text = 'Kérlek várj...';
		fontSize = '';
		switch (num) {
			case 1:
			maxSize = '';
			textPos = 'vertical';
			break;
			case 2:
			text = '';
			maxSize = 30;
			textPos = 'vertical';
			break;
			case 3:
			maxSize = 30;
			textPos = 'horizontal';
			fontSize = '18px';
			break;
		}
		el.waitMe({
			effect: effect,
			text: text,
			bg: 'rgba(255,255,255,0.7)',
			color: '#000',
			maxSize: 50,
			waitTime: -1,
			source: 'img.svg',
			textPos: 'horizontal',
			fontSize: '18px',
			onClose: function(el) {}
		});
	}
    </script>
<?php endif; ?>