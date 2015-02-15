<?php

/*
Plugin Name: Data-Triplify JSON
Description: Triplify your wordpress posts
Author: Douglas Paranhos & Eduardo Andrade
Version: 0.1
Author URI: http://dontpad.com/lalala
*/

include_once dirname( __FILE__ ) .'/render.php';
include_once dirname( __FILE__ ) .'/TYPE_TEXT.php';

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
		new TYPE_TEXT( $type, $structure );
		exit();
	}

}

function triplify(){
	global $wpdb;
	//creating datatable
	$table_name = "wp_triplify_configurations";
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE $table_name (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					tipo VARCHAR(55) NOT NULL,
					coluna VARCHAR(100) NOT NULL,
					valor_correspondente VARCHAR(100) NOT NULL,
					uri BOOLEAN NOT NULL,
					UNIQUE KEY id (id)
				);";
		
		$wpdb->query($sql);
	}
	echo "alalal";
	$abc = new Render();
}

add_action( 'wp_ajax_triplify_action', 'triplify_action_callback' );
function triplify_action_callback() {
	
	global $wpdb;
	
	//saving correspondences
	foreach(array_values($_POST['arrayCorrespondencias']) as $opcoes){
		$post = $_POST["post_type"];
		$coluna = $opcoes["coluna"];
		$valor_correspondente = $opcoes["valor"];
		
		if($opcoes[uri] == 'true'){
			$uri = true;
		} else {
			$uri = false;
		}
		
		$tabela = 'wp_triplify_configurations';
		$valor_anterior_banco = $wpdb->get_results("SELECT count(*) FROM $wpdb->triplify_configurations WHERE tipo=".$post." and coluna=".$coluna."");
		if($valor_anterior_banco > 0){
			$wpdb->update($tabela, array('tipo' => $_POST["post_type"], 'coluna' => $coluna, 'uri' => $uri, 'valor_correspondente' => $opcoes["valor"]), array('tipo' => $_POST["post_type"], 'coluna' => $coluna));
		} else {
			$wpdb->insert($tabela, array('tipo' => $_POST["post_type"], 'coluna' => $coluna, 'uri' => $uri, 'valor_correspondente' => $opcoes["valor"]));
		}
		
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
