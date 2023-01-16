<?php if(isset($fornecedores) && count($fornecedores) > 0 ) : ?>

	<div class="row-fluid inline">
		<img src='/portal/img/marker/blue.png'> Fornecedores de Engenharia
		<img src='/portal/img/marker/red-pushpin.png'> Endereço da Unidade
	</div>
	<?php
	if(Ambiente::TIPO_MAPA == 1) {
	?>
		<div style="width: 100%; height: 400px; background: none repeat scroll 0% 0% rgb(229, 227, 223); position: relative;" id="canvas_mapa"> </div>
		<script type="text/javascript">
			$(function() {
				if (typeof(window.google) != 'undefined') {
					
					var map_coords = new google.maps.LatLng(<?php echo $latitude; ?>,<?php echo $longitude; ?>);
					var map_config = { zoom: 4, center: map_coords, mapTypeId: google.maps.MapTypeId.ROADMAP };
					
					map = new google.maps.Map(document.getElementById('canvas_mapa'), map_config);
					
					var marker_regulador_image = new google.maps.MarkerImage("/portal/img/marker/blue.png", new google.maps.Size(32, 32), new google.maps.Point(0, 0), new google.maps.Point(16, 32));
					var marker_filtro_image = new google.maps.MarkerImage("/portal/img/marker/red-pushpin.png", new google.maps.Size(32, 32), new google.maps.Point(0, 0), new google.maps.Point(16, 32));
					dado = new google.maps.LatLng('<?php echo $latitude; ?>', '<?php echo $longitude; ?>');
					
					map_marker = new google.maps.Marker({ position: dado, map: map, icon: marker_filtro_image,});
					map.setCenter(dado);
					map.setZoom(<?php echo $zoom; ?>);

					var latitude = <?php echo $latitude; ?>;
					var longitude = <?php echo $longitude; ?>;

	//				rectangle = new google.maps.Rectangle({
	//					strokeColor: "#666",
	//					strokeOpacity: 0.6,
	//					strokeWeight: 2,
	//					fillColor: "#CCC",
	//					fillOpacity: 0.40,
	//					map: map,
	//					bounds: new google.maps.LatLngBounds(
	//					new google.maps.LatLng(<?php echo $latitude_min; ?>, <?php echo $longitude_min; ?>),
	//					new google.maps.LatLng(<?php echo $latitude_max; ?>, <?php echo $longitude_max; ?>)
	//					)
	//				});	

					var circle = new google.maps.Circle({
						strokeColor: "#A5BFFF",
						strokeOpacity: 0.6,
						strokeWeight: 2,
						fillColor: "#E0E9FF",
						fillOpacity: 0.40,
						map: map,
						center: {lat:<?php echo $latitude; ?>, lng: <?php echo $longitude; ?>},
						radius: <?php echo $raio; ?> * 1000
					});	
									
					<?php foreach($fornecedores as $key => $dado ) : ?>
						<?php if($dado['FornecedorEndereco']['latitude'] && $dado['FornecedorEndereco']['longitude']) : ?>
							var dado = new google.maps.LatLng(<?php echo $dado['FornecedorEndereco']['latitude']; ?>, <?php echo $dado['FornecedorEndereco']['longitude']; ?>);
							var marker_title = "<?php echo 'Fornecedor: '. $dado['Fornecedor']['nome']; ?>\n";
							
							<?php
							if( isset($dado['FornecedorContato']) ){
								foreach ($dado['FornecedorContato'] as $key => $telefone ) {?>
									marker_title += "Telefone: ( <?php echo substr($telefone, 0, 2) . "-" . substr($telefone, 2, 4) . "." . substr($telefone, 6, strlen($telefone)); ?> )";
								<?}
							}?>				
							map_marker = new google.maps.Marker({ position: dado, map: map, title: marker_title, icon: marker_regulador_image});					
						<?php endif; ?> 
					<?php endforeach; ?>
				} else {
					var html  = '<div class="alert alert-error">';
					html += '    <h4>Erro na api do googlemaps</h4>';
					html += '    <h5>Verifique as suas configurações de proxy, ou se o script da api está carregado corretamente.</h5>';
					html += '    </div>';
					$("#canvas_mapa").html(html);
				}
			});
		</script>
	<?php
	}
	else if(Ambiente::TIPO_MAPA == 2) {

	    if(empty($latitude)){
	        $latitude = '0';
	    }
	    if(empty($longitude)){
	        $longitude = '0';
	    }
	    
	    $mapOptions = array(	            
	            'polygon_string' => null, 
	            'latitude_center' => $latitude,
	            'longitude_center' => $longitude,	            
	            'zoom'	=> 5,
	            'width' => '100%',
	            'height' => '400px',
	            'style' => 'background: none repeat scroll 0% 0% rgb(229, 227, 223); position: relative;',
	            'raio'	=> $raio,
	            'fornecedores' => $fornecedores
	        ); 
	    
	    echo $this->Mapa->localizaCredenciado($mapOptions);
	
	}//fim tipo mapa

	?>
	<br />
	<br />
	<table class='table table-striped horizontal-scroll'>
		<thead>
			<tr>
				<th>Código</th>
				<th>Fornecedor</th>
				<th>Bairro</th>
				<th>Cidade/Estado</th>
				<th>Contato</th>
				<th>Distância</th>
				<th>Distância de Carro</th>
				<th>Ordem de Serviço</th>
			</tr>
		</thead>
		<tbody id="lista_reguladores">
            <?php foreach ($fornecedores as $fornecedor) :?>
	            <tr <?php if($fornecedor['Fornecedor']['interno']) : ?> style="border: 2px solid #CCC;" <?php endif; ?>>
	                <td><?= $fornecedor['Fornecedor']['codigo'] ?></td>
	                <td>
		                <?php echo $fornecedor['Fornecedor']['nome']; ?> 
	                	<?php if($fornecedor['Fornecedor']['interno']) : ?>
	                		<b>(RHHealth Interno)</b>
	                	<?php endif; ?>
	                </td>
	                <td><?= $fornecedor['FornecedorEndereco']['cidade'] ?> / <?= $fornecedor['FornecedorEndereco']['estado_descricao'] ?></td>
	                <td><?= $fornecedor['FornecedorEndereco']['cidade'] ?> / <?= $fornecedor['FornecedorEndereco']['estado_descricao'] ?></td>
	                <?php $fornecedor['FornecedorContato']['descricao'] = isset($fornecedor['FornecedorContato']['descricao']) ? trim($fornecedor['FornecedorContato']['descricao']) : ''; ?>
	                <td>
	                	<?php if($fornecedor['FornecedorContato']['descricao']) : ?>
	                		<?php echo substr($fornecedor['FornecedorContato']['descricao'], 0, 2) . "-" . substr($fornecedor['FornecedorContato']['descricao'], 2, 4) . "." . substr($fornecedor['FornecedorContato']['descricao'], 6, strlen($fornecedor['FornecedorContato']['descricao'])); ?>
	                	<?php endif; ?>
	                </td>
	                <td>
	                	<?php if(isset($fornecedor['FornecedorEndereco']['distancia'])) : ?>
	                		<?= str_replace(".", ",", $fornecedor['FornecedorEndereco']['distancia']) . " Km" ?>
	                	<?php endif; ?>
	                </td>
	                <td><?= (isset($fornecedor['FornecedorEndereco']['distancia_google']) && !empty($fornecedor['FornecedorEndereco']['distancia_google'])) ? $fornecedor['FornecedorEndereco']['distancia_google'] . " (" . $fornecedor['FornecedorEndereco']['tempo_google'] . ")" : "Não Avaliado"; ?> </td>
	                <td class="center">
	                	<?php echo $html->link('Enviar', 'javascript:void(0);', array('class' => 'badge badge-empty badge-alert', 'onclick' => 'enviar_ordem_servico(this, '.$fornecedor['Fornecedor']['codigo'].', '.$codigo_cliente.', "' . $fornecedor['Fornecedor']['nome'] . '", "' . $nome_cliente . '", "'.$fornecedor['ListaDePrecoProdutoServico']['codigo_servico'].'","'.$fornecedor['FornecedorContatoEmail']['descricao'].'")')); ?>
	                </td>
	            </tr>
            <?php endforeach; ?>
		</tbody>
	</table>
