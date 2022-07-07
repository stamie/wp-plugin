<?php

if ( ! isset( $repeater_posts ) ) $repeater_posts = 'posts';
if ( ! isset( $repeater_costum_post_types ) ) $repeater_costum_post_types = array();
if ( ! isset( $repeater_post_type ) ) $repeater_post_type = 'post';
if ( ! isset( $repeater_post_cat ) ) $repeater_post_cat = 'category';

$array = array(
    'type' => 'group',
    'heading' => __( 'Posts' ));

$options = array(

/*
   'cpt' => array(
   	'type' => 'select',
   	'heading' => 'Custom Post Types',
   	'param_name' => 'cpt',
   	'options' =>  $repeater_costum_post_types,
   	'config' => array(
      		'placeholder' => 'Select...',
    	)
     )*/
);
//$i=0;

$ids = array(
	'ids' => array(
        'type' => 'select',
        'heading' => 'Custom Posts',
        'param_name' => 'ids',
//        'conditions' => 'cpt === ""',
        'config' => array(
            'multiple' => true,
            'placeholder' => 'Select..',
            'postSelect' => array(
                'post_type' => array_keys($repeater_costum_post_types),

            ),
        )
    ));
/*

foreach( $repeater_costum_post_types as $key => $value){

	$ids = array_merge($ids ,  array(
	'ids'.$i=> array(
        'type' => 'select',
        'heading' => 'Custom Posts',
        'param_name' => 'ids',
        'conditions' => 'cpt == "'.$key.'"',
        'config' => array(
            'multiple' => true,
            'placeholder' => 'Select..',
            'postSelect' => array(
                'post_type' => strtolower($value),

            ),
        )
    )));

	$i++;
}
*/

$options = array_merge($options, $ids);
$options = array_merge($options, array(
    'cat' => array(
        'type' => 'select',
        'heading' => 'Category',
        'param_name' => 'cat',
        'conditions' => 'ids === ""',
        'default' => '',
        'config' => array(
            'multiple' => true,
            'placeholder' => 'Select...',
            'termSelect' => array(
                'post_type' => $repeater_post_cat,
                'taxonomies' => $repeater_post_cat
            ),
        )
    ),

    'repeater_posts' => array(
        'type' => 'textfield',
        'heading' => 'Total Posts',
        'conditions' => 'ids === ""',
        'default' => '8',
    ),

    'offset' => array(
        'type' => 'textfield',
        'heading' => 'Offset',
        'conditions' => 'ids === ""',
        'default' => '',
    ),

  )
);
$array['options']=$options;

return $array;

