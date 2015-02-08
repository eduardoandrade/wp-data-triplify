<?php

include_once( "arc2/ARC2.php" );
	
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
		
		//get configurations previously saved.
		
		$array = array();
		global $wpdb;
		$resultado = $wpdb->get_results("SELECT distinct meta_key FROM $wpdb->postmeta WHERE post_id in(SELECT ID FROM $wpdb->posts WHERE post_type = '".$type."')");
		foreach($resultado as $resultadoX){
			$valor = get_option($type."#triplificator#".$resultadoX->meta_key, null);
			if($valor != null) array_push($array, array($resultadoX->meta_key, $valor));
		}
		
		$tabela = $wpdb->prefix . 'posts';
		foreach ( $wpdb->get_col( "DESC " . $tabela, 0 ) as $coluna ){
			$valor = get_option($type."#triplificator#".$coluna, null);
			if($valor != null) array_push($array, array($coluna, $valor));
		}
		
		//removing elements which correspondence were not defined
		/*foreach($posts as $post){
			foreach($post as $key => $postX){
				if(!array_key_exists($key, array_keys($array))) {
					unset($postX->$key);
				}
			}
		}*/
		//replacing keys for the ones the user defined for that type
		foreach($posts as $post){
			foreach($array as $valores){
				if (array_key_exists($valores[0], $post)) {
					$post->$valores[1] = $post->$valores[0];
					unset($post->$valores[0]);
				}
			}
		}
		
		//ver um jeito esperto de fazer isso, talvez switch (tem switch em php?), um for em um array?
		if(strcmp(strtoupper($structure), 'JSON') == 0){
			 @include_once dirname( __FILE__ ) .'/jsonld/jsonld.php';
			$JSON = json_encode($posts);
			print_r($JSON);
		} else if(strcmp(strtoupper($structure), 'RDF') == 0){
			$parser = ARC2::getRDFParser();
			$JSON = json_encode($posts);
			$parser->parse($JSON);
			print_r($parser);
		} else if(strcmp(strtoupper($structure), 'XML') == 0){
			//$parser = ARC2::getRDFXMLParser();
			$JSON = json_encode($posts);
			//$array = json_decode($JSON);
			$parser->parse($JSON);
			print_r($parser);
			//print_r(array2xml($array));
			//=====================
			//$array =  json_decode($JSON);
			//$xml = new SimpleXMLElement('<root/>');
			//array_flip($posts);
			//array_walk_recursive($array, array ($xml, 'addChild'));
			//print $xml->asXML();
		} else if(strcmp(strtoupper($structure), 'TURTLE') == 0){
			$parser = ARC2::getTurtleParser();
			$JSON = json_encode($posts);
			$parser->parse($JSON);
			print_r($parser);
		} 
		
		//exit();
		return;
	}
}
?>
