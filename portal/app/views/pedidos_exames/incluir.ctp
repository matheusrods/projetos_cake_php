	<style>
		.control-group {
			float: left;
			margin-right: 15px;
			margin-bottom: 3px;
		}

		.form-actions {
			margin-top: 0;
			margin-bottom: 0;
			padding: 10px;
		}

		select {
			width: auto;
		}

		input[type="radio"],
		input[type="checkbox"] {
			margin: 0;
		}

		hr {
			margin: 0 0 8px 0;
		}

		label {
			margin-bottom: 0;
			line-height: 18px;
		}

		;
	</style>

	<div class='inline well'>
		<?php echo $this->BForm->input('Empresa.razao_social', array('value' => $dados_cliente_funcionario['Empresa']['razao_social'], 'class' => 'input-xlarge', 'label' => 'Empresa', 'readonly' => true, 'type' => 'text')); ?>
		<?php echo $this->BForm->input('Cliente.razao_social', array('value' => $dados_cliente_funcionario['Cliente']['razao_social'], 'class' => 'input-xlarge', 'label' => 'Unidade', 'readonly' => true, 'type' => 'text')); ?>
		<?php echo $this->BForm->input('Empresa.codigo_documento', array('value' => $dados_cliente_funcionario['Empresa']['codigo_documento'], 'class' => 'input-xlarge', 'label' => 'CNPJ', 'readonly' => true, 'type' => 'text')); ?>
		<?php echo $this->BForm->input('Setor.descricao', array('value' => $dados_cliente_funcionario['ClienteFuncionario']['setor'], 'class' => 'input-xlarge', 'label' => 'Setor', 'readonly' => true, 'type' => 'text')); ?>
		<div class="clear"></div>
		<?php echo $this->BForm->input('Funcionario.nome', array('value' => $dados_cliente_funcionario['Funcionario']['nome'], 'class' => 'input-xlarge', 'label' => 'Funcionario', 'readonly' => true, 'type' => 'text')); ?>
		<?php echo $this->BForm->input('Funcionario.cpf', array('value' => $dados_cliente_funcionario['Funcionario']['cpf'], 'class' => 'input-xlarge', 'label' => 'CPF', 'readonly' => true, 'type' => 'text')); ?>
		<?php echo $this->BForm->input('Funcionario.data_nascimento', array('value' => $dados_cliente_funcionario['Funcionario']['data_nascimento'], 'class' => 'input-xlarge', 'label' => 'Data nascimento', 'readonly' => true, 'type' => 'text')); ?>
		<?php echo $this->BForm->input('Cargo.descricao', array('value' => $dados_cliente_funcionario['ClienteFuncionario']['cargo'], 'class' => 'input-xlarge', 'label' => 'Cargo', 'readonly' => true, 'type' => 'text')); ?>
		<div class="clear"></div>

	</div>

	<?php if (count($dados_cliente_funcionario['Riscos'])) : ?>
		<div class='inline well'>
			<h5> Riscos:</h5>
			<span style="font-weight: normal;">
				<p><?php echo implode("</p><p> - ", $dados_cliente_funcionario['Riscos']); ?></p>
			</span>
			<div class="clear"></div>
		</div>
	<?php endif; ?>

	<div class='inline well' id="parametros">
		<img src="/portal/img/default.gif" style="padding: 10px;">
		Carregando parametrizações do pedido...
	</div>

	<div id="caminho-pao"></div>

	<div class='actionbar-right'>
		<a href="javascript:void(0);" onclick="manipula_modal('modal_exames_assinatura', 1);" class="btn btn-success"><i class="icon-plus icon-white"></i> Incluir Exame</a>
	</div>

	<div id="listagem">
		<table class="table table-striped" style="border: 2px solid #EFEFEF;">
			<thead>
				<tr>
					<th class="input-xxlarge">Exame</th>
					<th class="input-xlarge">Tipo</th>
					<th class="input-small" style="text-align: center;">Retirar</th>
				</tr>
			</thead>
			<!-- 
	        <tbody>
	        	<?php if (count($dados_exames)) : ?>
		        	<?php foreach ($dados_exames as $key => $exame) : ?>
		        	
						<?php if (empty($exame['valor'])) : ?>
							<?php $color = '#FFDBDB'; ?>
							<?php $msg_assinatura = '<div class="help-block error-message" style="font-size:12px; color:#b94a48;">Exame não tem ASSINATURA NO CONTRATO!</div>'; ?>
						<?php else :  ?>
							<?php $color = ''; ?>
							<?php $msg_assinatura = ''; ?>
						<?php endif; ?>
							        	
			            <tr>
			                <td class="input-xlarge" style="background: <?php echo !$exame['valor'] ? '#FFDBDB' : ''; ?>;">
			                	<?php echo $exame['descricao'] . ' ' . $msg_assinatura; ?>
			                </td>
			                <td class="input-xlarge" style="background: <?php echo !$exame['valor'] ? '#FFDBDB' : ''; ?>;">

			                	<?php if (isset($exame['tipo']) && ($exame['tipo'] == '1')) : ?>
			                		<?php echo $this->BForm->input('codigo_tipos_exames_pedidos', array('label' => false, 'class' => 'form-control uf input-large', 'style' => 'text-transform: uppercase;', 'default' => (isset($exame['tipo']) && $exame['tipo'] ? $exame['tipo'] : NULL), 'options' => $lista_tipos_exames_pcmso, 'disabled' => 'disabled')); ?>
			                	<?php else : ?>
			                		<?php echo $this->BForm->input('codigo_tipos_exames_pedidos', array('label' => false, 'class' => 'form-control uf input-large', 'style' => 'text-transform: uppercase;', 'default' => (isset($exame['tipo']) && $exame['tipo'] ? $exame['tipo'] : ''), 'options' => $lista_tipos_exames_outro, 'onchange' => 'atualiza_tipo(this, ' . $key . ');')); ?>
			                	<?php endif; ?>
		                				                	
			                </td>
			                <td class="input-medium" style="background: <?php echo !$exame['valor'] ? '#FFDBDB' : ''; ?>; text-align: center;">
			                	<a href="javascript:void(0);" onclick="removeExame(<?php echo $key; ?>, <?php echo $codigo_cliente_funcionario; ?>, this); " class="icon-trash">
			                	</a>
			                </td>
			            </tr>
		        	<?php endforeach; ?>	        	
	        	<?php endif; ?> 
	    	</tbody>
	    	 -->
			<tbody id="carregando_exames" style="display: none;">
				<tr>
					<td colspan="4">
						<img src="/portal/img/default.gif" style="padding: 10px;">
						Aguarde, carregando os exames...
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="form-actions well">
		<span id="botao_avancar" style="display: <?php echo count($dados_exames) ? 'block' : 'none'; ?>;">
			<a href="/portal/pedidos_exames/lista_pedidos/<?php echo $codigo_cliente_funcionario; ?>" class="btn btn-default">Voltar</a>
			<a href="javascript:void(0);" onclick="valida_proxima_etapa(<?php echo $codigo_cliente_funcionario; ?>)" class="btn btn-primary">Avançar</a>
		</span>
		<span id="carregando" style="display: none;">
			<img src="/portal/img/default.gif" style="padding: 10px;" />
			Gravando as preferências dos serviços...
		</span>
	</div>

	<div class="modal fade" id="modal_exames_assinatura">
		<div class="modal-dialog modal-lg" style="position: static;">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="gridSystemModalLabel">Serviços Disponíveis na Assinatura:</h4>
					<div class="clear"></div>
				</div>
				<div class="modal-body" style="height: 600px; overflow: scroll;">
					<?php if (count($produtos_servicos)) : ?>
						<table style="width: 100%" class="table-striped">
							<?php foreach ($produtos_servicos as $key_produto => $produto) : ?>
								<tr>
									<td style="background: #CCC; text-align: center;" colspan="3">
										<b>Adiciona Exame ao Pedido:</b>
									</td>
								</tr>
								<?php foreach ($produto['ClienteProdutoServico2'] as $key_servico => $servico) : ?>
									<tr>
										<td style="width: 110px; text-align: center;">
											<?php if (isset($servico['Servico']['cadastrado']) && ($servico['Servico']['cadastrado'] == 'nao')) : ?>
												<span style="font-size: 9px; color: red;">(não cadastrado)</span>
											<?php else : ?>
												<input class="checkbox_exames" type="checkbox" value="<?php echo $servico['codigo']; ?>" name="tabela.<?php echo $key_servico; ?>.exame">
											<?php endif; ?>
										</td>
										<td>
											<?php echo utf8_encode(strtoupper($servico['Servico']['descricao'])); ?>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php endforeach; ?>
						</table>
						<div class='actionbar-right'>
							<label><a href="javascript:void(0);" onclick="adicionaExames(<?php echo $this->passedArgs[0]; ?>);" class="btn btn-success btn-sm right" title="Incluir">Incluir no Pedido!</a></label>
						</div>
					<?php else : ?>
						<div class="alert alert-danger">Este cliente não possui assinatura de serviços.</div>
						<div class='actionbar-right'>
							<label><a href="javascript:void(0);" onclick="manipula_modal('modal_exames_assinatura', 0);" class="btn btn-danger btn-sm right" title="Incluir">Fechar</a></label>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modal_carregando">
		<div class="modal-dialog modal-sm" style="position: static;">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="gridSystemModalLabel">Aguarde, carregando informações...</h4>
				</div>
				<div class="modal-body">
					<img src="/portal/img/ajax-loader.gif" style="padding: 10px;">
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modal_selecao_parametros" data-backdrop="static" style="width: 85%; left: 8%; top: 15%; margin: 0 auto;">
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
							<span><b>TIPO PEDIDO EXAME: (*) </b></span>
							<div class="input-group input" style="font-size: 16px;">
								<div class="muda_cor">
									<?php echo $this->BForm->input('tipo', array('options' => array('exame_admissional' => 'Exame Admissional', 'exame_periodico' => 'Periódico', 'exame_demissional' => 'Demissional', 'exame_retorno' => 'Retorno ao Trabalho', 'exame_mudanca' => 'Mudança de Riscos Ocupacionais', 'pontual' => 'Pontual'), 'label' => true, 'type' => 'radio', 'div' => true, 'style' => '')) ?> <br />
								</div>
								<div style="clear: both;"></div>
								<div class="msg_error_especifico" style="position:absolute; display: none;">
									<span style="color:#B94A48;font-size:13px">
										<p>Informe o momento da solicitação do exame.</p>
									</span>
								</div>
								<hr />
								<?php echo $this->BForm->input('portador_deficiencia', array('label' => 'Avaliação Portador de Deficiência', 'options' => array('Avaliação Portador de Deficiência'), 'type' => 'checkbox')) ?>

								<div style="clear: both;"></div>
								<hr />
								<span class="span4" style="font-size: 14px; padding-top: 6px; margin: 0;">Data da solicitação:</span>
								<?php echo $this->BForm->input('data_solicitacao', array('span' => 'Data da solicitação', 'class' => 'data', 'style' => 'width: 80px; margin-bottom: 5px;', 'label' => false)); ?>
								<div style="clear: both;"></div>
							</div>
							<hr />

							<input type="hidden" name="codigo_endereco" id="codigo_endereco" value="<?php echo isset($dados_cliente_funcionario['Endereco']['VEndereco']['endereco_codigo']) ? $dados_cliente_funcionario['Endereco']['VEndereco']['endereco_codigo'] : ''; ?>" />
							<input type="hidden" name="grava_endereco" value="0" />


							<div class="input-group input" style="margin-left: 0;">
								<label style="line-height: 30px;">ENDEREÇO PARA REALIZAÇÃO DOS EXAMES: </label>

								<div class="control-group ">
									<div class="input-prepend">
										<span class="add-on" style="float: left; width: 50%">Mudar Endereço?</span>
										<div style="width: 39%; border: 1px solid #CCC; float: left; height: 18px; padding: 5px;">
											<label>
												NÃO <input type="radio" name="data[mudar_endereco]" value="0" checked="checked" onchange="mudar_endereco(this);">
												SIM <input type="radio" name="data[mudar_endereco]" value="1" onchange="mudar_endereco(this);">
											</label>
										</div>
									</div>
								</div>
								<div style="clear: both;"></div>
								<div class="control-group ">
									<div class="input-prepend">
										<span class="add-on">Cep</span>
										<input value="<?php echo $dados_cliente_funcionario['Endereco']['VEndereco']['endereco_cep']; ?>" name="cep" id="cep" type="text" onchange="$('#pesquisa_cep_0').show(); " class="formata-cep" onchange="$('#pesquisa_cep').show();" style="width: 100px;" disabled="disabled">
									</div>
								</div>
								<div class="control-group ">
									<img src="/portal/img/default.gif" id="carregando_cep" style="padding: 10px 0 0 10px; display: none;">
									<label style="float: left; padding: 5px 0; font-size: 10px;" id="pesquisa_cep">
										<a href="javascript:void(0);" onclick="buscaCep(false); $('#pesquisa_cep').hide(); $('#carregando_cep').show();">COMPLETAR ENDEREÇO</a>
									</label>
								</div>
								<div style="clear: both;"></div>
								<div class="control-group ">
									<div class="input-prepend">
										<span class="add-on" style="text-align: left;">Logradouro</span>
										<input value="<?php echo $dados_cliente_funcionario['Endereco']['VEndereco']['endereco_tipo']; ?> <?php echo $dados_cliente_funcionario['Endereco']['VEndereco']['endereco_logradouro']; ?>" name="endereco" id="endereco" style="width: 230px;" type="text" disabled="disabled">
									</div>
								</div>
								<div class="control-group ">
									<div class="input-prepend" style="width: 100px; text-align: left;">
										<span class="add-on">Número</span>
										<input value="<?php echo $dados_cliente_funcionario['Endereco']['ClienteEndereco']['numero']; ?>" name="numero" style="width: 55px;" type="text" disabled="disabled">
									</div>
								</div>
								<div style="clear: both;"></div>
								<div class="control-group" style="width: 108px;">
									<div class="input-prepend" style="width: 100px; text-align: left;">
										<span class="add-on">Estado</span>
										<select name="estado" onchange="buscaCidades(this)" style="width: 55px;" id="estado" disabled="disabled">
											<option value="">Selecione</optio>
												<?php foreach ($estados as $key => $estado) : ?>
											<option value="<?php echo $key; ?>" <?php echo ($dados_cliente_funcionario['Endereco']['VEndereco']['endereco_codigo_estado'] == $key) ? 'selected="selected"' : ''; ?>><?php echo $estado; ?></option>
										<?php endforeach; ?>
										</select>
									</div>
								</div>
								<div class="control-group">
									<div class="input-prepend" style="width: 100px; text-align: left;">
										<span class="add-on">Cidade</span>
										<select name="cidade" id="cidade" style="width: 220px;" disabled="disabled">
											<?php foreach ($lista_cidades as $key => $cidade) : ?>
												<option value="<?php echo $key; ?>" <?php echo ($dados_cliente_funcionario['Endereco']['VEndereco']['endereco_codigo_cidade'] == $key) ? 'selected="selected" ' : ''; ?>><?php echo $cidade; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<!-- 
				    			<div id="carregando_cidade_0" style="display: none; text-align: left; border: 1px solid #CCCCCC; padding: 8px;">
				    				<img src="/portal/img/ajax-loader.gif" border="0"/>
				    			</div>
				    			 -->
								</div>
								<div style="clear: both;"></div>
							</div>
						</div>

						<div class="span6" style="background: #EFEFEF;">

							<div style="width: 100%; height: 340px; background: none repeat scroll 0% 0% rgb(229, 227, 223); position: relative;" id="canvas_mapa">
							</div>
							'
							<script src="https://maps.googleapis.com/maps/api/js?sensor=false&key=<?php echo Ambiente::getGoogleKey(1); ?>"></script>
							<script type="text/javascript">
								$(function() {
									if (typeof(window.google) != 'undefined') {

										var map_coords = new google.maps.LatLng('<?php echo $dados_cliente_funcionario['Endereco']['ClienteEndereco']['latitude']; ?>', '<?php echo $dados_cliente_funcionario['Endereco']['ClienteEndereco']['longitude']; ?>');
										var map_config = {
											zoom: 4,
											center: map_coords,
											mapTypeId: google.maps.MapTypeId.ROADMAP
										};

										map = new google.maps.Map(document.getElementById('canvas_mapa'), map_config);

										var marker_regulador_image = new google.maps.MarkerImage("/portal/img/marker/blue.png", new google.maps.Size(32, 32), new google.maps.Point(0, 0), new google.maps.Point(16, 32));
										var marker_filtro_image = new google.maps.MarkerImage("/portal/img/marker/red-pushpin.png", new google.maps.Size(32, 32), new google.maps.Point(0, 0), new google.maps.Point(16, 32));
										dado = new google.maps.LatLng('<?php echo $dados_cliente_funcionario['Endereco']['ClienteEndereco']['latitude']; ?>', '<?php echo $dados_cliente_funcionario['Endereco']['ClienteEndereco']['longitude']; ?>');

										map_marker = new google.maps.Marker({
											position: dado,
											map: map,
											icon: marker_filtro_image,
										});
										map.setCenter(dado);
										map.setZoom(13);

									} else {
										var html = '<div class="alert alert-error">';
										html += '    <h4>Erro na api do googlemaps</h4>';
										html += '    <h5>Verifique as suas configurações de proxy, ou se o script da api está carregado corretamente.</h5>';
										html += '    </div>';

										$("#canvas_mapa").html(html);
									}
								});
							</script>
							<div class="span12">
								<div class="span6">
									<div class="input-prepend">
										<span class="add-on">Latitude</span>
										<input name="latitude" style="width: 120px;" type="text" value="<?php echo $dados_cliente_funcionario['Endereco']['ClienteEndereco']['latitude']; ?>" disabled="disabled" />
									</div>
								</div>
								<div class="span6">
									<div class="input-prepend">
										<span class="add-on">Longitude</span>
										<input name="longitude" style="width: 120px;" type="text" value="<?php echo $dados_cliente_funcionario['Endereco']['ClienteEndereco']['longitude']; ?>" disabled="disabled" />
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="form-actions center" id="rodape_botoes">
						<a href="/portal/pedidos_exames/lista_pedidos/<?php echo $codigo_cliente_funcionario; ?>" class="btn btn-default btn-lg"><i class="glyphicon glyphicon-fast-backward"></i> Voltar</a>

						<a href="javascript:void(0);" onclick="gravar_informacoes();" class="btn btn-success btn-lg">
							<i class="glyphicon glyphicon-share"></i> Avançar
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function() {
		$(".modal").css("z-index", "-1");
		
		if("' . $mostra_modal_parametros . '" == "1") {
			manipula_modal("modal_selecao_parametros", 1);
		} else {
			atualiza_parametros("' . $codigo_cliente_funcionario . '");
		}
		
		$("input[name=\"numero\"]").blur(function() {
			atualiza_coordenadas();	
		});
		
		atualiza_coordenadas();
		setup_mascaras();
		setup_datepicker();
		
		// seta etapa 1
		$("#caminho-pao").load("/portal/pedidos_exames/caminho_pao/1");
	});
		
		
	function atualiza_parametros(codigo_cliente_funcionario) {
		$("#parametros").load("/portal/pedidos_exames/carrega_parametros/' . $codigo_cliente_funcionario . '");
	}		
		
	function atualiza_tipo(element, chave) {
		$.ajax({
	        type: "POST",
	        url: "/portal/pedidos_exames/atualiza_tipo/' . $codigo_cliente_funcionario . '",
	        dataType: "json",
	        data: "exame=" + chave + "&codigo_tipo=" + $(element).val(),
			beforeSend: function() {
				$("#botao_avancar").hide();
				$("#carregando").show();
			},
	        success: function(retorno) {
				
				if(retorno == "0")
					alert("tente novamente");
		
	        },
			complete: function() {
				$("#carregando").hide();
				$("#botao_avancar").show();
			}
	    });			
	}
		


	function adicionaExames(codigo_cliente) {
		exames = "";
		$(".checkbox_exames").each(function(i, element_exames_disponiveis) {
			if(element_exames_disponiveis.checked) {
				exames = exames + $(element_exames_disponiveis).val() + ",";
			}
		});
		
		if(exames.length) {
			codigos = exames.substring(0,(exames.length - 1));
			$.ajax({
		        type: "POST",
		        url: "/portal/pedidos_exames/adiciona_mais_exames",
		        dataType: "html",
		        data: "exames=" + codigos + "&codigo_cliente=" + codigo_cliente,
				beforeSend: function() {
					manipula_modal("modal_exames_assinatura", 0);
					manipula_modal("modal_carregando", 1);
				},
		        success: function(conteudo) {
					$("#listagem").html(conteudo);
					$("#botao_avancar").show();
		        },
				complete: function() {
					manipula_modal("modal_carregando", 0);
				}
		    });
		}
	}
		
	function removeExame(chave, codigo_cliente_funcionario, element) {
		$.ajax({
	        type: "POST",
	        url: "/portal/pedidos_exames/remove_exame",
	        dataType: "json",
	        data: "chave=" + chave + "&codigo_cliente_funcionario=" + codigo_cliente_funcionario,
			beforeSend: function() {
				manipula_modal("modal_carregando", 1);
			},
	        success: function(json) {
				if(json) {
					$(element).parents("tr").fadeOut().remove();
				}
	        },
			complete: function() {
				manipula_modal("modal_carregando", 0);
			}
	    });	
	}
		
	function valida_proxima_etapa(codigo_cliente_funcionario) {
		$.ajax({
	        type: "POST",
	        url: "/portal/pedidos_exames/valida_proxima_etapa",
	        dataType: "json",
	        data: "codigo_cliente_funcionario=" + codigo_cliente_funcionario,
			beforeSend: function() {
				manipula_modal("modal_carregando", 1);
			},
	        success: function(json) {
				if(json) {
					window.location = document.location.origin + "/portal/pedidos_exames/selecionar_fornecedores/" + codigo_cliente_funcionario;
				} else {
					manipula_modal("modal_carregando", 0);
					swal({
						type: "error",
						title: "Não é permitido avançar!",
						text: "Não foi selecionado o TIPO DO EXAME, OU Existe exames sem ASSINATURA NO CONTRATO",
						showCancelButton: false
					});
				}
	        },
			complete: function() { }
	    });	
	}		
		
	function buscaCep(nao_desabilita) {
		
		var cepCliente = $.trim($("#cep").val());
		if(cepCliente != "") {
			cepCliente = cepCliente.replace("-", "");
			
			if(cepCliente.length == 8) {
			    $.ajax({
			        type: "POST",
			        url: "/portal/enderecos/buscar_endereco_cep/" + cepCliente,
			        dataType: "json",
			        beforeSend: function() {
						$("#carregando_cep").show();
		
						$("input[name=\"cep\"]").prop("disabled", true);
						$("input[name=\"endereco\"]").prop("disabled", true);
						$("input[name=\"numero\"]").prop("disabled", true);
						$("select[name=\"estado\"]").prop("disabled", true);
						$("select[name=\"cidade\"]").prop("disabled", true);
						$("input[name=\"latitude\"]").prop("disabled", true);
						$("input[name=\"longitude\"]").prop("disabled", true);		
		
					},
			        success: function(json) {
			    		if(json.VEndereco) {
							$("#endereco").val(json.VEndereco.endereco_tipo + " " + json.VEndereco.endereco_logradouro);
							$("#estado").val(json.VEndereco.endereco_codigo_estado);
		
							buscaCidade(json.VEndereco.endereco_codigo_estado, json.VEndereco.endereco_codigo_cidade, nao_desabilita);
		
							$("#codigo_endereco").val(json.VEndereco.endereco_codigo);
		
							atualiza_coordenadas();
			    		} else {
			    			$("carregando_cep").hide();
			    			alert("Cep não encontrado!");
			    		}
			        },
			        complete: function() {
		
					}
			    });
			} else if(cepCliente.length > 0) {
				alert("Cep inválido");
			}			
		}		
	}
		
	function buscaCidade(idEstado, idCidade, nao_desabilita) {
		$.ajax({
	        type: "POST",
	        url: "/portal/enderecos/carrega_combo_cidade/" + idEstado,
	        dataType: "html",
	        beforeSend: function() { 
	        	$("#carregando_cidade").show();
	        },
	        success: function(retorno) {
	        	$("#cidade").html(retorno);
		
				if(idCidade) {
					$("#cidade").val(idCidade)
				}
	        },
	        complete: function() { 
				$("#carregando_cidade").hide();
			
				if(!nao_desabilita) {	
					$("input[name=\"cep\"]").prop("disabled", false);
					$("input[name=\"endereco\"]").prop("disabled", false);
					$("input[name=\"numero\"]").prop("disabled", false);
					$("select[name=\"estado\"]").prop("disabled", false);
					$("select[name=\"cidade\"]").prop("disabled", false);
					$("input[name=\"latitude\"]").prop("disabled", false);
					$("input[name=\"longitude\"]").prop("disabled", false);
				}
		
				$("#carregando_cep").hide();
				$("#pesquisa_cep").show();
	        }
	    });		
	}
		
	function buscaCidades(element) {
		buscaCidade($(element).val());
	}
		
	function gravar_informacoes() {
		codigo_cliente_funcionario = $("input[name=\"data[codigo_cliente_funcionario]\"]").is(":checked");
		
		exame_admissional = $("input#TipoExameAdmissional").is(":checked");
		exame_periodico = $("input#TipoExamePeriodico").is(":checked");
		exame_demissional = $("input#TipoExameDemissional").is(":checked");
		exame_retorno = $("input#TipoExameRetorno").is(":checked");
		exame_mudanca = $("input#TipoExameMudanca").is(":checked");
		pontual       = $("input#TipoPontual").is(":checked");

		portador_deficiencia = $("input#portador_deficiencia").is(":checked");
		data_solicitacao = $("input#data_solicitacao").val();

		// Testa se alguma opção foi escolhida no radio de Momento de Solicitação de Exame
		if(!exame_admissional && !exame_periodico && !exame_demissional && !exame_retorno && !exame_mudanca && !pontual){
			$(".msg_error").css("display","block");
			$(".muda_cor").css("color","#B94A48");
			$(".msg_error_especifico").css("display","block");
			setTimeout(function(){ $(".msg_error").css("display","none"); }, 5000);
			return false;
		}

		grava_endereco = $("input[name=\"grava_endereco\"]").val();
		
		cep = $("input[name=\"cep\"]").val();
		endereco = $("input[name=\"endereco\"]").val();
		numero = $("input[name=\"numero\"]").val();
		codigo_estado = $("select[name=\"estado\"]").val();
		codigo_cidade = $("select[name=\"cidade\"]").val();
		estado = $("select[name=\"estado\"] option:selected").text();
		cidade = $("select[name=\"cidade\"] option:selected").text();
		latitude = $("input[name=\"latitude\"]").val();
		longitude = $("input[name=\"longitude\"]").val();
		codigo_endereco = $("input[name=\"codigo_endereco\"]").val();

		
		data = "exame_admissional=" + exame_admissional + 
			   "&exame_periodico=" + exame_periodico + 
			   "&exame_demissional=" + exame_demissional + 
			   "&exame_retorno=" + exame_retorno + 
			   //"&qualidade_vida=" + qualidade_vida +
			   "&pontual=" + pontual + 
			   "&exame_mudanca=" + exame_mudanca + 
			   "&portador_deficiencia=" + portador_deficiencia + 
			   "&data_solicitacao=" + data_solicitacao +
			   "&cep=" + cep + "&endereco=" + endereco + "&numero=" + numero + "&estado=" + estado + "&cidade=" + cidade + "&codigo_endereco=" + codigo_endereco + "&latitude=" + latitude + "&longitude=" + longitude + "&codigo_cidade=" + codigo_cidade + "&codigo_estado=" + codigo_estado + "&grava_endereco=" + grava_endereco;

		$.ajax({
	        type: "POST",
	        url: "/portal/pedidos_exames/grava_parametros_busca_exames/' . $codigo_cliente_funcionario . '",
	        dataType: "json",
			data: data,
	        beforeSend: function() {
				$("#rodape_botoes").html("<img src=\"/portal/img/default.gif\" style=\"padding: 10px;\"> Carregando exames do PCMSO.");
			},
	        success: function(json) {
	        	if(json == 1) {
					atualiza_lista();
					manipula_modal("modal_selecao_parametros", 0);
				}
	        },
	        complete: function() { 
				atualiza_parametros("' . $codigo_cliente_funcionario . '");
			}
	    });
	}
		
	function atualiza_lista() {
	    $.ajax({
	        type: "GET",
	        url: "/portal/pedidos_exames/atualiza_lista_exames/' . $codigo_cliente_funcionario . '",
	        dataType: "html",
	        beforeSend: function() {
				$("#carregando_exames").show();
			},
	        success: function(conteudo) {
				$("#listagem").html(conteudo);
				manipula_modal("modal_selecao_parametros", 0);
	        },
	        complete: function() {

			}
	    });		
	}
		
	function atualiza_mapa(latitude, longitude) {
		var map_coords = new google.maps.LatLng(latitude, longitude);
		var map_config = { zoom: 4, center: map_coords, mapTypeId: google.maps.MapTypeId.ROADMAP };
		
		map = new google.maps.Map(document.getElementById("canvas_mapa"), map_config);
		
		var marker_regulador_image = new google.maps.MarkerImage("/portal/img/marker/blue.png", new google.maps.Size(32, 32), new google.maps.Point(0, 0), new google.maps.Point(16, 32));
		var marker_filtro_image = new google.maps.MarkerImage("/portal/img/marker/red-pushpin.png", new google.maps.Size(32, 32), new google.maps.Point(0, 0), new google.maps.Point(16, 32));
		dado = new google.maps.LatLng(latitude, longitude);
		
		map_marker = new google.maps.Marker({ position: dado, map: map, icon: marker_filtro_image,});
		map.setCenter(dado);
		map.setZoom(13);
	}
		
	function atualiza_coordenadas() {
		$.ajax({
			url: baseUrl + "clientes_enderecos/buscaXY/" + Math.random(),
			type: "post",
			dataType: "json",
			data: { "codigo_endereco": $("#codigo_endereco").val(), "endereco_numero": $("input[name=\"numero\"]").val() },
			beforeSend: function() { },
			success: function(data) {
				if(data == 0) {
					$("input[name=\"latitude\"]").val(0);
					$("input[name=\"longitude\"]").val(0);
				} else {
					$("input[name=\"latitude\"]").val(data.latitude);
					$("input[name=\"longitude\"]").val(data.longitude);
				}
	
				atualiza_mapa( $("input[name=\"latitude\"]").val(), $("input[name=\"longitude\"]").val() );
			},
			complete: function() {
				
			}
		});
	}
		
	var cep = $("input[name=\"cep\"]").val();
	var endereco = $("input[name=\"endereco\"]").val();
	var numero = $("input[name=\"numero\"]").val();
	var estado = $("input[name=\"estado\"]").val();
	var cidade = $("input[name=\"cidade\"]").val();
	var latitude = $("input[name=\"latitude\"]").val();
	var longitude = $("input[name=\"longitude\"]").val();
		
	function mudar_endereco(element) {

		if($(element).val() == "1") {
			$("input[name=\"cep\"]").prop("disabled", false).val("");
			$("input[name=\"endereco\"]").prop("disabled", false).val("");
			$("input[name=\"numero\"]").prop("disabled", false).val("");
			$("select[name=\"estado\"]").prop("disabled", false).val("");
			$("select[name=\"cidade\"]").html($("<option>", {value: "", text: "Selecione"})).prop("disabled", false);
			$("input[name=\"latitude\"]").prop("disabled", false).val("");
			$("input[name=\"longitude\"]").prop("disabled", false).val("");
			$("input[name=\"grava_endereco\"]").val(1);
		} else {
			$("input[name=\"cep\"]").val(cep).prop("disabled", true);
			$("input[name=\"endereco\"]").val(endereco).prop("disabled", true);
			$("input[name=\"numero\"]").val(numero).prop("disabled", true);
			$("select[name=\"estado\"]").val(estado).prop("disabled", true);
			$("select[name=\"cidade\"]").val(cidade).prop("disabled", true);
			$("input[name=\"latitude\"]").val(latitude).prop("disabled", true);
			$("input[name=\"longitude\"]").val(longitude).prop("disabled", true);
			$("input[name=\"grava_endereco\"]").val(0);
			buscaCep(true);
		}
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