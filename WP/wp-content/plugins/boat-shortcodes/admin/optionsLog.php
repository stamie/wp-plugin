<style>
    <?php include __DIR__ . '/admin.css' ?>
</style>

<div class="wrap">

    <h1>Opciózások és Foglalások státuszai</h1> 

<h2 id="nav-tab-wrapper" class="wp-tab-bar nav-tab-wrapper">
	<a href="#tabs-1" id="general-tab" class="nav-tab options">Összes Opciózás</a>
	
	<!--a href="#tabs-3" id="general-tab" class="nav-tab">Extrák</a-->
</h2>

<div class="wp-tab-panel shortcode" id="tabs-1">
  
<div class="alert"></div>

<?php

$order    = "bo.`id`";
$by       = "DESC";
$perPage  = (isset($_GET['per_page'])&&intval($_GET['per_page']))?intval($_GET['per_page']):50;
$loadPage = (isset($_GET['load_page'])&&intval($_GET['load_page'], false))?intval($_GET['load_page']):1;
$pageIndex = $loadPage;

$loadPage--;
$loadPage    = $loadPage * $perPage;

$order_by = array('asc'=>array("channel" =>"", "order"=>"", "order_own"=>"", "name"=>"", "date_from"=>"", "date_to"=>"", "send_date"=>"", "status"=>"", "status_mod"=>"", "user_price"=>"", "user_id" => "", "email"=>"", "datas"=>""), 
                'desc' =>array("channel" =>"", "order"=>"", "order_own"=>"", "name"=>"", "date_from"=>"", "date_to"=>"", "send_date"=>"", "status"=>"", "status_mod"=>"", "user_price"=>"", "user_id" => "", "email"=>"", "datas"=>""));
/* */
if (isset($_GET["asc"])){
    if (isset($order_by["asc"][$_GET["asc"]])) {
        $order_by["asc"][$_GET["asc"]] = " disabled";
    }
    switch ($_GET["asc"]){
        case "channel":
            $order = "bo.xml_id";
            break;
        case "order":
            $order = "bo.xml_json_id";
            break;
        case "order_own":
            $order = "bo.id";
            break;
        case "date_from":
            $order = "bo.`period_from`";
            break;
        case "date_to":
            $order = "bo.period_to";
            break;
        case "send_date":
            $order = "bo.`create_date`";
            break;
        case "name":
            $order = "p.post_title";
            break;
        case "status":
            $order = "bo.reservation_status";
            break;
        case "status_mod":
            $order = "bo.`modify_date`";
            break;
        case "user_price":
            $order = "cast(bo.user_price as decimal(10,2))";
            break;
        case "user_id":
            $order = "bo.user_id";
            break;
        case "email":
            $order = "bo.email";
            break;
        case "datas":
            $order = "concat(bo.first_name, bo.last_name, c.name, bo.zip_code, bo.city, bo.address)";
            break;
    }
    $by = "ASC";
} else if (isset($_GET["desc"])){
    if (isset($order_by["desc"][$_GET["desc"]])) {
        $order_by["desc"][$_GET["desc"]] = " disabled";
    }
    switch ($_GET["desc"]){
        case "channel":
            $order = "bo.xml_id";
            break;
        case "order":
            $order = "bo.xml_json_id";
            break;
        case "order_own":
            $order = "bo.id";
            break;
        case "date_from":
            $order = "bo.`period_from`";
            break;
        case "date_to":
            $order = "bo.period_to";
            break;
        case "send_date":
            $order = "bo.`create_date`";
            break;
        case "name":
            $order = "p.post_title";
            break;
        case "status":
            $order = "bo.reservation_status";
            break;
        case "status_mod":
            $order = "bo.`modify_date`";
            break;
        case "user_price":
            $order = "cast(bo.user_price as decimal(10,2))";
            break;
        case "user_id":
            $order = "bo.user_id";
            break;
        case "email":
            $order = "bo.email";
            break;
        case "datas":
            $order = "concat(bo.first_name, bo.last_name, c.name, bo.zip_code, bo.city, bo.address)";
            break;    
    }
    
    $by = "DESC";
    
} 
$url = "/wp-admin/admin.php?".htmlentities("page=boat-shortcodes/admin/optionsLog.php");
$url2 = "/wp-admin/admin.php";
$where = '1 = 1';
if ( isset($_GET['order_id']) && $_GET['order_id'] !=''){
    $where .= ' AND bo.xml_json_id = '.$_GET['order_id'];
}
if ( isset($_GET['order_wp_id']) && $_GET['order_wp_id'] !=''){
    $where .= ' AND bo.id = '.$_GET['order_wp_id'];
}
if ( isset($_GET['order_status'])  && $_GET['order_status'] !='' && $_GET['order_status'] != 'ALL'){
    $where .= ' AND bo.reservation_status like \''.$_GET['order_status'].'\'';
} 
if ( isset($_GET['user_id']) && $_GET['user_id'] !=''){
    $where .= ' AND bo.user_id = '.$_GET['user_id'].'';
}
if ( isset($_GET['email']) && $_GET['email'] !=''){
    $where .= ' AND ((bo.email like \'%'.$_GET['email'].'%\') OR (u.user_email like \'%'.$_GET['email'].'%\'))';
}
if ( isset($_GET['billing_inf']) && $_GET['billing_inf'] !=''){
    $where .= " AND lower(concat(bo.first_name, ' ', bo.last_name, ' ', bo.country, ', ', bo.zip_code, ' ', bo.city, ' ', bo.address)) like lower('%".$_GET['billing_inf']."%')";
}
if ( isset($_GET['boat_name']) && $_GET['boat_name'] !=''){
    $where .= " AND p.post_name like '%".$_GET['boat_name']."%'";
}
 
