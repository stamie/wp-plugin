<?php

function reg_afo_custom_avatar( $avatar, $id_or_email, $size, $default, $alt='') {
    $user = false;
	$profileimage_as_avatar = get_option( 'profileimage_as_avatar' );
		
    if ( is_numeric( $id_or_email ) ) {
			
        $id = (int) $id_or_email;
        $user = get_user_by( 'id' , $id );
			
        } elseif ( is_object( $id_or_email ) ) {
			
            if ( ! empty( $id_or_email->user_id ) ) {
                $id = (int) $id_or_email->user_id;
                $user = get_user_by( 'id' , $id );
            }
			
    } else {
        $user = get_user_by( 'email', $id_or_email );	
    }
		
    if ( $user && is_object( $user ) ) {
			
		if($profileimage_as_avatar == 'Yes'){
			if ( $user->data->ID ) {
					$avatar_url = get_the_author_meta( 'reg_profile_image_url', $user->data->ID );
					$avatar = "<img alt='{$alt}' src='{$avatar_url}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
			}
		}
			
    }

    return $avatar;
}