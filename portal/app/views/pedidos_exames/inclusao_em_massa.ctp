<style>
	.control-group {
		float: left;
		margin-right: 15px;
		margin-bottom: 5px;
	}

	select {
		width: auto;
	}

	input[type="radio"],
	input[type="checkbox"] {
		margin: 0;
	}

	hr {
		margin: 0 0 20px 0;
	}

	label {
		margin-bottom: 0
	}

	;
</style>

<?php echo $this->BForm->input('codigos_clientes_funcionarios', array('type' => 'hidden', 'id' => 'codigos_clientes_funcionarios', 'value' => $codigos_clientes_funcionarios)); ?>

<div class='inline well'>
	<?php echo $this->BForm->input('Empresa.razao_social', array('value' => $grupo_economico['Empresa']['razao_social'], 'class' => 'input-xlarge', 'label' => 'Empresa', 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Empresa.codigo_documento', array('value' => $grupo_economico['Empresa']['codigo_documento'], 'class' => 'input-xlarge', 'label' => 'CNPJ', 'readonly' => true, 'type' => 'text')); ?>
	<div style="clear: both;"></div>
</div>

<div class='inline well' id="parametros">
	<img src="/portal/img/default.gif" style="padding: 10px;">
	Carregando parametrizações do pedido...
</div>

<div id="caminho-pao"></div>

<div class='actionbar-right' id="mais_incluir_exame">
	<a href="javascript:void(0);" onclick="manipula_modal('modal_exames_assinatura', 1);" class="btn btn-success"><i class="icon-plus icon-white"></i> Incluir Exame</a>
</div>

<?php foreach ($grupo_economico['cliente'] as $k_cliente => $cliente) : ?>

	<div style="clear: both;"><br /><br /></div>
	<div class="inline" style="border: 2px dashed #CCC; padding: 10px;">

		<h4><?php echo $cliente['Cliente']['codigo']; ?> - <?php echo $cliente['Cliente']['razao_social']; ?></h4>
		<h5><?php echo $cliente['ClienteEndereco']['cidade'] . " / " . $cliente['ClienteEndereco']['estado_abreviacao']; ?></h5>

		<?php foreach ($cliente['cliente_funcionario'] as $k_cliente_funcionario => $funcionario) : ?>

			<div class="inline well" style="background: #A7BACE; font-weight: bold; box-shadow: 0 8px 12px 5px #888;">
				<?php echo $this->BForm->input('Funcionario.nome', array('value' => $funcionario['Funcionario']['nome'], 'class' => 'input-xlarge', 'label' => 'Funcionario', 'readonly' => true, 'type' => 'text')); ?>
				<?php echo $this->BForm->input('Setor.descricao', array('value' => $funcionario['Setor']['descricao'], 'class' => 'input-xlarge', 'label' => 'Setor', 'readonly' => true, 'type' => 'text')); ?>
				<?php echo $this->BForm->input('Cargo.descricao', array('value' => $funcionario['Cargo']['descricao'], 'class' => 'input-xlarge', 'label' => 'Cargo', 'readonly' => true, 'type' => 'text')); ?>
				<div style="clear: both;"></div>

				<?php if (isset($funcionario['Riscos']) && count($funcionario['Riscos'])) : ?>
					<h5> Riscos:</h5>
					<span style="font-weight: normal;">
						<p><?php echo implode("</p><p> - ", $funcionario['Riscos']); ?></p>
					</span>
				<?php endif; ?>
			</div>

			<div id="listagem_<?php echo $k_cliente_funcionario; ?>" style="padding: 0 15px; margin-top: -5px;">
				<table class="table table-striped" style="background: #F5F5F5; border: 2px solid #EFEFEF;">
					<thead>
						<tr>
							<th class="input-xxlarge">Exame</th>
							<th class="input-xlarge">Tipo</th>
							<th class="input-small" style="text-align: center;">Retirar</th>
						</tr>
					</thead>
					<tbody>
						<?php if (isset($funcionario['exames_selecionados'])) : ?>
							<?php foreach ($funcionario['exames_selecionados'] as $k_exame => $exame) : ?>

								<!-- olhar a aplicacao de exame -->
								<?php print ""; ?>


								<?php if (!isset($exame['assinatura']['ClienteProdutoServico2']['valor'])) : ?>
									<?php $color = '#FFDBDB'; ?>
									<?php $msg_assinatura = '<div class="help-block error-message" style="font-size:12px; color:#b94a48;">Exame não tem ASSINATURA NO CONTRATO!</div>'; ?>
								<?php elseif (empty($exame['fornecedores'])) : ?>
									<?php $color = '#FFDBDB'; ?>
									<?php $msg_assinatura = '<div class="help-block error-message" style="font-size:12px; color:#b94a48;">Exame não tem CREDENCIADO!</div>'; ?>
								<?php else :  ?>
									<?php $color = ''; ?>
									<?php $msg_assinatura = ''; ?>
								<?php endif; ?>

								<tr>
									<td class="input-xlarge" style="background: <?php echo $color ?>;">
										<?php echo $exame['Exame']['descricao'] . ' ' . $msg_assinatura; ?>
									</td>
									<td class="input-xlarge" style="background: <?php echo $color ?>;">

										<?php if (isset($exame['tipo']) && ($exame['tipo'] == '1')) : ?>
											<?php echo $this->BForm->input('codigo_tipos_exames_pedidos', array('label' => false, 'class' => 'form-control uf input-large', 'style' => 'text-transform: uppercase;', 'default' => (isset($exame['tipo']) && $exame['tipo'] ? $exame['tipo'] : NULL), 'options' => $lista_tipos_exames_pcmso, 'disabled' => 'disabled')); ?>
										<?php else : ?>
											<?php echo $this->BForm->input('codigo_tipos_exames_pedidos', array('label' => false, 'class' => 'form-control uf input-large', 'style' => 'text-transform: uppercase;', 'default' => (isset($exame['tipo']) && $exame['tipo'] ? $exame['tipo'] : ''), 'options' => $lista_tipos_exames_outro, 'onchange' => 'atualiza_tipo(this, ' . $k_exame . ', ' . $k_cliente_funcionario . ', ' . $k_cliente . ');')); ?>
										<?php endif; ?>

									</td>
									<td class="input-medium" style="background: <?php echo $color ?>; text-align: center;">
										<a href="javascript:void(0);" onclick="removeExameGrupo(<?php echo $k_exame; ?>, <?php echo $k_cliente; ?>, <?php echo $k_cliente_funcionario; ?>, <?php echo $codigo_grupo_economico; ?>, this); " class="icon-trash">

										</a>
									</td>
								</tr>

							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>

					<tbody id="carregando_exames_<?php echo $k_cliente_funcionario; ?>" style="display: none;">
						<tr>
							<td colspan="4">
								<img src="/portal/img/default.gif" style="padding: 10px;">
								Aguarde, carregando os exames...
							</td>
						</tr>
					</tbody>

				</table>
			</div>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>

<div class="form-actions well">
	<span id="botao_avancar">
		<a href="/portal/clientes_funcionarios/selecao_funcionarios" class="btn btn-default">Voltar</a>
		<a href="javascript:void(0);" onclick="valida_proxima_etapa(<?php echo $codigo_grupo_economico; ?>)" class="btn btn-primary">Avançar</a>
	</span>
	<span id="carregando" style="display: none;">
		<img src="/portal/img/default.gif" style="padding: 10px;" />
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
									<b><?php echo $produto['Produto']['descricao']; ?></b>
								</td>
							</tr>
							<?php foreach ($produto['ClienteProdutoServico2'] as $key_servico => $servico) : ?>
								<tr>
									<td style="width: 110px; text-align: center;">
										<?php if (isset($servico['Servico']['cadastrado']) && ($servico['Servico']['cadastrado'] == 'nao')) : ?>
											<span style="font-size: 9px; color: red;">(não cadastrado)</span>
										<?php else : ?>
											<input class="checkbox_exames" type="checkbox" value="<?php echo $servico['codigo_servico']; ?>" name="tabela.<?php echo $key_servico; ?>.exame" id="checkbox_exames_<?php echo $servico['codigo_servico']; ?>">
										<?php endif; ?>
									</td>
									<td>
										<?php echo strtoupper($servico['Servico']['descricao']); ?>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</table>
					<?php if (count($grupos_detalhes) > 0) : ?>
						<table style="width: 100%" class="table-striped">
							<tr>
								<td style="background: #CCC; text-align: center;" colspan="3">
									<b>GRUPOS DE EXAMES</b>
								</td>
							</tr>
						</table>
						<?php foreach ($grupos_detalhes as $key_grupo => $grupo) : ?>
							<table style="width: 100%" class="table-striped">
								<td style="width: 110px; background: #CCC; text-align: center;">
									<input class="checkbox_grupos" type="checkbox" value="<?php echo $grupo['DetalheGrupoExame']['codigo']; ?>" name="tabela.<?php echo $key_grupo; ?>.grupo" onclick="verifica_grupos_exames()">
								</td>
								<td style="background: #CCC; padding: 0 115px;">
									<?php echo $grupo['DetalheGrupoExame']['descricao']; ?>
								</td>
								<?php if (!count($grupo['GrupoExame']) > 0) : ?>
									<tr>
										<td style="width: 110px; text-align: center;" colspan="2">
											<?php echo "Este grupo não possui exames cadastrados." ?>
										</td>
									</tr>
								<?php else : ?>
									<?php foreach ($grupo['GrupoExame'] as $exame) : ?>
										<tr>
											<td style="width: 110px; text-align: center;" colspan="2">
												<?php echo $exame['Exame']['descricao']; ?>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</table>
						<?php endforeach; ?>
					<?php endif; ?>
					<div class='actionbar-right'>
						<label><a href="javascript:void(0);" onclick="adicionaExamesGrupo(<?php echo $this->passedArgs[0]; ?>);" class="btn btn-success btn-sm right" title="Incluir">Incluir no Pedido!</a></label>
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

<div class="modal fade" id="modal_selecao_parametros" data-backdrop="static">
	<div class="modal-dialog modal-lg" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<div class="msg_error" style="position: absolute;display: none;">
					<div class="alert alert-error">
						<p>Não foi possível gravar os dados, verifique os erros abaixo, os campos com ( * ) deve ser preenchidos!!!</p>
					</div>
				</div>
				<h4 class="modal-title" id="gridSystemModalLabel">Inclusão de Pedidos</h4>
			</div>
			<div class="modal-body" style="max-height: 100%;">
				<div class="row-fluid">
					<div class="span12">
						<b>SOLICITAÇÃO DE EXAMES TIPO: </b>
						<div class="input-group input" style="font-size: 16px;">
							<div class="muda_cor">
								<?php echo $this->BForm->input('tipo', array('options' => array('exame_admissional' => 'Exame Admissional', 'exame_periodico' => 'Periódico', 'exame_demissional' => 'Demissional', 'exame_retorno' => 'Retorno ao Trabalho', 'exame_monitoracao' => 'Monitoração Pontual', 'exame_mudanca' => 'Mudança de Riscos Ocupacionais', 'pontual' => 'Pontual'), 'label' => true, 'type' => 'radio', 'div' => true, 'style' => '')) ?> <br />
							</div>
							<div style="clear: both;"></div>
							<div class="msg_error_especifico" style="position:absolute; display: none;">
								<span style="color:#B94A48;font-size:13px">
									<p>Informe o momento da solicitação do exame.</p>
								</span>
							</div>
						</div>
						<div id="tipos_exames_opcoes">
							<hr />
							<?php echo $this->BForm->input('portador_deficiencia', array('label' => 'Avaliação Portador de Deficiência', 'options' => array('Avaliação Portador de Deficiência'), 'type' => 'checkbox')) ?>
							<?php if ($aso_emba == 1) : ?>
								<?php echo $this->BForm->input('aso_embarcados', array('label' => 'ASO Modelo Embarcados', 'options' => array('ASO Modelo Embarcados'), 'type' => 'checkbox')) ?>
							<?php endif; ?>
							<div style="clear: both;"></div>
						</div>
						<div id="mensagem" class="alert" style="display: none; background: #ffc4c4; border-radius: 10px; border: 3px solid #d10808; padding: 0 20px 20px 20px; margin-top: 20px;">
						</div>
						<!-- <div style="padding-top: 10px;"> -->
						<?php //echo $this->BForm->input('data_solicitacao', array('label' => 'Data da <span id="data">solicitação</span> <span class="pull-right margin-right-20"><i class="icon-question-sign" data-toggle="tooltip" title="Insira a data."></i></span>', 'class' => 'data', 'value' => date('d/m/Y'))) 
						?>
						<!-- <div style="clear: both;"></div> -->
						<!-- <hr /> -->
						<!-- </div> -->
					</div>
				</div>
				<div class="form-actions center" id="rodape_botoes">
					<a href="/portal/clientes_funcionarios/selecao_funcionarios/" class="btn btn-default btn-lg"><i class="glyphicon glyphicon-fast-backward"></i> Voltar</a>

					<a href="javascript:void(0);" onclick="valida_risco(<?php echo $codigo_grupo_economico; ?>);" class="btn btn-success btn-lg" id="botao-verificar-risco">
						<i class="glyphicon glyphicon-share"></i> Avançar
					</a>

					<a href="javascript:void(0);" onclick="gravar_informacoes(<?php echo $codigo_grupo_economico; ?>, <?php echo $grupo_economico['Empresa']['codigo']; ?>);" class="btn btn-success btn-lg" id="botao-avancar" style="display:none;">
						<i class="glyphicon glyphicon-share"></i> Avançar
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal_funcionarios_sem_ppra" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="gridSystemModalLabel">Inclusão de Pedidos</h4>
			</div>
			<div class="modal-body" style="height: 600px; overflow: auto;">
				<?php if (isset($dados_funcionarios_sem_ppra) && count($dados_funcionarios_sem_ppra) > 0) : ?>
					<div class="alert alert-error">
						<p>A Unidade + Setor + Cargo do(s) funcionário(s) listado(s) abaixo, está passando por análise técnica, assim que pronto comunicamos via e-mail</p>
					</div>
					<div style="clear: both;"><br></div>
					<table style="width: 100%" class="table-striped">
						<?php foreach ($dados_funcionarios_sem_ppra as $codigo => $nome) : ?>
							<tr>
								<td>
									<?php echo " " . $nome['Funcionario']['nome'] . ""; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
					<?php //elseif(count($ppra_pcmso) > 0): 
					?>
					<!-- <div class="alert alert-error">
						<p>Existe PGR e/ou PCMSO com status diferente de FINALIZADO</p>
					</div> -->
				<?php endif; ?>
			</div>
			<!-- FINAL MODAL BODY -->

			<div class="form-actions center" id="rodape_botoes" style="margin-bottom: 5px;">
				<a href="/portal/clientes_funcionarios/selecao_funcionarios/" class="btn btn-default btn-lg"><i class="glyphicon glyphicon-fast-backward"></i> Voltar</a>
			</div>
			<!-- FINAL RODAPE BOTOES -->
		</div>
	</div>
	<!-- FINAL MODAL DIALOG -->
</div>
<!-- FINAL modal_funcionarios_sem_ppra -->


<!-- MODAL HIERARQUIA ALERTA PENDENTE PARA O CLIENTE -->
<div class="modal fade" id="modal_hierarquia_pendente" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="gridSystemModalLabel">PGR e PCMSO pendentes</h4>
			</div>
			<div class="modal-body" style="height: 350px;font-size: 14px">
				<br>
				<p>Você está tentando agendar um exame, mas ainda não tem PGR e PCMSO aplicados, ou seja, não há aplicação de risco e exame para a combinação de unidade + setor + cargo necessária. </p>
				<div style="clear: both;"><br><br>
					<p>Reforçando o procedimento para esse processo: aguarde a liberação da nossa área técnica para, então, emitir o pedido desses exames. </p><br><br>
					<p><b>Assim que isso acontecer, você será notificado por e-mail.</p></b><br><br>
					<p>Ficou claro? Se não, não pense duas vezes antes de procurar nosso time (<a href="mailto:relacionamento@rhhealth.com.br">relacionamento@rhhealth.com.br</a>) para mais esclarecimentos.</p>

				</div>

			</div>
			<div class="form-actions" id="rodape_botoes" style="margin-bottom: 5px;">
				<a href="javascript:void(0);" onclick="manipula_modal('modal_hierarquia_pendente', 0);" class="btn btn-default btn-lg"><i class="glyphicon glyphicon-fast-backward"></i> FECHAR</a>
			</div>
		</div>
	</div>
</div>
<!-- FINAL MODAL HIERARQUIA ALERTA PENDENTE PARA O CLIENTE -->


</div>

<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function() {
		$(".modal").css("z-index", "-1");
		if("' . $mostra_modal_sem_ppra . '" == "1") {

			if("' . !empty($_SESSION['Auth']['Usuario']['codigo_cliente']) . '") {
				manipula_modal("modal_hierarquia_pendente", 1);
			} else {
				manipula_modal("modal_funcionarios_sem_ppra", 1);
			}	

		}else{

			if("' . $mostra_modal_parametros . '" == "1") {
				manipula_modal("modal_selecao_parametros", 1);
			} else {
				atualiza_parametros("' . $codigo_grupo_economico . '");
			}

			// seta etapa 1
			$("#caminho-pao").load("/portal/pedidos_exames/caminho_pao/1");

			$("input[type=\"radio\"]").click(function(elemento) {
				if($(this).attr("id") != "TipoExameDemissional") {
					$("#mensagem").hide();
					$("#data").html("Solicitação");
					$("#tipos_exames_opcoes").show();
					$("#botao-verificar-risco").hide();
					$("#botao-avancar").show();
				} else {
					$("#botao-avancar").hide();
					$("#botao-verificar-risco").show();
				}
			});

			setup_mascaras();
			setup_datepicker();
		}
	});
	
	function atualiza_parametros(codigo_grupo_economico) {

		//retira o incluir exames
		if(!$("input#TipoPontual").is(":checked")) {
			$("#mais_incluir_exame").hide();
		}


		$("#parametros").load("/portal/pedidos_exames/carrega_parametros/" + codigo_grupo_economico + "/1");
	}

	function atualiza_tipo(element, chave, codigo_cliente_funcionario, codigo_cliente) {
		$.ajax({
			type: "GET",
			url: "/portal/pedidos_exames/atualiza_tipo_grupo/' . $codigo_grupo_economico . '/" + codigo_cliente + "/" + codigo_cliente_funcionario + "/" + chave + "/" + $(element).val(),
			dataType: "json",
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

	function valida_risco(codigo_grupo_economico) {
		var bkp_botoes = $("#rodape_botoes").html();
		
		if($("input#TipoExameDemissional").is(":checked")) {
			$.ajax({
				type: "GET",
				url: "/portal/pedidos_exames/verifica_risco_cnae/" + codigo_grupo_economico,
				dataType: "html",
				beforeSend: function() {
					$("#rodape_botoes").html("<img src=\"/portal/img/default.gif\" style=\"padding: 10px;\">Validando grau de risco do CNAE.");
				},
				success: function(conteudo) {
					$("#rodape_botoes").html(bkp_botoes);

					if(conteudo.trim()) {

						// mostra msg
						$("#tipos_exames_opcoes").hide();
						$("#mensagem").html(conteudo).show();
						$("#data").html("Demissão");

						$("#botao-verificar-risco").hide();
						$("#botao-avancar").show();
					} else {

						// avançar
						$("#botao-avancar").click();
					}

				},
				complete: function() {
					
				}
			});

		} else {
			// avançar
			$("#botao-avancar").click();
		}	
	}

	function gravar_informacoes(codigo_grupo_economico, codigo_cliente) {
		
		// Testa se alguma opção foi escolhida no radio de Momento de Solicitação de Exame
		if(	!$("input#TipoExameAdmissional").is(":checked") && 
			!$("input#TipoExamePeriodico").is(":checked") && 
			!$("input#TipoExameDemissional").is(":checked") && 
			!$("input#TipoExameRetorno").is(":checked") && 
			!$("input#TipoExameMudanca").is(":checked") && 
			!$("input#TipoExameMonitoracao").is(":checked") && 
			!$("input#TipoPontual").is(":checked")){

				$(".msg_error").css("display","block");
				$(".muda_cor").css("color","#B94A48");
				$(".msg_error_especifico").css("display","block");
				setTimeout(function(){ $(".msg_error").css("display","none"); }, 5000);
				return false;
			}

			data = 	"codigo_grupo_economico=" + codigo_grupo_economico +
			"&exame_admissional=" + $("input#TipoExameAdmissional").is(":checked") + 
			"&exame_periodico=" + $("input#TipoExamePeriodico").is(":checked") + 
			"&exame_demissional=" + $("input#TipoExameDemissional").is(":checked") + 
			"&exame_retorno=" + $("input#TipoExameRetorno").is(":checked") + 
			"&exame_mudanca=" + $("input#TipoExameMudanca").is(":checked") + 
			"&exame_monitoracao=" + $("input#TipoExameMonitoracao").is(":checked") + 
			"&portador_deficiencia=" + $("input#portador_deficiencia").is(":checked") + 
			"&aso_embarcados=" + $("input#aso_embarcados").is(":checked") + 
			"&pontual=" + $("input#TipoPontual").is(":checked") +
			"&data_solicitacao=" + $("input#data_solicitacao").val();

			$.ajax({
				type: "POST",
				url: "/portal/pedidos_exames/grava_parametros_busca_exames_grupo/",
				dataType: "json",
				data: data,
				beforeSend: function() {
					$("#rodape_botoes").html("<img src=\"/portal/img/default.gif\" style=\"padding: 10px;\"> Carregando exames do PCMSO.");
				},
				success: function(json) {

					if(json == 1) {

					//pega os ids dos clientes
						var codCliente = $("#codigos_clientes_funcionarios").val().split("|");
						for(var i=0;i<codCliente.length;i++){
							if(codCliente[i] != "") {
								atualiza_lista(codCliente[i]);
							}
						}					
						manipula_modal("modal_selecao_parametros", 0);
					}
				},
				complete: function() {
					atualiza_parametros(codigo_grupo_economico);
				}
			});			
		}
		
		function adicionaExamesGrupo(codigo_grupo_economico) {

			var exames = "";
			$(".checkbox_exames").each(function(i, element_exames_disponiveis) {
				if(element_exames_disponiveis.checked) {
					exames = exames + $(element_exames_disponiveis).val() + ",";
				}
			});

			if(exames.length) {
				codigos_exames = exames.substring(0,(exames.length - 1));

				$.ajax({
					type: "POST",
					url: "/portal/pedidos_exames/lista_exames_grupo/' . $codigo_grupo_economico . '",
					dataType: "json",
					data: "codigo_grupo_economico=' . $codigo_grupo_economico . '&exames=" + codigos_exames,
					beforeSend: function() {
						manipula_modal("modal_exames_assinatura", 0);
						manipula_modal("modal_carregando", 1);
					},
					success: function(retorno) {
						$.each(retorno, function(codigo_cliente, cliente) {
							$.each(cliente.cliente_funcionario, function(codigo_cliente_funcionario, cliente_funcionario) {
								$.ajax({
									type: "POST",
									url: "/portal/pedidos_exames/recarrega_listagem_exames_por_funcionario",
									dataType: "html",
									data: "codigo_grupo_economico=" + codigo_grupo_economico + "&codigo_cliente=" + codigo_cliente + "&codigo_cliente_funcionario=" + codigo_cliente_funcionario,
									beforeSend: function() {
										$("#listagem_" + codigo_cliente_funcionario).html("<img src=\"/portal/img/default.gif\" style=\"padding: 10px;\"> Carregando a grade de serviços...");
									},
									success: function(conteudo) {
										$("#listagem_" + codigo_cliente_funcionario).html(conteudo);
									},
									complete: function() {

									}
								});
							});
						});
					},
					complete: function() {
						manipula_modal("modal_carregando", 0);
						$("#botao_avancar").show();
					}
				});		
			}
		}
		
		function removeExameGrupo(chave, codigo_cliente, codigo_cliente_funcionario, codigo_grupo_economico, element) {

			$.ajax({
				type: "POST",
				url: "/portal/pedidos_exames/remove_exame_grupo",
				dataType: "json",
				data: "chave=" + chave + "&codigo_cliente=" + codigo_cliente + "&codigo_cliente_funcionario=" + codigo_cliente_funcionario + "&codigo_grupo_economico=" + codigo_grupo_economico,
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
		
		function valida_proxima_etapa(codigo_grupo_economico) {

			if($("input#TipoExamePeriodico").is(":checked")) {
				$.ajax({
					type: "POST",
					url: "/portal/pedidos_exames/valida_pedido_periodico",
					dataType: "json",
					data: "codigo_grupo_economico=" + codigo_grupo_economico,
					beforeSend: function() {
						manipula_modal("modal_carregando", 1);
					},
					success: function(json) {
						if(json.length) {
							var msg = "<strong>Exames ainda vigentes: </strong><br /><br />";

							$.each(json, function(codigo_cliente_funcionario, exame) {
								data_certa = exame.data_exibicao.split("-");
								msg = msg + " " + exame.nome_exame + " é vigente até " + data_certa[2] + "/" + data_certa[1] + "/" + data_certa[0] + " (funcionário " + exame.nome_funcionario + ") <br /> ";	
							});

							msg = msg + " <br />Não será necessário a realização desse(s) exame(s) no momento. Em caso de dúvidas, acione a Equipe RHHealth (0800.0142659)";

							manipula_modal("modal_carregando", 0);
							swal({
								type: "error",
								title: "Não é permitido avançar!",
								text: msg,
								html: true,
								showCancelButton: false
							});

						} else {
							valida_proxima_etapa_grupo(codigo_grupo_economico);
						}
					}
				});

			} else {
				valida_proxima_etapa_grupo(codigo_grupo_economico);
			}
		}		
		
		function valida_proxima_etapa_grupo(codigo_grupo_economico) {

			$.ajax({
				type: "POST",
				url: "/portal/pedidos_exames/valida_proxima_etapa_grupo",
				dataType: "json",
				data: "codigo_grupo_economico=" + codigo_grupo_economico,
				beforeSend: function() {

				},
				success: function(json) {
					if(json.valido) {
						window.location = document.location.origin + "/portal/pedidos_exames/selecionar_fornecedores_grupo/" + codigo_grupo_economico;
					} else {
						manipula_modal("modal_carregando", 0);
						swal({
							type: "error",
							title: "Não é permitido avançar!",
							text: json.mensagem,
							showCancelButton: false
						});
					}
				},
				complete: function() {

				}
			});	
		}		
		
		function buscaCep() {

			var erCep = /^\d{5}-\d{3}$/;
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

								buscaCidade(json.VEndereco.endereco_codigo_estado, json.VEndereco.endereco_codigo_cidade);
								$("#codigo_endereco").val(json.VEndereco.endereco_codigo);

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
		
		function buscaCidade(idEstado, idCidade) {
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
					$("input[name=\"cep\"]").prop("disabled", false);
					$("input[name=\"endereco\"]").prop("disabled", false);
					$("input[name=\"numero\"]").prop("disabled", false);
					$("select[name=\"estado\"]").prop("disabled", false);
					$("select[name=\"cidade\"]").prop("disabled", false);
					$("input[name=\"latitude\"]").prop("disabled", false);
					$("input[name=\"longitude\"]").prop("disabled", false);

					$("#carregando_cep").hide();
					$("#pesquisa_cep").show();
					$("#carregando_cidade").hide();
				}
			});		
		}
		
		function buscaCidades(element) {
			buscaCidade($(element).val());
		}
		
		function atualiza_lista(codigo_funcionario_cliente) {

			$.ajax({
				type: "GET",
				url: "/portal/pedidos_exames/atualiza_lista_exames_grupo/' . $codigo_grupo_economico . '/"+codigo_funcionario_cliente,
				dataType: "html",
				beforeSend: function() {
					$("#carregando_exames_"+codigo_funcionario_cliente).show();
				},
				success: function(conteudo) {
					$("#listagem_"+codigo_funcionario_cliente).html(conteudo);
					manipula_modal("modal_selecao_parametros", 0);
				},
				complete: function() {

				}
			});		
		}
		
		function mudar_endereco(element) {

			if($(element).val() == "1") {
				$("input[name=\"cep\"]").prop("disabled", false);
				$("input[name=\"endereco\"]").prop("disabled", false);
				$("input[name=\"numero\"]").prop("disabled", false);
				$("select[name=\"estado\"]").prop("disabled", false);
				$("select[name=\"cidade\"]").prop("disabled", false);

				$("input[name=\"latitude\"]").prop("disabled", false);
				$("input[name=\"longitude\"]").prop("disabled", false);
				$("input[name=\"grava_endereco\"]").val(1);

			} else {
				$("input[name=\"cep\"]").prop("disabled", true);
				$("input[name=\"endereco\"]").prop("disabled", true);
				$("input[name=\"numero\"]").prop("disabled", true);
				$("select[name=\"estado\"]").prop("disabled", true);
				$("select[name=\"cidade\"]").prop("disabled", true);

				$("input[name=\"latitude\"]").prop("disabled", true);
				$("input[name=\"longitude\"]").prop("disabled", true);
				$("input[name=\"grava_endereco\"]").val(0);
			}
		}
		
		function manipula_modal(id, mostra) {

			if(id == "modal_hierarquia_pendente" && mostra == 0 ){
				
				id = "modal_funcionarios_sem_ppra";
				mostra = 1;
					
			}	

			if(mostra) {
				$(".modal").css("z-index", "-1");

				$("#" + id).css("z-index", "1050");
				$("#" + id).modal("show");
			} else {
				$("#" + id).css("z-index", "-1");
				$("#" + id).modal("hide");
			}
		}		

		function verifica_grupos_exames(){
			var grupos = "";
			$(".checkbox_grupos").each(function(i,element_grupos_disponiveis){
				if(element_grupos_disponiveis.checked) {
					grupos = grupos + $(element_grupos_disponiveis).val() + ",";
				}
			});
			
			if(grupos.length) {
				codigos_grupos = grupos.substring(0,(grupos.length - 1));

				$.ajax({
					type: "POST",
					url: "/portal/detalhes_grupos_exames/lista_servicos_grupo",
					dataType: "json",
					data: "grupos=" + codigos_grupos,
					beforeSend: function() {
						bloquearDiv($("#modal_exames_assinatura"));
					},
					success: function(dados_grupos_exames){
						$.each(dados_grupos_exames, function(i,codigo){
							$("#checkbox_exames_"+i).prop("checked", true);
						});
					},
					complete: function() {
						desbloquearDiv($("#modal_exames_assinatura"));
					}
				});
			}
		}
		'); ?>