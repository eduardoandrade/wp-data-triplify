<?php
	
class JSON_TEXT {
	
	function __construct($type, $structure){
		add_action( 'show_data_hook', array( $this, 'show_data' ), 10, 2);
		do_action('show_data_hook', $type, $structure);
	}
	
	function show_data($type, $structure){

		$posts_array = array( 'post_type' => $type );
		$posts = get_posts($posts_array);
		
		if(empty($posts)){
			echo "No posts were found with given post_type." ;
			exit();
		}
		
		//$array_com_options_que_devemos_procurar = get_option($type . '#triplificador#' . '(.+?)');//não deve poder expressão regular aqui
		
		//ver um jeito esperto de fazer isso, talvez switch (tem switch em php?), um for em um array?
		if(strcmp(strtoupper($structure), 'JSON') == 0){
			@include_once dirname( __FILE__ ) .'/jsonld/jsonld.php';
			$JSON = json_encode($posts);
			print_r($JSON);
		} else if(strcmp(strtoupper($structure), 'XML') == 0){
			//http://php.net/manual/en/function.xml-parse-into-struct.php
			//http://arc.semsol.org/
			//ARC2 que está no .zip do plugin
		} else if(strcmp(strtoupper($structure), 'RDF') == 0){
			//http://arc.semsol.org/
			//ARC2 que está no .zip do plugin
		} else if(strcmp(strtoupper($structure), 'TURTLE') == 0){
			//http://arc.semsol.org/
			//ARC2 que está no .zip do plugin
		} 
		
		//exit();
		return;
	}
}
?>