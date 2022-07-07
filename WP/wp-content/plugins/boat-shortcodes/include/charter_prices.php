<?php
    $return = ''; // '<tr><th class="property-table-column  property-table-count2" colspan="3">'.__('Charter Prices', 'boat-shortcodes2').'</th></tr>';

    $date = date('Y-m-d H:i:s');
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
    $resultOptions    = $wpdb->get_results($query.$whereOptions, OBJECT);
    //var_dump($query.$whereIncluded);
    $queryBeeding = "SELECT * FROM yacht as y INNER JOIN standard_equipment as se
        ON y.xml_id = se.xml_id 
            
            AND y.xml_json_id = se.yacht_id

    WHERE (y.id = $id AND y.xml_id in (SELECT x.id FROM xml as x where x.slug like 'nausys') AND se.equipment_id in (11, 604494))";

    $result = $wpdb->get_results($queryBeeding, OBJECT);
    if ($result) {
        $return .= '<tr><td>'.__('Beeding', 'boat-shortcodes2').'</td><td class="green">'.__('Included in the Price', 'boat-shortcodes2').'</td><td>'.__('---', 'boat-shortcodes2').'</td><td>'.__('Every locations', 'boat-shortcodes2').'</td></tr>';
    }

    $queryWelcomeKit = "SELECT * FROM yacht as y INNER JOIN standard_equipment as se
    ON y.xml_id = se.xml_id 
        
        AND y.xml_json_id = se.yacht_id

    WHERE (y.id = $id AND y.xml_id in (SELECT x.id FROM xml as x where x.slug like 'nausys') AND se.equipment_id in (446458, 17766))";

    $result = $wpdb->get_results($queryWelcomeKit, OBJECT);
    if ($result) {
        $return .= '<tr><td>'.__('Welcome KIT', 'boat-shortcodes2').'</td><td class="green">'.__('Included in the Price', 'boat-shortcodes2').'</td><td>'.__('---', 'boat-shortcodes2').'</td><td>'.__('Every locations', 'boat-shortcodes2').'</td></tr>';
    }

    $queryWIFI = "SELECT * FROM yacht as y INNER JOIN standard_equipment as se
    ON y.xml_id = se.xml_id 
        
        AND y.xml_json_id = se.yacht_id

    WHERE (y.id = $id AND y.xml_id in (SELECT x.id FROM xml as x where x.slug like 'nausys') AND se.equipment_id in (477829))";

    $result = $wpdb->get_results($queryWIFI, OBJECT);
    if ($result) {
        $return .= '<tr><td>'.__('WIFI', 'boat-shortcodes2').'</td><td class="green">'.__('Included in the Price', 'boat-shortcodes2').'</td><td>'.__('---', 'boat-shortcodes2').'</td><td>'.__('Every locations', 'boat-shortcodes2').'</td></tr>';
    }
    $index = 1;
    if ($resultIncluded) {
        foreach ($resultIncluded as $value) {

            $return .= '<tr><td>'.__($value->service_name, 'boat-shortcodes2').'</td><td class="green">'.__('Included in the Price', 'boat-shortcodes2').'</td><td>'.__('---', 'boat-shortcodes2').'</td>';
           
            if ($value->if_bases == 0) {
                $return .= '<td>'.__('Every locations', 'boat-shortcodes2').'</td></tr>';
            } else {
                $queryBases = "SELECT p.name pname from port p INNER JOIN base b ON b.location_id = p.xml_json_id 
                AND b.xml_id = p.xml_id
                
                INNER JOIN services_valid_for_bases svb ON b.xml_id = svb.xml_id AND b.xml_json_id = svb.base_id 
                                    
                
                WHERE
                    $value->service_id = svb.service_id
                    AND $value->yacht_id = svb.yacht_id 
                    AND $value->season_id = svb.season_id
                ";
    
                $ports = $wpdb->get_results($queryBases, OBJECT);
                $return .= '<td><button class="same_locations" attr_index="'.$index.'">'.__('Same locations', 'boat-shortcodes').'</button><ul attr="hidden" class="same_locations '.$index++.'">';
                
                foreach ($ports as $port){
                    $return .= '<li>'.$port->pname.'</li>';
                }

                $return .= '</ul></td></tr>';
            }
        }
    }

    if ($resultObligatory){
        foreach ($resultObligatory as $value) {

            $return .= '<tr><td>'.__($value->service_name, 'boat-shortcodes2').'</td><td class="red">'.__('Obligatory', 'boat-shortcodes2').'</td><td>'.number_format(floatval($value->price), 2, ',', ' ').' <span class="cur">'.$value->currency.'</span> '.__($value->price_measure_name, 'boat-shortcodes2').'</td>';
           
            if ($value->if_bases == 0) {
                $return .= '<td>'.__('Every locations', 'boat-shortcodes2').'</td></tr>';
            } else {
                $queryBases = "SELECT p.name pname from port p INNER JOIN base b ON b.location_id = p.xml_json_id 
                AND b.xml_id = p.xml_id
                INNER JOIN services_valid_for_bases svb ON b.xml_id = svb.xml_id AND b.xml_json_id = svb.base_id 
                                    
                
                WHERE
                    $value->service_id = svb.service_id
                    AND $value->yacht_id = svb.yacht_id 
                    AND $value->season_id = svb.season_id
                ";
    
                $ports = $wpdb->get_results($queryBases, OBJECT);
                $return .= '<td><button class="same_locations" attr_index="'.$index.'">'.__('Same locations', 'boat-shortcodes').'</button><ul attr="hidden" class="same_locations '.$index++.'">';
                
                foreach ($ports as $port){
                    $return .= '<li>'.$port->pname.'</li>';
                }

                $return .= '</ul></td></tr>';
            }
        }
   

    }

    if ($resultOptions){

        
        foreach ($resultOptions as $value) {

            $return .= '<tr><td>'.__($value->service_name, 'boat-shortcodes2').'</td><td class="grey">'.__('Optional', 'boat-shortcodes2').'</td><td>'.number_format(floatval($value->price), 2, ',', ' ').' <span class="cur">'.$value->currency.'</span> '.__($value->price_measure_name, 'boat-shortcodes2').'</td>';
            
            if ($value->if_bases == 0) {
                $return .= '<td>'.__('Every locations', 'boat-shortcodes2').'</td></tr>';
            } else {
                $queryBases = "SELECT p.name pname from port p INNER JOIN base b ON b.location_id = p.xml_json_id 
                AND b.xml_id = p.xml_id
                INNER JOIN services_valid_for_bases svb ON b.xml_id = svb.xml_id AND b.xml_json_id = svb.base_id 
                WHERE
                    $value->service_id = svb.service_id
                    AND $value->yacht_id = svb.yacht_id 
                    AND $value->season_id = svb.season_id
                ";
    
                $ports = $wpdb->get_results($queryBases, OBJECT);
                $return .= '<td><button class="same_locations" attr_index="'.$index.'">'.__('Same locations', 'boat-shortcodes').'</button><ul attr="hidden" class="same_locations '.$index++.'">';
                
                foreach ($ports as $port){
                    $return .= '<li>'.$port->pname.'</li>';
                }

                $return .= '</ul></td></tr>';
            }
        }
    


    }

    if ($return != '') {
        $return = '<tr><th class="property-table-column  property-table-count2" colspan="4">'.__('Options & Services', 'boat-shortcodes').'</th></tr>'.$return;
    }

    ?>
