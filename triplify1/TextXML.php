<?php

include_once( "arc2/ARC2.php" );
require_once( "functions.php" );

class TextXML {
	
	function __construct($option_URI_base, $array_contendo_prefixos_usados, $posts) {
		echo htmlentities('<?xml version="1.0"?>');
		echo htmlentities ("<posts>");
		foreach($posts as $post){
			$XML = "<post ";
			foreach($array_contendo_prefixos_usados as $object){//always there will be at maximum one of each.
				if(strcmp(strtolower($object->prefix), 'dc') == 0) $XML = $XML."xmlns:".$object->prefix."= \"http://purl.org/dc/elements/1.1\" ";
				else if(strcmp(strtolower($object->prefix), 'foaf') == 0) $XML = $XML."xmlns:".$object->prefix."= \"http://xmlns.com/foaf/0.1/\" ";
				else if(strcmp(strtolower($object->prefix), 'rdf') == 0) $XML = $XML."xmlns:".$object->prefix."= \"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" ";
				else if(strcmp(strtolower($object->prefix), 'rdfs') == 0) $XML = $XML."xmlns:".$object->prefix."= \"http://www.w3.org/2000/01/rdf-schema#\" ";
			}
			$XML = $XML.">";
			$XML = $XML."<URI>";
			$XML = $XML.$option_URI_base.$post->ID;
			$XML = $XML."</URI>";
			echo htmlentities($XML);
			
			foreach($array_contendo_prefixos_usados as $object){
				$property = $object->fullProperty;
				echo htmlentities("<".$object->fullProperty.">");
				echo $post->$property;
				echo htmlentities("</".$object->fullProperty.">");
			}
			echo htmlentities("</post>");
		}
		echo htmlentities ("</posts>");
	}
	
}

?>
