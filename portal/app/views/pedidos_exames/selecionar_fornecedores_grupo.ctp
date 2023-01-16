<style>
	.control-group { float: left; margin-right: 15px; margin-bottom: 3px; }
	.form-actions { margin-top: 0; margin-bottom: 0; padding: 10px; }
	select { width: auto; }
	input[type="radio"], input[type="checkbox"] { margin: 0; }
	hr { margin: 0 0 8px 0; }
	label { margin-bottom: 0; line-height: 18px; };
</style>

<div class='inline well'>
	<?php echo $this->BForm->input('Empresa.razao_social', array('value' => $grupo_economico['Empresa']['razao_social'], 'class' => 'input-xlarge', 'label' => 'Empresa' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Empresa.codigo_documento', array('value' => $grupo_economico['Empresa']['codigo_documento'], 'class' => 'input-xlarge', 'label' => 'CNPJ' , 'readonly' => true, 'type' => 'text')); ?>
	<div style="clear: both;"></div>
</div>

<div class='inline well' id="parametros">
	<img src="/portal/img/default.gif" style="padding: 10px;">
	Carregando parametrizações do pedido...
</div>

<div id="caminho-pao"></div>

<?php if(isset($grupo_economico['cliente']) && count($grupo_economico['cliente'])) : ?>
	<?php echo $this->BForm->create('PedidosExames', array('url' => array('controller' => 'pedidos_exames', 'action' => 'selecionar_fornecedores_grupo', $this->passedArgs[0]))); ?>
		<?php foreach($grupo_economico['cliente'] as $key_cliente => $cliente) : ?>
		
			<div style="clear: both;"><br /><br /></div>
			<div class="inline" style="border: 2px dashed #CCC; padding: 10px;">
				<h4><?php echo $cliente['Cliente']['codigo']; ?> - <?php echo $cliente['Cliente']['razao_social']; ?></h4>
				<h5><?php echo $cliente['ClienteEndereco']['cidade'] . " / " . $cliente['ClienteEndereco']['estado_abreviacao']; ?></h5>

				<div class="inline well" style="background: #C3DDF7; font-weight: bold; box-shadow: 0px 6px 10px 6px #888;">
					<h4 style="color: #888;">Endereço Referência p/ Busca de Fornecedores:</h4>
					<?php echo $this->BForm->input('PedidosExames.' . $key_cliente . '.endereco', array('value' => $cliente['ClienteEndereco']['logradouro'], 'class' => 'input-xlarge', 'label' => 'Endereço', 'readonly' => true, 'type' => 'text')); ?>
					<?php echo $this->BForm->input('PedidosExames.' . $key_cliente . '.numero', array('value' => $cliente['ClienteEndereco']['numero'], 'class' => 'input-small', 'label' => 'Número' , 'readonly' => true, 'type' => 'text')); ?>
					<?php echo $this->BForm->input('PedidosExames.' . $key_cliente . '.cidade', array('value' => $cliente['ClienteEndereco']['cidade'], 'class' => 'input-large', 'label' => 'Cidade' , 'readonly' => true, 'type' => 'text')); ?>
					<?php echo $this->BForm->input('PedidosExames.' . $key_cliente . '.estado', array('value' => $cliente['ClienteEndereco']['estado_descricao'], 'class' => 'input-small', 'label' => 'Estado' , 'readonly' => true, 'type' => 'text')); ?>
					<?php echo $this->BForm->input('PedidosExames.' . $key_cliente . '.raio', array('value' => ($raio ? $raio : '30'), 'class' => 'input-small', 'label' => 'Raio de Busca', 'type' => 'text', 'id' => "raio_{$key_cliente}")); ?>
					
					<div style="clear: both;"></div>
					<a href="javascript:void(0);" onclick="manipula_modal('modal_selecao_endereco_<?php echo $key_cliente; ?>', 1);" class="btn btn-alert"> Trocar Endereço </a>
					<a href="javascript:void(0);" onclick="atualiza_lista_fornecedores( '<?php echo $codigo_grupo_economico; ?>', '<?php echo $key_cliente; ?>', 0);" class="btn btn-primary">Refazer Busca!</a>			
				</div>
				<div class="lista_cliente" style="padding: 0 15px; margin-top: -5px;" id="lista_fornecedores_<?php echo $key_cliente; ?>"></div>
			</div>

			<div class="modal fade" id="modal_selecao_endereco_<?php echo $key_cliente; ?>" data-backdrop="static" style="width: 85%; left: 8%; top: 15%; margin: 0 auto;">
				<div class="modal-dialog modal-lg" style="position: static;">
					<div class="modal-content">
						<div class="modal-header">
							<div class="msg_error" style="position: absolute;display: none;">
								<div class="alert alert-error">
									<p>Não foi possível gravar os dados, verifique os erros abaixo, os campos com ( * ) deve ser preenchidos!!!</p>
								</div>
							</div>
							<h4 class="modal-title" id="gridSystemModalLabel" style="line-height: 0;">Seleção de Parametros para Inclusão de Pedidos de Exames</h4>
						</div>
				    	<div class="modal-body" style="max-height: 100%;">
				    		<div class="row-fluid">
				    			<div class="span6">
									<input type="hidden" name="grava_endereco" value="0" />
			
									<div class="input-group input" style="margin-left: 0;">
										<label style="line-height: 30px;">ENDEREÇO PARA REALIZAÇÃO DOS EXAMES: </label>
										
										<div style="clear: both;"></div>							
										<div class="control-group ">
											<div class="input-prepend">
												<span class="add-on">Cep</span>
												<input value="<?php echo $cliente['ClienteEndereco']['cep']; ?>" name="cep" id="cep_<?php echo $key_cliente; ?>" type="text" onchange="$('#pesquisa_cep_0').show(); " class="formata-cep" onchange="$('#pesquisa_cep_<?php echo $key_cliente; ?>').show();" style="width: 100px;">
											</div>
										</div>
										<div class="control-group ">
							    			<img src="/portal/img/default.gif" id="carregando_cep_<?php echo $key_cliente; ?>" style="padding: 10px 0 0 10px; display: none;">
							    			<label style="float: left; padding: 5px 0; font-size: 10px;" id="pesquisa_cep_<?php echo $key_cliente; ?>">
							    				<a href="javascript:void(0);" onclick="buscaCep(false, <?php echo $key_cliente; ?>); $('#pesquisa_cep_<?php echo $key_cliente; ?>').hide(); $('#carregando_cep_<?php echo $key_cliente; ?>').show();">COMPLETAR ENDEREÇO</a>
							    			</label>
										</div>
										<div style="clear: both;"></div>
										<div class="control-group ">
											<div class="input-prepend">
												<span class="add-on" style="text-align: left;">Estado</span>
												<input value="<?php echo $cliente['ClienteEndereco']['estado_descricao']; ?>" name="estado" style="width: 55px;" type="text">
											</div>
										</div>
										<div class="control-group ">
											<div class="input-prepend" style="width: 100px; text-align: left;">
												<span class="add-on">Cidade</span>
												<input value="<?php echo $cliente['ClienteEndereco']['cidade']; ?>" name="cidade" style="width: 110px;" type="text">
											</div>
										</div>
										<div class="control-group ">
											<div class="input-prepend">
												<span class="add-on" style="text-align: left;">Logradouro</span>
												<input value="<?php echo $cliente['ClienteEndereco']['logradouro']; ?>" name="endereco" id="endereco_<?php echo $key_cliente; ?>" style="width: 230px;" type="text" onchange="atualiza_coordenadas(<?php echo $key_cliente; ?>);">
											</div>					
										</div>
										<div class="control-group ">
											<div class="input-prepend" style="width: 100px; text-align: left;">
												<span class="add-on">Número</span>
												<input value="<?php echo $cliente['ClienteEndereco']['numero']; ?>" name="numero" style="width: 55px;" type="text" onchange="atualiza_coordenadas(<?php echo $key_cliente; ?>);">
											</div>
										</div>
										<div style="clear: both;"></div>
										<div class="control-group">
											<div class="input-prepend">
												<span class="add-on">Latitude</span>
												<input name="latitude" style="width: 150px;" type="text" value="<?php echo $cliente['ClienteEndereco']['latitude']; ?>" />
											</div>							
											<div class="input-prepend">
												<span class="add-on">Longitude</span>
												<input name="longitude" style="width: 155px;" type="text" value="<?php echo $cliente['ClienteEndereco']['longitude']; ?>" />
											</div>							
										</div>
										<div style="clear: both;"></div>
									</div>
				    			</div>
				    		
								<div class="span6" style="background: #EFEFEF; margin-bottom: 15px;">
									<div id="canvas_mapa_<?php echo $key_cliente; ?>"></div>

									<input type="hidden" id="ambiente_tipo_mapa" value="<?php echo Ambiente::TIPO_MAPA; ?>" />

									<?php
									if(Ambiente::TIPO_MAPA == 1) {
									?>
										<script src="https://maps.googleapis.com/maps/api/js?sensor=false&key=<?php echo Ambiente::getGoogleKey(1); ?>">
										<script type="text/javascript">
											$(function() {
												if (typeof(window.google) != 'undefined') {
													var map_coords = new google.maps.LatLng('<?php echo $cliente['endereco_busca']['parametros']['latitude']; ?>', '<?php echo $cliente['endereco_busca']['parametros']['longitude']; ?>');
													var map_config = { zoom: 4, center: map_coords, mapTypeId: google.maps.MapTypeId.ROADMAP };
													
													map = new google.maps.Map(document.getElementById('canvas_mapa_<?php echo $key_cliente; ?>'), map_config);
													
													var marker_regulador_image = new google.maps.MarkerImage("/portal/img/marker/blue.png", new google.maps.Size(32, 32), new google.maps.Point(0, 0), new google.maps.Point(16, 32));
													var marker_filtro_image = new google.maps.MarkerImage("/portal/img/marker/red-pushpin.png", new google.maps.Size(32, 32), new google.maps.Point(0, 0), new google.maps.Point(16, 32));
													dado = new google.maps.LatLng('<?php echo $cliente['endereco_busca']['parametros']['latitude']; ?>', '<?php echo $cliente['endereco_busca']['parametros']['longitude']; ?>');
													
													map_marker = new google.maps.Marker({ position: dado, map: map, icon: marker_filtro_image,});
													map.setCenter(dado);
													map.setZoom(14);
												} else {
													var html  = '<div class="alert alert-error">';
													html += '    <h4>Erro na api do googlemaps</h4>';
													html += '    <h5>Verifique as suas configurações de proxy, ou se o script da api está carregado corretamente.</h5>';
													html += '    </div>';
													
													$("#canvas_mapa_<?php echo $key_cliente; ?>").html(html);
												}
											});
										</script>

									<?php
									}
									else if(Ambiente::TIPO_MAPA == 2) {

									 	$latitude_min = null;
									    $latitude_max = null;
									    $longitude_min = null;
									    $longitude_max = null;
									        
									            
									    $latitude = 0;
									    if(!empty($cliente['ClienteEndereco']['latitude'])){
									        $latitude = $cliente['ClienteEndereco']['latitude'];
									    }

									    $longitude = 0;
									    if(!empty($cliente['ClienteEndereco']['longitude'])){
									        $longitude = $cliente['ClienteEndereco']['longitude'];
									    }

									    $latitude_min    = $latitude - ($raio / 111.18);
									    $latitude_max    = $latitude + ($raio / 111.18);
									    $longitude_min   = $longitude - ($raio / 111.18);
									    $longitude_max   = $longitude + ($raio / 111.18);
									    
									    $mapOptions = array(
									            'title' => $cliente['Cliente']['razao_social'],
									            'polygon_string' => null, 
									            'latitude_center' => $latitude,
									            'longitude_center' => $longitude,
									            'rectangle' => array(
									                'lat_min' => $latitude_min, 
									                'lat_max' => $latitude_max, 
									                'lng_min' => $longitude_min, 
									                'lng_max' => $longitude_max
									            ),
									            'polygon_input' => 'ClienteEnderecoPoligono',
									            'latitude_input' => 'ClienteEnderecoLatitude',
									            'longitude_input' => 'ClienteEnderecoLongitude',
									            'range_input' => 'ClienteEnderecoRaio',
									            'width' => '100%',
									            'height' => '340px',
									            'style' => 'background: none repeat scroll 0% 0% rgb(229, 227, 223); position: relative;'
									        ); 
									    
									    echo $this->Mapa->mapaClientes($mapOptions);
									
									}//fim tipo mapa

									?>

								</div>
								
							</div>
							<div class="form-actions center" id="rodape_botoes_<?php echo $key_cliente; ?>">
								<a href="javascript:void(0);" onclick="manipula_modal('modal_selecao_endereco_<?php echo $key_cliente; ?>', 0);" class="btn btn-alert btn-lg">
									<i class="glyphicon glyphicon-share"></i> CANCELAR
								</a>
															
								<a href="javascript:void(0);" onclick="gravar_informacoes(<?php echo $codigo_grupo_economico; ?>, <?php echo $key_cliente; ?>);" class="btn btn-success btn-lg">
									<i class="glyphicon glyphicon-share"></i> SALVAR
								</a>
							</div>				
				    	</div>
				    </div>
				</div>
			</div>

		<?php endforeach; ?>
		<div class='form-actions well'>
			<a href="/portal/pedidos_exames/inclusao_em_massa/<?php echo $this->passedArgs[0]; ?>" class="btn">Voltar</a>
			<a href="javascript:void(0);" onclick="gerarPedido(this);" class="btn btn-primary">Avançar</a>
		</div>	
	<?php echo $this->BForm->end(); ?>
	
<?php else:?>
	<div class="alert">Nenhum fornecedor foi encontrado no raio de <?php echo $raio; ?> Km do endereço selecionado.</div>
<?php endif;?>

<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function() {
		
		$(".modal").css("z-index", "-999");
		setup_mascaras();
		
		$("#caminho-pao").load("/portal/pedidos_exames/caminho_pao/2");	
		
		atualiza_parametros("'.$codigo_grupo_economico.'");
		atualiza_lista_fornecedores_por_cliente("'.$codigo_grupo_economico.'");

	});

	//manda o submit para a controller
	function gerarPedido(elemento) {

		$(elemento).html("<img src=\"/portal/img/default.gif\">");

		$("#PedidosExamesSelecionarFornecedoresGrupoForm").submit();

	}
		
	function atualiza_lista_fornecedores_por_cliente(codigo_grupo_economico) {
		$(".lista_cliente").each(function(i, element) {
			codigo_cliente = $(element).attr("id").replace("lista_fornecedores_", "");
			atualiza_lista_fornecedores(codigo_grupo_economico, codigo_cliente, 1);
		});
	}
		
	function atualiza_parametros(codigo_grupo_economico) {
		$("#parametros").load("/portal/pedidos_exames/carrega_parametros/" + codigo_grupo_economico + "/1");
	}		
		
	function atualiza_lista_fornecedores(codigo_grupo_economico, codigo_cliente, por_cliente) {
		raio = $("#raio_" + codigo_cliente).val();
		$.ajax({
	        type: "GET",
			url: "/portal/pedidos_exames/lista_fornecedores_por_cliente/" + codigo_grupo_economico + "/" + codigo_cliente + "/" + raio,
	        dataType: "html",
	        beforeSend: function() {
				if(por_cliente) {
					$("#lista_fornecedores_" + codigo_cliente).html("<div class=\"well\"><img src=\"/portal/img/default.gif\" style=\"padding: 10px;\"> Buscando fornecedores para os exames selecionados...</div>");
				} else {
					bloquearDiv($("#lista_fornecedores_" + codigo_cliente));
				}
			},
	        success: function(conteudo) {
				$("#lista_fornecedores_" + codigo_cliente).html(conteudo);
	        },
	        complete: function() {
		
			}
	    });
	}		
		
	function buscaCep(nao_desabilita, codigo_cliente) {
		var cepCliente = $.trim($("#cep_" + codigo_cliente).val());
		
		if(cepCliente != "") {
			cepCliente = cepCliente.replace("-", "");
			
			if(cepCliente.length == 8) {
			    $.ajax({
			        type: "POST",
			        url: "/portal/enderecos/buscar_endereco_cep/" + cepCliente,
			        dataType: "json",
			        beforeSend: function() {
						$("#carregando_cep_" + codigo_cliente).show();
		
						$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"cep\"]").prop("disabled", true);
						$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"endereco\"]").prop("disabled", true);
						$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"numero\"]").prop("disabled", true);
						$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"estado\"]").prop("disabled", true);
						$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"cidade\"]").prop("disabled", true);
						$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"latitude\"]").prop("disabled", true);
						$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"longitude\"]").prop("disabled", true);
		
					},
			        success: function(json) {
			    		if(json.VEndereco) {
							$("#endereco_" + codigo_cliente).val(json.VEndereco.endereco_tipo + " " + json.VEndereco.endereco_logradouro);
							$("#estado_" + codigo_cliente).val(json.VEndereco.endereco_codigo_estado);
		
							buscaCidade(json.VEndereco.endereco_codigo_estado, json.VEndereco.endereco_codigo_cidade, nao_desabilita, codigo_cliente);
							$("#codigo_endereco_" + codigo_cliente).val(json.VEndereco.endereco_codigo);
		
							$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"cidade\"]").val(json.VEndereco.endereco_cidade);
							$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"estado\"]").val(json.VEndereco.endereco_estado);

							atualiza_coordenadas(codigo_cliente);
			    		} else {
			    			$("carregando_cep_" + codigo_cliente).hide();
			    			alert("Não foi possível completar o endereço com o CEP informado!");
			    		}
			        },
			        complete: function() {
			        	$("#carregando_cep_" + codigo_cliente).hide();
			        	$("#pesquisa_cep_" + codigo_cliente).show();
						$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"cep\"]").prop("disabled", false);
						$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"endereco\"]").prop("disabled", false);
						$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"numero\"]").prop("disabled", false);
						$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"estado\"]").prop("disabled", false);
						$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"cidade\"]").prop("disabled", false);
						$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"latitude\"]").prop("disabled", false);
						$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"longitude\"]").prop("disabled", false);
					}
			    });
			} else if(cepCliente.length > 0) {
				alert("Cep inválido");
			}			
		}		
	}	
		
	function atualiza_coordenadas(codigo_cliente) {
		
		logradouro = $("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"endereco\"]").val();
		numero = $("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"numero\"]").val();
		cidade = $("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"cidade\"]").val();
		estado = $("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"estado\"]").val();

		$.ajax({
			url: baseUrl + "clientes_enderecos/busca_lat_log/"+Math.random(),
			type: "post",
			dataType: "json",
			data: { "endereco": logradouro+", "+ numero +" - "+ cidade + " - " +estado},
			beforeSend: function() { },
			success: function(data) {
				if (data == 0){
					atualiza_coordenadas_sem_numero(codigo_cliente);
				}
				else {
					$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"latitude\"]").val(data.latitude);
					$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"longitude\"]").val(data.longitude);
					atualiza_mapa( $("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"latitude\"]").val(), $("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"longitude\"]").val(), codigo_cliente);
				}
			},
			complete: function() {

			}
		});
	}

	function atualiza_coordenadas_sem_numero(codigo_cliente) {

		logradouro = $("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"endereco\"]").val();

		$.ajax({
			url: baseUrl + "clientes_enderecos/busca_lat_log/"+Math.random(),
			type: "post",
			dataType: "json",
			data: { "endereco": logradouro},
			beforeSend: function() { },
			success: function(data) {
				if (data == 0){
					alert("O endereço inserido é inválido!");
					$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"latitude\"]").val(0);
					$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"longitude\"]").val(0);
				}
				else {
					$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"latitude\"]").val(data.latitude);
					$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"longitude\"]").val(data.longitude);
					atualiza_mapa( $("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"latitude\"]").val(), $("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"longitude\"]").val(), codigo_cliente);
				}
			},
			complete: function() {

			}
		});
	}
		
	function buscaCidade(idEstado, idCidade, nao_desabilita, codigo_cliente) {
		
		$.ajax({
	        type: "POST",
	        url: "/portal/enderecos/carrega_combo_cidade/" + idEstado,
	        dataType: "html",
	        beforeSend: function() { 
	        	$("#carregando_cidade_" + codigo_cliente).show();
	        },
	        success: function(retorno) {
	        	$("#cidade_" + codigo_cliente).html(retorno);
		
				if(idCidade) {
					$("#cidade_" + codigo_cliente).val(idCidade)
				}
	        },
	        complete: function() { 
				$("#carregando_cidade_" + codigo_cliente).hide();
			
				if(!nao_desabilita) {	
					$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"cep\"]").prop("disabled", false);
					$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"endereco\"]").prop("disabled", false);
					$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"numero\"]").prop("disabled", false);
					$("#modal_selecao_endereco_" + codigo_cliente + " select[name=\"estado\"]").prop("disabled", false);
					$("#modal_selecao_endereco_" + codigo_cliente + " select[name=\"cidade\"]").prop("disabled", false);
					$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"latitude\"]").prop("disabled", false);
					$("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"longitude\"]").prop("disabled", false);
				}
		
				$("#carregando_cep_" + codigo_cliente).hide();
				$("#pesquisa_cep_" + codigo_cliente).show();
	        }
	    });		
	}
		
	function atualiza_mapa(latitude, longitude, codigo_cliente) {

		if($("#ambiente_tipo_mapa").val() == 1) {

			// console.log("opa googlemaps");

			var map_coords = new google.maps.LatLng(latitude, longitude);
			var map_config = { zoom: 4, center: map_coords, mapTypeId: google.maps.MapTypeId.ROADMAP };
			
			map = new google.maps.Map(document.getElementById("canvas_mapa_" + codigo_cliente), map_config);
			
			var marker_regulador_image = new google.maps.MarkerImage("/portal/img/marker/blue.png", new google.maps.Size(32, 32), new google.maps.Point(0, 0), new google.maps.Point(16, 32));
			var marker_filtro_image = new google.maps.MarkerImage("/portal/img/marker/red-pushpin.png", new google.maps.Size(32, 32), new google.maps.Point(0, 0), new google.maps.Point(16, 32));
			dado = new google.maps.LatLng(latitude, longitude);
			
			map_marker = new google.maps.Marker({ position: dado, map: map, icon: marker_filtro_image,});
			map.setCenter(dado);
			map.setZoom(13);
		
		}
		else if($("#ambiente_tipo_mapa").val() == 2) {

			Mapa.Client.removeMarker("", "ErroCallback");

			Mapa.Client.addMarkerBallon(longitude, latitude, "https://portal.rhhealth.com.br/portal/img/marker/blue.png", "Endereco", undefined, undefined, undefined, undefined, undefined, undefined, undefined, undefined, "ErroCallback", true, undefined, undefined);

			Mapa.Client.setCenter(longitude, latitude,"",12);
		}
	}

	function buscaCidades(element, codigo_cliente) {
		buscaCidade($(element).val(), codigo_cliente);
	}
		
	function gravar_informacoes(codigo_grupo_economico, codigo_cliente) {
		
		cep = $("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"cep\"]").val();
		endereco = $("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"endereco\"]").val();
		numero = $("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"numero\"]").val();
		codigo_estado = $("#modal_selecao_endereco_" + codigo_cliente + " select[name=\"estado\"]").val();
		codigo_cidade = $("#modal_selecao_endereco_" + codigo_cliente + " select[name=\"cidade\"]").val();

		// estado = $("#modal_selecao_endereco_" + codigo_cliente + " select[name=\"estado\"] option:selected").text();
		// cidade = $("#modal_selecao_endereco_" + codigo_cliente + " select[name=\"cidade\"] option:selected").text();
		
		estado = $("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"estado\"]").val();
		cidade = $("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"cidade\"]").val();

		latitude = $("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"latitude\"]").val();
		longitude = $("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"longitude\"]").val();
		codigo_endereco = $("#modal_selecao_endereco_" + codigo_cliente + " input[name=\"codigo_endereco\"]").val();

		data = "codigo_grupo_economico=" + codigo_grupo_economico + "&codigo_cliente=" + codigo_cliente + "&cep=" + cep + "&endereco=" + endereco + "&numero=" + numero + "&estado=" + estado + "&cidade=" + cidade + "&codigo_endereco=" + codigo_endereco + "&latitude=" + latitude + "&longitude=" + longitude + "&codigo_cidade=" + codigo_cidade + "&codigo_estado=" + codigo_estado;
		
		var backup_botoes = $("#rodape_botoes_" + codigo_cliente).html();
		$.ajax({
	        type: "POST",
			url: "/portal/pedidos_exames/atualiza_parametros_endereco_busca_fornecedores",
	        dataType: "json",
			data: data,
	        beforeSend: function() {
				$("#rodape_botoes_" + codigo_cliente).html("<img src=\"/portal/img/default.gif\" style=\"padding: 10px;\"> Salvando nova parametrização de endereço... ");
			},
	        success: function(json) {
		
	        	if(json == 1) {
					atualiza_lista_fornecedores(codigo_grupo_economico, codigo_cliente, 0);
					manipula_modal("modal_selecao_endereco_" + codigo_cliente, 0);
					$("#PedidosExames" + codigo_cliente + "Endereco").val(endereco);
					$("#PedidosExames" + codigo_cliente + "Numero").val(numero);
					$("#PedidosExames" + codigo_cliente + "Cidade").val(cidade);
					$("#PedidosExames" + codigo_cliente + "Estado").val(estado);
				} else {
					alert("latitude e longitude inválidas");
				}
	        },
	        complete: function() {
				$("#rodape_botoes_" + codigo_cliente).html(backup_botoes);
			}
	    });
	}
		
	function manipula_modal(id, mostra) {
		if(mostra) {
			$(".modal").css("z-index", "-1");
			$("#" + id).css("z-index", "1050");
			$("#" + id).modal("show");
		} else {
			$("#" + id).css("z-index", "-1");
			$("#" + id).modal("hide");
		}
	}
		
'); ?>