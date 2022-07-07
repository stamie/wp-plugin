<?php
function send_option_email(){
    global $wpdb;
    apply_filters( 'wp_mail_content_type', 'text/html' );

    $get_mail_template = $wpdb->get_row("SELECT post_title, post_content FROM {$wpdb->prefix}posts where post_name like 'thank-you-option' and post_type like 'boat_mail'", OBJECT);

    if ($get_mail_template && isset($_GET['ref'])){
        $ref   = intval($_GET['ref']);
        $datas = $wpdb->get_row("SELECT * from boat_option where id=$ref", OBJECT);
        $body  = make_body($get_mail_template->post_content, $datas);
        
        $datas->list_price = number_format(myRound($datas->list_price), 0, '.', ' ');
        $datas->user_price = number_format(myRound($datas->user_price), 0, '.', ' ');
        $headers = array('Content-Type: text/html; charset=UTF-8');
        if ($datas->email)
            $is_send = wp_mail($datas->email, $get_mail_template->post_title, $body, $headers);
        
        $wpdb->update('boat_option', array('send_email' => $is_send), array('id' => $datas->id));
        if ($is_send)
            return __('OK', 'boat-shortcodes');
        return __('email send error', 'boat-shortcodes');
    }
    return __('otpion error', 'boat-shortcodes');
}
add_shortcode('send-option-email', 'send_option_email');

function make_body($template, $datas) {

    $temp = $template;

    $searchArray = array('[first-name]', 
                         '[last-name]',
                         '[listaar]',
                         '[vegsoar]',
                         '[kezdo-datum]',
                         '[veg-datum]',
                         '[statusz]',
                         '[bekuldese]',
                         '[rendeles-szama]',
                         '[url]',
                         '[/url]',
                         '[hajonev]'
    );
    $url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'];
    $url = '<a href="'.$url.'/option-popup?option='.$datas->id.'">';
    $urlEnd = '</a>';
    $boatname = do_shortcode('[boat-title id="'.$datas->yacht_id.'"]');
    $replaceArray = array($datas->first_name, 
                        $datas->last_name,
                        number_format(myRound($datas->list_price), 0, '.', ' ').' '.$datas->currency,
                        number_format(myRound($datas->user_price), 0, '.', ' ').' '.$datas->currency,
                        $datas->period_from,
                        $datas->period_to,
                        $datas->reservation_status,
                        $datas->create_date,
                        $datas->id,
                        $url,
                        $urlEnd,
                        $boatname
                    );

    $temp = str_replace($searchArray, $replaceArray, $temp);

    $html = '<html><head></head><body>'.$temp.'</body></html>';
    return $html;
}

?>