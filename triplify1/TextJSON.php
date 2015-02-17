<?php

include_once( 'jsonld/jsonld.php' );
include_once( 'functions.php' );

class TextJSON {
	
	function __construct($prefixos, $posts) {
		
		//global $wpdb;
		$context = array();
		
		foreach($posts as $post){
			foreach($prefixos as $object){
				if(strcmp(getPrefix($prefix), 'dc') == 0){
					if($object->uri == 1){
						$context = array($object->prefix => (object)array("@id" => "http://purl.org/dc/elements/1.1".$object->prefix, "@type" => "@id"));//ATENÇÃÃÃÃÃÃÃO
					} else {
						$context = array($object->prefix => (object)array("@id" => "http://purl.org/dc/elements/1.1".$object->prefix));//ATENÇÃÃÃÃÃÃÃO
					}
				} else if(strcmp(getPrefix($prefix),'foaf') == 0){
					if($object->uri == 1){
						$context = array($object->prefix => (object)array("@id" => "http://xmlns.com/foaf/0.1/".$object->prefix, "@type" => "@id"));//ATENÇÃÃÃÃÃÃÃO
					} else {
						$context = array($object->prefix => (object)array("@id" => "http://xmlns.com/foaf/0.1/".$object->prefix));//ATENÇÃÃÃÃÃÃÃO
					}
				} else if(strcmp(getPrefix($prefix), 'owl') == 0){
					if($object->uri == 1){
						$context = array($object->prefix => (object)array("@id" => "http://www.w3.org/2002/07/owl#".$object->prefix, "@type" => "@id"));//ATENÇÃÃÃÃÃÃÃO
					} else {
						$context = array($object->prefix => (object)array("@id" => "http://www.w3.org/2002/07/owl#".$object->prefix));//ATENÇÃÃÃÃÃÃÃO
					}
				}
				else if(strcmp(getPrefix($prefix), 'rdf') == 0){
					if($object->uri == 1){
						$context = array($object->prefix => (object)array("@id" => "http://www.w3.org/1999/02/22-rdf-syntax-ns#".$object->prefix, "@type" => "@id"));//ATENÇÃÃÃÃÃÃÃO
					} else {
						$context = array($object->prefix => (object)array("@id" => "http://www.w3.org/1999/02/22-rdf-syntax-ns#".$object->prefix));//ATENÇÃÃÃÃÃÃÃO
					}
				} else if(strcmp(getPrefix($prefix), 'rdfs') == 0){
					if($object->uri == 1){
						$context = array($object->prefix => (object)array("@id" => "http://www.w3.org/2000/01/rdf-schema#".$object->prefix, "@type" => "@id"));//ATENÇÃÃÃÃÃÃÃO
					} else {
						$context = array($object->prefix => (object)array("@id" => "http://www.w3.org/2000/01/rdf-schema#".$object->prefix));//ATENÇÃÃÃÃÃÃÃO
					}
				} else if(strcmp(getPrefix($prefix), 'xsd') == 0){
					if($object->uri == 1){
						$context = array($object->prefix => (object)array("@id" => "http://www.w3.org/2001/XMLSchema#".$object->prefix, "@type" => "@id"));//ATENÇÃÃÃÃÃÃÃO
					} else {
						$context = array($object->prefix => (object)array("@id" => "http://www.w3.org/2001/XMLSchema#".$object->prefix));//ATENÇÃÃÃÃÃÃÃO
					}
				}
			}
			
			$compacted = jsonld_compact((object)$post, (object)$context);
			echo json_encode($compacted, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		}	
	}
	
}

?>
