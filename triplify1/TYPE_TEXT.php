<?php
	
include_once("TextJSON.php");
include_once("TextRDF.php");
include_once("TextXML.php");
include_once("TextTURTLE.php");
include_once("prefixColumnUri.php");

class TYPE_TEXT {
	
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
		
		$array = $this->getConfigurationsPreviouslySaved($type);//get configurations previously saved.
		$array_contendo_prefixos_usados = $this->getPrefixesUsed($type, $posts, $array);//replacing keys for the ones the user defined for that type, and at the same time figuring out the prefixes from the columns
		
		$option_URI_base = get_option("#triplificator_uri_base#".$type);
		
		//ver um jeito esperto de fazer isso, talvez switch (tem switch em php?), um for em um array?
		if(strcmp(strtoupper($structure), 'JSON') == 0) new TextJSON($array_contendo_prefixos_usados, $posts); 
		else if(strcmp(strtoupper($structure), 'RDF') == 0) new TextRDF($option_URI_base ,$array_contendo_prefixos_usados, $posts);
		else if(strcmp(strtoupper($structure), 'XML') == 0) new TextXML($posts);
		else if(strcmp(strtoupper($structure), 'TURTLE') == 0) new TextTURTLE($posts);
		
		//exit();
		return;
	}
	
	function getConfigurationsPreviouslySaved($type){
		
		$array = array();
		global $wpdb;
		$resultado = $wpdb->get_results("SELECT distinct meta_key FROM $wpdb->postmeta WHERE post_id in(SELECT ID FROM $wpdb->posts WHERE post_type = '".$type."')");
		foreach($resultado as $resultadoX){
			$valor = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}triplify_configurations WHERE tipo='".$type."' and coluna='".$resultadoX->meta_key."'", OBJECT);
			if($valor != null) array_push($array, array($resultadoX->meta_key, $valor->valor_correspondente));
		}

		$tabela = $wpdb->prefix . 'posts';
		foreach ( $wpdb->get_col( "DESC " . $tabela, 0 ) as $coluna ){
			$valor = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}triplify_configurations WHERE tipo='".$type."' and coluna='".$coluna."'", OBJECT);
			if($valor != null) array_push($array, array($coluna, $valor->valor_correspondente));
		}
		
		return $array;
	}
	
	function getPrefixesUsed($type, $posts, $array){
		
		global $wpdb;
		$array_contendo_prefixos_usados = array();
		
		foreach($posts as $post){
			foreach($array as $valores){
				if (array_key_exists($valores[0], $post)) {
					$post->$valores[1] = $post->$valores[0];
					if(!strcmp(strtolower($valores[0]),"id")==0) unset($post->$valores[0]);
					if(strpos($valores[1], ":")){// if prefix contains ':'
						$explode = explode(':', $valores[1]);
						$prefixos = array();
						foreach($array_contendo_prefixos_usados as $prefixo){//get only prefixes
							if(!in_array($prefixo->prefix, $prefixos)) array_push($prefixos, $prefixo->prefix);
						}
						
						if(!in_array($explode[0], $prefixos)){//if array don't contains tha specific prefix, i add an object with that prefix
							$uri = $wpdb->get_row("SELECT uri FROM {$wpdb->prefix}triplify_configurations WHERE tipo='".$type."' and coluna='".$valores[0]."'", OBJECT);//see if this is a URI column or not
							$object = new prefixColumnUri();
							$object->prefix = $explode[0];
							$object->coluna = $explode[1];
							$object->uri = $uri->uri;
							$object->fullProperty = $valores[1];
							array_push($array_contendo_prefixos_usados, $object);
							//array_push($objetos_usados, $object);
						}
					}
				}
			}
		}
		//print_r($array_contendo_prefixos_usados);
		//echo count($array_contendo_prefixos_usados);
		return $array_contendo_prefixos_usados;
	}
}
?>