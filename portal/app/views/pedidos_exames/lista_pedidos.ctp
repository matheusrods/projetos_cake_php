<div class='inline well'>
		<?php echo $this->BForm->input('Empresa.razao_social', array('value' => $dados_cliente_funcionario['Empresa']['razao_social'], 'class' => 'input-xlarge', 'label' => 'Empresa' , 'readonly' => true, 'type' => 'text')); ?>
		<?php echo $this->BForm->input('Cliente.razao_social', array('value' => $dados_cliente_funcionario['Cliente']['nome_fantasia'], 'class' => 'input-xlarge', 'label' => 'Unidade' , 'readonly' => true, 'type' => 'text')); ?>
		<?php echo $this->BForm->input('Empresa.codigo_documento', array('value' => $dados_cliente_funcionario['Empresa']['codigo_documento'], 'class' => 'input-xlarge', 'label' => 'CNPJ' , 'readonly' => true, 'type' => 'text')); ?>
		<?php echo $this->BForm->input('Setor.descricao', array('value' => $dados_cliente_funcionario['Setor']['descricao'], 'class' => 'input-xlarge', 'label' => 'Setor', 'readonly' => true, 'type' => 'text')); ?>
		
		<div class="clear"></div>
		<?php echo $this->BForm->input('Funcionario.nome', array('value' => $dados_cliente_funcionario['Funcionario']['nome'], 'class' => 'input-xlarge', 'label' => 'Funcionario' , 'readonly' => true, 'type' => 'text')); ?>
		<?php echo $this->BForm->input('Funcionario.cpf', array('value' => $dados_cliente_funcionario['Funcionario']['cpf'], 'class' => 'input-xlarge', 'label' => 'CPF' , 'readonly' => true, 'type' => 'text')); ?>
		<?php echo $this->BForm->input('Funcionario.data_nascimento', array('value' => $dados_cliente_funcionario['Funcionario']['data_nascimento'], 'class' => 'input-xlarge', 'label' => 'Data nascimento' , 'readonly' => true, 'type' => 'text')); ?>
		<?php echo $this->BForm->input('Cargo.descricao', array('value' => $dados_cliente_funcionario['Cargo']['descricao'], 'class' => 'input-xlarge', 'label' => 'Cargo' , 'readonly' => true, 'type' => 'text')); ?>	
		<div class="clear"></div>
	</div>

	<div class="row-fluid inline" style="text-align:right; padding: 10px 0;">
    	<?php echo $this->BForm->create('FuncionarioSetorCargo', array('type' => 'post' ,'url' => array('controller' => 'pedidos_exames','action' => 'inclusao_em_massa', $codigo_grupo_economico))); ?>
	    	<?php echo $this->BForm->input('FuncionarioSetorCargo.0.codigo', array('type' => 'hidden', 'value' => $codigo_funcionario_setor_cargo)); ?>

	    	<?php if($pedido_bloqueado): ?>
		    	<button id="botao" type="button" class="btn btn-lg" onclick="pedido_bloqueado()" ><i class="glyphicon glyphicon-share"></i> <i class="icon-plus icon"></i> Incluir Pedido </button>
			<?php else: ?>
	    		<button id="botao" type="submit" class="btn btn-success btn-lg" ><i class="glyphicon glyphicon-share"></i> <i class="icon-plus icon-white"></i> Incluir Pedido </button>
	    	<?php endif; ?>
	    <?php echo $this->BForm->end(); ?>		    
	</div>
	
	<div id="listagem">
	    <table class="table table-striped">
	        <thead>
	            <tr>
		            <th class="input-small">Número Pedido</th>
		            <th class="input-xlarge">Data do Pedido</th>
		            <th class="input-small">Baixa Último Exame</th>
		            <th class="input-xlarge" style="text-align: right;">Tipo do Pedido</th>
		            <th class="input-small" style="">Status</th>
		            <th class="input-xlarge" style="text-align: right;">Ações:</th>
	            </tr>
	        </thead>
	        <tbody>
	        	<?php if(count($lista_pedidos)) : ?>
	        		
		        	<?php foreach($lista_pedidos as $key => $item) : ?>		        		
			            <tr id="pedido_<?php echo $item['PedidoExame']['codigo']; ?>">
			                <td class="input-small"><?php echo $item['PedidoExame']['codigo']; ?></td>
			                <td class="input-xlarge"><?php echo $item['PedidoExame']['data_inclusao']; ?></td>
			                <td class="input-small"><?php echo isset($item[0]['baixa_ultimo_exame']) ? $item[0]['baixa_ultimo_exame'] : '-'; ?></td>
			                <td class="input-xlarge" style="text-align: right;">
			                	<?php echo (isset($item['PedidoExame']['tipo_exame']) && !empty($item['PedidoExame']['tipo_exame'])) ? $item['PedidoExame']['tipo_exame'] : '-'; ?>
			                </td>
			                <td>

								<?php

									/* 
									Checa as seguintes opções:
										1 = Pendente de Baixa
										2 = Baixado Parcialmente
										3 = Baixado Total
									*/

									// Define padrões icones
									$iconStatus = array( 1 => "important",
														2 => "warning" ,
														3 => "success",
														4 => "info",
														5 => "danger",
														6 => "warning"
													);
									// Codigo Status
									$stPedidoCodigo = $item['PedidoExame']['codigo_status_pedidos_exames'];
									// Escreve Icon Status
									echo '<span class="badge-empty badge badge-'. $iconStatus[$stPedidoCodigo] .'" title="'. $item[0]['_status_'] .'"></span>';
									
								?>
								<?php if ($uperfis_que_nao_podem_ver == '0'
								): ?>
									<?php if(
										$item['PedidoExame']['codigo_status_pedidos_exames'] == 2 OR 
										$item['PedidoExame']['codigo_status_pedidos_exames'] == 3 OR
										$item['PedidoExame']['codigo_status_pedidos_exames'] == 6
									): ?>
										<?php echo $this->Html->link('',  array('controller' => 'itens_pedidos_exames_baixa', 'action' => 'baixa', $item['PedidoExame']['codigo'], 'lista_pedidos_exames', $codigo_funcionario_setor_cargo),
											array(  'class' => 'icon-download-alt', 
											'title' => 'Baixa de Pedido', 
											'style' => 'margin-left: 5px;'
										)); ?>
									<?php endif; ?>
								<?php endif; ?>
								<?php 
								$Configuracao = &ClassRegistry::init('Configuracao');
								if($item['PedidoExame']['exibe_aso'] == true): ?>
									<?php if(($item['PedidoExame']['codigo_status_pedidos_exames'] != StatusPedidoExame::CANCELADO) && ($item['ItemPedidoExame']['codigo_exame'] == $Configuracao->getChave('INSERE_EXAME_CLINICO'))): ?>
										<?php if($item['AuditoriaExame']['codigo_status_auditoria_imagem'] == 3 || $item['AuditoriaExame']['codigo_status_auditoria_imagem'] == 4 || (($item['AuditoriaExame']['codigo_status_auditoria_imagem'] == 6 || $item['AuditoriaExame']['codigo_status_auditoria_imagem'] == 5) && $item['AnexoExame']['aprovado_auditoria'] != null)):?>

											<?php 

											$codigoItemPedidoExame = json_encode($item['ItemPedidoExame']['codigo']);
											$arquivo = '';
											$arquivo = end(glob(DIR_ANEXOS.$codigoItemPedidoExame.DS.'anexo_item_exame_'.$codigoItemPedidoExame.'.*'));
											$arquivo_app = '';
											
											if(strstr($item['AnexoExame']['caminho_arquivo'],'https://api.rhhealth.com.br')):
												$arquivo_app = $item['AnexoExame']['caminho_arquivo'];
											
											elseif(strstr($item['AnexoExame']['caminho_arquivo'],'http://api.rhhealth.com.br')):
												$arquivo_app = $item['AnexoExame']['caminho_arquivo'];
											
											endif;
									
											?>	
											<?php if( !empty($arquivo_app)): ?>
												<?php echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')), $arquivo_app, array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo do item')) ?>
											<?php elseif(!empty($arquivo)): ?>
												<?php echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')), '/files/anexos_exames/'.$codigo_item_pedido.'/'.basename($arquivo), array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo do item')) ?>
											<?php endif; ?>
										<?php endif; ?>
									<?php endif; ?>
								<?php endif; ?>            
							</td>
			                <td class="input-xlarge">
			                	<div style="text-align: right;">
				                	
									<a href="javascript:void(0);" onclick="print_item(this, <?php echo $item['PedidoExame']['codigo']; ?>, <?php echo $codigo_cliente_funcionario; ?>);" title="Ver Pedido"><i class="icon-print"></i></a>
				                	
									<?php echo $this->Html->link('<i class="icon-envelope"></i>', array('action' => 'notificacao', $item['PedidoExame']['codigo_func_setor_cargo'], $item['PedidoExame']['codigo']), array('escape' => false, 'title' =>'Notificar')); ?>

									 <a href="javascript:void(0);" onclick="log_pedido('<?php echo $item['PedidoExame']['codigo']; ?>');"><i class="icon-eye-open" title="Log do Pedido"></i></a>

 									<?php //if($item['PedidoExame']['codigo_status_pedidos_exames'] == StatusPedidoExame::PENDENTE_BAIXA || $item['PedidoExame']['codigo_status_pedidos_exames'] == StatusPedidoExame::PARCIALMENTE_BAIXADO || $item['PedidoExame']['codigo_status_pedidos_exames'] == StatusPedidoExame::CONCLUIDO_PARCIAL): ?>
										<?php if($item['PedidoExame']['codigo_status_pedidos_exames'] == StatusPedidoExame::PENDENTE_BAIXA || $item['PedidoExame']['codigo_status_pedidos_exames'] == StatusPedidoExame::PARCIALMENTE_BAIXADO || $item['PedidoExame']['codigo_status_pedidos_exames'] == StatusPedidoExame::CONCLUIDO_PARCIAL): ?>
 										<?php // PC-3158 - Deixa REAGENDAR ORDEM DE CHEGADA
											//if(!empty($item[0]['exame_baixa'])): ?>
											<?php echo $this->Html->link('<i class="icon-calendar"></i>', array('action' => 'agendamento_grupo',$codigo_grupo_economico, $item['PedidoExame']['codigo'], 'reagendamento'), array('escape' => false, 'title' =>'Reagendar')); ?>
 										<?php //endif; ?>
									<?php endif; ?>
									
									<?php if(empty($item[0]['baixa_ultimo_exame'])) : ?>
										<?php if($item['PedidoExame']['codigo_status_pedidos_exames'] == StatusPedidoExame::CANCELADO) : ?>										
											<a href="javascript:void(0); return false;" class="label label-default">Cancelado</a>
										<?php else : ?>
											<a id="cancelamento_pedido_<?php echo $item['PedidoExame']['codigo']; ?>" href="javascript:void(0);" class="label label-important" onclick="manipula_modal('modal_cancelamento_<?php echo $item['PedidoExame']['codigo']; ?>', 1); carrega_contatos_pedido(<?php echo $item['PedidoExame']['codigo']; ?>);">Cancelar</a>
										<?php endif; ?>
									<?php endif; ?>
									
									<?php if($item['PedidoExame']['codigo_status_pedidos_exames'] == StatusPedidoExame::PENDENTE_BAIXA || $item['PedidoExame']['codigo_status_pedidos_exames'] == StatusPedidoExame::PARCIALMENTE_BAIXADO){ ?>
										<a id="concluir_pedido_parcial_<?php echo $item['PedidoExame']['codigo']; ?>" href="javascript:void(0);" class="label label-warning" onclick="manipula_modal('modal_concluir_pedido_parcial_<?php echo $item['PedidoExame']['codigo']; ?>', 1);">Concluir Parcialmente</a>
									<?php } ?>
			                	</div>
								
								<div class="modal fade" id="modal_cancelamento_<?php echo $item['PedidoExame']['codigo']; ?>" data-backdrop="static" style="width: 65%; left: 16%; top: 15%; margin: 0 auto;">
									<div class="modal-dialog modal-md" style="position: static;">
										<div class="modal-content">
											<div class="modal-header" style="text-align: center;">
												<h4>Cancelamento do Pedido: #<?php echo $item['PedidoExame']['codigo']; ?></h4>
											</div>
											<div class="modal-body" style="min-height: 450px;">
												<strong>Data do Pedido: </strong><?php echo $item['PedidoExame']['data_inclusao']; ?><br />
												<strong>Funcionário: </strong><?php echo $dados_cliente_funcionario['Funcionario']['nome']; ?><br /><br />
												
												<?php echo $this->BForm->input('MotivoCancelamento.' . $item['PedidoExame']['codigo'] . '.descricao', array('class' => 'input-xxlarge', 'label' => '<strong>Motivo de Cancelamento</strong>', 'options' => $motivos_cancelamento)); ?>
												<label id="mensagem_motivo_<?php echo $item['PedidoExame']['codigo']; ?>" style="color: red; font-weight: bold; display: none; margin: -15px 0 20px 0;">
													Você precisa selecionar um motivo para cancelar o pedido!
												</label>
												
												<strong>Serão enviados e-mails notificando sobre o cancelamento para os seguintes endereços:</strong>
												<div id="contatos_<?php echo $item['PedidoExame']['codigo']; ?>">
													<br /><br /><br />
													<img src="/portal/img/default.gif" style="padding: 10px;"> Carregando lista de contatos...
													<br /><br /><br /><br /><br /><br />
												</div>
												<div class="well">
													<a href="javascript:void(0);" onclick="confirma_cancelamento(this, '<?php echo $item['PedidoExame']['codigo']; ?>');" class="btn btn-success">Confirmar</a>
													<a href="javascript:void(0);" onclick="manipula_modal('modal_cancelamento_<?php echo $item['PedidoExame']['codigo']; ?>', 0);" class="btn btn-default">Cancelar</a>												
												</div>
											</div>
										</div>
									</div>
								</div>	

								<div class="modal fade" id="modal_concluir_pedido_parcial_<?php echo $item['PedidoExame']['codigo']; ?>" data-backdrop="static" style="width: 65%; left: 16%; top: 15%; margin: 0 auto;">
									<div class="modal-dialog modal-md" style="position: static;">
										<div class="modal-content">
											<div class="modal-header" style="text-align: center;">
												<h4>Concluir Parcialmente o Pedido: #<?php echo $item['PedidoExame']['codigo']; ?></h4>
											</div>
											<div class="modal-body" style="min-height: 450px;">
												<strong>Data do Pedido: </strong><?php echo $item['PedidoExame']['data_inclusao']; ?><br />
												<strong>Funcionário: </strong><?php echo $dados_cliente_funcionario['Funcionario']['nome']; ?><br /><br />
												
												<?php echo $this->BForm->input('MotivoConclusaoParcial.' . $item['PedidoExame']['codigo'] . '.motivo', array('class' => 'input-xxlarge', 'label' => '<strong>Motivo da Conclusão</strong>', 'options' => $motivos_conclusao)); ?>
												<label id="mensagem_motivo_conclusao<?php echo $item['PedidoExame']['codigo']; ?>" style="color: red; font-weight: bold; display: none; margin: -15px 0 20px 0;">
													Você precisa selecionar um motivo para concluir parcialmente o pedido!
												</label>

												<label><strong>Descrição:</strong></label>
												<?php echo $this->BForm->textarea('MotivoConclusaoParcial.' . $item['PedidoExame']['codigo'] . '.descricao', array('style' => 'min-height:150px; min-width:450px')); ?>
												<label id="mensagem_descricao_conclusao<?php echo $item['PedidoExame']['codigo']; ?>" style="color: red; font-weight: bold; display: none; margin: -15px 0 20px 0;">
													Você precisa descrever um motivo para concluir parcialmente o pedido!
												</label>
												
												<div class="well">
													<a href="javascript:void(0);" onclick="confirma_conclusao(this, '<?php echo $item['PedidoExame']['codigo']; ?>');" class="btn btn-success">Confirmar</a>
													<a href="javascript:void(0);" onclick="manipula_modal('modal_concluir_pedido_parcial_<?php echo $item['PedidoExame']['codigo']; ?>', 0);" class="btn btn-default">Cancelar</a>												
												</div>
											</div>
										</div>
									</div>
								</div>							
								
			                </td>
			            </tr>
		        	<?php endforeach; ?>	        	
	        	<?php endif; ?> 
	    	</tbody>
	    </table>
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
	<div class='form-actions well'>
    	<?php echo $html->link('Voltar', array('controller' => 'clientes_funcionarios', 'action' => 'selecao_funcionarios'), array('class' => 'btn btn-default')); ?>
	</div>

	<div class="modal fade" id="modal_realizacao" data-backdrop="static"></div>

