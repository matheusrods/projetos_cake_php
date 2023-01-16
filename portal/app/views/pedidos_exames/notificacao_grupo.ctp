	
<div class='inline well'>
	<?php echo $this->BForm->input('Empresa.razao_social', array('value' => $grupo_economico['Empresa']['razao_social'], 'class' => 'input-xlarge', 'label' => 'Empresa' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Empresa.codigo_documento', array('value' => $grupo_economico['Empresa']['codigo_documento'], 'class' => 'input-xlarge', 'label' => 'CNPJ' , 'readonly' => true, 'type' => 'text')); ?>
	<div style="clear: both;"></div>
</div>

<div class='inline well' id="parametros">
	<img src="/portal/img/default.gif" style="padding: 10px;">
</div>

<div id="caminho-pao"></div>

<?php echo $this->BForm->create('PedidosExames', array('url' => array('controller' => 'pedidos_exames','action' => 'notificacao_grupo', $this->passedArgs[0]))); ?>

<?php foreach($array_pedido_relatorio_especifico as $k => $tipo) : ?>
	<?php echo $this->BForm->hidden('relatorio_especifico.' . $k, array('value' => $tipo)); ?>
<?php endforeach; ?>

<table class="table table-striped">
	<thead>
		<tr>
			<th class="input-small">Tipo</th>
			<th class="input-small" style="text-align: center;">Quantidade de vias</th>
			<th class="input-large" style="text-align: center;">Funcionário</th>
			<th class="input-large" style="text-align: center;">Solicitante</th>
			<th class="input-large" style="text-align: center;">Fornecedor</th>
		</tr>
	</thead>
	<tbody id="validacao">
		<?php foreach ($dados_tipo_notificacao as $k => $tipo): 	
			$obrigatorio = isset($notificacao_itens_obrigatorios[$k]) ? 'obrigatorio' : '';
		?>
		<?php
			switch ($k):
				case 1:
					if(isset($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['reagendamento']) && $_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['reagendamento'] == 1){
						$funcionario_check = '';
						$solicitante_check = '';
						$fornecedor_check = '';
						$disabled = 'disabled';
						$obrigatorio = '';
					} else {
						$funcionario_check = 'checked';
						$solicitante_check = 'checked';
						$fornecedor_check = 'checked';
						$disabled = '';
					}
						
				break;
				case 2:
					if(isset($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['reagendamento']) && $_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['reagendamento'] == 1) {

						foreach ($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['itens_exames']['exames'] as $key => $dado_reagendamento) {

							if(isset($dado_reagendamento[$codigo_aso])){
								$funcionario_check = '';
								$solicitante_check = '';
								$fornecedor_check = 'checked';
								$disabled = '';	
							} else {
								$funcionario_check = '';
								$solicitante_check = '';
								$fornecedor_check = '';
								$disabled = 'disabled';	
							}
						}
					} else {
						$funcionario_check = '';
						$solicitante_check = '';
						$fornecedor_check = 'checked';
						$disabled = '';
					}
				break;
				case 3:	

					if(isset($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['reagendamento']) && $_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['reagendamento'] == 1) {

						foreach ($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['itens_exames']['exames'] as $key => $dado_reagendamento) {

							if(isset($dado_reagendamento[$codigo_aso])){
								$funcionario_check = '';
								$solicitante_check = '';
								$fornecedor_check = 'checked';
								$disabled = '';	
							} else {
								$funcionario_check = '';
								$solicitante_check = '';
								$fornecedor_check = '';
								$disabled = 'disabled';	
							}
						}
					} else {
						$funcionario_check = '';
						$solicitante_check = '';
						$fornecedor_check = 'checked';
						$disabled = '';						
					}
				break;
				case 4:	
				if($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['portador_deficiencia']['portador_deficiencia'] == 1):

					if(isset($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['reagendamento']) && $_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['reagendamento'] == 1) {

						foreach ($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['itens_exames'] as $key => $dado_reagendamento) {

							if(isset($dado_reagendamento[$codigo_pcd])){
								$funcionario_check = '';
								$solicitante_check = '';
								$fornecedor_check = 'checked';
								$disabled = '';
								$obrigatorio = 'obrigatorio';
							} else {
								$funcionario_check = '';
								$solicitante_check = '';
								$fornecedor_check = '';
								$disabled = 'disabled';
								$obrigatorio = '';
							}
						}
					} else {
						$funcionario_check = '';
						$solicitante_check = '';
						$fornecedor_check = 'checked';
						$disabled = '';
						$obrigatorio = 'obrigatorio';
					}
				else:
					$funcionario_check = '';
					$solicitante_check = '';
					$fornecedor_check = '';
					$disabled = 'disabled';
					$obrigatorio = '';
				endif;

				break;
				case 5:	
					$funcionario_check = 'checked';
					$solicitante_check = 'checked';
					$fornecedor_check = '';
					$disabled = '';
				break;
				case 6:
					if(isset($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['reagendamento']) && $_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['reagendamento'] == 1) {
						
						foreach ($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['itens_exames'] as $key_reag => $dado_reagendamento) {							

							if(isset($dado_reagendamento[$codigo_audio])){
								$funcionario_check = '';
								$solicitante_check = '';
								$fornecedor_check = 'checked';
								$disabled = '';	
							} else {
								$funcionario_check = '';
								$solicitante_check = '';
								$fornecedor_check = 'checked';
								$disabled = 'disabled';	
							}					
						}
					} else {
						$funcionario_check = '';
						$solicitante_check = '';
						$fornecedor_check = 'checked';
						$disabled = '';						
					}
				break;
				case 8:
					if(isset($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['reagendamento']) && $_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['reagendamento'] == 1) {

						foreach ($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['itens_exames'] as $key_reag => $dado_reagendamento) {

							if(isset($dado_reagendamento[$codigo_av_psico])){
								$funcionario_check = '';
								$solicitante_check = 'checked';
								$fornecedor_check = 'checked';
								$disabled = '';
							} else {
								$funcionario_check = '';
								$solicitante_check = '';
								$fornecedor_check = '';
								$disabled = 'disabled';
							}

						}

					} else {
						$funcionario_check = '';
						$solicitante_check = 'checked';
						$fornecedor_check = 'checked';
						$disabled = '';						
					}
				break;	            		
				default:
					$funcionario_check = '';
					$disabled = '';
				break;
			endswitch;
			?>	            
			<tr <?php if(!empty($obrigatorio)): ?> class="obrigatorio" <?php endif;?> >
				<td class="input-small"><?php echo $tipo; ?></td>

				<td class="input-large" style="text-align: center;">
					<?php if($k == 2): ?>
						<input type="text"  class="input-mini" name="data[PedidosExames][vias_aso]" value="<?php echo !empty($vias_aso) ? $vias_aso : 1;?>">
					<?php endif;?>
				</td>
				<td class="input-large" style="text-align: center;"><input type="checkbox" name="data[PedidosExames][funcionario][<?php echo $k; ?>]" value="1" multiple="multiple" <?=$funcionario_check;?> <?php echo $disabled;?> ></td>
				<td class="input-large" style="text-align: center;"><input type="checkbox" name="data[PedidosExames][cliente][<?php echo $k; ?>]" value="1"  multiple="multiple" <?=$solicitante_check;?> <?php echo $disabled;?> ></td>
				<td class="input-large" style="text-align: center;"><input type="checkbox" name="data[PedidosExames][fornecedor][<?php echo $k; ?>]" value="1"  multiple="multiple" <?=$fornecedor_check;?> <?php echo $disabled;?> ></td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>

<table style="width: 100%">
	<?php foreach($notificar as $id_pedido => $dados_notificacao) : ?>
		<thead>
			<tr style="background: #BCCAD8;">
				<td colspan="<?php echo count($dados_notificacao) - 1; ?>" style="padding: 15px;">
					<h4>PEDIDO #<?php echo $id_pedido; ?></h4>
				</td>
				<td colspan="1">
					<a href="javascript:void(0);" onclick="pre_visualizar(this, <?php echo $id_pedido; ?>, <?php echo isset($array_pedido_relatorio_especifico[$id_pedido]) ? '1' : '0'; ?>);" class="btn btn-warning" style="float: right;"><i class="icon-white icon-eye-open"></i> Pré Visualizar Relatórios</a>
				</td>		    			
			</tr>		    		
			<tr>
				<?php foreach($dados_notificacao as $nome_relatorio => $info) : ?>
					<td style="width: <?php echo round(100 / count($dados_notificacao)); ?>%; padding: 15px;"><strong><?php echo $nome_relatorio; ?>:</strong></td>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<tr>
				<?php foreach($dados_notificacao as $nome_relatorio => $info) : ?>
					<td style="border: 1px solid #CCC; padding: 15px;" valign="top">
						<?php foreach($info as $codigo => $item) : ?>
							<span style="font-size: 11px;" data-toggle="tooltip" title="<?php echo $item['nome']; ?>"><?php echo $this->Text->truncate($item['nome'], 50, array('ellipsis' => '...', 'exact' => false)); ?></span><br />
							<?php echo $this->BForm->input('Email.' . $nome_relatorio . '.' . $codigo . '.email', array('class' => 'form-control js-valida-email', 'label' => false, 'style' => 'width: 95%;', 'value' => $item['email'], 'onblur' => 'ajusta_campo(this, "' . $nome_relatorio . '", "' . $codigo . '");')); ?>
							<?php echo $this->BForm->hidden('Email.' . $nome_relatorio . '.' . $codigo . '.nome', array('value' => $item['nome'])); ?>
						<?php endforeach; ?>
					</td>
				<?php endforeach; ?>
			</tr>
			<tr style="background: #FFF;">
				<td colspan="<?php echo count($dados_notificacao); ?>">
					<div class="modal fade" id="modal_pre_visualizacao_<?php echo $id_pedido; ?>">
						<div class="modal-dialog modal-lg" style="position: static;">
							<div class="modal-content">
								<div class="modal-body" style="height: 600px;" id="conteudo_modal_pre_visualizacao_<?php echo $id_pedido; ?>">

								</div>
							</div>
						</div>
					</div>
					<hr />
				</td>
			</tr>			    		
		</tbody>
	<?php endforeach; ?>
</table>   

<div class="modal fade" id="modal_resumo" data-backdrop="static" style="width: 65%; left: 16%; top: 15%; margin: 0 auto;">
	<div class="modal-dialog modal-sm" style="position: static;">
		<div class="modal-content">
			<div class="modal-header" style="text-align: center;">
				<h3>RESUMO:</h3>
			</div>

			<div class="modal-body" style="min-height: 390px;">
				<?php if(count($_SESSION['grupo_economico'][$codigo_grupo_economico]['pedidos_salvos']) > 1) : ?>
					<strong>OBS: Este pedido em grupo, gerou <?php echo count($_SESSION['grupo_economico'][$codigo_grupo_economico]['pedidos_salvos']); ?> pedido(s) individuais.</strong>
					<br /><br />
				<?php endif; ?>

				<?php $sim = array(); $nao = array(); ?>
				<?php foreach($pedido_lote as $k_pedido => $pedido) : ?>
					<?php if(isset($pedidos_sugestao[$k_pedido])) : ?>
						<?php $sim[$k_pedido] = $pedido; ?>
					<?php else : ?>
						<?php $nao[$k_pedido] = $pedido; ?>
					<?php endif; ?>
				<?php endforeach; ?>

				<?php if(count($sim)) : ?>
					<h5><span style="color: #ff0000;">NÃO SERÃO NOTIFICADOS</span>, pois existem exames pendente de agendamento:</h5>
					<?php foreach($sim as $k_pedido => $pedido) : ?>
						<?php echo $this->BForm->hidden('PedidosExames.sugestao.' . $k_pedido, array('value' => '1')); ?>
						<p># Pedido <?php echo $k_pedido; ?> - <?php echo $pedido; ?></p>
					<?php endforeach; ?>
					<br />						
				<?php endif; ?>

				<?php if(count($nao)) : ?>
					<h5><span style="color: #ff0000;">SERÃO NOTIFICADOS</span>, pois foram agendados os exames:</h5>
					<?php foreach($nao as $k_pedido => $pedido) : ?>
						<?php echo $this->BForm->hidden('PedidosExames.sugestao.' . $k_pedido, array('value' => '0')); ?>
						<p># Pedido <?php echo $k_pedido; ?> - <?php echo $pedido; ?></p>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
		<div class="modal-footer">
			<div class="right">
				<a href="javascript:void(0);" id="botao_<?php echo $k; ?>" onclick="submit_form();" class="btn btn-success">CONFIRMAR</a>
			</div>
		</div>									    
	</div>
</div>

<div class="form-actions well">
	<?php if(isset($pedidos_sugestao) && count($pedidos_sugestao)) : ?>
		<?php echo $this->BForm->hidden('PedidosExames.tem_sugestao', array('value' => '1')); ?>
		<a href="javascript:void(0);" onclick="mostra_resumo();" class="btn btn-primary"><i class="icon-white icon-thumbs-up"></i> Gravar Preferências</a>
	<?php else : ?>
		<?php echo $this->BForm->hidden('PedidosExames.tem_sugestao', array('value' => '0')); ?>
		<a href="javascript:void(0);" onclick="submit_form();" class="btn btn-primary"><i class="icon-white icon-thumbs-up"></i> Enviar Notificações!</a>
	<?php endif; ?>
</div>			

<?php echo $this->BForm->end(); ?>

<?php echo $this->Javascript->codeBlock("
	jQuery(document).ready(function() {
		$('.modal').css('z-index', '-1');
		atualiza_parametros(".$codigo_grupo_economico.");
		$('#caminho-pao').load('/portal/pedidos_exames/caminho_pao/4');
	});
	function ajusta_campo(elemento, nome, codigo) {
		$('input[name=\"data[Email][' + nome + '][' + codigo + '][email]\"]').val($(elemento).val());
	}
	function atualiza_parametros(codigo_grupo_economico) {
		$('#parametros').load('/portal/pedidos_exames/carrega_parametros/' + codigo_grupo_economico + '/1');
	}
	function manipula_modal(id, mostra) {
		if(mostra) {
			$('#' + id).css('z-index', '1050');
			$('#' + id).modal('show');
		} else {
			$('.modal').css('z-index', '-1');
			$('#' + id).modal('hide');
		}
	}			
	function mostra_resumo() {
		manipula_modal('modal_resumo', 1);
	}
	function pre_visualizar(element, codigo_pedido, audiometria) {
		var element_origin = $(element).html();
		$.ajax({
			type: 'POST',
			url: '/portal/pedidos_exames/retorna_link_relatorios/',
			dataType: 'html',
			data: 'codigo_pedido=' + codigo_pedido + '&audiometria=' + audiometria,
			beforeSend: function() {
				$(element).html('<img src=\'/portal/img/default.gif\'>');
			},
			success: function(retorno) {
				manipula_modal('modal_pre_visualizacao_' + codigo_pedido, 1);
				$('#conteudo_modal_pre_visualizacao_' + codigo_pedido).html(retorno);
			},
			complete: function() {
				$(element).html(element_origin);
			}
		});				
	}	
	function submit_form(){
		var erro = false;
		var mensagem_erro = '<div style=\"text-align:center\">O(s) relatório(s) mencionado(s) abaixo deve(m) ser enviado(s) pelo menos para um usuário:<br><br>';
		$('.js-valida-email').css({borderColor: '#ccc'});
		$('#validacao tr[class=\"obrigatorio\"]').each(function(index, value) {
			var exame = $(this).find('td:first-child').text();
			var validacao = false;
			var i = 0
			$(this).find('input[type=\"checkbox\"]:enabled').each(function(index2, value2) {
				if($(this).is(':checked')) {
					validacao = true;
				}
				i++;
			});
			if(!validacao && i>0) {
				erro = true;
				mensagem_erro += '<span style=\"font-weight:bold\">-'+exame+'</span><br>';
			}	
		});
		mensagem_erro += '</div>';
		if(erro){	
			swal({
				type: 'warning',
				title: 'Atenção',
				text: mensagem_erro,
				html: true
			});
		} else {
				// valida se os campos de e-mail estão preenchidos caso sejam necessários
			var funcionario_email = true;
			$('[name^=\"data[PedidosExames][funcionario]\"]').each(function(index, value) {
				if($(this).is(':checked') == true) {
					$('input[name^=\"data[Email][Funcionario]\"]').each(function(index2, value2) {
						if(value2.value.trim() == '') {
							funcionario_email = false;
							$(this).css({borderColor: 'red'});
						}
					});
				}
			});
			var cliente_email = true;
			$('[name^=\"data[PedidosExames][cliente]\"]').each(function(index, value) {
				if($(this).is(':checked') == true) {
					$('input[name^=\"data[Email][Cliente]\"]').each(function(index2, value2) {
						if(value2.value.trim() == '') {
							funcionario_email = false;
							$(this).css({borderColor: 'red'});
						}
					});
				}
			});
			var fornecedor_email = true;
			$('[name^=\"data[PedidosExames][fornecedor]\"]').each(function(index, value) {
				if($(this).is(':checked') == true) {
					$('input[name^=\"data[Email][Fornecedor]\"]').each(function(index2, value2) {
						if(value2.value.trim() == '') {
							fornecedor_email = false;
							$(this).css({borderColor: 'red'});
						}
					});
				}
			});
			if(funcionario_email && cliente_email && fornecedor_email) {
				$('#PedidosExamesNotificacaoGrupoForm').submit();;
			} else {
				swal({
					type: 'warning',
					title: 'Atenção',
					text: 'Há campos de e-mail obrigatórios que não foram preenchidos, por favor verifique.'
				});
			}
		}
	}
	", false); ?>	