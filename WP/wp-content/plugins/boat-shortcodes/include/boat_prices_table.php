<?php
$query = "SELECT p.date_from df, p.date_to dt, p.price price, p.currency cur, p.type wtype FROM yacht as y 
INNER JOIN yacht_season as ys 
    ON ys.yacht_id = y.xml_json_id 
        AND ys.xml_id = y.xml_id 
        
INNER JOIN price as p 
    ON p.season_id = ys.season_id 
        AND p.yacht_id = y.xml_json_id 
        AND ys.xml_id = p.xml_id 
        
INNER JOIN season as s 
    ON s.xml_json_id = ys.season_id 
        AND ys.xml_id = s.xml_id 
        
WHERE y.id = $id 
    AND p.is_active = 1
/*    AND now() BETWEEN s.date_from AND s.date_to */
ORDER BY p.date_from ASC";
$objects = $wpdb->get_results($query, OBJECT);
$return = '<table class="property-table-right" style="width:100%;">';
$return .= '<tr><th class="property-table-column property-table-count1">'.__('Period/Price', 'boat-shortcodes').'</th><th colspan="2">'.__('1 Week', 'boat-shortcodes').'</th><th>'.__('Type', 'boat-shortcodes').'</th></tr>';
$cur = '';
foreach ($objects as $object){
    $return .= '<tr><td>'.date('Y-m-d', strtotime($object->df)).__(' to ', 'boat-shortcodes').date('Y-m-d', strtotime($object->dt)).'</td>
    <td colspan="2">'.$object->price.' '.$object->cur.'</td><td>'.__($object->wtype, 'boat-shortcodes2').'</td></tr>';
    $cur = $object->cur;

}

//$return .= '</table>';

$query = "SELECT rd.discount_item_id discount_item ,s.date_from date_from, s.date_to date_to, rd.amount rdamount, rd.type rdtype, d.name dname FROM yacht as y 
INNER JOIN regular_discount as rd
    ON y.xml_json_id=rd.yacht_id
       
        AND y.xml_id=rd.xml_id
INNER JOIN season as s
    ON s.xml_json_id=rd.season_id
        
        AND s.xml_id=rd.xml_id
        
INNER JOIN discount_item as d
    ON d.xml_json_id=rd.discount_item_id
        AND d.xml_id=rd.xml_id
WHERE y.id = $id and rd.is_active = 1 /* AND now() between s.date_from AND s.date_to */";
$objects = $wpdb->get_results($query, OBJECT);
//$return .= '<table>';
$return .= '<tr><th >'.__('Period/Discounts', 'boat-shortcodes').'</th><th>'.__('Discount name', 'boat-shortcodes').'</th><th>'.__('Type', 'boat-shortcodes').'</th><th>'.__('Amount', 'boat-shortcodes').'</th></tr>';
foreach ($objects as $object){
    $from = date ('Y-m-d', strtotime($object->date_from));
    $to   = date ('Y-m-d', strtotime($object->date_to));
    $amount = ' '.$cur;
    if ($object->rdtype == "PERCENTAGE")
        $amount = ' %';
    $return .= '<tr class="discount_'.$object->discount_item.'"><td>'.$from.__(' to ','boat-shortcodes').$to.'</td>
    
    <td>'.$object->dname.'</td>
    <td>'.__($object->rdtype, 'boat-shortcodes2').'</td><td>'.$object->rdamount.$amount.'</td></tr>';

}
global $boatDatas;
if (isset($boatDatas['discounts']) && is_array($boatDatas['discounts']) && count($boatDatas['discounts'])>0){
    $xml_id = $boatDatas['xml_id'];
    
    if (is_array($boatDatas['discounts']) && count($boatDatas['discounts'])>0){
        foreach ($boatDatas['discounts'] as $discount){ 
            $amount = ' '.$boatDatas['currency'];
            if ($discount->type == "PERCENTAGE")
                $amount = ' %';
            $discount_name = $wpdb->get_row("SELECT name from discount_item where xml_id={$xml_id} and xml_json_id={$discount->discountItemId}");
            if ($discount_name){
                $dateFrom = date('Y-m-d', strtotime($boatDatas['date_from']));
                $dateTo   = date('Y-m-d', strtotime($boatDatas['date_to']));
                $return  .= '<tr class="discount_'.$discount->discountItemId.'"><td> '.$dateFrom.__(' to ', 'boat-shortcodes').$dateTo.' </td>'.
                '<td>'.$discount_name->name.'</td>'.
                '<td>'.__($discount->type, 'boat-shortcodes2').'</td><td>'.$discount->amount.$amount.'</td></tr>';
                
              
            }
        }
    }
}
$return .= '</table>';
