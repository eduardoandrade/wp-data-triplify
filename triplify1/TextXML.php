<?php

include_once( "arc2/ARC2.php" );
require_once( "functions.php" );

class TextXML {
	
	function __construct($posts) {
		
		//$parser = ARC2::getRDFXMLParser();
		//$JSON = json_encode($posts);
		//$array = json_decode($JSON);
		//$parser->parse($JSON);
		print_r($posts);
		//print_r(array2xml($array));
		//=====================
		//$array =  json_decode($JSON);
		//$xml = new SimpleXMLElement('<root/>');
		//array_flip($posts);
		//array_walk_recursive($array, array ($xml, 'addChild'));
		//print $xml->asXML();
		
		//print_r($JSON);
	}
	
}
