<?php
class Render {
	
	function __construct() {
		
	add_action( 'admin_footer', 'triplify_javascript' );
	/*add_action( 'admin_enqueue_scripts', 'my_enqueue' );
	do_action('admin_enqueue_scripts');
	do_action('wp_ajax_my_action');*/
		
		if(!isset($_POST['termoPesquisado'])){
	?>
			<div>
			<form action="" method="POST">
				<h3>Digite a URL que deseja acessar para verificar os dados triplificados:</h3>
				<br/>
					<code><?php bloginfo('url'); ?>/</code> <input name="url_base" value="<?php echo get_option("triplify_url_base_dados", "tri");?>" id="postType_base"/>
				<h3>Digite o post-type que deseja triplificar: </h3>
				<br/>
					<input name="postType" value="" id="postType"/>
					<button name="termoPesquisado" type="submit" class="button-primary">Pesquisar</button>
				</form>	
				<br/>
			</div>
	<?php
		} else {
			//$termo = pegaValores($_POST["postType"]);
			$termo = $_POST["postType"];
			$this->salvaUrlBase($_POST["url_base"]);
	?>
			<div id="corpo">
				<h2>Você está procurando por <?php echo $termo; ?></h2>
				
				<h4> Defina a URI Base dos posts:</h4>
				<?php $uriBase = get_option("#triplificator_uri_base#".$_POST["postType"], 'URI base');
				echo"<input name='uriBase' value='".$uriBase."'  id='uriBase'/>"
				?>
				<br/>
				
				<h4>Defina as equivalências e marque o checkbox caso o resultado mostrado por essa coluna seja uma URI: </h4>
<?php
				global $wpdb;
				
				$resultado = $wpdb->get_results("SELECT distinct meta_key FROM $wpdb->postmeta WHERE post_id in(SELECT ID FROM $wpdb->posts WHERE post_type = '".$termo."')");
				
				$correspondecias;
				$contador = 1;
				$post = $_POST["postType"];
					
				$tabela = $wpdb->prefix . 'posts';
				foreach ( $wpdb->get_col( "DESC " . $tabela, 0 ) as $coluna ){
					$registro = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}triplify_configurations WHERE tipo='".$post."' and coluna='".$coluna."'", OBJECT);
					if($registro == null) {
						$valor = 'correspondencia';
						$checked = "";
					}
					else{
						$valor = $registro->valor_correspondente;
						if($registro->uri == true) $checked = 'checked';
						else $checked = "";
					}
					
					
					echo "<div><p>".
					$contador."- <input type='checkbox' id='uri".$contador."' ".$checked."/>".
					$coluna." => ".
					"<input class='input_triplify_posts' value='". $valor ."' id='correspondencia".$contador."' mk='".$coluna."' contador='".$contador."'/>".
					"</p></div>";
					$contador++;
				}
				
				foreach($resultado as $resultadoX)
				{
					$registro = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}triplify_configurations WHERE tipo='".$post."' and coluna='".$resultadoX->meta_key."'", OBJECT);
					if($registro == null){
						$valor = 'correspondencia';
						$checked = "";
					}
					else{
						$valor = $registro->valor_correspondente;
						if($registro->uri == true) $checked = 'checked';
						else $checked = "";
					}
					
					echo "<div><p>".
					$contador."- <input  type='checkbox' id='uri".$contador."' ".$checked."/>".
					$resultadoX->meta_key." => ".
					"<input class='input_triplify' value='". $valor ."' id='correspondencia".$contador."'  mk='".$resultadoX->meta_key."' contador='".$contador."'/>". 
					"</p></div>";
					$contador++;
				}

?>
				<input type='hidden' id='post_type' name='post_type' value="<?php echo $termo; ?>" />
				<br/>
				<button id="id" name="triplify" class="button-primary">Salvar opções</button>
			</div><?php
		}?>
		<div id="corpo2" style="display:none">
			<h2>Opções salvas!</h2>
			<h3>Acesse <code><?php bloginfo('url');?><?php echo get_option("triplify_url_base_dados", "tri")?>/<?php echo $termo; ?>/formato_desejado_dos_dados</code> para obter os dados. Caso o formato não seja especificado, o resultado será mostrado em JSON</h3>
		</div>
		<?php
	
		function triplify_javascript() { ?>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				$("#id").click(function(){
					var post_type = $('#post_type').val(); 
					var arrayCorrespondencias = new Array();
					$('.input_triplify').each(function(k,v,w){
						var mk 	= $(this).attr('mk');
						var v	= $(this).val();
						var contadorX = $(this).attr('contador').toString();
			
						if($.trim(v) != 'correspondencia' &&  $.trim(v) != ''){
							var post_triplify = new Object();
							
							post_triplify.coluna = mk;
							post_triplify.valor = v;
							
							var identificador = "#uri";
							var concatenate = identificador.concat(contadorX);
							var checkbox = $(concatenate);
							if(checkbox.is(":checked")){
								post_triplify.uri = 'true';
							} else {
								post_triplify.uri = 'false';
							}
							
							arrayCorrespondencias.push(post_triplify);
						}
					});
					$('.input_triplify_posts').each(function(k,v,w){
						var mk 	= $(this).attr('mk');
						var v	= $(this).val();
						var contadorX = $(this).attr('contador').toString();
			
						if($.trim(v) != 'correspondencia' &&  $.trim(v) != ''){
							
							var post_triplify = new Object();
							
							post_triplify.coluna = mk;
							post_triplify.valor = v;
							
							var identificador = "#uri";
							var concatenate = identificador.concat(contadorX);
							var checkbox = $(concatenate);
							if(checkbox.is(":checked")){
								post_triplify.uri = 'true';
							} else {
								post_triplify.uri = 'false';
							}

							arrayCorrespondencias.push(post_triplify);
						}
					});
					var uri_base = $('#uriBase').val();
					var data = {
							'action' : 'triplify_action',
							'post_type': post_type,
							'uri_base': uri_base,
							'arrayCorrespondencias': arrayCorrespondencias
					};
					$.post(ajaxurl, data, function(response) { 
						<!-- ver o que fazer quando falhar a requisição -->
					});
					
					$("#corpo").hide(1000);
					$("#corpo2").show(1000);
					
				});
				var availableTags = [
					"dc:abstract", 
					"dc:accessRights", 
					"dc:accrualMethod", 
					"dc:accrualPeriodicity", 
					"dc:accrualPolicy",
					"dc:alternative",
					"dc:audience",
					"dc:available",
					"dc:bibliographicCitation", 
					"dc:conformsTo", 
					"dc:contributor", 
					"dc:coverage", 
					"dc:created", 
					"dc:creator", 
					"dc:date", 
					"dc:dateAccepted", 
					"dc:dateCopyrighted", 
					"dc:dateSubmitted", 
					"dc:description", 
					"dc:educationLevel", 
					"dc:extent", 
					"dc:format", 
					"dc:hasFormat", 
					"dc:hasPart", 
					"dc:hasVersion", 
					"dc:identifier", 
					"dc:instructionalMethod", 
					"dc:isFormatOf", 
					"dc:isPartOf", 
					"dc:isReferencedBy", 
					"dc:isReplacedBy", 
					"dc:isRequiredBy", 
					"dc:issued", 
					"dc:isVersionOf", 
					"dc:language", 
					"dc:license", 
					"dc:mediator", 
					"dc:medium", 
					"dc:modified", 
					"dc:provenance", 
					"dc:publisher", 
					"dc:references", 
					"dc:relation", 
					"dc:replaces", 
					"dc:requires", 
					"dc:rights", 
					"dc:rightsHolder", 
					"dc:source", 
					"dc:spatial", 
					"dc:subject", 
					"dc:tableOfContents", 
					"dc:temporal", 
					"dc:title", 
					"dc:type", 
					"dc:valid",
					"dc:contributor",
					"dc:coverage",
					"dc:creator",
					"dc:date",
					"dc:description",
					"dc:format",
					"dc:identifier",
					"dc:language",
					"dc:publisher",
					"dc:relation",
					"dc:rights",
					"dc:source",
					"dc:subject",
					"dc:title",
					"dc:type",
					"foaf:Agent",
					"foaf:Person",
					"foaf:name",
					"foaf:title",
					"foaf:img",
					"foaf:depiction",
					"foaf:depicts",
					"foaf:familyName",
					"foaf:givenName",
					"foaf:knows",
					"foaf:based_near",
					"foaf:age",
					"foaf:made",
					"foaf:maker",
					"foaf:primaryTopic",
					"foaf:primaryTopicOf",
					"foaf:Project",
					"foaf:Organization",
					"foaf:Group",
					"foaf:member",
					"foaf:Document",
					"foaf:Image",
					"foaf:nick",
					"foaf:mbox",
					"foaf:homepage",
					"foaf:weblog",
					"foaf:openid",
					"foaf:jabberID",
					"foaf:mbox_sha1sum",
					"foaf:interest",
					"foaf:topic_interest",
					"foaf:topic",
					"foaf:page",
					"foaf:workplaceHomepage",
					"foaf:workInfoHomepage",
					"foaf:schoolHomepage",
					"foaf:publications",
					"foaf:currentProject",
					"foaf:pastProject",
					"foaf:account",
					"foaf:OnlineAccount",
					"foaf:accountName",
					"foaf:accountServiceHomepage",
					"foaf:PersonalProfileDocument",
					"foaf:tipjar",
					"foaf:sha1",
					"foaf:thumbnail",
					"foaf:logo",
					"rdf:HTML",
					"rdf:langString",
					"rdf:PlainLiteral",
					"rdf:type",
					"rdf:Property",
					"rdf:Statement",
					"rdf:subject",
					"rdf:predicate",
					"rdf:object",
					"rdf:Bag",
					"rdf:Seq",
					"rdf:Alt",
					"rdf:value",
					"rdf:List",
					"rdf:nil",
					"rdf:first",
					"rdf:rest",
					"rdf:XMLLiteral",
					"rdfs:Resource",
					"rdfs:Class",
					"rdfs:subClassOf",
					"rdfs:subPropertyOf",
					"rdfs:comment",
					"rdfs:label",
					"rdfs:domain",
					"rdfs:range",
					"rdfs:seeAlso",
					"rdfs:isDefinedBy",
					"rdfs:Literal",
					"rdfs:Container",
					"rdfs:ContainerMembershipProperty",
					"rdfs:member",
					"rdfs:Datatype"
				];
				$(".input_triplify_posts").autocomplete({
				  source: availableTags
				});
				$(".input_triplify").autocomplete({
				  source: availableTags
				});
				$(".input_triplify_posts").click(function(){
					if($(this).val() == 'correspondencia'){
						$(this).val('');
					}
				});
				$(".input_triplify").click(function(){
					if($(this).val() == 'correspondencia'){
						$(this).val('');
					}
				});
			});
			</script> <?php
		}
	
	}
		/*function my_enqueue($hook) {
		echo "aaaaaaaaaaa";
		if( 'index.php' != $hook ) {
			// Only applies to dashboard panel
			return;
		}
		echo "bbbbbbbb";
		wp_enqueue_script( 'ajax-script', plugins_url( '/js/scripts.js', __FILE__ ), array('jquery') );
		echo "ccccccccc";
		// in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
		wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );
		echo "dddddddd";
	}
	
	
	function my_action_callback() {
		global $wpdb;
		$whatever = intval( $_POST['whatever'] );
		$whatever += 10;
        echo $whatever;
		wp_die();
	}*/
	
	function salvaUrlBase($option){
		$option_saved = get_option("triplify_url_base_dados", null);
		if($option_saved == null) add_option("triplify_url_base_dados", $option);
		else if(strcmp(strtolower($option_saved), strtolower($option)) == 0) return;
		else update_option("triplify_url_base_dados", $option);
	}
}
 ?>