if ( isset($_GET['begin_date']) && $_GET['begin_date'] !=''){
    $where .= " AND DATEDIFF( bo.period_from , '".$_GET['begin_date']."')>=-1";
    $where .= " AND DATEDIFF( bo.period_from , '".$_GET['begin_date']."')<=1";

}
if ( isset($_GET['end_date']) && $_GET['end_date'] !=''){
    $where .= " AND DATEDIFF( bo.period_to , '".$_GET['end_date']."')>=-1";
    $where .= " AND DATEDIFF( bo.period_to , '".$_GET['end_date']."')<=1";
}
if ( isset($_GET['start_date']) && $_GET['start_date'] !=''){
    $where .= " AND DATEDIFF( bo.create_date , '".$_GET['start_date']."')<=1";
    $where .= " AND DATEDIFF( bo.create_date , '".$_GET['start_date']."')>=-1";
}

if ( isset($_GET['xml_channel']) && $_GET['xml_channel'] !='ALL'){
    $where .= " AND bo.xml_id in (SELECT id from xml where slug like '".$_GET['xml_channel']."')";
}
global $wpdb;//SELECT p.post_title FROM {$wpdb->prefix}posts p WHERE p.ID in (SELECT wy.wp_id FROM wp_yacht wy WHERE wy.id = bo.yacht_id)
$sql = "SELECT bo.message user_message, u.user_email, bo.user_price, bo.currency, bo.id bo_id, p.post_title, p.post_name, p.ID wp_id, c.name country_name, x.class_name, bo.`xml_json_id`, bo.`period_from`, bo.`period_to`, bo.`reservation_status`, bo.`create_date`, bo.`modify_date`, bo.`user_id`, bo.`last_name`, bo.`first_name`, bo.`city`, bo.`zip_code`, bo.`address`, bo.`phone_number`, bo.`email`, bo.`company`, bo.`vat_number`, bo.yacht_id FROM `boat_option` as bo left join `xml` as x
 ON x.id = bo.xml_id left join country as c on c.xml_id=bo.xml_id and c.xml_json_id=bo.country
 left join {$wpdb->prefix}posts p ON p.ID in (SELECT wy.wp_id FROM wp_yacht wy WHERE wy.id = bo.yacht_id)
 left join {$wpdb->prefix}users u ON u.ID = bo.user_id WHERE {$where}
 ORDER BY $order $by "; 