<?php else:?>
	<div class='alert alert-warning'><strong>Não há registros encontrados para os critérios pesquisados.</strong></div>
<?php endif;?>
<div class='form-actions well'>
	<a href="javascript:void(0);" onclick="window.history.go(-1);" class="btn btn-default">Voltar</a>
</div>

<div class="modal fade" id="modal_ordem_servico">
	<div class="modal-dialog modal-sm" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="gridSystemModalLabel">Enviar Ordem de Serviço</h4>
			</div>
	    	<div class="modal-body">
	    		O aviso da ordem de serviço será enviada por e-mail para o fornecedor escolhido, solicitando que se dirija ao endereço do cliente para execução do Serviço.
	    		<br /><br />	    	
				<div class="right" id="div_botoes">
					<?php echo $this->BForm->create('OrdemServico', array('type' => 'post' ,'url' => array('controller' => 'clientes_implantacao', 'action' => 'enviar_ordem_servico')));?>
						<?php echo $this->BForm->hidden('var_aux', array('value' => $var_aux, 'id' => 'var_aux')); ?>
						<?php echo $this->BForm->hidden('codigo_fornecedor', array('value' => '', 'id' => 'codigo_fornecedor')); ?>
						<?php echo $this->BForm->hidden('codigo_cliente', array('value' => '', 'id' => 'codigo_cliente')); ?>
						<?php echo $this->BForm->hidden('codigo_servico', array('value' => '', 'id' => 'codigo_servico')); ?>
						
						<?php if(isset($servicos[$codigo_servico])) : ?>
							<br />
							<strong>Solicitação do Serviço:</strong><br />
							<?php echo $servicos[$codigo_servico]; ?>
						<?php endif; ?>
						
						<br /><br />
						<strong>Fornecedor:</strong><br />
						<span id="nome_fornecedor"></span><br /><br />
						
						<strong>Cliente à Atender:</strong><br />
						<span id="nome_cliente"></span><br /><br />			
						
						<a href="javascript:void(0);" class="btn btn-danger" onclick="$('#modal_ordem_servico').modal('hide');">Fechar</a>

						<a href="javascript:void(0);" class="btn btn-success" id="btn_confirmar_fornecedor" onclick="enviaOrdemServico(this)">Confirmar</a>

						<span id="msg_email_cadastro" >Não existe email cadastrado para este fornecedor, é obrigatório um email de contato para esta fase do sistema.</span><br /><br />

					<?php echo $this->BForm->end(); ?>
				</div>				
	    	</div>
	    </div>
	</div>
