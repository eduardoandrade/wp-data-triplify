<?php

include_once( "arc2/ARC2.php" );
require_once( "functions.php" );

class TextRDF {
	
	function __construct($option_URI_base, $array_contendo_prefixos_usados, $posts) {
		$RDF = '<?xml version="1.0"?>';
		$RDF = $RDF."<rdf:RDF ";
		$RDF = $RDF."xmlns:rdf= \"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" ";//every post have to have
		
		
		foreach($array_contendo_prefixos_usados as $object){
			if(strcmp(strtolower($object->prefix), 'dc') == 0) $RDF = $RDF."xmlns:".$object->prefix."= \"http://purl.org/dc/elements/1.1\" ";
			else if(strcmp(strtolower($object->prefix), 'foaf') == 0) $RDF = $RDF."xmlns:".$object->prefix."= \"http://xmlns.com/foaf/0.1/\" ";
			//else if(strcmp(strtolower($object->prefix), 'rdf') == 0) $RDF = $RDF.$object->prefix."= \"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" ";
			else if(strcmp(strtolower($object->prefix), 'rdfs') == 0) $RDF = $RDF."xmlns:".$object->prefix."= \"http://www.w3.org/2000/01/rdf-schema#\" ";
		}
		$RDF = $RDF.">";
		
		foreach($posts as $post){
			$RDF= $RDF."<rdf:Description ";
			$RDF= $RDF."rdf:about=\"".$option_URI_base.$post->ID."\"";
			$RDF= $RDF.">";
			foreach($array_contendo_prefixos_usados as $object){
				$property = $object->fullProperty;
				$RDF = $RDF."<".$object->fullProperty.">";
				$RDF = $RDF.$post->$property;
				$RDF = $RDF."</".$object->fullProperty.">";
			}
			$RDF= $RDF."</rdf:Description>";
		}
		
		$RDF = $RDF." </rdf:RDF>";
	
		echo htmlentities($RDF);
		//echo $RDF;
	}
	
}

?>