$limit = "LIMIT {$loadPage},{$perPage}";
 $rows = $wpdb->get_results($sql.$limit, OBJECT);
 $countRows = count($wpdb->get_results($sql, OBJECT));
 $pageNum   = intdiv($countRows, $perPage);
 $pageNum   = ($pageNum*$perPage)<$countRows?($pageNum+1):$pageNum;
 $orderBy = isset($_GET['desc'])?$_GET['desc']:'order_own';
 $orderBy = isset($_GET['asc'])?$_GET['asc']:$orderBy;
 
?>
<div class="waitContainer"></div>
<form id="pages_load" method="get" action="<?=$url2?>" class="load_pages">
<select name="per_page" onchange="jQuery('#pages_load').submit()">
    <option value="10" <?=($perPage==10)?"selected": ""?> >10</option>
    <option value="25" <?=($perPage==25)?"selected": ""?> >25</option>
    <option value="50" <?=($perPage==50)?"selected": ""?> >50</option>
    <option value="75" <?=($perPage==75)?"selected": ""?> >75</option>
    <option value="100" <?=($perPage==100)?"selected": ""?> >100</option>
</select>
<?php
if ( isset($_GET['xml_channel'])){
?>
<input type="hidden" value="<?=$_GET['xml_channel']?>" name="xml_channel"/>
<?php
}
?>
<?php
if ( isset($_GET['order_id'])){
?>
<input type="hidden" value="<?=$_GET['order_id']?>" name="order_id"/>
<?php
}
if ( isset($_GET['order_wp_id'])){
?>
<input type="hidden" value="<?=$_GET['order_wp_id']?>" name="order_wp_id"/>
<?php
}
if ( isset($_GET['order_status']) && $_GET['order_status'] != 'ALL'){
?>
<input type="hidden" value="<?=$_GET['order_status']?>" name="order_status"/>
<?php
} 
if ( isset($_GET['user_id'])){
?>
<input type="hidden" value="<?=$_GET['user_id']?>" name="user_id"/>
<?php
}
if ( isset($_GET['email'])){
?>
<input type="hidden" value="<?=$_GET['email']?>" name="email"/>
<?php
}
if ( isset($_GET['billing_inf'])){
?>
<input type="hidden" value="<?=$_GET['billing_inf']?>" name="billing_inf"/>
<?php
}
if ( isset($_GET['boat_name'])){
?>
<input type="hidden" value="<?=$_GET['boat_name']?>" name="boat_name"/>
<?php
}
 
if ( isset($_GET['begin_date'])){
?>
<input type="hidden" value="<?=$_GET['begin_date']?>" name="begin_date"/>
<?php
}
if ( isset($_GET['end_date'])){
?>
<input type="hidden" value="<?=$_GET['end_date']?>" name="end_date"/>
<?php
}
if ( isset($_GET['start_date'])){
?>
<input type="hidden" value="<?=$_GET['start_date']?>" name="start_date"/>
<?php
}
?>
<input type="hidden" value="boat-shortcodes/admin/optionsLog.php" name="page"/>
<input type="hidden" value="<?=$orderBy?>" name="<?=strtolower($by)?>" />
</form>
<div class="pager">
<?php
if ($pageIndex > 1) {
?>
    <span class="scrollPage"><a href="<?=$url?>&<?=strtolower($by)?>=<?=$orderBy?>&per_page=<?=$perPage?>&load_page=<?=1?>"><<</a></span>
    <span class="scrollPage"><a href="<?=$url?>&<?=strtolower($by)?>=<?=$orderBy?>&per_page=<?=$perPage?>&load_page=<?=($pageIndex-1)?>"><</a></span>
<?php
}
for ($index = 1; $index < ($pageNum+1); $index++):
?>
    <span class="scrollPage"><a href="<?=$url?>&<?=strtolower($by)?>=<?=$orderBy?>&per_page=<?=$perPage?>&load_page=<?=$index?>"><?=$index?></a></span>
<?php
endfor;
if ($pageIndex < $pageNum) {
?>
    <span class="scrollPage"><a href="<?=$url?>&<?=strtolower($by)?>=<?=$orderBy?>&per_page=<?=$perPage?>&load_page=<?=($pageIndex+1)?>">></a></span>
    <span class="scrollPage"><a href="<?=$url?>&<?=strtolower($by)?>=<?=$orderBy?>&per_page=<?=$perPage?>&load_page=<?=($pageNum)?>">>></a></span>
<?php
}
?>
</div>

