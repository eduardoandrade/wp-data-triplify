<?php

include_once( 'jsonld/jsonld.php' );

class TextJSON {
	
	function __construct($array_contendo_prefixos_usados, $posts) {
		
		
		//global $wpdb;
		$context = array();
		
		foreach($posts as $post){
			foreach($array_contendo_prefixos_usados as $object){
				if(strcmp(strtolower($object->prefix), 'dc') == 0){
					if($object->uri == 1){
						$context = array($object->prefix => (object)array("@id" => "http://purl.org/dc/elements/1.1".$object->prefix, "@type" => "@id"));//ATENÇÃÃÃÃÃÃÃO
					} else {
						$context = array($object->prefix => (object)array("@id" => "http://purl.org/dc/elements/1.1".$object->prefix));//ATENÇÃÃÃÃÃÃÃO
					}
				} else if(strcmp(strtolower($object->prefix),'foaf') == 0){
					if($object->uri == 1){
						$context = array($object->prefix => (object)array("@id" => "http://xmlns.com/foaf/0.1/".$object->prefix, "@type" => "@id"));//ATENÇÃÃÃÃÃÃÃO
					} else {
						$context = array($object->prefix => (object)array("@id" => "http://xmlns.com/foaf/0.1/".$object->prefix));//ATENÇÃÃÃÃÃÃÃO
					}
				} else if(strcmp(strtolower($object->prefix), 'rdf') == 0){
					if($object->uri == 1){
						$context = array($object->prefix => (object)array("@id" => "http://www.w3.org/1999/02/22-rdf-syntax-ns#".$object->prefix, "@type" => "@id"));//ATENÇÃÃÃÃÃÃÃO
					} else {
						$context = array($object->prefix => (object)array("@id" => "http://www.w3.org/1999/02/22-rdf-syntax-ns#".$object->prefix));//ATENÇÃÃÃÃÃÃÃO
					}
				} else if(strcmp(strtolower($object->prefix), 'rdfs') == 0){
					if($object->uri == 1){
						$context = array($object->prefix => (object)array("@id" => "http://www.w3.org/2000/01/rdf-schema#".$object->prefix, "@type" => "@id"));//ATENÇÃÃÃÃÃÃÃO
					} else {
						$context = array($object->prefix => (object)array("@id" => "http://www.w3.org/2000/01/rdf-schema#".$object->prefix));//ATENÇÃÃÃÃÃÃÃO
					}
				}
			}
			//$compacted = jsonld_compact((object)$post, (object)$context);
			//echo json_encode($compacted, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		}
		//print_r(array_values($posts));

		//$uri = $wpdb->get_row("SELECT uri FROM {$wpdb->prefix}triplify_configurations WHERE tipo='".$type."' and coluna='".$valores[0]."'", OBJECT);
		
		//$compacted = jsonld_compact((object)$posts, (object)$context);
		//echo json_encode($compacted, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		
	}
	
	/*function format_post($post){
		$array = array();
		foreach((array)$post as $key => $value){
			array_push($array, array($key, utf8_strtr($value)));
			//echo(format_property($value));
		}
		
		return $array;
	}*/
	
	/*echo "{ ";
		echo "\"@context\": {";
		foreach($contexts as $prefix){
			echo $prefix." ";
		} 
		echo " },";
		$JSON = json_encode($posts);
		
		$JSON = str_replace("[", "", $JSON);
		$JSON = str_replace("]", "", $JSON);
		$JSON = str_replace("{", "", $JSON);
		$JSON = str_replace("}", "", $JSON);
		
		print_r ($JSON);
		echo " }";*/
	
}

?>
