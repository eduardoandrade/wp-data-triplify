<?php

include_once( "arc2/ARC2.php" );
require_once( "functions.php" );

class TextXML {
	
	function __construct($option_URI_base, $array_contendo_prefixos_usados, $posts) {
		 $XML = '<?xml version="1.0?>';
		foreach($posts as $post){
			$XML = $XML."<POST>";
			$XML = $XML."<URI>";
			$XML = $XML.$option_URI_base.$post->ID;
			$XML = $XML."</URI>";
			foreach($array_contendo_prefixos_usados as $object){
				$property = $object->fullProperty;
				 $XML =  $XML."<".$object->fullProperty.">";
				 $XML =  $XML.$post->$property;
				 $XML =  $XML."</".$object->fullProperty.">";
			}
			$XML = $XML."</POST>";
		}
		
		echo htmlentities($XML);
	}
	
}

?>