<form id="find_pages" method="get" action="<?=$url2?>" class="find_pages">

<label for="id">Csatorna
<select name="xml_channel" id="xml_channel">
    <option value="ALL"  >ALL</option>
    <option value="nausys" >Nausys</option>
</select>
</label>
<label for="order_id">Rendelés ID
<input type="text"   name="order_id" id="order_id" />
</label>
<label for="order_wp_id">WP Rendelés ID
<input type="text"   name="order_wp_id" id="order_wp_id" />
</label>
<label for="begin_date">Kezdő dátum
<input type="date"   name="begin_date" id="begin_date" />
</label>
<label for="end_date">Vég dátum
<input type="date"   name="end_date"   id="end_date" />
</label>
<label for="order_status">Státusz
<select name="order_status" id="order_status">
    <option value="ALL"         >ALL STATUS</option>
    <option value="OPTION"      >OPTION</option>
    <option value="RESERVATION" >RESERVATION</option>
    <option value="STORNO"      >STORNO</option>
    <option value="OPTION_SEND_ERROR"   >O.S_ERROR</option>
</select>
</label>
<label for="start_date">Beküldés dátuma
<input type="date"   name="start_date" id="start_date" />
</label>
<label for="user_id">User ID
<input type="text"   name="user_id"     id="user_id" />
</label>
<label for="email">Email
<input type="text"   name="email"       id="email" />
</label>
<label for="billing_inf">Számlázási Inf.
<input type="text"   name="billing_inf" id="billing_inf" />
</label>
<label for="boat_name">Hajó neve
<input type="text"   name="boat_name"   id="boat_name" />
</label>
<input type="hidden" value="boat-shortcodes/admin/optionsLog.php" name="page" id="page" />
<button type="button" id="torol">Visszaállítás</button>
<button type="button" id="mezok_torol">Mezők törlése</button>
<button type="submit">Keres</button>

</form>
<script>
    jQuery("#torol").on('click', function(){
        jQuery("#find_pages").find("input").each(function () {
            jQuery(this).val('');
        });
        jQuery("#find_pages").find("select").each(function () {
            jQuery(this).val('ALL');
        });
        jQuery("#page").val("boat-shortcodes/admin/optionsLog.php");
        jQuery("#find_pages").submit();
    });
    
    jQuery("#mezok_torol").on('click', function(){
        jQuery("#find_pages").find("input").each(function () {
            jQuery(this).val('');
        });
        jQuery("#find_pages").find("select").each(function () {
            jQuery(this).val('ALL');
        });
        jQuery("#page").val("boat-shortcodes/admin/optionsLog.php");
       
    });
