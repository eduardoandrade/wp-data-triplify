<?php

include_once( "arc2/ARC2.php" );
require_once( "functions.php" );

class TextXML {
	
	function __construct($option_URI_base, $array_contendo_objetos_usados, $prefixos, $posts) {
		echo htmlentities('<?xml version="1.0"?>');
		echo htmlentities ("<posts>");
		foreach($posts as $post){
			$XML = "<post ";
			foreach($prefixos as $prefixo){//always there will be at maximum one of each.
				if((getPrefix($prefixo), 'dc') == 0) $XML = $XML."xmlns:".$prefixo."= \"http://purl.org/dc/elements/1.1\" ";
				else if((getPrefix($prefixo), 'foaf') == 0) $XML = $XML."xmlns:".$prefixo."= \"http://xmlns.com/foaf/0.1/\" ";
				else if((getPrefix($prefixo), 'owl') == 0) $XML = $XML."xmlns:".$prefix."= \"http://www.w3.org/2002/07/owl#\" ";
				else if((getPrefix($prefixo), 'rdf') == 0) $XML = $XML."xmlns:".$prefixo."= \"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" ";
				else if((getPrefix($prefixo), 'rdfs') == 0) $XML = $XML."xmlns:".$prefixo."= \"http://www.w3.org/2000/01/rdf-schema#\" ";
				else if((getPrefix($prefixo), 'xsd') == 0) $XML = $XML."xmlns:".$prefixo."= \"http://www.w3.org/2001/XMLSchema#\" ";
			}
			$XML = $XML.">";
			$XML = $XML."<URI>";
			$XML = $XML.$option_URI_base.$post->ID;
			$XML = $XML."</URI>";
			echo htmlentities($XML);
			
			foreach($array_contendo_objetos_usados as $object){
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
