<?php

/**
 * @package HelloDear
 * @version 0.1
 */
/*
Plugin Name: Data-Triplify JSON
Description: Triplify your posts
Author: Douglas Paranhos & Eduardo Andrade
Version: 0.1
Author URI: http://dontpad.com/lalala
*/

@include_once dirname( __FILE__ ) .'/render.php';
@include_once dirname( __FILE__ ) .'/jsontext.php';

add_action('admin_init', 'flush_rewrite_rules');
add_action('admin_menu', 'triplificator_admin_actions');
add_action('generate_rewrite_rules', 'triplificator_add_rewrite_rules');
add_action('template_redirect', 'my_page_template_redirect' );
add_action('init', 'custom_rewrite_tag', 10, 0);

function triplificator_admin_actions(){
	add_options_page('Data-Triplify', 'Data-Triplify', 'manage_options', __FILE__, 'triplify');
}

function custom_rewrite_tag() {
  add_rewrite_tag('%type%', '([^&]+)');
  add_rewrite_tag('%structure%', '([^&]+)');
}

function triplificator_add_rewrite_rules(){

	global $wp_rewrite;

	$keytag = '%type%';
	$keytag2 = '%structure%';
	
	$wp_rewrite->add_rewrite_tag($keytag, '(.+?)', 'type=');
	$wp_rewrite->add_rewrite_tag($keytag2, '(.+?)', 'structure=');
	
	$keywords_structure = $wp_rewrite->root . "tri/$keytag/$keytag2";
	$keywords_rewrite = $wp_rewrite->generate_rewrite_rules($keywords_structure);
	
	$wp_rewrite->rules = $keywords_rewrite + $wp_rewrite->rules;
	return $wp_rewrite->rules;
}

function my_page_template_redirect(){
	global $wp_query;
	
	$type = get_query_var( 'type' ) ? get_query_var( 'type' ) : false;
	$structure = get_query_var( 'structure' ) ? get_query_var( 'structure' ) : 'JSON';

	if($type != false){
		if($type === 'info'){
			echo "RESTful service working";
			exit();
		}
		
		//chamar método que salva opções no banco
		new JSON_TEXT( $type, $structure );
		exit();
	}

}

function triplify(){

	$abc = new Render();
}

add_action( 'wp_ajax_triplify_action', 'triplify_action_callback' );
function triplify_action_callback() {
	
	//saving correspondences
	foreach(array_values($_POST['arrayCorrespondencias']) as $opcoes){
		$option_name = $_POST["post_type"]."#triplificator#".$opcoes["coluna"];
		$option_value = $opcoes["valor"];
		
		if(get_option( $option_name, null ) == null) add_option($option_name, $option_value);
		else update_option($option_name, $option_value);
	}
	
	//saving base url
	$option_name = "#triplificator_uri_base#".$_POST["post_type"];
	$uri_base_value = $_POST['uri_base'];
	
	if(get_option( $option_name, null ) == null) add_option($option_name, $uri_base_value);
	else update_option($option_name, $uri_base_value);
	
	wp_die();
}

//tentar colocar os scripts em um arquivo .js e "importar" ele aqui
//add_action('admin_init', 'admin_load_scripts');
//function admin_load_scripts() 
//{
	//$js_file = plugins_url( 'scripts.js', __FILE__ ); 
	//wp_enqueue_script('admin-scripts', $js_file, array('jquery')); 
//}

/* EOF */