</script>
<table class="boat_options_log">
    <thead>
        <tr>
            <th><span class="logs">Csatorna</span> <span class="order_by"><button type="button"<?=$order_by["asc"]["channel"]; ?> class="up<?=$order_by["asc"]["channel"]?>" onclick="window.open('<?=$url?>&asc=channel', '_parent')" ><i class="fa-solid fa-arrow-up-wide-short"> </i></button><button type="button"<?=$order_by["desc"]["channel"] ?> class="down<?=$order_by["desc"]["channel"]?>" onclick="window.open('<?=$url?>&desc=channel', '_parent')"><i class="fa-solid fa-arrow-down-short-wide"> </i></button></span></th>
            <th><span class="logs">Rendelés ID</span> <span class="order_by"><button type="button"<?=$order_by["asc"]["order"]; ?> class="up<?=$order_by["asc"]["order"]?>" onclick="window.open('<?=$url?>&asc=order', '_parent')" ><i class="fa-solid fa-arrow-up-wide-short"> </i></button><button type="button"<?=$order_by["desc"]["order"] ?> class="down<?=$order_by["desc"]["order"]?>" onclick="window.open('<?=$url?>&desc=order', '_parent')"><i class="fa-solid fa-arrow-down-short-wide"> </i></button></span></th>
            <th><span class="logs">Rendelés ID wp</span> <span class="order_by"><button type="button"<?=$order_by["asc"]["order_own"]; ?> class="up<?=$order_by["asc"]["order_own"]?>" onclick="window.open('<?=$url?>&asc=order_own', '_parent')" ><i class="fa-solid fa-arrow-up-wide-short"> </i></button><button type="button"<?=$order_by["desc"]["order_own"] ?> class="down<?=$order_by["desc"]["order_own"]?>" onclick="window.open('<?=$url; ?>&desc=order_own', '_parent')"><i class="fa-solid fa-arrow-down-short-wide"> </i></button></span></th>
            <th><span class="logs">Kezdő dátum</span> <span class="order_by"><button type="button"<?=$order_by["asc"]["date_from"]; ?> class="up<?=$order_by["asc"]["date_from"]?>" onclick="window.open('<?=$url?>&asc=date_from', '_parent')" ><i class="fa-solid fa-arrow-up-wide-short"> </i></button><button type="button"<?=$order_by["desc"]["date_from"] ?> class="down<?=$order_by["desc"]["date_from"]?>" onclick="window.open('<?=$url; ?>&desc=date_from', '_parent')"><i class="fa-solid fa-arrow-down-short-wide"> </i></button></span></th>
            <th><span class="logs">Vég dátum</span> <span class="order_by"><button type="button"<?=$order_by["asc"]["date_to"]; ?> class="up<?=$order_by["asc"]["date_to"]?>" onclick="window.open('<?=$url?>&asc=date_to', '_parent')" ><i class="fa-solid fa-arrow-up-wide-short"> </i></button><button type="button"<?=$order_by["desc"]["date_to"] ?> class="down<?=$order_by["desc"]["date_to"]?>" onclick="window.open('<?=$url?>&desc=date_to', '_parent')"><i class="fa-solid fa-arrow-down-short-wide"> </i></button></span></th>
            <th><span class="logs">Státusz</span> <span class="order_by"><button type="button"<?=$order_by["asc"]["status"]; ?> class="up<?=$order_by["asc"]["status"]?>" onclick="window.open('<?=$url?>&asc=status', '_parent')" ><i class="fa-solid fa-arrow-up-wide-short"> </i></button><button type="button"<?=$order_by["desc"]["status"] ?> class="down<?=$order_by["desc"]["status"]?>" onclick="window.open('<?=$url?>&desc=status', '_parent')"><i class="fa-solid fa-arrow-down-short-wide"> </i></button></span><span class="refresh_status"><button id="refresh_status"><i class="refresh_status">refresh status</i></button></span></th>
            <th><span class="logs">Státusz Mód.</span> <span class="order_by"><button type="button"<?=$order_by["asc"]["status_mod"]; ?> class="up<?=$order_by["asc"]["status_mod"]?>" onclick="window.open('<?=$url?>&asc=status_mod', '_parent')" ><i class="fa-solid fa-arrow-up-wide-short"> </i></button><button type="button"<?=$order_by["desc"]["status_mod"] ?> class="down<?=$order_by["desc"]["status_mod"]?>" onclick="window.open('<?=$url?>&desc=status_mod', '_parent')"><i class="fa-solid fa-arrow-down-short-wide"> </i></button></span></th>
            <th><span class="logs">Beküldés dátuma</span> <span class="order_by"><button type="button"<?=$order_by["asc"]["send_date"]; ?> class="up<?=$order_by["asc"]["send_date"]?>" onclick="window.open('<?=$url?>&asc=send_date', '_parent')" ><i class="fa-solid fa-arrow-up-wide-short"> </i></button><button type="button"<?=$order_by["desc"]["send_date"] ?> class="down<?=$order_by["desc"]["send_date"]?>" onclick="window.open('<?=$url?>&desc=send_date', '_parent')"><i class="fa-solid fa-arrow-down-short-wide"> </i></button></span></th>
            <th><span class="logs">User ID</span> <span class="order_by"><button type="button"<?=$order_by["asc"]["user_id"]; ?> class="up<?=$order_by["asc"]["user_id"]?>" onclick="window.open('<?=$url?>&asc=user_id', '_parent')" ><i class="fa-solid fa-arrow-up-wide-short"> </i></button><button type="button"<?=$order_by["desc"]["user_id"] ?> class="down<?=$order_by["desc"]["user_id"]?>" onclick="window.open('<?=$url?>&desc=user_id', '_parent')"><i class="fa-solid fa-arrow-down-short-wide"> </i></button></span></th>
            <th><span class="logs">Telefonszám</span></th>
            <th><span class="logs">Email</span> <span class="order_by"><button type="button"<?=$order_by["asc"]["email"]; ?> class="up<?=$order_by["asc"]["email"]?>" onclick="window.open('<?=$url?>&asc=email', '_parent')" ><i class="fa-solid fa-arrow-up-wide-short"> </i></button><button type="button"<?=$order_by["desc"]["email"] ?> class="down<?=$order_by["desc"]["email"]?>" onclick="window.open('<?=$url?>&desc=email', '_parent')"><i class="fa-solid fa-arrow-down-short-wide"> </i></button></span></th>
            <th><span class="logs">Ár</span> <span class="order_by"><button type="button"<?=$order_by["asc"]["user_price"]; ?> class="up<?=$order_by["asc"]["user_price"]?>" onclick="window.open('<?=$url?>&asc=user_price', '_parent')" ><i class="fa-solid fa-arrow-up-wide-short"> </i></button><button type="button"<?=$order_by["desc"]["user_price"] ?> class="down<?=$order_by["desc"]["user_price"]?>" onclick="window.open('<?=$url?>&desc=user_price', '_parent')"><i class="fa-solid fa-arrow-down-short-wide"> </i></button></span></th>
            <th><span class="logs">Számlázási adatok</span> <span class="order_by"><button type="button"<?=$order_by["asc"]["datas"]; ?> class="up<?=$order_by["asc"]["datas"]?>" onclick="window.open('<?=$url?>&asc=datas', '_parent')" ><i class="fa-solid fa-arrow-up-wide-short"> </i></button><button type="button"<?=$order_by["desc"]["datas"] ?> class="down<?=$order_by["desc"]["datas"]?>" onclick="window.open('<?=$url?>&desc=datas', '_parent')"><i class="fa-solid fa-arrow-down-short-wide"> </i></button></span></th>
            <th><span class="logs">Hajó</span> <span class="order_by"><button type="button"<?=$order_by["asc"]["name"]; ?> class="up<?=$order_by["asc"]["name"]?>" onclick="window.open('<?=$url?>&asc=name', '_parent')" ><i class="fa-solid fa-arrow-up-wide-short"> </i></button><button type="button"<?=$order_by["desc"]["name"] ?> class="down<?=$order_by["desc"]["name"]?>" onclick="window.open('<?=$url?>&desc=name', '_parent')"><i class="fa-solid fa-arrow-down-short-wide"> </i></button></span></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($rows as $row): ?>
        <tr>
            <td class="<?=strtolower($row->class_name)?>"><?=$row->class_name?></td>
            <td><?=$row->xml_json_id?></td>
            <td><?=$row->bo_id?></td>
            <td><?=substr($row->period_from,0,10)?></td>
            <td><?=substr($row->period_to,0,10)?></td>
            <td class="status <?=$row->reservation_status?>"><span><?=($row->reservation_status=='OPTION_SEND_ERROR')?'O.S_ERROR':$row->reservation_status?></span></td>
            <td><?=isset($row->modify_date)?$row->modify_date:'---'?></td>
            <td><?=$row->create_date?></td>
            <td><?=$row->user_id?></td>
            <td><?=$row->phone_number?></td>
            <td><?php if ($row->email == $row->user_email){
                ?><span class="green"><?=$row->email?></span>
                <?php
            } else {
                ?><span class="red"><?=$row->email?></span>
                <span class="green"><?=$row->user_email?></span>
                <?php
            }
            ?>
            </td>
            <td class="prive"><span class="list-discounted-price"><?=number_format(myRound($row->user_price), 0, '.', ' ')?> <span class="cur"><?=$row->currency; ?></span></span></td>
            <td><?=$row->first_name?> <?=$row->last_name?><br>
                <?=$row->country_name?>, <?=$row->zip_code?> <?=$row->city?> <br>
                <?=$row->address?>

            </td>
            <td><?php if (isset($row->wp_id)): ?>
                <a href="/<?=$row->post_name?>" target="_blank" ><?=$row->post_title?></a>
                <?php else : ?>
                NINCS ÉRTELMEZVE (ID: <?=$row->yacht_id?>)
                <?php endif; ?>
                <?php
                    if ($row->user_message && $row->user_message!==''):
                ?>
                    <button type="button" attr-bo-id="<?=$row->bo_id?>" class="show_message_to_option <?=$row->bo_id?>">Show message</button>
                    <button type="button" attr-bo-id="<?=$row->bo_id?>" class="hide_message_to_option <?=$row->bo_id?> hidden">Hide message</button>
                <?php endif; ?>
            </td>
        </tr>
    <?php
        if ($row->user_message && $row->user_message!==''):
    ?>
        <tr class="user_message <?=$row->bo_id?> hidden"><th colspan="14"><span class="message_text"><?=$row->user_message?></span></th></tr>
    <?php endif; ?>
    <?php endforeach; ?>
    </tbody>