</div>

<?php echo $this->Javascript->codeBlock("

	$(document).ready(function() {

		$('#msg_email_cadastro').hide();

	});
	
	function enviar_ordem_servico(element, codigo_fornecedor, codigo_cliente, nome_fornecedor, nome_cliente, codigo_servico, email='') {

		$('#msg_email_cadastro').hide();
		$('#btn_confirmar_fornecedor').show();

		$('#modal_ordem_servico #codigo_fornecedor').val(codigo_fornecedor);
		$('#modal_ordem_servico #codigo_cliente').val(codigo_cliente);
		$('#modal_ordem_servico #codigo_servico').val(codigo_servico);
		
		$('#modal_ordem_servico select[name=\"fornecedor\"] ').val(codigo_fornecedor);
		
		$('#modal_ordem_servico #nome_fornecedor').html(nome_fornecedor);
		$('#modal_ordem_servico #nome_cliente').html(nome_cliente);

		if(email == '') {
			$('#btn_confirmar_fornecedor').hide();
			$('#msg_email_cadastro').show();
		}

			
      	$('#modal_ordem_servico').modal('show');
	}

	function enviaOrdemServico(element) {
		$(element).html('<img src=\"/portal/img/default.gif\">');
		$('#OrdemServicoLocalizarCredenciadosListagemForm').submit();
	}
"); ?>	