<?php
    $return = ''; 

    $query = "SELECT serv.name service_name, se.service_id service_id, se.yacht_id yacht_id, se.season_id season_id, se.obligatory obligatory, se.price price, se.currency currency, se.have_valid_for_bases if_bases, 
                        pm.name price_measure_name
    FROM yacht as y 
    INNER JOIN yacht_season_service as se
        ON y.xml_id = se.xml_id 
            AND y.xml_json_id = se.yacht_id
    INNER JOIN service as serv
        ON serv.xml_id = se.xml_id 
            AND serv.xml_json_id = se.service_id
    LEFT JOIN price_measure as pm
        ON pm.xml_id = se.xml_id 
            AND pm.xml_json_id = se.price_measure_id
    INNER JOIN season s 
        ON s.xml_id = se.xml_id 
            AND s.xml_json_id = se.season_id
            AND '{$dateFrom}' between date_from and date_to";

    $whereIncluded   = " WHERE y.id = $id AND y.xml_id in (SELECT x.id FROM xml as x where x.slug like 'nausys') AND se.obligatory = 1 AND se.price like '0.00'";
    $whereObligatory = " WHERE y.id = $id AND y.xml_id in (SELECT x.id FROM xml as x where x.slug like 'nausys') AND se.obligatory = 1 AND se.price not like '0.00'";
    $whereOptions    = " WHERE y.id = $id AND y.xml_id in (SELECT x.id FROM xml as x where x.slug like 'nausys') AND se.obligatory = 0";

    $resultIncluded   = $wpdb->get_results($query.$whereIncluded, OBJECT);
    $resultObligatory = $wpdb->get_results($query.$whereObligatory, OBJECT);
    $index = 1;

    if ($resultObligatory){
        foreach ($resultObligatory as $value) {
            $return .= '<tr><td class="obligatory">'.__($value->service_name, 'boat-shortcodes').'</td><td class="obligatory">'.number_format(floatval($value->price), 2, ',', ' ').' <span class="cur">'.$value->currency.'</span> '.$value->price_measure_name.'</td></tr>';
        }
    }
?>