</table>
<script>
    jQuery(".show_message_to_option").on('click', function(){
        var attr_id = jQuery(this).attr('attr-bo-id');
        jQuery(".show_message_to_option").removeClass("hidden");        
        jQuery(this).addClass("hidden");
        jQuery(".hide_message_to_option").addClass("hidden");
        jQuery(".hide_message_to_option."+attr_id).removeClass("hidden");

        jQuery(".user_message").addClass("hidden");
        jQuery(".user_message."+attr_id).removeClass("hidden");
        
    });
    jQuery(".hide_message_to_option").on('click', function(){
        var attr_id = jQuery(this).attr('attr-bo-id');
        jQuery(this).addClass("hidden");
        jQuery(".show_message_to_option."+attr_id).removeClass("hidden");

        jQuery(".user_message."+attr_id).addClass("hidden");
        
    });
</script>
<div class="pager">
<?php
if ($pageIndex > 1) {
?>
    <span class="scrollPage"><a href="<?=$url?>&<?=strtolower($by)?>=<?=$orderBy?>&per_page=<?=$perPage?>&load_page=<?=1?>"><<</a></span>
    <span class="scrollPage"><a href="<?=$url?>&<?=strtolower($by)?>=<?=$orderBy?>&per_page=<?=$perPage?>&load_page=<?=($pageIndex-1)?>"><</a></span>
<?php
}
for ($index = 1; $index < ($pageNum+1); $index++):
?>
    <span class="scrollPage"><a href="<?=$url?>&<?=strtolower($by)?>=<?=$orderBy?>&per_page=<?=$perPage?>&load_page=<?=$index?>"><?=$index?></a></span>
