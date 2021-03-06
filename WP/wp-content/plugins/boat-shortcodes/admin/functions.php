<?php

const RUNNER_ERROR = -2;
const AUTH_ERROR = -1;
const DONT_ERROR = 0;
const ERROR_BASE = 1;
const ERROR_COMPANY = 2;
const ERROR_COUNTRY = 3;
const ERROR_DISCOUNTITEM = 4;
const ERROR_ENGINEBUILDER = 5;
const ERROR_EQUIPMENT = 6;
const ERROR_EQUIPMENTCATEGORY = 7;
const ERROR_PORT = 8;
const ERROR_REGION = 9;
const ERROR_SAILTYPE = 10;
const ERROR_SEASON = 11;
const ERROR_SERVICE = 12;
const ERROR_STEERINGTYPE = 13;
const ERROR_YACHTBUILDER = 14;
const ERROR_YACHTCATEGORY = 15;
const ERROR_YACHTMODEL = 16;
const ERROR_YACHT = 17;
const ERROR_WPSYNC = 18;

function writeErrorsForSyncron($arrayErrors, $id, $string = ''){

    $return = '';
    $name = '';
    if ($string !='') {
        switch ($string){
        case 'Country Syncron':
            $name = 'country';
            break;

        case 'Equipment Category Syncron':
            $name = 'equipmentcategory';
        break;
        case 'Equipment Syncron':
            $name = 'equipment';
            break;
        case 'Yacht Builder Syncron':
            $name = 'yachtbuilder';
            break;
        
        case 'Engine Builder Syncron':
            $name = 'enginebuilder';
            break;
        
        case 'Yacht Category Syncron':
            $name = 'yachtcategory';
            break;
        
        case 'Yacht Model Syncron':
            $name = 'yachtmodel';
            break;
        
        case 'Yacht Syncron':
            $name = 'yacht';
            break;
        
        case 'Discount Item Syncron':
            $name = 'discountitem';
            break;
        
        case 'Session Syncron':
            $name = 'session';
            break;
        
        case 'Region Syncron':
            $name = 'region';
            break;
        
        case 'Base Syncron':
            $name = 'base';
            break;
        
        case 'Port Syncron':
            $name = 'port';
            break;
        
        case 'Steering Type Syncron':
            $name = 'steeringtype';
            break;
        
        case 'Sail Type Syncron':
            $name = 'sailtype';
            break;
        
        case 'Service Syncron':
            $name = 'services';
            break;
        
        case 'Company Syncron':
            $name = 'company';
            break;
        
        case 'WordPress Syncron':
            $name = 'wpsync';
            break;
        
        case 'Little Syncron':
            $name = 'littlesync';
            break;
        
        case 'Big Syncron':
            $name = 'littlesync';
            break;
        default:
            $name = '';
            break;
    }
    }
    if (is_object($arrayErrors) && isset($arrayErrors->error) ){


        if ( is_array($arrayErrors->error) && count($arrayErrors->error) > 0 ){
            
            $return .= '<h1 class="errors">';
            foreach($arrayErrors->error as $error) {

                switch ($error){
                    case RUNNER_ERROR :
                        $return .= __('Fut??si hiba', 'boat-shortcodes');
                        break;
                    case AUTH_ERROR:
                        $return .= __('Authentik??ci??s hiba', 'boat-shortcodes');
                        break;
                    case DONT_ERROR:
                        $return .= __('Ismeretlen hiba', 'boat-shortcodes');
                        break;
                    case ERROR_BASE:
                        $return .= __('B??zis szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_COMPANY:
                        $return .= __('C??gek szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_COUNTRY:
                        $return .= __('Orsz??gok szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_DISCOUNTITEM:
                        $return .= __('Kedvezm??nyek szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_ENGINEBUILDER:
                        $return .= __('Motor??p??t??k szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_EQUIPMENT:
                        $return .= __('felszerelts??g szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_EQUIPMENTCATEGORY:
                        $return .= __('Felstzerelts??g kateg??ria szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_PORT:
                        $return .= __('Kik??t??k szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_REGION:
                        $return .= __('R??gi??k szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_SAILTYPE:
                        $return .= __('Vitorl??s t??pusok szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_SEASON:
                        $return .= __('Szezonok szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_SERVICE:
                        $return .= __('Szolg??ltat??s szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_STEERINGTYPE:
                        $return .= __('Korm??ny t??pusok szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_YACHTBUILDER:
                        $return .= __('Yacht ??p??t??k szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_YACHTCATEGORY:
                        $return .= __('Yacht kateg??ri??k szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_YACHTMODEL:
                        $return .= __('Yacht modellek szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_YACHT:
                        $return .= __('Yachtok szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_WPSYNC:
                        $return .= __('WordPress szinkron hib??ja', 'boat-shortcodes');
                        break;
                }
                $return .= '<br>';

            }
            $return .= '</h1><button type="button" class="sync repeat-sync" attr-sync="'.$name.'" attr-id="'.$id.'">'.__('??jra szinkroniz??l', 'boat-shortcodes').'</button>';
        } else if(is_array($arrayErrors->error) && count($arrayErrors->error)==0){
            $return .= '<h1 class="noErrors">'.__('Nincs hiba', 'boat-shortcodes').'</h1>';

        } else if(!is_array($arrayErrors->error)) {
            $return .= '<h1 class="errors">';
            switch($arrayErrors->error) {
                case RUNNER_ERROR :
                    $return .= __('Fut??si hiba', 'boat-shortcodes');
                    
                    break;
                case AUTH_ERROR:
                    $return .= __('Authentik??ci??s hiba', 'boat-shortcodes');
                    break;
                case DONT_ERROR:
                    $return .= __('Ismeretlen hiba', 'boat-shortcodes');
                    break;
                case ERROR_BASE:
                    $return .= __('B??zis szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_COMPANY:
                    $return .= __('C??gek szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_COUNTRY:
                    $return .= __('Orsz??gok szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_DISCOUNTITEM:
                    $return .= __('Kedvezm??nyek szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_ENGINEBUILDER:
                    $return .= __('Motor??p??t??k szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_EQUIPMENT:
                    $return .= __('felszerelts??g szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_EQUIPMENTCATEGORY:
                    $return .= __('Felstzerelts??g kateg??ria szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_PORT:
                    $return .= __('Kik??t??k szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_REGION:
                    $return .= __('R??gi??k szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_SAILTYPE:
                    $return .= __('Vitorl??s t??pusok szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_SEASON:
                    $return .= __('Szezonok szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_SERVICE:
                    $return .= __('Szolg??ltat??s szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_STEERINGTYPE:
                    $return .= __('Korm??ny t??pusok szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_YACHTBUILDER:
                    $return .= __('Yacht ??p??t??k szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_YACHTCATEGORY:
                    $return .= __('Yacht kateg??ri??k szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_YACHTMODEL:
                    $return .= __('Yacht modellek szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_YACHT:
                    $return .= __('Yachtok szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_WPSYNC:
                    $return .= __('WordPress szinkron hib??ja', 'boat-shortcodes');
                    break;
            }
            $return .= '</h1><button type="button" class="sync repeat-sync" attr-sync="'.$name.'" attr-id="'.$id.'">'.__('??jra szinkroniz??l', 'boat-shortcodes').'</button>';
        }

    } else if (is_array($arrayErrors) && isset($arrayErrors['error'])){
        if ( is_array($arrayErrors['error']) && count($arrayErrors['error']) > 0 ){
            
            $return .= '<h1 class="errors">';
            foreach($arrayErrors['error'] as $error) {

                switch ($error){
                    case RUNNER_ERROR :
                        $return .= __('Fut??si hiba', 'boat-shortcodes');
                        break;
                    case AUTH_ERROR:
                        $return .= __('Authentik??ci??s hiba', 'boat-shortcodes');
                        break;
                    case DONT_ERROR:
                        $return .= __('Ismeretlen hiba', 'boat-shortcodes');
                        break;
                    case ERROR_BASE:
                        $return .= __('B??zis szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_COMPANY:
                        $return .= __('C??gek szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_COUNTRY:
                        $return .= __('Orsz??gok szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_DISCOUNTITEM:
                        $return .= __('Kedvezm??nyek szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_ENGINEBUILDER:
                        $return .= __('Motor??p??t??k szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_EQUIPMENT:
                        $return .= __('felszerelts??g szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_EQUIPMENTCATEGORY:
                        $return .= __('Felstzerelts??g kateg??ria szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_PORT:
                        $return .= __('Kik??t??k szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_REGION:
                        $return .= __('R??gi??k szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_SAILTYPE:
                        $return .= __('Vitorl??s t??pusok szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_SEASON:
                        $return .= __('Szezonok szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_SERVICE:
                        $return .= __('Szolg??ltat??s szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_STEERINGTYPE:
                        $return .= __('Korm??ny t??pusok szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_YACHTBUILDER:
                        $return .= __('Yacht ??p??t??k szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_YACHTCATEGORY:
                        $return .= __('Yacht kateg??ri??k szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_YACHTMODEL:
                        $return .= __('Yacht modellek szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_YACHT:
                        $return .= __('Yachtok szinkron hib??ja', 'boat-shortcodes');
                        break;
                    case ERROR_WPSYNC:
                        $return .= __('WordPress szinkron hib??ja', 'boat-shortcodes');
                        break;
                }
                $return .= '<br>';

            }
            $return .= '</h1><button type="button" class="sync repeat-sync" attr-sync="'.$name.'" attr-id="'.$id.'">'.__('??jra szinkroniz??l', 'boat-shortcodes').'</button>';
        } else if(is_array($arrayErrors['error']) && count($arrayErrors['error']) == 0){
            $return .= '<h1 class="noErrors">'.__('Nincs hiba', 'boat-shortcodes').'</h1>';

        } else {
            $return .= '<h1 class="errors">';
            switch($arrayErrors['error']) {
                case RUNNER_ERROR :
                    $return .= __('Fut??si hiba', 'boat-shortcodes');
                    break;
                case AUTH_ERROR:
                    $return .= __('Authentik??ci??s hiba', 'boat-shortcodes');
                    break;
                case DONT_ERROR:
                    $return .= __('Ismeretlen hiba', 'boat-shortcodes');
                    break;
                case ERROR_BASE:
                    $return .= __('B??zis szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_COMPANY:
                    $return .= __('C??gek szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_COUNTRY:
                    $return .= __('Orsz??gok szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_DISCOUNTITEM:
                    $return .= __('Kedvezm??nyek szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_ENGINEBUILDER:
                    $return .= __('Motor??p??t??k szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_EQUIPMENT:
                    $return .= __('felszerelts??g szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_EQUIPMENTCATEGORY:
                    $return .= __('Felstzerelts??g kateg??ria szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_PORT:
                    $return .= __('Kik??t??k szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_REGION:
                    $return .= __('R??gi??k szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_SAILTYPE:
                    $return .= __('Vitorl??s t??pusok szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_SEASON:
                    $return .= __('Szezonok szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_SERVICE:
                    $return .= __('Szolg??ltat??s szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_STEERINGTYPE:
                    $return .= __('Korm??ny t??pusok szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_YACHTBUILDER:
                    $return .= __('Yacht ??p??t??k szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_YACHTCATEGORY:
                    $return .= __('Yacht kateg??ri??k szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_YACHTMODEL:
                    $return .= __('Yacht modellek szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_YACHT:
                    $return .= __('Yachtok szinkron hib??ja', 'boat-shortcodes');
                    break;
                case ERROR_WPSYNC:
                    $return .= __('WordPress szinkron hib??ja', 'boat-shortcodes');
                    break;
            }
            $return .= '</h1><button type="button" class="sync repeat-sync" attr-sync="'.$name.'" attr-id="'.$id.'">'.__('??jra szinkroniz??l', 'boat-shortcodes').'</button>';
        }

    } else {
        $return .= '<h1 class="noErrors">'.__('Nincs hiba', 'boat-shortcodes').'</h1>';
    }

return $return;
}