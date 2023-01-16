<?php echo $this->Buonny->link_css('jqueryui/hot-sneaks/jquery-ui-1.9.2.custom.css'); ?>
<?php echo $this->Buonny->link_js('jqueryui/jquery-ui-1.9.2.custom'); ?>

<style>
	.btn-day { border: 3px solid #2f96b4; border-radius: 15px; margin: 10px; padding: 3px; text-align: center; width: 70px; float: left; }
	.btn-day:hover, .pinta { background: #db4865 url("/portal/css/jqueryui/hot-sneaks/images/ui-bg_diagonals-small_40_db4865_40x40.png") repeat scroll 50% 50%; font-weight: bold; color: #FFF; }	
	.pinta-amarelo { background: #ffff38 url("/portal/css/jqueryui/hot-sneaks/images/ui-bg_diagonals-small_75_ccd232_40x40.png") repeat scroll 50% 50%; font-weight: bold; color: #888 }
	.table th, .table td {line-height: 15px;}
</style>

<div class='inline well'>
	<?php echo $this->BForm->input('Empresa.razao_social', array('value' => $dados_cliente_funcionario['Empresa']['razao_social'], 'class' => 'input-xlarge', 'label' => 'Empresa' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Cliente.razao_social', array('value' => $dados_cliente_funcionario['Cliente']['razao_social'], 'class' => 'input-xlarge', 'label' => 'Unidade' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Empresa.codigo_documento', array('value' => $dados_cliente_funcionario['Empresa']['codigo_documento'], 'class' => 'input-xlarge', 'label' => 'CNPJ' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Setor.descricao', array('value' => $dados_cliente_funcionario['Setor']['descricao'], 'class' => 'input-xlarge', 'label' => 'Setor', 'readonly' => true, 'type' => 'text')); ?>
	<div class="clear"></div>
	<?php echo $this->BForm->input('Funcionario.nome', array('value' => $dados_cliente_funcionario['Funcionario']['nome'], 'class' => 'input-xlarge', 'label' => 'Funcionario' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Funcionario.cpf', array('value' => $dados_cliente_funcionario['Funcionario']['cpf'], 'class' => 'input-xlarge', 'label' => 'CPF' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Funcionario.data_nascimento', array('value' => $dados_cliente_funcionario['Funcionario']['data_nascimento'], 'class' => 'input-xlarge', 'label' => 'Data nascimento' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Cargo.descricao', array('value' => $dados_cliente_funcionario['Cargo']['descricao'], 'class' => 'input-xlarge', 'label' => 'Cargo' , 'readonly' => true, 'type' => 'text')); ?>
	<div class="clear"></div>
</div>

	<div class='inline well' id="parametros">
		<img src="/portal/img/default.gif" style="padding: 10px;">
		Carregando parametrizações do pedido...
	</div>

<div id="caminho-pao"></div>

<?php echo $this->BForm->create('PedidosExames', array('url' => array('controller' => 'pedidos_exames', 'action' => 'agendamento', $this->passedArgs[0]))); ?>
<?php echo $this->BForm->hidden('codigo_cliente_funcionario', array('value' => $this->passedArgs[0])); ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Exame</th>
			<th>Fornecedor</th>
			<!-- <th>Tipo de exame</th> -->
			<th>Tipo Atendimento</th>
			<td>Horário Agendamento</td>	            
		</tr>
	</thead>
	<tbody>
		<?php foreach ($exames_agendamento as $k => $item) : ?>
			<tr>
				<td><?php echo $item['exame']['descricao']; ?></td>
				<td>
					<?php echo $item['fornecedor']['razao_social']; ?><br />
					<?php if(!empty($item['fornecedor']['telefone'])) : ?>
						<span style="font-size: 10px;">
							TELEFONE: <?php echo $item['fornecedor']['telefone'];  ?>
						</span> 
					<?php endif; ?>
				</td>
				<!-- <td><?php echo $tipo_exame ?></td>-->
				<td>
					<?php if($item['exame']['tipo_atendimento'] == '1') : ?>
						<?php echo $this->BForm->hidden('ItemPedidoExame.' . $k . '.tipo_atendimento', array('value' => '1')); ?>
						<?php echo $this->BForm->hidden('ItemPedidoExame.' . $k . '.data_agendamento', array('value' => (isset($this->data['ItemPedidoExame'][$k]['data_agendamento']) ? $this->data['ItemPedidoExame'][$k]['data_agendamento'] : ''))); ?>
						<?php echo $this->BForm->hidden('ItemPedidoExame.' . $k . '.hora_agendamento', array('value' => (isset($this->data['ItemPedidoExame'][$k]['hora_agendamento']) ? $this->data['ItemPedidoExame'][$k]['hora_agendamento'] : ''))); ?>
						HORA MARCADA
					<?php else : ?>
						<?php echo $this->BForm->hidden('ItemPedidoExame.' . $k . '.tipo_atendimento', array('value' => '0')); ?>
						ORDEM DE CHEGADA
					<?php endif; ?>
				</td>
				<td>
				
					<?php if($item['fornecedor']['utiliza_sistema_agendamento'] == '1') : ?>
					
						<?php if(isset($item['Agenda']) && isset($lista_datas_disponiveis[$item['fornecedor']['codigo']][$item['Agenda']])) : ?>

							<div id="botao_seleciona_agenda_<?php echo $k; ?>" style="display: <?php echo isset($this->data['ItemPedidoExame'][$k]['data_agendamento']) && !empty($this->data['ItemPedidoExame'][$k]['data_agendamento']) ? 'none' : 'block'; ?>;">
								<a href="javascript:void(0);" onclick="mostra_modal_agendamento(this, <?php echo $k; ?>, <?php echo $codigo_cliente_funcionario; ?>, <?php echo $item['fornecedor']['codigo']; ?>, <?php echo $k; ?>, <?php echo $item['exame']['codigo_servico']; ?>);"><label class="label label-important">ESCOLHER MELHOR DIA E HORA!</label></a>
							</div>

							<div id="botao_mostra_agenda_<?php echo $k; ?>" style="display: <?php echo isset($this->data['ItemPedidoExame'][$k]['data_agendamento']) && !empty($this->data['ItemPedidoExame'][$k]['data_agendamento']) ? 'block' : 'none'; ?>;">
								Data: <input type="text" class="input-small form-control pinta-amarelo" name="data[ItemPedidoExame][<?php echo $k; ?>][data_agendamento]" disabled="disabled" style="margin-right: 18px;" value="<?php echo isset($this->data['ItemPedidoExame'][$k]['data_agendamento']) ? $this->data['ItemPedidoExame'][$k]['data_agendamento'] : ''; ?>"/>
								Hora: <input type="text" class="input-small form-control pinta-amarelo" name="data[ItemPedidoExame][<?php echo $k; ?>][hora_agendamento]" disabled="disabled" value="<?php echo isset($this->data['ItemPedidoExame'][$k]['hora_agendamento']) ? $this->data['ItemPedidoExame'][$k]['hora_agendamento'] : ''; ?>"/>

								<a href="javascript:void(0);" onclick="mostra_modal_agendamento(this, <?php echo $k; ?>, <?php echo $codigo_cliente_funcionario; ?>, <?php echo $item['fornecedor']['codigo']; ?>, <?php echo $k; ?>, <?php echo $item['exame']['codigo_servico']; ?>);"><label class="label label-important">ALTERAR</label></a>
							</div>

							<div class="modal fade" id="modal_agendamento_<?php echo $k; ?>" data-backdrop="static" style="width: 85%; left: 8%; top: 15%; margin: 0 auto;">
								<div class="modal-dialog modal-sm" style="position: static;">
									<div class="modal-content">
										<div class="modal-header" style="text-align: center;">
											<h3>AGENDAMENTO DE EXAMES:</h3>
										</div>

										<div style="margin: 5px 20px;">
											<div class='inline well'>
												<?php echo $this->BForm->input('Empresa.razao_social', array('value' => $dados_cliente_funcionario['Empresa']['razao_social'], 'class' => 'input-large', 'label' => 'Empresa' , 'readonly' => true, 'type' => 'text')); ?>
													<?php echo $this->BForm->input('Cliente.razao_social', array('value' => $dados_cliente_funcionario['Cliente']['razao_social'], 'class' => 'input-large', 'label' => 'Unidade' , 'readonly' => true, 'type' => 'text')); ?>
												<?php echo $this->BForm->input('Empresa.codigo_documento', array('value' => $dados_cliente_funcionario['Empresa']['codigo_documento'], 'class' => 'input-large', 'label' => 'CNPJ' , 'readonly' => true, 'type' => 'text')); ?>
													<?php echo $this->BForm->input('Setor.descricao', array('value' => $dados_cliente_funcionario['Setor']['descricao'], 'class' => 'input-large', 'label' => 'Setor', 'readonly' => true, 'type' => 'text')); ?>
													<div class="clear"></div>
												<?php echo $this->BForm->input('Funcionario.nome', array('value' => $dados_cliente_funcionario['Funcionario']['nome'], 'class' => 'input-large', 'label' => 'Funcionario' , 'readonly' => true, 'type' => 'text')); ?>
													<?php echo $this->BForm->input('Funcionario.cpf', array('value' => $dados_cliente_funcionario['Funcionario']['cpf'], 'class' => 'input-large', 'label' => 'CPF' , 'readonly' => true, 'type' => 'text')); ?>													
												<?php echo $this->BForm->input('Funcionario.data_nascimento', array('value' => $dados_cliente_funcionario['Funcionario']['data_nascimento'], 'class' => 'input-large', 'label' => 'Data nascimento' , 'readonly' => true, 'type' => 'text')); ?>
												<?php echo $this->BForm->input('Cargo.descricao', array('value' => $dados_cliente_funcionario['Cargo']['descricao'], 'class' => 'input-large', 'label' => 'Cargo' , 'readonly' => true, 'type' => 'text')); ?>
												<div class="clear"></div>
											</div>	            							
										</div>

										<div class="modal-body" style="min-height: 390px;">
											<div class="span5" style="border-right: 1px solid #CCC;">
												<label><b>FORNECEDOR:</b></label>
												<?php echo $item['fornecedor']['razao_social']; ?>
												<br /><br />

												<label><b>EXAME:</b></label>
												<?php echo $item['exame']['descricao']; ?>
												<br /><br />

												<label><b>DATAS COM AGENDA DISPONÍVEIS:</b></label>
												<div class="pull-left">
													<div class="pull-left margin-right-15"><span class="legenda-dia-disponivel"></span>data disponível</div>
													<div class="clear"></div>
													<div class="pull-left margin-right-15"><span class="legenda-dia-atual"></span>data atual</div>
												</div>
												<div class="pull-left">
													<div class="pull-left"><span class="legenda-dia-indisponivel"></span>data indisponível</div>
													<div class="clear"></div>
													<div class="pull-left"><span class="legenda-dia-selecionado"></span>data selecionada</div>			
												</div>

												<div class="clear"></div>	    		
												<div id="datepicker" class="margin-top-10"></div>
											</div>
											<div class="span5">
												<b style="font-size: 18px;">Horários Disponíveis <span id="texto_dia">:</span></b>
												<div class="well">
													<div id="dias_disponiveis_<?php echo $k; ?>">
														<label class="label label-info">Escolher o melhor dia, ao lado!</label>									    				
													</div>
													<div style="clear: both;"></div>
												</div>
												<br />
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<div class="right">
											<a href="javascript:void(0);" id="botao_<?php echo $k; ?>" style="display: none;" onclick="confirma_agendamento('<?php echo $k; ?>');" class="btn btn-success">CONFIRMAR</a>
										</div>
									</div>									    
								</div>
							</div>
						<?php else : ?>
							<label class="label">ORDEM DE CHEGADA</label>
						<?php endif; ?>
					<?php else : ?>

						<?php // if($eh_cliente) : ?>

						<?php if(1) : ?>

							<?php if($item['exame']['tipo_atendimento'] == '1') : ?>
								<span id="opcao_<?php echo $k; ?>">
									<input type="radio" value="1" onclick="controla_agendamento_cliente(1, <?php echo $k; ?>);" name="data[ItemPedidoExame][<?php echo $k; ?>][sugerido]" class="opcao_agendamento"/> <strong>Agendamento pela RHHealth.</strong><br />
									<input type="radio" value="0" onclick="controla_agendamento_cliente(0, <?php echo $k; ?>);" name="data[ItemPedidoExame][<?php echo $k; ?>][sugerido]" class="opcao_agendamento"/> <strong>Eu vou agendar!</strong>
								</span>

								<div id="link_<?php echo $k; ?>" style="display: none;">
									<a href="javascript:void(0);" onclick="controla_agendamento_cliente(1, <?php echo $k; ?>);" class="label label-important">Alterar datas sugeridas</a>
								</div>

								<div class="modal fade" id="modal_sugestao_<?php echo $k; ?>" data-backdrop="static">
									<div class="modal-dialog modal-sm" style="position: static;">
										<div class="modal-content">
											<div class="modal-header" style="text-align: center;">
												<h4>PRECISAMOS DE 3 SUGESTÕES DE DATAS:</h4>
											</div>
											<div class="modal-body" style="min-height: 520px;">
												<label class="alert">- Informar sugestões de datas/horários! Breve retornaremos com o agendamento.</label>

												<div style="padding: 15px; margin-top: -10px;">
													
													<span style="font-size: 10px;">
														<strong>FORNECEDOR:</strong>
													</span><br />
													<strong><?php echo $item['fornecedor']['razao_social']; ?></strong><br />
													<?php if(!empty($item['fornecedor']['telefone'])) : ?>
														<span style="font-size: 10px;">
															<strong>TELEFONE:</strong> <?php echo $item['fornecedor']['telefone'];  ?>
														</span> 
													<?php endif; ?>

													<div class="clear" style="margin-top:15px;"></div>
														
													<?php if(!empty($item['fornecedor']['horarios_funcionamento'])) { ?>
													<span style="font-size: 10px;">
														<strong>HORÁRIO DE ATENDIMENTO:</strong>
													</span><br />
													<?php foreach ($item['fornecedor']['horarios_funcionamento'] as $key => $horario_funcionamento) {
													$dias_semana = explode(',', $horario_funcionamento['FornecedorHorario']['dias_semana']);
													$label = 'De: ';
													foreach ($dias_semana as $key => $dia_semana) {
														if($key > 0) $label .= ', ';
														$label .= $array_dias_semana[$dia_semana];
													}
													$label .= '.';
													echo $label . '<br />';
													if(strlen($horario_funcionamento['FornecedorHorario']['de_hora']) < 4) $horario_funcionamento['FornecedorHorario']['de_hora'] = 0 . $horario_funcionamento['FornecedorHorario']['de_hora'];
													$label = 'Das ' . substr($horario_funcionamento['FornecedorHorario']['de_hora'], 0, 2) . ':' . substr($horario_funcionamento['FornecedorHorario']['de_hora'], 2, 2);

													if(strlen($horario_funcionamento['FornecedorHorario']['ate_hora']) < 4) $horario_funcionamento['FornecedorHorario']['ate_hora'] = 0 . $horario_funcionamento['FornecedorHorario']['ate_hora'];
													$label .= ' às '. substr($horario_funcionamento['FornecedorHorario']['ate_hora'], 0, 2) . ':' . substr($horario_funcionamento['FornecedorHorario']['ate_hora'], 2, 2) . '.';
													echo $label . '<br /><br />';
													} ?>
													<?php } ?>

													<div>&nbsp;</div>
													<h4>Sugestão 01:</h4>

													<div class="row-fluid">
														<div class="span4">
															Data: <input type="text" class="input-small form-control data" name="data[ItemPedidoExame][<?php echo $k; ?>][sugestao][0][data_sugestao_agendamento]" value="<?php echo isset($this->data['ItemPedidoExame'][$k]['sugestao'][0]['data_sugestao_agendamento']) ? $this->data['ItemPedidoExame'][$k]['sugestao'][0]['data_sugestao_agendamento'] : ''; ?>" onblur="desmarca_red(this);"/>
														</div>
														<div class="span6">
															<span style="margin-left: 18px;">Hora:</span> <input type="text" class="input-small form-control hora" name="data[ItemPedidoExame][<?php echo $k; ?>][sugestao][0][hora_sugestao_agendamento]" value="<?php echo isset($this->data['ItemPedidoExame'][$k]['sugestao'][0]['hora_sugestao_agendamento']) ? $this->data['ItemPedidoExame'][$k]['sugestao'][0]['hora_sugestao_agendamento'] : ''; ?>"/>
															<br /><br />
														</div>
													</div>
													<h4>Sugestão 02:</h4>
													<div class="row-fluid">
														<div class="span4">
															
															Data: <input type="text" class="input-small form-control data" name="data[ItemPedidoExame][<?php echo $k; ?>][sugestao][1][data_sugestao_agendamento]" value="<?php echo isset($this->data['ItemPedidoExame'][$k]['sugestao'][1]['data_sugestao_agendamento']) ? $this->data['ItemPedidoExame'][$k]['sugestao'][1]['data_sugestao_agendamento'] : ''; ?>" onblur="desmarca_red(this);"/>
														</div>
														<div class="span4">
															
															<span style="margin-left: 18px;">Hora:</span> <input type="text" class="input-small form-control hora" name="data[ItemPedidoExame][<?php echo $k; ?>][sugestao][1][hora_sugestao_agendamento]" value="<?php echo isset($this->data['ItemPedidoExame'][$k]['sugestao'][1]['hora_sugestao_agendamento']) ? $this->data['ItemPedidoExame'][$k]['sugestao'][1]['hora_sugestao_agendamento'] : ''; ?>"/>
															<br /><br />
														</div>
													</div>

													<h4>Sugestão 03:</h4>
													<div class="row-fluid">
														<div class="span4">
															
															Data: <input type="text" class="input-small form-control data" name="data[ItemPedidoExame][<?php echo $k; ?>][sugestao][2][data_sugestao_agendamento]" value="<?php echo isset($this->data['ItemPedidoExame'][$k]['sugestao'][2]['data_sugestao_agendamento']) ? $this->data['ItemPedidoExame'][$k]['sugestao'][2]['data_sugestao_agendamento'] : ''; ?>" onblur="desmarca_red(this);"/>
														</div>
														<div class="span6">
															
															<span style="margin-left: 18px;">Hora:</span> <input type="text" class="input-small form-control hora" name="data[ItemPedidoExame][<?php echo $k; ?>][sugestao][2][hora_sugestao_agendamento]" value="<?php echo isset($this->data['ItemPedidoExame'][$k]['sugestao'][2]['hora_sugestao_agendamento']) ? $this->data['ItemPedidoExame'][$k]['sugestao'][2]['hora_sugestao_agendamento'] : ''; ?>"/>
															<br /><br />																							
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="modal-footer">
											<div class="right">
												<a href="javascript:void(0);" id="botao_<?php echo $k; ?>_cancel" onclick="manipula_modal('modal_sugestao_<?php echo $k; ?>', 0);" class="btn btn-danger btnc">FECHAR</a>
												<a href="javascript:void(0);" id="botao_<?php echo $k; ?>" onclick="confirma_sugestao(<?php echo $k; ?>);" class="btn btn-success btnc">CONFIRMAR SUGESTÃO</a>
											</div>
										</div>									    
									</div>
								</div>
							<?php endif;?>

							<label class="label" id="label_ordem_chegada_<?php echo $k; ?>" style="display: <?php echo $item['exame']['tipo_atendimento'] == '1' ? 'none' : ''; ?>">ORDEM DE CHEGADA</label>

							<div id="agenda_<?php echo $k; ?>" style="display: none;">
								<div class="row-fluid">
									<div class="span6" style="margin-bottom: 12px;">
										<div>Data:</div>
										<input type="text" class="input-small data form-control obrigatorio" name="data[ItemPedidoExame][<?php echo $k; ?>][data_agendamento]" />
									</div>
									<div class="span6">
										<div>Hora: </div>
										<input type="text" class="input-small hora form-control obrigatorio" name="data[ItemPedidoExame][<?php echo $k; ?>][hora_agendamento]" />
										<br />
									</div>
								</div>
								<div style="font-size: 0.9em; border-bottom: 1px solid #CCC; background: #EFEFEF; padding: 1px; margin-bottom: 5px">
									<b>ATENÇÃO:</b> Fornecedor não utiliza nossa agenda,<br /> ligar 
									para agendar o horário conforme sua disponibilidade.<br />
									<?php if(!empty($item['fornecedor']['telefone'])) : ?>
										<span style="font-size: 10px; font-weight: bold;">
											<strong>TELEFONE:</strong> <?php echo $item['fornecedor']['telefone'];  ?>
										</span> 
									<?php endif; ?>									
								</div>
							</div>	            				

						<?php else : ?>
            				<!-- 
	            				<?php if($item['exame']['tipo_atendimento'] == '1') : ?>
									<span id="agenda_<?php echo $k; ?>" style="display: <?php echo ($item['exame']['tipo_atendimento'] == '1') ? '' : 'none'; ?>">
										Data: <input type="text" class="input-small data form-control" name="data[ItemPedidoExame][<?php echo $k; ?>][data_agendamento]" />
										Hora: <input type="text" class="input-small hora form-control" name="data[ItemPedidoExame][<?php echo $k; ?>][hora_agendamento]" />
										<br />
										<span style="font-size: 0.9em; border-bottom: 1px solid #CCC; background: #EFEFEF; padding: 1px;">
											<b>ATENÇÃO:</b> Fornecedor não utiliza nossa agenda, ligar <br />
											para agendar o horário conforme sua disponibilidade.
										</span>
									</span>
	            				<?php else : ?>
	            					<label class="label" id="label_ordem_chegada_<?php echo $k; ?>">ORDEM DE CHEGADA</label>
	            				<?php endif; ?>
	            			-->
	            		<?php endif; ?>
	            	<?php endif; ?>
	            </td>
	        </tr>
	    <?php endforeach; ?>
	</tbody>
</table>
<div class='form-actions well'>
	<a href="/portal/pedidos_exames/incluir/<?php echo $this->passedArgs[0]; ?>" class="btn">Voltar</a>
	<a href="javascript:void(0);" onclick="validaFormAgendamento();" class="btn btn-primary btn-ok">Avançar</a>
</div>
<?php echo $this->BForm->end(); ?>

<div class="modal fade" id="modal_carregando">
	<div class="modal-dialog modal-sm" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="gridSystemModalLabel">Aguarde, buscando vagas na agenda...</h4>
			</div>
			<div class="modal-body">
				<img src="/portal/img/ajax-loader.gif" style="padding: 10px;">
			</div>
		</div>
	</div>
</div>

<style type="text/css">
	.error{
		border: 1px solid red !important;
	}
	.error-message{
		display: block;
		position: relative;
		top: -9px;
		clear: both;
		color: red;
	}
</style>

<?php echo $this->Javascript->codeBlock('
		
	jQuery(document).ready(function() {
		
		customCode.init();
		
		setup_mascaras(); setup_time(); 
		$(".data").datepicker({
			minDate: new Date(),
			dateFormat: \'dd/mm/yy\',
			showOn : \'button\',
			buttonImage : baseUrl + \'img/calendar.gif\',
			buttonImageOnly : true,
			buttonText : \'Escolha uma data\',
			dayNames : [\'Domingo\',\'Segunda\',\'Terça\',\'Quarta\',\'Quinta\',\'Sexta\',\'Sabado\'],
			dayNamesShort : [\'Dom\',\'Seg\',\'Ter\',\'Qua\',\'Qui\',\'Sex\',\'Sab\'],
			dayNamesMin : [\'D\',\'S\',\'T\',\'Q\',\'Q\',\'S\',\'S\'],
			monthNames : [\'Janeiro\',\'Fevereiro\',\'Março\',\'Abril\',\'Maio\',\'Junho\',\'Julho\',\'Agosto\',\'Setembro\',\'Outubro\',\'Novembro\',\'Dezembro\'],
			monthNamesShort : [\'Jan\',\'Fev\',\'Mar\',\'Abr\',\'Mai\',\'Jun\',\'Jul\',\'Ago\',\'Set\',\'Out\',\'Nov\',\'Dez\']
		}).mask("99/99/9999").addClass(\'binded\');
		
		$(".data").focusout(function() {
			$(this).removeClass("error").parent().parent().find(".error-message").remove();

			if( !  $(this).parents(".modal-dialog").find(".data").hasClass("error") ) {
				$(this).parents(".modal-dialog").find(".btnc").show();
				$(".btn-ok").show();
			}

			dataAtual = new Date();
			ano = dataAtual.getFullYear().toString();
			mes = (dataAtual.getMonth() + 1).toString();
			dia = dataAtual.getDate().toString();
		
			if(mes.length != 2)
				mes = "0" + mes;
	
			if(dia.length != 2)
				dia = "0" + dia;
		
			var data_atual = ano + mes + dia;
			var data_digitada = this.value.split(\'/\')[2] + this.value.split(\'/\')[1] + this.value.split(\'/\')[0];
		
			if(parseInt(data_digitada) < parseInt(data_atual)) {
				$(this).addClass("error").parent().parent().append( $("<span>", {text: "A data não pode ser menor que a atual.", class: "error-message"}) );
				$(this).parents(".modal-dialog").find(".btnc").hide();
				$(".btn-ok").hide();
			}
		});

		
		
		$(".modal").css("z-index", "-1");
		
		atualiza_parametros("'.$codigo_cliente_funcionario.'");
		
		// seta etapa
		$("#caminho-pao").load("/portal/pedidos_exames/caminho_pao/3");				
	});

	function confirma_agendamento(chave) {
		manipula_modal("modal_agendamento_" + chave, 0);
	}
		
	function atualiza_parametros(codigo_cliente_funcionario) {
		$("#parametros").load("/portal/pedidos_exames/carrega_parametros/'.$codigo_cliente_funcionario.'");
	}		

	function validaFormAgendamento() {

		var valido = 1;
		$("input[type=\"radio\"].opcao_agendamento:visible").each(function(index, val) {
			if($("[name=\"" + $(this).attr("name") + "\"].opcao_agendamento:visible").is(":checked") == false) {
				valido = 0;
			}
		});
		
		if(valido) {
			$("#PedidosExamesAgendamentoForm").find("input.obrigatorio:visible").each(function(key, elemento){
				if($(elemento).val() == "") {
					valido = 0;
					$(elemento).css("border", "1px solid #B94A48");
				} else {
					$(elemento).css("border", "1px solid #CCC");
				}
			});		
		}
		
		if(valido) {
			$("#PedidosExamesAgendamentoForm").submit();
		} else {
			swal("Atenção", "A inserção de data e horário de agendamento deve ser obrigatória!", "error");
		}
	}

	function confirma_sugestao(chave) {

		/**********
		var valido = true;
		$("#modal_sugestao_" + chave + " input.data").each(function() {
			if($(this).val() == "") {
				$(this).css({"border":"1px solid red"});
				valido = false;
			}
		});
		***********/
		
		// if(valido) {
		$("#opcao" + chave).html("<label class=\"label label-important\"> AGENDAMENTO SUGERIDO - RETORNO EM 48H. </label>");
		manipula_modal("modal_sugestao_" + chave, 0);
		// }
	}

	function desmarca_red(elemento) {
		if(($(elemento).val() != "") || ($(elemento).val() != "__/__/____")) {
			$(elemento).css({"border" : "1px solid #CCC"});
		}
	}

	function manipula_modal(id, mostra) {
		if(mostra) {
			$("#" + id).css("z-index", "1050");
			$("#" + id).modal("show");
		} else {
			$(".modal").css("z-index", "-1");
			$("#" + id).modal("hide");
		}
	}

	function  mostra_modal_agendamento(element, key, codigo_cliente_funcionario, codigo_fornecedor, codigo_exame, codigo_servico) {
		manipula_modal("modal_carregando", 1);
		
		$("#botao_seleciona_agenda_" + codigo_exame).hide();
		$("#botao_mostra_agenda_" + codigo_exame).show();
		
		$.getJSON("/portal/pedidos_exames/datas_disponiveis/" + codigo_cliente_funcionario + "/" + codigo_fornecedor + "/" + codigo_exame  + "/" + codigo_servico, function(data) {
			manipula_modal("modal_carregando", 0);
			manipula_modal("modal_agendamento_" + key, 1);
			customCode.setupDatepicker(data, codigo_exame);
		});
	}

	function carregaHorarios(data, ranges, servico) {
		var data_formatada = data.split("-")[2] + "/" + data.split("-")[1] + "/" + data.split("-")[0];
		$("input[name=\"data[ItemPedidoExame][" + servico + "][data_agendamento]\"]").val(data_formatada);
		
		$("#dias_disponiveis_" + servico).html("");
		$("#texto_dia").html(" em " + data_formatada + ":");
		
		jQuery.each(ranges[data_formatada].horas_disponiveis, function(i, value) {
			if(i.length < 4)
				i = "0" + i;

			$("#dias_disponiveis_" + servico).append("<label class=\"btn-day\" style=\"margin: 10px;\">" + (i.substr(0, 2) + ":" + i.substr(2, 2)) + "</label>");
		});
		
		$(".btn-day").removeClass("pinta");
		
		$("body").on("click", ".btn-day", function() {
			marca_horario(this, servico);
		});
	}

	function marca_horario(element, servico) {
		$("input[name=\"data[ItemPedidoExame][" + servico + "][hora_agendamento]\"]").val($(element).text());
		$("#botao_" + servico).show();
		
		$(".btn-day").removeClass("pinta");
		$(element).addClass("pinta");
	}

	function filtra_hora(codigo_fornecedor, codigo_servico, element, k) {
		var data = $(element).val();
		
		$.ajax({
			type: "POST",
			url: "/portal/pedidos_exames/filtra_horario_dia",
			dataType: "html",
			data: "codigo_fornecedor=" + codigo_fornecedor + "&codigo_servico=" + codigo_servico + "&dia=" + $(element).val() + "&k=" + k,
			beforeSend: function() {
				$("#hora_" + codigo_servico).html("<img src=\"/portal/img/ajax-loader.gif\" style=\"padding: 10px;\">");
			},
			success: function(retorno) {
				$("#hora_" + codigo_servico).html(retorno);
			},
			complete: function() {

			}
		});	
	}	

	function controla_campo(element, chave) {
		if($(element).val() == "1") {
			$("#label_ordem_chegada_" + chave).hide();
			$("#agenda_" + chave).show();
		} else {
			$("#agenda_" + chave).hide();
			$("#label_ordem_chegada_" + chave).show();
		}
	}

	function controla_campo_cliente(element, chave) {
		
		if($(element).val() == "1") {
			$("#label_ordem_chegada_" + chave).hide();
			$("#opcao_" + chave).show();
		} else {
			$("#opcao_" + chave).hide();
			$("#label_ordem_chegada_" + chave).show();
		}
	}		

	function controla_agendamento_cliente(valor, chave) {
		
		if(valor == 1) {
			manipula_modal("modal_sugestao_" + chave, 1);
			$("#link_" + chave).show();
		} else {
			$("#opcao_" + chave).hide();
			$("#agenda_" + chave).show();
			$("#link_" + chave).hide();
		}			
	}		

	var customCode = {
		tooltip: "",
		init: function() {

	    	// Define o texto padrão exibido no tooltip
			this.tooltip = "Confira a programação para o dia";

	        // Tradução do datepicker para pt-BR (Brasil)
			$.datepicker.regional["pt-BR"] = {
				closeText: "Fechar"
				, prevText: "< Anterior"
				, nextText: "Próximo >"
				, currentText: "Hoje"
				, monthNames: ["Janeiro", "Fevereiro", "Mar&ccedil;o", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"]
				, monthNamesShort: ["Jan" , "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"]
				, dayNames: ["Domingo", "Segunda-feira", "Terça-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira", "Sábado"]
				, dayNamesShort: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"]
				, dayNamesMin: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"]
				, weekHeader: "Sm"
				, dateFormat: "yy-mm-dd"
				, firstDay: 0
				, isRTL: false
				, showMonthAfterYear: false
				, yearSuffix: ""
			};

		    // Configura idioma do Datepicker
			$.datepicker.setDefaults($.datepicker.regional["pt-BR"]);
		},

	    // Configura o Datepicker
		setupDatepicker: function(ranges, servico) {

			var datas_liberadas = [];
			jQuery.each(ranges, function(index, range) {
				datas_liberadas.push(range.start.split("/")[2] + "-" + range.start.split("/")[1] + "-" + range.start.split("/")[0]);
			});		
			
			$( "#datepicker" ).datepicker({
				onSelect: function(date, inst) {
					carregaHorarios(date, ranges, servico);
				},		
				beforeShowDay: function(date) {
					data_comparacao = $.datepicker.formatDate("yy-mm-dd", date);

					if(datas_liberadas.indexOf(data_comparacao) >= 0) {
						return [true, "ui-highlight", ""];
					} else {
						return [false, "", ""];
					}
				},
				minDate: customCode.getDateRange(ranges, "min"),
				maxDate: customCode.getDateRange(ranges, "max")
			});
		},
		
		getDateRange: function(ranges, type) {
			var date = new Date(), dates = [], retDate;

			if(!type)
				type = "min";

			$.each(ranges, function(index, range){
				dates.push(new Date(index.split("/")[2], Math.round(index.split("/")[1] - 1), index.split("/")[0]));
			});
			
			if (type == "max") {
				retDate = new Date(Math.max.apply(null, dates));
			}

			if (type == "min")
				retDate = new Date(Math.min.apply(null, dates));

			return retDate;
		}		
	};

	');
	 ?>