<?php
endfor;
if ($pageIndex < $pageNum) {
?>
    <span class="scrollPage"><a href="<?=$url?>&<?=strtolower($by)?>=<?=$orderBy?>&per_page=<?=$perPage?>&load_page=<?=($pageIndex+1)?>">></a></span>
    <span class="scrollPage"><a href="<?=$url?>&<?=strtolower($by)?>=<?=$orderBy?>&per_page=<?=$perPage?>&load_page=<?=($pageNum)?>">>></a></span>
<?php
}
?>
</div>

<script>
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
    jQuery("#refresh_status").on('click', function(){
        
        jQuery.ajax({
                type: "POST",
                url:  "<?php echo get_option('yii_url', '/'); ?>booking/refresh",
                beforeSend: function(){
                    jQuery(".loader").addClass("waitMeTo_Container");
                    jQuery(".waitContainer").css({"height":"50px"});
                    run_waitMe(jQuery(".waitContainer"), 1, "progressBar");
                },
                success:function(data){ 
                    jQuery(".loader").removeClass("waitMeTo_Container");
                    jQuery(".waitContainer").css({"height":"0px"});
                    window.open("<?=$url?>", "_parent");
                }
        });
    
    });
</script>

<div class="wp-tab-panel shortcode" id="tabs-2" style="display: none;">
  <h2>Shortcode minták</h2>
</div>

</div>