<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function() {
		setup_mascaras();
		$(".modal").css("z-index", "-1");
	});
		
	function manipula_modal(id, mostra) {
		if(mostra) {
			$("#" + id).css("z-index", "1050");
			$("#" + id).modal("show");
		} else {
			$(".modal").css("z-index", "-1");
			$("#" + id).modal("hide");
		}
	}		
		
	function confirma_cancelamento(elemento, codigo_pedido) {
		var element_origin = $(elemento).html();
		var codigo_motivo_cancelamento = $("#MotivoCancelamento" + codigo_pedido + "Descricao").val();
		
		if(codigo_motivo_cancelamento != 0) {
			$("#mensagem_motivo_" + codigo_pedido).hide();
		
			$.ajax({
		        type: "GET",
		        url: "/portal/pedidos_exames/cancelamento_pedido_exame/" + codigo_pedido + "/" + codigo_motivo_cancelamento,
		        dataType: "json",
		        beforeSend: function() {
					$(elemento).html("<img src=\"/portal/img/default.gif\">");
				},
		        success: function(json) {
					if(json) {
						manipula_modal("modal_cancelamento_" + codigo_pedido, 0);
						$("#cancelamento_pedido_" + codigo_pedido).html("Cancelado").attr("class", "label label-default");

						manipula_modal("modal_carregando", 1);
						location.reload();

					} else {
						manipula_modal("modal_cancelamento_" + codigo_pedido, 0);
						swal({type: "error", title: "Houve um erro.", text: "Houve um erro ao tentar cancelar o pedido!"});
					}
		        },
		        complete: function() {
					
				}
		    });				
		} else {
			$("#mensagem_motivo_" + codigo_pedido).show();
		}
		
	}

	function confirma_conclusao(elemento, codigo_pedido) {
		
		var codigo_motivo = $("#MotivoConclusaoParcial" + codigo_pedido + "Motivo").val();
		var descricao_motivo = $("#MotivoConclusaoParcial" + codigo_pedido + "Descricao").val();
		
		if(codigo_motivo != 0) {
			if(descricao_motivo != "") {

				$("#mensagem_motivo_conclusao" + codigo_pedido).hide();
				$("#mensagem_descricao_conclusao" + codigo_pedido).hide();
			
				$.ajax({
					type: "POST",
					url: "/portal/pedidos_exames/conclusao_parcial_pedido_exame/",
					dataType: "json",
					data: "codigo_pedido=" + codigo_pedido + "&codigo_motivo=" + codigo_motivo + "&descricao_motivo=" + descricao_motivo,
					
					beforeSend: function() {
						$(elemento).html("<img src=\"/portal/img/default.gif\">");
					},
					success: function(json) {
						if(json) {
							manipula_modal("modal_concluir_pedido_parcial_" + codigo_pedido, 0);
							$("#concluir_pedido_parcial_" + codigo_pedido).html("Concluído").attr("class", "label label-default");

							manipula_modal("modal_carregando", 1);
							location.reload();

						} else {
							manipula_modal("modal_concluir_pedido_parcial_" + codigo_pedido, 0);
							swal({type: "error", title: "Houve um erro.", text: "Houve um erro ao tentar concluir o pedido!"});
						}
					}
				});	
			
			} else {
				$("#mensagem_descricao_conclusao" + codigo_pedido).show();
			}
		} else {
			$("#mensagem_motivo_conclusao" + codigo_pedido).show();
		}
		
	}
		
	function carrega_contatos_pedido(codigo_pedido) {
		$.get(baseUrl + "pedidos_exames/carrega_contatos_pedido/" + codigo_pedido, function(data) {
			$("#contatos_" + codigo_pedido).html(data);
		});
	} 
		
	function cancelamento_pedido_exame(element, codigo_pedido) {
		var element_origin = $(element).html();
		
		$.ajax({
	        type: "POST",
	        url: "/portal/pedidos_exames/cancelamento_pedido_exame/",
	        dataType: "html",
	        data: "codigo_pedido=" + codigo_pedido,
	        beforeSend: function() {
				$(element).html("<img src=\"/portal/img/default.gif\">");
			},
	        success: function(conteudo) {
		
	        },
	        complete: function() {
				$(element).html(element_origin);
			}
	    });		
		
	}
		
	function print_item(element, codigo_pedido, codigo_cliente_funcionario) {
		var element_origin = $(element).html();
		
		$.ajax({
	        type: "POST",
	        url: "/portal/pedidos_exames/visualizar/",
	        dataType: "json",
	        data: "codigo_pedido=" + codigo_pedido + "&codigo_cliente_funcionario=" + codigo_cliente_funcionario,
	        beforeSend: function() {
				$(element).html("<img src=\"/portal/img/default.gif\">");
			},
	        success: function(json) {
				if(json) {
					var protocol = (window.location.host == \'tstportal.rhhealth.com.br\' || window.location.host == \'portal.rhhealth.com.br\') ? \'https://\' : \'http://\';
					$.each(json, function(key_fornecedor, item) {
						window.open(protocol + window.location.host + "/portal/pedidos_exames/imprime/" + codigo_pedido + "/" + key_fornecedor + "/" + codigo_cliente_funcionario);
					});
				}
	        },
	        complete: function() {
				$(element).html(element_origin);
			}
	    });				
	}

	function pedido_bloqueado()
	{
		var mensagem = "Não é possível emitir um novo pedido de exames para esse colaborador, pois, existe um pedido em aberto. Mais detalhes em: <a target=\"_blank\" href=\"https://rhhealth1.freshdesk.com/support/solutions/articles/32000025805-como-cancelar-um-pedido-de-exame-agendado\">aqui.</a>";
		swal({type: "error", title: "Emissão de Pedido Bloqueado.", text: mensagem, html:true });
	}

    function log_pedido(codigo_pedido_exame){
        var janela = window_sizes();
        window.open(baseUrl + "pedidos_exames/log_pedidos/" + codigo_pedido_exame + "/" + Math.random(), janela, "scrollbars=yes,menubar=no,height="+(janela.height-200)+",width="+(janela.width-80)+",resizable=yes,toolbar=no,status=no");
    }
		
'); ?>