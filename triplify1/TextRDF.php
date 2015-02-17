<?php

include_once( "arc2/ARC2.php" );
require_once( "functions.php" );

class TextRDF {
	
	function __construct($option_URI_base, $array_contendo_objetos_usados, $prefixos, $posts) {
		$RDF = '<?xml version="1.0" encoding="UTF-8"?>';
		$RDF = $RDF."<rdf:RDF ";
		$RDF = $RDF."xmlns:rdf= \"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" ";//every post have to have
		
		
		foreach($prefixos as $prefix){//always there will be at maximum one of each.
			if(strcmp(getPrefix($prefix), 'dc') == 0) $RDF = $RDF."xmlns:".$prefix."= \"http://purl.org/dc/elements/1.1\" ";
			else if(strcmp(getPrefix($prefix), 'foaf') == 0) $RDF = $RDF."xmlns:".$prefix."= \"http://xmlns.com/foaf/0.1/\" ";
			else if(strcmp(getPrefix($prefix), 'owl') == 0) $RDF = $RDF."xmlns:".$prefix."= \"http://www.w3.org/2002/07/owl#\" ";
			else if(strcmp(getPrefix($prefix), 'rdfs') == 0) $RDF = $RDF."xmlns:".$prefix."= \"http://www.w3.org/2000/01/rdf-schema#\" ";
			else if(strcmp(getPrefix($prefixo), 'xsd') == 0) $RDF = $RDF."xmlns:".$prefix."= \"http://www.w3.org/2001/XMLSchema#\" ";
		}
		$RDF = $RDF.">";
		echo htmlentities($RDF);
		
		foreach($posts as $post){
			$RDF= "<rdf:Description ";
			$RDF= $RDF."rdf:about=\"".$option_URI_base.$post->ID."\"";
			$RDF= $RDF.">";
			echo htmlentities($RDF);
			
			foreach($array_contendo_objetos_usados as $object){
				$property = $object->fullProperty;
				echo htmlentities("<".$object->fullProperty.">");
				echo $post->$property;
				echo htmlentities("</".$object->fullProperty.">");
			}
			echo htmlentities("</rdf:Description>");
		}
		
		echo htmlentities("</rdf:RDF>");
	}
	
}

?>
