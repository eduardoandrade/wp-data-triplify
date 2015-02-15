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
		
		//get configurations previously saved.
		$array = $this->getConfigurationsPreviouslySaved($type);

		//removing elements which correspondence were not defined
		/*foreach($posts as $post){
			foreach($post as $key => $postX){
				if(!array_key_exists($key, array_keys($array))) {
					unset($postX->$key);
				}
			}
		}*/
		global $wpdb;
		$array_contendo_prefixos_usados = array();
		
		//replacing keys for the ones the user defined for that type, and at the same time figuring out the prefixes from the columns
		foreach($posts as $post){
			foreach($array as $valores){
				if (array_key_exists($valores[0], $post)) {
					$post->$valores[1] = $post->$valores[0];
					unset($post->$valores[0]);
					if(strpos($valores[1], ":")){// if prefix contains ':'
						$explode = explode(':', $valores[1]);
						if(!in_array($explode[0], array_keys($array_contendo_prefixos_usados))){//if array don't contains
							$uri = $wpdb->get_row("SELECT uri FROM {$wpdb->prefix}triplify_configurations WHERE tipo='".$type."' and coluna='".$valores[0]."'", OBJECT);//see if this is a URI column or not
							$object = new prefixColumnUri();
							$object->prefix = $explode[0];
							$object->coluna = $explode[1];
							$object->uri = $uri->uri;
							array_push($array_contendo_prefixos_usados, $object);
						}
					}
				}
			}
		}
		
		/*foreach($array_contendo_prefixos_usados as $object){
			echo $object->prefix;
			echo "======================";
			echo $object->coluna;
			echo "======================";
			echo $object->uri;
		}*/
		
		/*$contexts = array();
		foreach($array_contendo_prefixos_usados as $prefixo){
			if(strcmp(strtolower($prefixo), 'dc') == 0){
				array_push($contexts, "\"dc\": \"http://purl.org/dc/elements/1.1\"");
			} else if(strcmp(strtolower($prefixo),'foaf') == 0){
				array_push($contexts, "\"foaf\": \"http://xmlns.com/foaf/0.1\"");
			} else if(strcmp(strtolower($prefixo), 'rdf') == 0){
				array_push($contexts, "\"rdf\": \"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"");
			} else if(strcmp(strtolower($prefixo), 'rdfs') == 0){
				array_push($contexts, "\"rdfs\": \"http://www.w3.org/2000/01/rdf-schema#\"");
			}
		}*/

		//var_dump($array_contendo_prefixos_usados);
		
		//ver um jeito esperto de fazer isso, talvez switch (tem switch em php?), um for em um array?
		if(strcmp(strtoupper($structure), 'JSON') == 0){
			//include_once dirname( __FILE__ ) .'/jsonld/jsonld.php';
			
			new TextJSON($array_contendo_prefixos_usados, $posts);
		} else if(strcmp(strtoupper($structure), 'RDF') == 0){

			$render = new TextRDF($posts);
		} else if(strcmp(strtoupper($structure), 'XML') == 0){
			
			$render = new TextXML($posts);
		} else if(strcmp(strtoupper($structure), 'TURTLE') == 0){
			
			$render = new TextTURTLE($posts);
		} 
		
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
	
}
?>
