<?php echo $this->Buonny->link_css('jqueryui/hot-sneaks/jquery-ui-1.9.2.custom.css'); ?>
<?php echo $this->Buonny->link_js('jqueryui/jquery-ui-1.9.2.custom'); ?>

<style>
	.btn-day { border: 3px solid #2f96b4; border-radius: 15px; margin: 10px; padding: 3px; text-align: center; width: 70px; float: left; }
	.btn-day:hover, .pinta { background: #db4865 url("/portal/css/jqueryui/hot-sneaks/images/ui-bg_diagonals-small_40_db4865_40x40.png") repeat scroll 50% 50%; font-weight: bold; color: #FFF; }	
	.pinta-amarelo { background: #ffff38 url("/portal/css/jqueryui/hot-sneaks/images/ui-bg_diagonals-small_75_ccd232_40x40.png") repeat scroll 50% 50%; font-weight: bold; color: #888 }
	.table th, .table td { line-height: 15px; }
	.error{ border: 1px solid red !important; }
	.error-message{ display: block; position: relative; top: -9px; clear: both; color: red; }
	.mouse{
		cursor: pointer;
	}

	.container .content {
		display: none;
		padding : 5px;
	}

	.not-active {
		pointer-events: none;
		cursor: default;	
	}
</style>

<div class='inline well'>
	<?php echo $this->BForm->input('Empresa.razao_social', array('value' => $grupo_economico['Empresa']['razao_social'], 'class' => 'input-xlarge', 'label' => 'Empresa' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Empresa.codigo_documento', array('value' => Comum::formatarDocumento($grupo_economico['Empresa']['codigo_documento']), 'class' => 'input-xlarge', 'label' => 'CNPJ' , 'readonly' => true, 'type' => 'text')); ?>
	<div style="clear: both;"></div>
</div>

<div class='inline well' id="parametros">
	<img src="/portal/img/default.gif" style="padding: 10px;">
	Carregando parametrizações do pedido...
</div>

<div class="row-fluid" id="caminho-pao"></div>

<?php echo $this->BForm->create('PedidosExames', array('url' => array('controller' => 'pedidos_exames', 'action' => 'agendamento_grupo', $this->passedArgs[0]))); ?>

	<?php foreach($grupo_economico['cliente'] as $k_cliente => $cliente) : ?>

		<?php if (isset($cliente['Cliente'])) :?>
			<div style="clear: both;"><br /><br /></div>
			<div class="inline" style="border: 2px dashed #CCC; padding: 10px;">
			
				<h4><?php echo $cliente['Cliente']['razao_social']; ?></h4>
				<h5><?php echo $cliente['ClienteEndereco']['cidade'] . " / " . $cliente['ClienteEndereco']['estado_abreviacao']; ?></h5>
							
				<?php foreach($cliente['cliente_funcionario'] as $k_cliente_funcionario => $funcionario) : ?>
					<div class="inline well" style="background: #A7BACE; font-weight: bold;">
						<?php echo $this->BForm->input('Funcionario.nome', array('value' => $funcionario['Funcionario']['nome'], 'class' => 'input-xlarge', 'label' => 'Funcionario' , 'readonly' => true, 'type' => 'text')); ?>
						<?php echo $this->BForm->input('Setor.descricao', array('value' => $funcionario['Setor']['descricao'], 'class' => 'input-xlarge', 'label' => 'Setor', 'readonly' => true, 'type' => 'text')); ?>
						<?php echo $this->BForm->input('Cargo.descricao', array('value' => $funcionario['Cargo']['descricao'], 'class' => 'input-xlarge', 'label' => 'Cargo' , 'readonly' => true, 'type' => 'text')); ?>
						<div style="clear: both;"></div>
					</div>



					<div id="agendar_em_massa" class="row-fluid" style="text-align:right;margin-bottom: 10px;">
						<div >
							<div class="btn btn-success header">Agendar em massa</div>	

							<div class="content" style="display:none">
								
								<div class="well">
									<span class="" id="agenda_em_massa">
										Data: <input type="text" class="input-small data form-control obrigatorio" name="data_em_massa" />
										Hora: <input type="text" class="input-small hora form-control obrigatorio" name="hora_em_massa" />
										<br />
										<br>

										<div class="label label-important mouse voltar_agendamento_massa">Cancelar</div>
										<div class="label label-success mouse confirma_agendamento_massa">Salvar!</div>
									</span>
								</div>
								
							</div>
						</div>
					</div>

					<table class="table table-striped agendamento tabela_select_exames">
						<thead>
							<tr>
								<th><input type="checkbox" class="exames_select_all"></th>
								<th>Exame</th>
								<th>Fornecedor</th>
								<!-- <th>Tipo de exame</th>  -->
								<th>Tipo Atendimento</th>
								<th style="width: 300px;">Agendamento:</th>	            
							</tr>
						</thead>
						<tbody>
							<?php //debug($funcionario['exames_selecionados']); ?>
							<?php foreach ($funcionario['exames_selecionados'] as $k_exame => $item) : ?>								
								<?php $fornecedor_do_exame = $cliente['fornecedores_por_exame'][$grupo_economico['cliente_exame_fornecedor'][$k_cliente][$k_exame]][$k_exame]; 
								//debug($fornecedor_do_exame);
								?>
								<?php $codigo_lista_preco_produto_servico = isset($fornecedor_do_exame['ListaPrecoProdutoServico']['codigo']) ? $fornecedor_do_exame['ListaPrecoProdutoServico']['codigo'] : NULL; ?>
								
								<tr>
									<td>
										<?php
											// if(($fornecedor_do_exame['ListaPrecoProdutoServico']['tipo_atendimento'] == '1')) {
												?>
													<!-- <input type="checkbox" id="checkbox_<?= $k_exame ?>" name="checkbox" <?php echo (isset($item['Agendamento']['tipo']) && $item['Agendamento']['tipo'] == '2') ? "disabled='disabled'" : '';?> value="<?php echo $k_exame; ?>"></td> -->
												<?php
											// } else {
												?>
													<!-- <input type="checkbox" name="checkbox" disabled="disabled"></td> -->
												<?php
											// }										
										?>	

										<input type="checkbox" id="checkbox_<?= $k_exame ?>" name="checkbox" value="<?php echo $k_exame; ?>"></td>
																		
									<td>
										<?php echo $item['Exame']['descricao']; ?>
									</td>
									<td>
										<?php echo $cliente['fornecedores'][$grupo_economico['cliente_exame_fornecedor'][$k_cliente][$k_exame]]['razao_social']; ?><br />
										<?php if(!empty($fornecedor_do_exame['Servico']['telefone'])) : ?>
											<span style="font-size: 10px;">
												TELEFONE: <?php echo $fornecedor_do_exame['Servico']['telefone'];  ?>
											</span> 
										<?php endif; ?>
									</td>
									
									<!--
									<td>
										<?php echo $tipo_exame ?>
									</td>
									-->
									
									<td>
										<!-- projeto pc-3053 inclusao da data de atendimento por ordem de chegada -->
										<?php echo $this->BForm->hidden('ItemPedidoExame.'.$k_cliente_funcionario.'.' . $k_exame . '.data_agendamento', array('value' => (isset($this->data['ItemPedidoExame'][$k_cliente_funcionario][$k_exame]['data_agendamento']) ? $this->data['ItemPedidoExame'][$k_cliente_funcionario][$k_exame]['data_agendamento'] : ''))); ?>
										<?php echo $this->BForm->hidden('ItemPedidoExame.'.$k_cliente_funcionario.'.' . $k_exame . '.hora_agendamento', array('value' => (isset($this->data['ItemPedidoExame'][$k_cliente_funcionario][$k_exame]['hora_agendamento']) ? $this->data['ItemPedidoExame'][$k_cliente_funcionario][$k_exame]['hora_agendamento'] : ''))); ?>

										<?php if(($fornecedor_do_exame['ListaPrecoProdutoServico']['tipo_atendimento'] == '1') || (($fornecedor_do_exame['ListaPrecoProdutoServico']['tipo_atendimento'] == '') && $fornecedor_do_exame['Fornecedor']['tipo_atendimento'] == '1')) : ?>
											<?php echo $this->BForm->hidden('ItemPedidoExame.'.$k_cliente_funcionario.'.' . $k_exame . '.tipo_atendimento', array('value' => '1')); ?>
											HORA MARCADA
										<?php else : ?>
											<?php echo $this->BForm->hidden('ItemPedidoExame.'.$k_cliente_funcionario.'.' . $k_exame . '.tipo_atendimento', array('value' => '0', 'class' => 'ordem_chegada')); ?>
											ORDEM DE CHEGADA
										<?php endif; ?>
									</td>

									<td>										
									
										<!-- Fornecedor usa nosso sistema de agendamento ? -->
										<!-- incluido a validação do tipo de atendimento junto ao sistema de agendamento para contornar possiveis configurações erradas. CDCT-521  -->
										<?php if($fornecedor_do_exame['Fornecedor']['utiliza_sistema_agendamento'] == '1' && (($fornecedor_do_exame['ListaPrecoProdutoServico']['tipo_atendimento'] == '1') || (($fornecedor_do_exame['ListaPrecoProdutoServico']['tipo_atendimento'] == '') && $fornecedor_do_exame['Fornecedor']['tipo_atendimento'] == '1'))) : ?>
										
											<!-- Produto é Tipo Hora Marcada ? -->
											<?php if(($fornecedor_do_exame['ListaPrecoProdutoServico']['tipo_atendimento'] == '1') || (($fornecedor_do_exame['ListaPrecoProdutoServico']['tipo_atendimento'] == '') && $fornecedor_do_exame['Fornecedor']['tipo_atendimento'] == '1')) : ?>
											
												<!-- Tem agenda cadastrada ? -->
												<?php if(isset($cliente['pedido']['dados'][$grupo_economico['cliente_exame_fornecedor'][$k_cliente][$k_exame]][$item['Exame']['codigo_servico']]['Agenda']) && isset($lista_datas_disponiveis[$fornecedor_do_exame['Fornecedor']['codigo']][$item['Exame']['codigo_servico']])) : ?>
													
													<script>
														$(function(){ $("#agendar_em_massa").remove(); })
													</script>

													<div id="botao_seleciona_agenda_<?php echo $k_cliente_funcionario; ?>_<?php echo $k_exame; ?>" style="display: <?php echo isset($item['Agendamento']['data']) && !empty($item['Agendamento']['hora']) ? 'none' : 'block'; ?>;">
														<a href="javascript:void(0);" onclick="mostra_modal_agendamento(<?php echo $k_exame; ?>, <?php echo $k_cliente_funcionario; ?>, <?php echo $fornecedor_do_exame['Fornecedor']['codigo']; ?>, <?php echo $k_exame; ?>, <?php echo $item['Exame']['codigo_servico']; ?>);"><label class="label label-success">ESCOLHER MELHOR DIA E HORA!</label></a>
													</div>
													
													<div id="botao_mostra_agenda_<?php echo $k_cliente_funcionario; ?>_<?php echo $k_exame; ?>" style="display: <?php echo isset($item['Agendamento']['data']) && !empty($item['Agendamento']['hora']) ? 'block' : 'none'; ?>;">
														Data: <input type="text" class="input-small form-control pinta-amarelo" name="data[ItemPedidoExame][<?php echo $k_cliente_funcionario; ?>][<?php echo $k_exame; ?>][data_agendamento]" disabled="disabled" style="margin-right: 18px;" value="<?php echo isset($item['Agendamento']['data']) ? $item['Agendamento']['data'] : ''; ?>"/>
														Hora: <input type="text" class="input-small form-control pinta-amarelo" name="data[ItemPedidoExame][<?php echo $k_cliente_funcionario; ?>][<?php echo $k_exame; ?>][hora_agendamento]" disabled="disabled" value="<?php echo isset($item['Agendamento']['hora']) ? $item['Agendamento']['hora'] : ''; ?>"/>

														<a href="javascript:void(0);" onclick="remove_agendamento(this, '<?php echo $k_exame; ?>', '<?php echo $k_cliente_funcionario; ?>', '<?php echo $grupo_economico['pedido'][$k_cliente_funcionario]['salvo']['itens'][$k_exame]; ?>', '<?php echo $codigo_lista_preco_produto_servico; ?>', '<?php echo $codigo_grupo_economico; ?>', <?php echo $_SESSION['grupo_economico'][$codigo_grupo_economico]['pedido'][$k_cliente_funcionario]['salvo']['itens'][$k_exame]; ?>, <?php echo $fornecedor_do_exame['Fornecedor']['codigo']; ?>, <?php echo $item['Exame']['codigo_servico']; ?>);"><label class="label label-important">ALTERAR</label></a>
													</div>
													
													<div class="modal fade" id="modal_agendamento_<?php echo $k_cliente_funcionario; ?>_<?php echo $k_exame; ?>" data-backdrop="static" style="width: 85%; left: 8%; top: 15%; margin: 0 auto;">
														<div class="modal-dialog modal-sm" style="position: static;">
															<div class="modal-content">
																<div class="modal-header" style="text-align: center;">
																	<h3>AGENDAMENTO DE EXAMES:</h3>
																</div>
																<div style="margin: 5px 20px;">
																	<div class="inline well">
																		<?php echo $this->BForm->input('Empresa.razao_social', array('value' => $cliente['Cliente']['razao_social'], 'class' => 'input-large', 'label' => 'Cliente' , 'readonly' => true, 'type' => 'text')); ?>
																		<?php echo $this->BForm->input('Funcionario.nome', array('value' => $funcionario['Funcionario']['nome'], 'class' => 'input-large', 'label' => 'Funcionario' , 'readonly' => true, 'type' => 'text')); ?>
																		<?php echo $this->BForm->input('Setor.descricao', array('value' => $funcionario['Setor']['descricao'], 'class' => 'input-large', 'label' => 'Setor', 'readonly' => true, 'type' => 'text')); ?>
																		<?php echo $this->BForm->input('Cargo.descricao', array('value' => $funcionario['Cargo']['descricao'], 'class' => 'input-large', 'label' => 'Cargo' , 'readonly' => true, 'type' => 'text')); ?>
																		<div class="clear"></div>
																	</div>	            							
																</div>
					
																<div class="modal-body" style="min-height: 390px;">
																	<div class="span5" style="border-right: 1px solid #CCC;">
																		<label><b>FORNECEDOR:</b></label>
																		<?php echo $fornecedor_do_exame['Fornecedor']['razao_social']; ?>
																		<br /><br />
					
																		<label><b>EXAME:</b></label>
																		<?php echo $item['Exame']['descricao']; ?>
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
																		<div id="datepicker_<?php echo $k_cliente_funcionario; ?>_<?php echo $k_exame; ?>" class="margin-top-10"></div>
																	</div>
																	<div class="span5">
																		<b style="font-size: 18px;">Horários Disponíveis <span id="texto_dia">:</span></b>
																		<div class="well">
																			<div id="dias_disponiveis_<?php echo $k_cliente_funcionario; ?>_<?php echo $k_exame; ?>">
																				<label class="label label-info">Escolher o melhor dia, ao lado!</label>									    				
																			</div>
																			<div style="clear: both;"></div>
																		</div>
																		<br />
																	</div>
																</div>
															</div>
															<div class="modal-footer">
																<div style="float: left;">
																	<a href="javascript:void(0);" onclick="esconde_modal_agendamento(<?php echo $k_cliente_funcionario; ?>, <?php echo $k_exame; ?>);" class="btn btn-danger">CANCELAR</a>
																</div>
																<div class="right" id="botao_<?php echo $k_cliente_funcionario; ?>_<?php echo $k_exame; ?>" style="display: none;" >
																	<a href="javascript:void(0);" onclick="confirma_agendamento('<?php echo $k_exame; ?>', '<?php echo $k_cliente_funcionario; ?>', '<?php echo $grupo_economico['pedido'][$k_cliente_funcionario]['salvo']['itens'][$k_exame]; ?>', '<?php echo $codigo_lista_preco_produto_servico; ?>', '<?php echo $codigo_grupo_economico; ?>');" class="btn btn-success">CONFIRMAR</a>
																</div>
															</div>
														</div>
													</div>
													
												<?php else : ?>
													<label class="label labellabel-important">SEM AGENDA CADASTRADA *</label>
												<?php endif; ?>
											
											<!-- <?php //else : ?>
												<label class="label">ORDEM DE CHEGADA</label> -->
											<?php endif; ?>


																				
										<?php else : ?>

											<?php if(!empty($reagendamento)): ?>
												<?php if(!empty($item['Exame']['hora_agendada']) && !empty($item['Exame']['data_agendada'])): ?>
													<div class="well">
														<strong>Agendamento Anterior:</strong><br /><br />
														<p><b>Data: </b><?php echo $item['Exame']['data_agendada']; ?>&nbsp;&nbsp;/&nbsp;&nbsp;<b>Horário: </b><?php echo $item['Exame']['hora_agendada']; ?></label>												
													</div>
												<?php endif; ?>								
											<?php endif; ?>

											<div id="resumo_sugestoes_<?php echo $k_cliente_funcionario; ?>_<?php echo $k_exame; ?>" class="well" style="margin-bottom: -20px; display: <?php echo (isset($item['Agendamento']['sugestoes']) && count($item['Agendamento']['sugestoes'])) ? 'block' : 'none'; ?>">
												<strong>Sugestões de Agendamento: </strong><br /><br />
												<?php if(isset($item['Agendamento']['sugestoes']) && count($item['Agendamento']['sugestoes'])) : ?>
													<?php foreach($item['Agendamento']['sugestoes'] as $k => $sugestao) : ?>
														<label>Data 0<?php echo $k + 1; ?>: <?php echo $sugestao['data']; ?><?php echo (!empty($sugestao['hora']) && ($sugestao['hora'] != 0)) ? ' Horário: ' . $sugestao['hora'] : ''; ?></label>
													<?php endforeach; ?>
												<?php endif; ?>
											</div>
											
											<div id="link_remover_sugestao_<?php echo $k_cliente_funcionario; ?>_<?php echo $k_exame; ?>" style="display: <?php echo (isset($item['Agendamento']['tipo']) && ($item['Agendamento']['tipo'] == '3') && (isset($item['Agendamento']['sugestoes']) && count($item['Agendamento']['sugestoes']))) ? 'block' : 'none'; ?>; padding-left: 15px;">
												<a href="javascript:void(0);" onclick="remove_sugestao('<?php echo $k_exame; ?>', '<?php echo $k_cliente_funcionario; ?>', '<?php echo $grupo_economico['pedido'][$k_cliente_funcionario]['salvo']['itens'][$k_exame]; ?>', '<?php echo $codigo_lista_preco_produto_servico; ?>', '<?php echo $codigo_grupo_economico; ?>');" class="label label-important">Remover Sugestões!</a>
											</div>

											<?php if(!empty($reagendamento)): ?>
												<div class="well" style="display: <?php echo (isset($item['Agendamento']['tipo']) && $item['Agendamento']['tipo'] == '2') ? 'block' : 'none'; ?>">
													<strong>Agendamento Atual:</strong><br /><br />
													<p><b>Data: </b><?php echo $item['Agendamento']['data']; ?>&nbsp;&nbsp;/&nbsp;&nbsp;<b>Horário: </b><?php echo $item['Agendamento']['hora']; ?></label>												
												</div>
											<?php else: ?>
												<div class="well" style="display: <?php echo (isset($item['Agendamento']['tipo']) && $item['Agendamento']['tipo'] == '2') ? 'block' : 'none'; ?>">
													<strong>Agendamento Próprio:</strong><br /><br />
													<p><b>Data: </b><?php echo $item['Agendamento']['data']; ?>&nbsp;&nbsp;/&nbsp;&nbsp;<b>Horário: </b><?php echo $item['Agendamento']['hora']; ?></label>												
												</div>
											<?php endif; ?>										
					
											<label class="label" id="label_ordem_chegada_<?php echo $k_cliente_funcionario; ?>_<?php echo $k_exame; ?>" style="display: <?php echo $fornecedor_do_exame['ListaPrecoProdutoServico']['tipo_atendimento'] == '1' ? 'none' : 'none'; ?>">ORDEM DE CHEGADA</label>

											<?php 
											$tipo_atendimento = 1;
											if($fornecedor_do_exame['ListaPrecoProdutoServico']['tipo_atendimento'] != '1') {
												$tipo_atendimento = 0;
											} 
											?>

											<?php if(empty($item['Agendamento']['data']) && empty($item['Agendamento']['hora']) ): ?>												
												
												<span id="agenda_<?php echo $k_cliente_funcionario; ?>_<?php echo $k_exame; ?>">
													Data: <input type="text" class="input-small data form-control obrigatorio obr_odc" name="data[AgendamentoProprio][<?php echo $k_cliente_funcionario; ?>][<?php echo $k_exame; ?>][data_agendamento]" />
													
													<?php 
													if($tipo_atendimento == 0) :
													?>
														<input type="hidden" class="input-small hora form-control obrigatorio " name="data[AgendamentoProprio][<?php echo $k_cliente_funcionario; ?>][<?php echo $k_exame; ?>][hora_agendamento]" value="00:00"/>
													<?php
													//verifica se é hora marcada
													elseif($tipo_atendimento == '1') : 
													?>

														Hora: <input type="text" class="input-small hora form-control obrigatorio " name="data[AgendamentoProprio][<?php echo $k_cliente_funcionario; ?>][<?php echo $k_exame; ?>][hora_agendamento]" />
														<br />
														<span style="font-size: 0.9em; border-bottom: 1px solid #CCC; background: #EFEFEF; padding: 1px;">
															
															<?php
															
															//Pega o codigo do fornecedor
																$codigo_fornecedor = $fornecedor_do_exame['Fornecedor']['codigo'];

																$descricao_contato_fornecedor = $_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$_SESSION['grupo_economico'][$codigo_grupo_economico]['Empresa']['codigo']]['descricao_contato_fornecedor'][$codigo_fornecedor]['descricao_contato'];
															
															?>
															<b>ATENÇÃO:</b> <?= $descricao_contato_fornecedor?><br />
															
															


															<?php
															//Adiciona contatos do fornecedor a listagem de exames											
															$contatos_do_fornecedor = $_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$_SESSION['grupo_economico'][$codigo_grupo_economico]['Empresa']['codigo']]['contatos_do_fornecedor'][$codigo_fornecedor];
															
															if (!empty($contatos_do_fornecedor)) {
																foreach ($contatos_do_fornecedor as $key => $contato) {
																	?>													
																	<span style="font-weight: bold;">
																		<strong><?= $contato['TipoRetorno']['descricao'] ?>:</strong> <?= !empty($contato['FornecedorContato']['ddd']) ? "({$contato['FornecedorContato']['ddd']}) " . $contato['FornecedorContato']['descricao'] : $contato['FornecedorContato']['descricao'] ?>
																	</span><br>														
																	<?php
																}
															} else {
																if(!empty($fornecedor_do_exame['Servico']['telefone'])) : ?>
																	<span style="font-weight: bold;">
																		<strong>TELEFONE:</strong> <?php echo $fornecedor_do_exame['Servico']['telefone'];  ?>
																	</span><br>
																<?php endif;
															}												
															?>
														</span>

													<?php endif; ?>
													<br />

													<!-- chave, codigo_cliente_funcionario, codigo_item_pedido, codigo_lista_preco_produto_servico, codigo_grupo_economico  -->
													<input type="hidden" name="chave" value="<?php echo $k_exame; ?>">
													<input type="hidden" name="codigo_cliente_funcionario" value="<?php echo $k_cliente_funcionario; ?>">
													<input type="hidden" name="codigo_item_pedido" value="<?php echo $grupo_economico['pedido'][$k_cliente_funcionario]['salvo']['itens'][$k_exame]; ?>">
													<input type="hidden" name="codigo_lista_preco_produto_servico" value="<?php echo $codigo_lista_preco_produto_servico; ?>">
													<input type="hidden" name="codigo_grupo_economico" value="<?php echo $codigo_grupo_economico; ?>">

													<?php if(!empty($reagendamento)): ?>
														<input type="hidden" name="codigo_agendamento" value="<?php echo $item['Exame']['codigo_agendamento']; ?>">
													<?php endif; ?>

													<div onclick="voltar_agendamento_cliente('<?php echo $k_exame; ?>', '<?php echo $k_cliente_funcionario; ?>');" class="label label-important mouse">Cancelar</div>

													<?php if(!empty($reagendamento)): ?>
														<div  onclick="realizar_reagendamento('<?php echo $k_exame; ?>', '<?php echo $k_cliente_funcionario; ?>', '<?php echo $grupo_economico['pedido'][$k_cliente_funcionario]['salvo']['itens'][$k_exame]; ?>', '<?php echo $codigo_lista_preco_produto_servico; ?>', '<?php echo $codigo_grupo_economico; ?>', '<?php echo $item['Exame']['codigo_agendamento']; ?>', '<?php echo $tipo_atendimento; ?>');" class="label label-success mouse">Salvar!</div>

													<?php else : ?>
														<div  onclick="confirma_agendamento_proprio('<?php echo $k_exame; ?>', '<?php echo $k_cliente_funcionario; ?>', '<?php echo $grupo_economico['pedido'][$k_cliente_funcionario]['salvo']['itens'][$k_exame]; ?>', '<?php echo $codigo_lista_preco_produto_servico; ?>', '<?php echo $codigo_grupo_economico; ?>','<?php echo $tipo_atendimento; ?>');" class="label label-success mouse">Salvar!</div>
													<?php endif; ?>
												</span>
											<?php else: ?>

												<?php if(empty($item['Agendamento']['data']) ): ?>
													<span id="agenda_<?php echo $k_cliente_funcionario; ?>_<?php echo $k_exame; ?>">
														Data: <input type="text" class="input-small data form-control obrigatorio" name="data[AgendamentoProprio][<?php echo $k_cliente_funcionario; ?>][<?php echo $k_exame; ?>][data_agendamento]" />
														
	
														
														Hora: <input type="text" class="input-small hora form-control obrigatorio" name="data[AgendamentoProprio][<?php echo $k_cliente_funcionario; ?>][<?php echo $k_exame; ?>][hora_agendamento]" />
														<br />
														<span style="font-size: 0.9em; border-bottom: 1px solid #CCC; background: #EFEFEF; padding: 1px;">
															
															<?php
															
															//Pega o codigo do fornecedor
																$codigo_fornecedor = $fornecedor_do_exame['Fornecedor']['codigo'];
	
																$descricao_contato_fornecedor = $_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$_SESSION['grupo_economico'][$codigo_grupo_economico]['Empresa']['codigo']]['descricao_contato_fornecedor'][$codigo_fornecedor]['descricao_contato'];
															
															?>
															<b>ATENÇÃO:</b> <?= $descricao_contato_fornecedor?><br />
															
															
	
	
															<?php
															//Adiciona contatos do fornecedor a listagem de exames											
															$contatos_do_fornecedor = $_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$_SESSION['grupo_economico'][$codigo_grupo_economico]['Empresa']['codigo']]['contatos_do_fornecedor'][$codigo_fornecedor];
															
															if (!empty($contatos_do_fornecedor)) {
																foreach ($contatos_do_fornecedor as $key => $contato) {
																	?>													
																	<span style="font-weight: bold;">
																		<strong><?= $contato['TipoRetorno']['descricao'] ?>:</strong> <?= !empty($contato['FornecedorContato']['ddd']) ? "({$contato['FornecedorContato']['ddd']}) " . $contato['FornecedorContato']['descricao'] : $contato['FornecedorContato']['descricao'] ?>
																	</span><br>														
																	<?php
																}
															} else {
																if(!empty($fornecedor_do_exame['Servico']['telefone'])) : ?>
																	<span style="font-weight: bold;">
																		<strong>TELEFONE:</strong> <?php echo $fornecedor_do_exame['Servico']['telefone'];  ?>
																	</span><br>
																<?php endif;
															}												
															?>
														</span>
														
														<br />
	
														<!-- chave, codigo_cliente_funcionario, codigo_item_pedido, codigo_lista_preco_produto_servico, codigo_grupo_economico  -->
														<input type="hidden" name="chave" value="<?php echo $k_exame; ?>">
														<input type="hidden" name="codigo_cliente_funcionario" value="<?php echo $k_cliente_funcionario; ?>">
														<input type="hidden" name="codigo_item_pedido" value="<?php echo $grupo_economico['pedido'][$k_cliente_funcionario]['salvo']['itens'][$k_exame]; ?>">
														<input type="hidden" name="codigo_lista_preco_produto_servico" value="<?php echo $codigo_lista_preco_produto_servico; ?>">
														<input type="hidden" name="codigo_grupo_economico" value="<?php echo $codigo_grupo_economico; ?>">
	
														<div onclick="voltar_agendamento_cliente('<?php echo $k_exame; ?>', '<?php echo $k_cliente_funcionario; ?>');" class="label label-important mouse">Cancelar</div>
	
	
														<div  onclick="confirma_agendamento_proprio('<?php echo $k_exame; ?>', '<?php echo $k_cliente_funcionario; ?>', '<?php echo $grupo_economico['pedido'][$k_cliente_funcionario]['salvo']['itens'][$k_exame]; ?>', '<?php echo $codigo_lista_preco_produto_servico; ?>', '<?php echo $codigo_grupo_economico; ?>','<?php echo $tipo_atendimento; ?>');" class="label label-success mouse">Salvar!</div>
													
													</span>
												<?php endif; ?>
												
											<?php endif; ?>
											
											<!-- É tipo HORA MARCADA ? -->
											<?php if($fornecedor_do_exame['ListaPrecoProdutoServico']['tipo_atendimento'] != '1') : ?>

											<?php //else : ?>
												<?php echo $this->BForm->hidden('OrdemChegada', array('value' => '1')); ?>
												<!-- <label class="label">ORDEM DE CHEGADA</label>											 -->
												
												<?php if(!empty($reagendamento)): ?>
													<?php if($item['Exame']['status_baixa'] == 'baixado'): ?>
														<label class="badge badge-empty badge-important">BAIXADO</label>
													<?php endif; ?>
												<?php endif; ?>

											<?php endif; ?>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endforeach; ?>
			</div>	
		<?php endif; ?>										
	<?php endforeach; ?>

	<div class='form-actions well'>
		<a href="/portal/pedidos_exames/inclusao_em_massa/<?php echo $this->passedArgs[0]; ?>" class="btn">Voltar</a>
		<a href="javascript:void(0);" onclick="validaFormAgendamento('<?php echo $codigo_grupo_economico; ?>');" class="btn btn-primary btn-ok">Avançar</a>
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

<div class="modal fade" id="modal_carregando_validacao">
	<div class="modal-dialog modal-sm" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="gridSystemModalLabel">Aguarde, verificando os agendamentos...</h4>
			</div>
	    	<div class="modal-body">
	    		<img src="/portal/img/ajax-loader.gif" style="padding: 10px;">
	    	</div>
	    </div>
	</div>
</div>

<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function() {
		customCode.init();
		
		setup_mascaras(); setup_time(); setup_datepicker();
		$(".modal").css("z-index", "-1");
		
		// seta etapa
		$("#caminho-pao").load("/portal/pedidos_exames/caminho_pao/3");
		atualiza_parametros("'.$codigo_grupo_economico.'");
	});
		
	function atualiza_parametros(codigo_grupo_economico) {
		$("#parametros").load("/portal/pedidos_exames/carrega_parametros/" + codigo_grupo_economico + "/1");
	}				
		
	function confirma_agendamento(chave, codigo_cliente_funcionario, codigo_item_pedido, codigo_lista_preco_produto_servico, codigo_grupo_economico) {
		
		data_agendamento = $("input[name=\"data[ItemPedidoExame][" + codigo_cliente_funcionario + "][" + chave + "][data_agendamento]\"]").val();
		hora_agendamento = $("input[name=\"data[ItemPedidoExame][" + codigo_cliente_funcionario + "][" + chave + "][hora_agendamento]\"]").val();
		
		data_formatada = data_agendamento.split("/")[2] + "-" + data_agendamento.split("/")[1] + "-" + data_agendamento.split("/")[0];
		hora_formatada = hora_agendamento.split(":")[0] + hora_agendamento.split(":")[1];
		
		var bkp_elemento = $("#botao_" + codigo_cliente_funcionario + "_" + chave).html();
		
		$.ajax({
	        type: "POST",
	        url: "/portal/pedidos_exames/grava_agendamento",
	        dataType: "json",
	        data: "codigo_exame=" + chave + "&codigo_grupo_economico=" + codigo_grupo_economico + "&codigo_item_pedido=" + codigo_item_pedido + "&data_agendamento=" + data_formatada + "&hora_agendamento=" + hora_formatada + "&codigo_lista_preco_produto_servico=" + codigo_lista_preco_produto_servico,
			beforeSend: function() {
				$("#botao_" + codigo_cliente_funcionario + "_" + chave).html("<img src=\"/portal/img/default.gif\" > Gravando na Agenda!")
			},
	        success: function(retorno) {
				manipula_modal("modal_agendamento_" + codigo_cliente_funcionario + "_" + chave, 0);
	        },
			complete: function() {
		
				$("#botao_seleciona_agenda_" + codigo_cliente_funcionario + "_" + chave).hide();
		
				$("#data_selecioda_agenda_" + codigo_cliente_funcionario + "_" + chave).show();
				$("#data_selecioda_agenda_" + codigo_cliente_funcionario + "_" + chave).html("<label><b>Data: </b>" + data_agendamento + "</label>");
				$("#data_selecioda_agenda_" + codigo_cliente_funcionario + "_" + chave).append("<label><b>Hora: </b>" + hora_agendamento + "</label>");

				$("#botao_" + codigo_cliente_funcionario + "_" + chave).html(bkp_elemento);
			}
	    });		
	}
		
	function remove_agendamento(elemento, chave, codigo_cliente_funcionario, codigo_item_pedido, codigo_lista_preco_produto_servico, codigo_grupo_economico, codigo_fornecedor, codigo_servico) {
		
		var bkp_elemento = $(elemento).html();
		
		$.ajax({
	        type: "POST",
	        url: "/portal/pedidos_exames/remove_agendamento",
	        dataType: "json",
	        data: "codigo_exame=" + chave + "&codigo_grupo_economico=" + codigo_grupo_economico + "&codigo_item_pedido=" + codigo_item_pedido + "&codigo_lista_preco_produto_servico=" + codigo_lista_preco_produto_servico,
			beforeSend: function() {
				$(elemento).html("<img src=\"/portal/img/default.gif\" >");
			},
	        success: function(retorno) {
				if(retorno) {
					$("input[name=\"data[ItemPedidoExame][" + codigo_cliente_funcionario + "][" + chave + "][data_agendamento]\"]").val("");
					$("input[name=\"data[ItemPedidoExame][" + codigo_cliente_funcionario + "][" + chave + "][hora_agendamento]\"]").val("");
		
					// mostra modal e recarrega a agenda
					mostra_modal_agendamento(chave, codigo_cliente_funcionario, codigo_fornecedor, chave, codigo_servico);
					$("#dias_disponiveis_" + codigo_cliente_funcionario + "_" + chave).html("<label class=\"label label-info\">Escolher o melhor dia, ao lado!</label>")
				}
	        },
			complete: function() {
				$(elemento).html(bkp_elemento);
			}
	    });	
		
	}
		
	function confirma_agendamento_proprio(chave, codigo_cliente_funcionario, codigo_item_pedido, codigo_lista_preco_produto_servico, codigo_grupo_economico, tipo_atendimento) {
		
		data_agendamento = $("input[name=\"data[AgendamentoProprio][" + codigo_cliente_funcionario + "][" + chave + "][data_agendamento]\"]").val();
		hora_agendamento = $("input[name=\"data[AgendamentoProprio][" + codigo_cliente_funcionario + "][" + chave + "][hora_agendamento]\"]").val();
		
		if(tipo_atendimento == 0) {
			if(data_agendamento != "") {
				data_formatada = data_agendamento.split("/")[2] + "-" + data_agendamento.split("/")[1] + "-" + data_agendamento.split("/")[0];
				hora_formatada = hora_agendamento.split(":")[0] + hora_agendamento.split(":")[1];
				
				$.ajax({
			        type: "POST",
			        url: "/portal/pedidos_exames/grava_agendamento_proprio",
			        dataType: "json",
			        data: 	"codigo_grupo_economico=" + codigo_grupo_economico + 
			        		"&codigo_exame=" + chave + 
			        		"&codigo_item_pedido=" + codigo_item_pedido + 
			        		"&data_agendamento=" + data_formatada + 
			        		"&hora_agendamento=" + hora_formatada + 
			        		"&codigo_lista_preco_produto_servico=" + codigo_lista_preco_produto_servico +
			        		"&tipo_atendimento="+ tipo_atendimento,
					beforeSend: function() {
						$("#agenda_" + codigo_cliente_funcionario + "_" + chave).html("<img src=\"/portal/img/default.gif\" > Gravando na Agenda!");
					},
			        success: function(retorno) {
						if(retorno) {
							$("#agenda_" + codigo_cliente_funcionario + "_" + chave).html("<div class=\"well\"></div>");
				
							var conteudo = "<strong>Agendamento Próprio:</strong><br /><br />";
							conteudo = conteudo + "<p><b>Data: </b>" + data_agendamento;
				
							// if(hora_agendamento) {
							// 	conteudo = conteudo + " / <b>Horário: </b>" + hora_agendamento;
							// } 
				
							conteudo = conteudo + "</p>";
							$("#agenda_" + codigo_cliente_funcionario + "_" + chave + " .well").append(conteudo);
							$("#checkbox_" + chave).attr("disabled", "disabled");
						}
			        },
					complete: function() {
				
						var qtd_disabled = 0;						
						var qtd_checkbox = $("table.agendamento tr td input:checkbox").length;

						$("table.agendamento tr").each(function(){

							var chave_checkbox = $(this).find(":input[name=\"checkbox\"]");
							
							if (chave_checkbox.is(":disabled")) {
								qtd_disabled++;
							}
							
						});
				
						if (qtd_checkbox == qtd_disabled) {
							$("#agendar_em_massa").remove();
							console.log("Destroi");
						}
					}
			    });
			}
			else {
				swal("Atenção", "Você deve preencher a data agendada!", "error");
			}

		}
		else {

			if(data_agendamento != "" && hora_agendamento != "") {
			
				swal({
				  title: "Confirmar agendamento para " + data_agendamento + " às " + hora_agendamento + " hrs ?",
				  text: "Lembre-se, esta é data que você agendou diretamente com a clínica! Só confirme as informações se realmente estiverem corretas!",
				  type: "warning",
				  showCancelButton: true,
				  confirmButtonColor: "#47B22C",
				  confirmButtonText: "Sim, eu LIGUEI e AGENDEI a data!",
				  closeOnConfirm: true
				},
				function(){
				  
					data_formatada = data_agendamento.split("/")[2] + "-" + data_agendamento.split("/")[1] + "-" + data_agendamento.split("/")[0];
					hora_formatada = hora_agendamento.split(":")[0] + hora_agendamento.split(":")[1];
					
					$.ajax({
				        type: "POST",
				        url: "/portal/pedidos_exames/grava_agendamento_proprio",
				        dataType: "json",
				        data: 	"codigo_grupo_economico=" + codigo_grupo_economico + 
				        		"&codigo_exame=" + chave + 
				        		"&codigo_item_pedido=" + codigo_item_pedido + 
				        		"&data_agendamento=" + data_formatada + 
				        		"&hora_agendamento=" + hora_formatada + 
				        		"&codigo_lista_preco_produto_servico=" + codigo_lista_preco_produto_servico +
				        		"&tipo_atendimento="+ tipo_atendimento,
						beforeSend: function() {
							$("#agenda_" + codigo_cliente_funcionario + "_" + chave).html("<img src=\"/portal/img/default.gif\" > Gravando na Agenda!");
						},
				        success: function(retorno) {
							if(retorno) {
								$("#agenda_" + codigo_cliente_funcionario + "_" + chave).html("<div class=\"well\"></div>");
					
								var conteudo = "<strong>Agendamento Próprio:</strong><br /><br />";
								conteudo = conteudo + "<p><b>Data: </b>" + data_agendamento;
					
								if(hora_agendamento) {
									conteudo = conteudo + " / <b>Horário: </b>" + hora_agendamento;
								} 
					
								conteudo = conteudo + "</p>";
								$("#agenda_" + codigo_cliente_funcionario + "_" + chave + " .well").append(conteudo);
								$("#checkbox_" + chave).attr("disabled", "disabled");
							}
				        },
						complete: function() {
					
							var qtd_disabled = 0;						
							var qtd_checkbox = $("table.agendamento tr td input:checkbox").length;

							$("table.agendamento tr").each(function(){

								var chave_checkbox = $(this).find(":input[name=\"checkbox\"]");
								
								if (chave_checkbox.is(":disabled")) {
									qtd_disabled++;
								}
								
							});
					
							if (qtd_checkbox == qtd_disabled) {
								$("#agendar_em_massa").remove();
								console.log("Destroi");
							}
						}
				    });					
				
				});
					
			} else {
				swal("Atenção", "Você deve preencher a data/hora agendada!", "error");
			}
		}//fim else tipo_atendimento


		
	}

	function realizar_reagendamento(chave, codigo_cliente_funcionario, codigo_item_pedido, codigo_lista_preco_produto_servico, codigo_grupo_economico, codigo_agendamento, tipo_atendimento) {
		
		data_agendamento = $("input[name=\"data[AgendamentoProprio][" + codigo_cliente_funcionario + "][" + chave + "][data_agendamento]\"]").val();
		hora_agendamento = $("input[name=\"data[AgendamentoProprio][" + codigo_cliente_funcionario + "][" + chave + "][hora_agendamento]\"]").val();
			
		if(data_agendamento != "" && hora_agendamento != "") {
		
			swal({
			  title: "Confirmar agendamento para " + data_agendamento + " às " + hora_agendamento + " hrs ?",
			  text: "Lembre-se, esta é data que você agendou diretamente com a clínica! Só confirme as informações se realmente estiverem corretas!",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonColor: "#47B22C",
			  confirmButtonText: "Sim, eu LIGUEI e AGENDEI a data!",
			  closeOnConfirm: true
			},
			function(){
			  
				data_formatada = data_agendamento.split("/")[2] + "-" + data_agendamento.split("/")[1] + "-" + data_agendamento.split("/")[0];
				hora_formatada = hora_agendamento.split(":")[0] + hora_agendamento.split(":")[1];
				
				$.ajax({
			        type: "POST",
			        url: "/portal/pedidos_exames/grava_agendamento_proprio",
			        dataType: "json",
			        data: "codigo_grupo_economico=" + codigo_grupo_economico + "&codigo_exame=" + chave + "&codigo_item_pedido=" + codigo_item_pedido + "&data_agendamento=" + data_formatada + "&hora_agendamento=" + hora_formatada + "&codigo_lista_preco_produto_servico=" + codigo_lista_preco_produto_servico + "&codigo_agendamento=" + codigo_agendamento + "&tipo_atendimento=" + tipo_atendimento,
					beforeSend: function() {
						$("#agenda_" + codigo_cliente_funcionario + "_" + chave).html("<img src=\"/portal/img/default.gif\" > Gravando na Agenda!");
					},
			        success: function(retorno) {
						if(retorno) {
							$("#agenda_" + codigo_cliente_funcionario + "_" + chave).html("<div class=\"well\"></div>");
				
							var conteudo = "<strong>Agendamento Atual:</strong><br /><br />";
							conteudo = conteudo + "<p><b>Data: </b>" + data_agendamento;
				
							if(hora_agendamento) {
								conteudo = conteudo + " / <b>Horário: </b>" + hora_agendamento;
							} 
				
							conteudo = conteudo + "</p>";
							$("#agenda_" + codigo_cliente_funcionario + "_" + chave + " .well").append(conteudo);
							$("#checkbox_" + chave).attr("disabled", "disabled");
						}
			        },
					complete: function() {
				
						var qtd_disabled = 0;						
						var qtd_checkbox = $("table.agendamento tr td input:checkbox").length;

						$("table.agendamento tr").each(function(){

							var chave_checkbox = $(this).find(":input[name=\"checkbox\"]");
							
							if (chave_checkbox.is(":disabled")) {
								qtd_disabled++;
							}
							
						});
				
						if (qtd_checkbox == qtd_disabled) {
							$("#agendar_em_massa").remove();
							console.log("Destroi");
						}
					}
			    });					
			
			});
				
		} else {
			swal("Atenção", "Você deve preencher a data/hora agendada!", "error");
		}
		
	}		
		
	function confirma_agendamento_sugestao(chave, codigo_cliente_funcionario, codigo_item_pedido, codigo_lista_preco_produto_servico, codigo_grupo_economico) {
		
		data_01 = $("input[name=\"data[ItemPedidoExame][" + codigo_cliente_funcionario + "][" + chave + "][sugestao][0][data_sugestao_agendamento]\"]").val();
		hora_01 = $("input[name=\"data[ItemPedidoExame][" + codigo_cliente_funcionario + "][" + chave + "][sugestao][0][hora_sugestao_agendamento]\"]").val();
		
		data_02 = $("input[name=\"data[ItemPedidoExame][" + codigo_cliente_funcionario + "][" + chave + "][sugestao][1][data_sugestao_agendamento]\"]").val();
		hora_02 = $("input[name=\"data[ItemPedidoExame][" + codigo_cliente_funcionario + "][" + chave + "][sugestao][1][hora_sugestao_agendamento]\"]").val();
		
		data_03 = $("input[name=\"data[ItemPedidoExame][" + codigo_cliente_funcionario + "][" + chave + "][sugestao][2][data_sugestao_agendamento]\"]").val();
		hora_03 = $("input[name=\"data[ItemPedidoExame][" + codigo_cliente_funcionario + "][" + chave + "][sugestao][2][hora_sugestao_agendamento]\"]").val();
		
		if(data_01 != "") {
		
			data_01_formatada = data_01.split("/")[2] + "-" + data_01.split("/")[1] + "-" + data_01.split("/")[0];
			
			if(hora_01 != "")
				hora_01_formatada = hora_01.split(":")[0] + hora_01.split(":")[1];
	
			if(data_02 != "")
				data_02_formatada = data_02.split("/")[2] + "-" + data_02.split("/")[1] + "-" + data_02.split("/")[0];
			
			if(hora_02 != "")
				hora_02_formatada = hora_02.split(":")[0] + hora_02.split(":")[1];
			
			if(data_03 != "")
				data_03_formatada = data_03.split("/")[2] + "-" + data_03.split("/")[1] + "-" + data_03.split("/")[0];
			
			if(hora_03 != "")
				hora_03_formatada = hora_03.split(":")[0] + hora_03.split(":")[1];
	
			var data = "";
			
			if(typeof data_01_formatada != "undefined")
				data = "data_01=" + data_01_formatada;
			
			if(typeof hora_01_formatada != "undefined")
				data = data + "&hora_01=" + hora_01_formatada;
			
			if(typeof data_02_formatada != "undefined")
				data = data + "&data_02=" + data_02_formatada;
			
			if(typeof hora_02_formatada != "undefined")
				data=data + "&hora_02=" + hora_02_formatada;
			
			if(typeof data_03_formatada != "undefined")
				data = data + "&data_03=" + data_03_formatada;
			
			if(typeof hora_03_formatada != "undefined")
				data=data + "&hora_03=" + hora_03_formatada;
		
			var bkp_elemento = $("#botoes_sugestao_" + codigo_cliente_funcionario + "_" + chave).html();
		
			$.ajax({
		        type: "POST",
		        url: "/portal/pedidos_exames/grava_agendamento_sugestao",
		        dataType: "json",
		        data: data + "&codigo_exame=" + chave + "&codigo_grupo_economico=" + codigo_grupo_economico + "&codigo_item_pedido=" + codigo_item_pedido + "&codigo_lista_preco_produto_servico=" + codigo_lista_preco_produto_servico,
				beforeSend: function() {
					$("#botoes_sugestao_" + codigo_cliente_funcionario + "_" + chave).html("<img src=\"/portal/img/default.gif\" > Gravando Sugestões!")
				},
		        success: function(retorno) {
					if(retorno) {
						$("#opcao_" + codigo_cliente_funcionario + "_" + chave).hide();
						$("#resumo_sugestoes_" + codigo_cliente_funcionario + "_" + chave).show().html("<div class=\"well\"><strong>Sugestões de Agendamento:</strong></div>");
			
						if(hora_01 != "") {
							$("#resumo_sugestoes_" + codigo_cliente_funcionario + "_" + chave + " .well").append("<label>Data 01: " + data_01 + " Horário: " + hora_01 + "</label>");
						} else {
							$("#resumo_sugestoes_" + codigo_cliente_funcionario + "_" + chave + " .well").append("<label>Data 01: " + data_01 + "</label>");				
						}
			
						if(data_02 != "") {
							if(hora_02 != "") {
								$("#resumo_sugestoes_" + codigo_cliente_funcionario + "_" + chave + " .well").append("<label>Data 02: " + data_02 + " Horário: " + hora_02 + "</label>");
							} else {
								$("#resumo_sugestoes_" + codigo_cliente_funcionario + "_" + chave + " .well").append("<label>Data 02: " + data_02 + "</label>");				
							}		
						}
			
						if(data_03 != "") {
							if(hora_03 != "") {
								$("#resumo_sugestoes_" + codigo_cliente_funcionario + "_" + chave + " .well").append("<label>Data 03: " + data_03 + " Horário: " + hora_03 + "</label>");
							} else {
								$("#resumo_sugestoes_" + codigo_cliente_funcionario + "_" + chave + " .well").append("<label>Data 03: " + data_03 + "</label>");
							}
						}
		
						$("#link_remover_sugestao_" + codigo_cliente_funcionario + "_" + chave).show();
						manipula_modal("modal_sugestao_" + codigo_cliente_funcionario + "_" + chave, 0);
					}
		        },
				complete: function() {
					$("#botoes_sugestao_" + codigo_cliente_funcionario + "_" + chave).html(bkp_elemento);
				}
		    });
		
		} else {
			$("#valida_" + codigo_cliente_funcionario  + "_" + chave).show().delay(4000).queue(function () { $(this).hide(); });
		}
	}
		
	function remove_sugestao(codigo_exame, codigo_cliente_funcionario, codigo_item_pedido, codigo_lista_preco_produto_servico, codigo_grupo_economico) {
		
		var bkp_elemento = $("#link_remover_sugestao_" + codigo_cliente_funcionario + "_" + codigo_exame).html();
		
		$.ajax({
	        type: "POST",
	        url: "/portal/pedidos_exames/remove_sugestao",
	        dataType: "json",
	        data: "codigo_item_pedido=" + codigo_item_pedido + "&codigo_grupo_economico=" + codigo_grupo_economico + "&codigo_exame=" + codigo_exame  + "&codigo_cliente_funcionario="  + codigo_cliente_funcionario,
			beforeSend: function() {
				$("#resumo_sugestoes_" + codigo_cliente_funcionario + "_" + codigo_exame).hide();
				$("#link_remover_sugestao_" + codigo_cliente_funcionario + "_" + codigo_exame).html("<img src=\"/portal/img/default.gif\" > Excluíndo Sugestões!");
			},
	        success: function(retorno) {
				if(retorno) {
					$("#opcao_" + codigo_cliente_funcionario + "_" + codigo_exame).show();		
				}
	        },
			complete: function() {
				$("#link_remover_sugestao_" + codigo_cliente_funcionario + "_" + codigo_exame).html(bkp_elemento).hide();
			}
		});
	}
		
	function confirma_sugestao(chave, codigo_cliente_funcionario) {
		$("#resumo_sugestoes_" + codigo_cliente_funcionario + "_" + chave).show().html("<label class=\"label label-important\"> AGENDAMENTO SUGERIDO - RETORNO EM 48H. </label>");
	}		
		
	function validaFormAgendamento(codigo_grupo_economico) {
		
		var ordem_chegada = $("#PedidosExamesOrdemChegada").val();
		// console.log("ordem");
		// console.log(ordem_chegada);
		
		var validation_ordem_chegada = 1;

		if(ordem_chegada == 1) {
			$(".obr_odc").each(function(index, obj){
			    
			 //    console.log("valor");
			 //    console.log(index);
			 //    console.log(obj.name);
				// console.log(obj.value);
				
			    if(obj.value == "") {
			    	swal("Atenção", "O agendamento de todos os exames é obrigatório nesta etapa!", "error");
			    	validation_ordem_chegada = 0;
			    	return false;
			    }
			});
		}

		// return;
		if(validation_ordem_chegada == 1) {
			valido = 0;
			$.ajax({
		        type: "POST",
		        url: "/portal/pedidos_exames/valida_agendamento_grupo",
		        dataType: "json",
		        data: "codigo_grupo_economico=" + codigo_grupo_economico,
				beforeSend: function() {
					manipula_modal("modal_carregando_validacao", 1);				
				},
		        success: function(retorno) {
					valido = retorno;
		        },
				complete: function() {
					if(valido) {
						$("#PedidosExamesAgendamentoGrupoForm").submit();
					} else {
						manipula_modal("modal_carregando_validacao", 0);
						swal("Atenção", "O agendamento de todos os exames é obrigatório nesta etapa!", "error");
					}		
				}
			});

		}//fim validation_ordem_chegada

		
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
		
	function mostra_modal_agendamento(key, codigo_cliente_funcionario, codigo_fornecedor, codigo_exame, codigo_servico) {
		manipula_modal("modal_carregando", 1);
		
		$.getJSON("/portal/pedidos_exames/datas_disponiveis/" + codigo_cliente_funcionario + "/" + codigo_fornecedor + "/" + codigo_exame  + "/" + codigo_servico, function(data) {
		
			$("#botao_seleciona_agenda_" + codigo_cliente_funcionario + "_" + codigo_exame).hide();
			$("#botao_mostra_agenda_" + codigo_cliente_funcionario + "_" + codigo_exame).show();
		
			manipula_modal("modal_carregando", 0);
			manipula_modal("modal_agendamento_" + codigo_cliente_funcionario + "_" + key, 1);
		
			customCode.setupDatepicker(data, codigo_exame, codigo_cliente_funcionario);
		});
	}
		
	function esconde_modal_agendamento(codigo_cliente_funcionario, codigo_exame) {
		$("#botao_mostra_agenda_" + codigo_cliente_funcionario + "_" + codigo_exame).hide();
		
		$("#data_selecioda_agenda_" + codigo_cliente_funcionario + "_" + codigo_exame).hide();
		$("#botao_seleciona_agenda_" + codigo_cliente_funcionario + "_" + codigo_exame).show();
		
		manipula_modal("modal_agendamento_" + codigo_cliente_funcionario + "_" + codigo_exame, 0);
	}
		
	function carregaHorarios(data, ranges, servico, codigo_cliente_funcionario) {
		var data_formatada = data.split("-")[2] + "/" + data.split("-")[1] + "/" + data.split("-")[0];
		
		$("input[name=\"data[ItemPedidoExame][" + codigo_cliente_funcionario + "][" + servico + "][data_agendamento]\"]").val(data_formatada);
		
		$("#dias_disponiveis_" + codigo_cliente_funcionario + "_" + servico).html("");
		$("#modal_agendamento_" + codigo_cliente_funcionario + "_" + servico + " #texto_dia").html(" em " + data_formatada + ":");
		
		jQuery.each(ranges[data_formatada].horas_disponiveis, function(i, value) {
			if(i.length < 4){
				i = "0" + i;
			}

			$("#dias_disponiveis_" + codigo_cliente_funcionario + "_" + servico).append("<label class=\"btn-day\" style=\"margin: 10px;\">" + (i.substr(0, 2) + ":" + i.substr(2, 2)) + "</label>");
		});
		
		$("#dias_disponiveis_" + codigo_cliente_funcionario + "_" + servico + " .btn-day").removeClass("pinta");
		$("#dias_disponiveis_" + codigo_cliente_funcionario + "_" + servico).on("click", ".btn-day", function() {
			marca_horario(this, servico, codigo_cliente_funcionario);
		});
	}
		
	function marca_horario(element, servico, codigo_cliente_funcionario) {
		
		$("input[name=\"data[ItemPedidoExame][" + codigo_cliente_funcionario + "][" + servico + "][hora_agendamento]\"]").val($(element).text());
		$("#botao_" + codigo_cliente_funcionario + "_" + servico).show();
		
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
		
	function controla_campo(element, chave, codigo_cliente_funcionario) {
		if($(element).val() == "1") {
			$("#label_ordem_chegada_" + codigo_cliente_funcionario + "_" + chave).hide();
			$("#agenda_" + codigo_cliente_funcionario + "_" + chave).show();
		} else {
			$("#agenda_" + codigo_cliente_funcionario + "_" + chave).hide();
			$("#label_ordem_chegada_" + codigo_cliente_funcionario + "_" + chave).show();
		}
	}
		
	function controla_campo_cliente(element, chave) {
		
		if($(element).val() == "1") {
			$("#label_ordem_chegada_" + codigo_cliente_funcionario + "_" + chave).hide();
			$("#opcao_" + codigo_cliente_funcionario + "_" + chave).show();
		} else {
			$("#opcao_" + codigo_cliente_funcionario + "_" + chave).hide();
			$("#label_ordem_chegada_" + codigo_cliente_funcionario + "_" + chave).show();
		}
	}		
		
	function controla_agendamento_cliente(valor, chave, codigo_cliente_funcionario) {
		
		if(valor == 1) {
			manipula_modal("modal_sugestao_" + codigo_cliente_funcionario + "_" + chave, 1);
		
			$("#modal_sugestao_" + codigo_cliente_funcionario + "_" + chave + " .data").focusout(function() {
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
		
		} else {
			$("#opcao_" + codigo_cliente_funcionario + "_" + chave).hide();
			$("#agenda_" + codigo_cliente_funcionario + "_" + chave).show();
			$("#link_remover_sugestao_" + codigo_cliente_funcionario + "_" + chave).hide();
		}			
	}	
		
	function voltar_agendamento_cliente(chave, codigo_cliente_funcionario) {

		$("#agenda_" + codigo_cliente_funcionario + "_" + chave + " input").val("");
		
	}
		
	var customCode = {
	    tooltip: "",
	    init: function() {
		
	    	// Define o texto padrão exibido no tooltip
	        this.tooltip = "Confira a programação para o dia";

	        // Tradução do datepicker para pt-BR (Brasil)
		    $.datepicker.regional["pt-BR"] = {
				minDate: new Date(),
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
	    setupDatepicker: function(ranges, servico, codigo_cliente_funcionario) {
		
			var datas_liberadas = [];
			jQuery.each(ranges, function(index, range) {
		    	datas_liberadas.push(range.start.split("/")[2] + "-" + range.start.split("/")[1] + "-" + range.start.split("/")[0]);
			});		
			
			$("#datepicker_" + codigo_cliente_funcionario + "_" + servico).datepicker({
				onSelect: function(date, inst) {
					carregaHorarios(date, ranges, servico, codigo_cliente_funcionario);
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
	
		    if(!type) {
				type = "min";
			}
	
			$.each(ranges, function(index, range){
		    	dates.push(new Date(index.split("/")[2], Math.round(index.split("/")[1] - 1), index.split("/")[0]));
			});
			
		    if (type == "max") {
				retDate = new Date(Math.max.apply(null, dates));
			}
	
			if (type == "min") {
				retDate = new Date(Math.min.apply(null, dates));
			}

			return retDate;
		}		
	};
		
'); ?>

	<script>
		$(function(){

			$(".header").click(function () {

				$header = $(this);
				//getting the next element
				$content = $header.next();
				//open up the content needed - toggle the slide- if visible, slide up, if not slidedown.
				$content.slideToggle(500, function () {
					//execute this after slideToggle is done
					//change text of header based on visibility of content div
					
				});

			});

			$(".confirma_agendamento_massa").on("click", function(){

				data_agendamento = $("input[name='data_em_massa'").val();
				hora_agendamento = $("input[name='hora_em_massa'").val();										

				var qtd_checado = $("table.agendamento tr td input:checkbox:checked").length;
				
				if (qtd_checado <= 0) {
					swal("Atenção", "Você deve selecionar ao menos 1 exame para agendar!", "error");
					return false;
				}
				
	
				if(data_agendamento != "" && hora_agendamento != "") {
					
					swal({
						title: "Confirmar agendamentos para " + data_agendamento + " às " + hora_agendamento + " hrs ?",
						text: "Lembre-se, esta é a data que você agendou diretamente com a clínica! Só confirme as informações se realmente estiverem corretas!",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#47B22C",
						confirmButtonText: "Sim, eu LIGUEI e AGENDEI a data!",
						closeOnConfirm: true
					},
					function(){

						$("table.agendamento tr").each(function(){

							if ($(this).find(":input")) {

								
								if ($(this).find(":input").hasClass("hasDatepicker")) {

									var chave_checkbox = $(this).find(":input[name='checkbox']");																																								
									var chave = $(this).find(":input[name='chave']").val();
					
									if (chave_checkbox.is(":checked")) {
										var codigo_cliente_funcionario = $(this).find(":input[name='codigo_cliente_funcionario']").val();
										var codigo_item_pedido = $(this).find(":input[name='codigo_item_pedido']").val();
										var codigo_lista_preco_produto_servico = $(this).find(":input[name='codigo_lista_preco_produto_servico']").val();
										var codigo_grupo_economico = $(this).find(":input[name='codigo_grupo_economico']").val();
										var codigo_agendamento = $(this).find(":input[name='codigo_agendamento']").val();

										if(codigo_agendamento == undefined || codigo_agendamento == '') {
											confirma_agendamento_em_massa(chave, codigo_cliente_funcionario, codigo_item_pedido, codigo_lista_preco_produto_servico, codigo_grupo_economico, data_agendamento, hora_agendamento, null);
										} else {
											confirma_agendamento_em_massa(chave, codigo_cliente_funcionario, codigo_item_pedido, codigo_lista_preco_produto_servico, codigo_grupo_economico, data_agendamento, hora_agendamento, codigo_agendamento);
										}

									}									
								}
							}
						});
					});				
										
				} else {
					swal("Atenção", "Você deve preencher a data/hora agendada!", "error");
				}								
			})

			$(".voltar_agendamento_massa").on("click", function(){

				$("#agenda_em_massa input").val("");

			})

			function confirma_agendamento_em_massa(chave, codigo_cliente_funcionario, codigo_item_pedido, codigo_lista_preco_produto_servico, codigo_grupo_economico, data_agendamento, hora_agendamento, codigo_agendamento) {
										
				data_formatada = data_agendamento.split("/")[2] + "-" + data_agendamento.split("/")[1] + "-" + data_agendamento.split("/")[0];
				hora_formatada = hora_agendamento.split(":")[0] + hora_agendamento.split(":")[1];

				if(codigo_agendamento == null){
					codigo_agendamento = "";
				}
				
				$.ajax({
					type: "POST",
					url: "/portal/pedidos_exames/grava_agendamento_proprio",
					dataType: "json",
					data: "codigo_grupo_economico=" + codigo_grupo_economico + "&codigo_exame=" + chave + "&codigo_item_pedido=" + codigo_item_pedido + "&data_agendamento=" + data_formatada + "&hora_agendamento=" + hora_formatada + "&codigo_lista_preco_produto_servico=" + codigo_lista_preco_produto_servico + "&codigo_agendamento=" + codigo_agendamento,
					beforeSend: function() {
						$("#agenda_" + codigo_cliente_funcionario + "_" + chave).html("<img src=\"/portal/img/default.gif\" > Gravando na Agenda!");
					},
					success: function(retorno) {
						if(retorno) {
							$("#agenda_" + codigo_cliente_funcionario + "_" + chave).html("<div class=\"well\"></div>");

							if(codigo_agendamento != ""){
								var conteudo = "<strong>Agendamento Atual:</strong><br /><br />";
								conteudo = conteudo + "<p><b>Data: </b>" + data_agendamento;
							} else {
								var conteudo = "<strong>Agendamento Próprio:</strong><br /><br />";
								conteudo = conteudo + "<p><b>Data: </b>" + data_agendamento;								
							}
				
				
							if(hora_agendamento) {
								conteudo = conteudo + " / <b>Horário: </b>" + hora_agendamento;
							} 
				
							conteudo = conteudo + "</p>";
							$("#agenda_" + codigo_cliente_funcionario + "_" + chave + " .well").append(conteudo);

							$("#checkbox_" + chave).attr("disabled", "disabled");
						}
					},
					complete: function() {
							
						var qtd_disabled = 0;						
						var qtd_checkbox = $("table.agendamento tr td input:checkbox").length;

						$("table.agendamento tr").each(function(){

							var chave_checkbox = $(this).find(":input[name='checkbox']");
							
							if (chave_checkbox.is(":disabled")) {
								qtd_disabled++;
							}							
						});
				
						if (qtd_checkbox == qtd_disabled) {
							$("#agendar_em_massa").remove();						
						}										
					}
				});					
			}	

			$(".exames_select_all").on("change", function(){

				if ($(this).is(":checked")) {
					$('.tabela_select_exames tbody tr input:checkbox:not(":disabled")').prop('checked','checked');
				} else {
					$('.tabela_select_exames tbody tr input:checkbox:not(":disabled")').removeProp('checked');
				}
			});		
		})		
	</script>
