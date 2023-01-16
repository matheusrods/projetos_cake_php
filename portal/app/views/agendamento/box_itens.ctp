				<tr>
					<td style="background: #E9EDC9; font-weight: bold; border-left: 3px solid #999999;">Item:</td>
					<td style="background: #E9EDC9; font-weight: bold;">Exame:</td>
					<td style="background: #E9EDC9; font-weight: bold;">Fornecedor:</td>
					<td style="background: #E9EDC9; font-weight: bold;">Sugestões:</td>
					<td style="background: #E9EDC9; font-weight: bold;">Ação:</td>
					<td style="background: #E9EDC9; font-weight: bold;">Notificar:</td>
				</tr>
				<?php foreach($itens_pedido as $key => $item) : ?>
					<tr>
						<td style="background: #FFFFFF;  border-left: 3px solid #999999;"><?php echo $item['dados']['ItemPedidoExame']['codigo']; ?></td>
						<td style="background: #FFFFFF;"><?php echo $item['dados']['Exame']['descricao']; ?></td>
						<td style="background: #FFFFFF;"><?php echo $item['dados']['Fornecedor']['razao_social']; ?></td>
						<td style="background: #FFFFFF;">
							<?php if(isset($item['sugestoes']) && count($item['sugestoes'])) : ?>
								<?php foreach($item['sugestoes'] as $k => $sugestao) : ?>
									<?php if(empty($sugestao['data_sugerida'])) : ?>
										(sem sugestão)
									<?php else : ?>
										<?php echo $sugestao['data_sugerida']; ?>
										<?php echo $sugestao['hora_sugerida'] ? " - " . (substr(str_pad($sugestao['hora_sugerida'], 4, 0, STR_PAD_LEFT), 0, 2) . ":" . substr(str_pad($sugestao['hora_sugerida'], 4, 0, STR_PAD_LEFT), 2, 2)) : ''; ?><br />									
									<?php endif; ?>
								<?php endforeach; ?>							
							<?php else : ?>
								(sem sugestão)
							<?php endif; ?>
						</td>
						<td style="background: #FFFFFF;">
							<?php if(!empty($item['dados']['ItemPedidoExame']['data_agendamento']) && !empty($item['dados']['ItemPedidoExame']['hora_agendamento'])) : ?>
								<div id="botao_<?php echo $item['dados']['ItemPedidoExame']['codigo']; ?>">
									<strong>Data: </strong><?php echo $item['dados']['ItemPedidoExame']['data_agendamento']; ?><br />
									<strong>Horário: </strong><?php echo $item['dados']['ItemPedidoExame']['hora_agendamento']; ?>
								</div>
								
								<a href="javascript:void(0);" onclick="carrega_dados_cliente_funcionario(<?php echo $item['dados']['PedidoExame']['codigo_cliente_funcionario']; ?>, <?php echo $item['dados']['ItemPedidoExame']['codigo']; ?>); manipula_modal('modal_agendamento_<?php echo $item['dados']['ItemPedidoExame']['codigo']; ?>', 1); " class="label label-important">ALTERAR!</a>
								
							<?php else : ?>
								<div id="botao_<?php echo $item['dados']['ItemPedidoExame']['codigo']; ?>">
									<a href="javascript:void(0);" onclick="carrega_dados_cliente_funcionario(<?php echo $item['dados']['PedidoExame']['codigo_cliente_funcionario']; ?>, <?php echo $item['dados']['ItemPedidoExame']['codigo']; ?>); manipula_modal('modal_agendamento_<?php echo $item['dados']['ItemPedidoExame']['codigo']; ?>', 1); " class="label label-info">Agendar Exame!</a>
								</div>
							<?php endif; ?>
														
							<div class="modal fade" id="modal_agendamento_<?php echo $item['dados']['ItemPedidoExame']['codigo']; ?>" data-backdrop="static" style="width: 85%; left: 8%; top: 15%; margin: 0 auto;">
	           					<div class="modal-dialog modal-sm" style="position: static;">
	           						<div class="modal-content">
	           							<div class="modal-header" style="text-align: center;">
	           								<h3>AGENDAMENTO DE EXAMES:</h3>
	           							</div>
	           							
	           							<div id="box_<?php echo $item['dados']['ItemPedidoExame']['codigo']; ?>" style="margin: 5px 20px; background: #F5F5F5;">
	           								<img src="/portal/img/default.gif" style="padding: 15px;">Carregando dados do funcionário...
	           							</div>
	           							
	           							<div class="modal-body" style="min-height: 295px;">
	           								<div class="span5" style="border-right: 1px solid #CCC;">
												<br />
												<span style="font-size: 1.2em">
													<b>Fornecedor:</b><br />
													<?php echo $item['dados']['Fornecedor']['razao_social']; ?>
												</span>

									    		<?php if(!empty($item['contato']['numero'])) : ?>
									    			<br /><br />
									    			<span style="font-size: 1.2em">
									    				<?php // echo !empty($item['contato']['descricao']) ? $item['contato']['descricao'] : 'TELEFONE'; ?>
									    				<b>TELEFONE:</b><br /> 
									    				<?php echo $item['contato']['numero']; ?>
									    			</span> 
									    		<?php endif; ?>												
													           								
												<br /><br />
												<span style="font-size: 1.2em">
													<b style="font-size: 1.2em">Exame:</b><br />
													<?php echo $item['dados']['Exame']['descricao']; ?>
												</span>

												<br /><br />
												<span style="font-size: 1.2em">
													<b>Dias Sugeridos pelo Cliente</b><br />
													<?php if(isset($item['sugestoes']) && count($item['sugestoes'])) : ?>
														<?php foreach($item['sugestoes'] as $k => $sugestao) : ?>
															<?php if(!empty($sugestao['data_sugerida'])) : ?>
																- <a href="javascript:void(0);" onclick="atualiza_data('<?php echo $sugestao['data_sugerida']; ?>', '<?php echo $sugestao['hora_sugerida'] ? (substr(str_pad($sugestao['hora_sugerida'], 4, 0, STR_PAD_LEFT), 0, 2) . ":" . substr(str_pad($sugestao['hora_sugerida'], 4, 0, STR_PAD_LEFT), 2, 2)) : ''; ?>', '<?php echo $item['dados']['ItemPedidoExame']['codigo']; ?>', '<?php echo $item['dados']['Exame']['codigo']; ?>');">
																	<?php echo $sugestao['data_sugerida']; ?>
																	<?php echo $sugestao['hora_sugerida'] ? " - " . (substr(str_pad($sugestao['hora_sugerida'], 4, 0, STR_PAD_LEFT), 0, 2) . ":" . substr(str_pad($sugestao['hora_sugerida'], 4, 0, STR_PAD_LEFT), 2, 2)) : ''; ?><br />
																</a>															
															<?php else : ?>
																(sem sugestão)	
															<?php endif; ?>
														<?php endforeach; ?>							
													<?php else : ?>
														(sem sugestão)
													<?php endif; ?>
												</span>
	           								</div>
	           								<div class="span5">
												<br />
												<span style="font-size: 1.1em; border-bottom: 1px solid #CCC; background: #EFEFEF; padding: 1px;">
													<b>ATENÇÃO:</b> Este fornecedor não utiliza nossa agenda, ligar <br />
													para agendar o horário conforme sua disponibilidade.
												</span>
												<br /><br /><br />
												<?php echo $this->BForm->input('ItemPedidoExame.' . $item['dados']['ItemPedidoExame']['codigo'] . '.' . $item['dados']['Exame']['codigo'] . '.data_agendamento', array('class' => 'input-small data form-control', 'value' => (isset($this->data['ItemPedidoExame']['data_agendamento']) ? $this->data['ItemPedidoExame']['data_agendamento'] : ''))); ?>
							           			<?php echo $this->BForm->input('ItemPedidoExame.' . $item['dados']['ItemPedidoExame']['codigo'] . '.' . $item['dados']['Exame']['codigo'] . '.hora_agendamento', array('class' => 'input-small hora form-control', 'value' => (isset($this->data['ItemPedidoExame']['hora_agendamento']) ? $this->data['ItemPedidoExame']['hora_agendamento'] : ''))); ?>
	           								</div>
	           							</div>
								    </div>
								    <div class="modal-footer">
						    			<div class="right">
						    				<a href="javascript:void(0);" onclick="manipula_modal('modal_agendamento_<?php echo $item['dados']['ItemPedidoExame']['codigo']; ?>', 0);" class="btn btn-danger">CANCELAR</a>
						    				<a href="javascript:void(0);" id="botao_<?php echo $item['dados']['ItemPedidoExame']['codigo']; ?>_<?php echo $item['dados']['Exame']['codigo']; ?>" onclick="grava_agenda(this, '<?php echo $item['dados']['Exame']['codigo']; ?>', '<?php echo $item['dados']['ItemPedidoExame']['codigo']; ?>', '<?php echo $item['dados']['PedidoExame']['codigo']; ?>');" class="btn btn-success">CONFIRMAR</a>
						    			</div>
						    		</div>									    
								</div>
							</div>
						</td>
						<?php if(!isset($pedido_aux) || ($pedido_aux != $item['dados']['PedidoExame']['codigo'])) : ?>
							<?php $pedido_aux = $item['dados']['PedidoExame']['codigo']; ?>
							<td style="background: <?php echo $desabilita ? '#8E9092' : '#D20121'; ?>; text-align: center; vertical-align: middle;" rowspan="<?php echo count($itens_pedido); ?>" valign="middle">
								<?php echo $this->Html->link('<i class="icon-envelope icon-white"></i>', array('controller' => 'pedidos_exames', 'action' => 'notificacao', $item['dados']['PedidoExame']['codigo_cliente_funcionario'], $item['dados']['PedidoExame']['codigo'], 'agendamento'), array('escape' => false, 'title' =>'Notificar', 'id' => 'icone_notificacao_' . $item['dados']['PedidoExame']['codigo'], 'style' => ((isset($desabilita) && $desabilita == '1') ? 'display:none;' : ''))); ?>
							</td>						
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
				
				<?php echo $this->Javascript->codeBlock('
								
					jQuery(document).ready(function() {
						setup_mascaras(); setup_time(); setup_datepicker();
						$(".modal").css("z-index", "-1");
					});
						
					function atualiza_data(data, hora, codigo_item, codigo_exame) {
						$("input[name=\"data[ItemPedidoExame][" + codigo_item + "][" + codigo_exame + "][data_agendamento]\"]").val(data);
						$("input[name=\"data[ItemPedidoExame][" + codigo_item + "][" + codigo_exame + "][hora_agendamento]\"]").val(hora);
					}
						
					function grava_agenda(elemento, codigo_exame, codigo_item, codigo_pedido) {
				
						var data_origin = $("input[name=\"data[ItemPedidoExame][" + codigo_item + "][" + codigo_exame + "][data_agendamento]\"]").val();
						var hora_origin = $("input[name=\"data[ItemPedidoExame][" + codigo_item + "][" + codigo_exame + "][hora_agendamento]\"]").val();
						
						var data_agendamento = data_origin.replaceAll("/", "-");
						var hora_agendamento = hora_origin;
						
						if(data_agendamento == "" && hora_agendamento == "") {
							$("input[name=\"data[ItemPedidoExame][" + codigo_item + "][" + codigo_exame + "][data_agendamento]\"]").css("border", "1px solid red");
							$("input[name=\"data[ItemPedidoExame][" + codigo_item + "][" + codigo_exame + "][data_agendamento]\"]").change(function() {
								$(this).css("border", "1px solid #CCC");
							});
						
							$("input[name=\"data[ItemPedidoExame][" + codigo_item + "][" + codigo_exame + "][hora_agendamento]\"]").css("border", "1px solid red");
							$("input[name=\"data[ItemPedidoExame][" + codigo_item + "][" + codigo_exame + "][hora_agendamento]\"]").change(function() {
								$(this).css("border", "1px solid #CCC");
							});
						
							return false;
						} else if(data_agendamento == "") {
							$("input[name=\"data[ItemPedidoExame][" + codigo_item + "][" + codigo_exame + "][data_agendamento]\"]").css("border", "1px solid red");
							$("input[name=\"data[ItemPedidoExame][" + codigo_item + "][" + codigo_exame + "][data_agendamento]\"]").change(function() {
								$(this).css("border", "1px solid #CCC");
							});		
							return false;
						} else if(hora_agendamento == "") {
							$("input[name=\"data[ItemPedidoExame][" + codigo_item + "][" + codigo_exame + "][hora_agendamento]\"]").css("border", "1px solid red");
							$("input[name=\"data[ItemPedidoExame][" + codigo_item + "][" + codigo_exame + "][hora_agendamento]\"]").change(function() {
								$(this).css("border", "1px solid #CCC");
							});		
							return false;
						}
						
						var bkp_element = $(elemento).parent("div").html();
						
					    $.ajax({
					        type: "POST",
					        url: "/portal/agendamento/grava_agenda",
					        dataType: "json",
					        data: { "data_agendamento" : data_agendamento, "hora_agendamento" : hora_agendamento, "codigo_item_pedido" : codigo_item},
					        beforeSend: function() {
								$(elemento).parent("div").html("<img src=\"/portal/img/default.gif\" style=\"padding: 10px;\"> <b>Aguarde! Gravando as informações!</b>");
							},
					        success: function(json) {
								$("#botao_" + codigo_item).html("<strong>Data: </strong>" + data_origin + "<br /><strong>Horário: </strong>" + hora_origin).css({"background": "#F9FFC1"});
						
								$.ajax({
									type: "GET",
									url: "/portal/agendamento/consulta_disponibilidade_notificacao/" + codigo_pedido,
									dataType: "json",
									success: function(retorno) {
										if(retorno) {
											$("#icone_notificacao_" + codigo_pedido).show().parent("td").css("background", "#D20121");
										} else {
											$("#icone_notificacao_" + codigo_pedido).hide().parent("td").css("background", "#8E9092");
										}
									} 
								});
							},
					        complete: function() {
								$(".modal-footer .right").html(bkp_element);
								manipula_modal("modal_agendamento_" + codigo_item, 0);
							}
					    });
					}
						
					function carrega_dados_cliente_funcionario(codigo_clente_funcionario, codigo_pedido_item) {
						$("#box_" + codigo_pedido_item).load("/portal/agendamento/box_dados_cliente_funcionario/" + codigo_clente_funcionario);	
					} 	
						
				'); ?>			