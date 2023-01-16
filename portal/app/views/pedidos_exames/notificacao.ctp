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

<div id="caminho-pao"></div>

<?php echo $this->BForm->create('PedidosExames', array('url' => array('controller' => 'pedidos_exames','action' => 'notificacao', $this->passedArgs[0], $this->passedArgs[1]))); ?>
<?php echo $this->BForm->hidden('codigo_cliente_funcionario', array('value' => $this->passedArgs[0])); ?>
<?php if(isset($relatorio_especifico) && $relatorio_especifico) : ?>
	<?php echo $this->BForm->hidden('relatorio_especifico.' . $this->passedArgs[1], array('value' => '6')); ?>
<?php endif; ?>

<table class="table table-striped">
	<thead>
		<tr>
			<th class="input-medium">Tipo</th>
			<th class="input-medium" style="text-align: center;">Quantidade de vias</th>
			<th class="input-large" style="text-align: center;">Funcionário</th>
			<th class="input-large" style="text-align: center;">Solicitante</th>
			<th class="input-large" style="text-align: center;">Fornecedor</th>
		</tr>
	</thead>
	<tbody class="validacao">
		<?php foreach ($tipos_notificacao_valor as $k => $tipo): ?>



			<?php

			$piscossocial = 0;
			$audiometria = 0;
			$aso = 0;
			$ficha_assistencial = 0;

			foreach($dados_itens_pedido as $dado_item){
				if($dado_item['ItemPedidoExame']['codigo_exame'] == $codigo_av_psico){
					$piscossocial = 1;
				} else if($dado_item['ItemPedidoExame']['codigo_exame'] == $codigo_audio){
					$audiometria = 1;
				} else if($dado_item['ItemPedidoExame']['codigo_exame'] == $codigo_exame_aso){
					$aso = 1;
				} else if(in_array($dado_item['ItemPedidoExame']['codigo_exame'], $codigo_exame_assistencial)) {
					$ficha_assistencial = 1;
				}
			} 

			if($audiometria == 1){ 
				echo $this->BForm->input('exame_audiometria', array('type' => 'hidden', 'value' => $audiometria));
			}			

			//verifica se existe este indice
			if(!isset($dados_tipo_notificacao[$tipo['TipoNotificacaoValor']['codigo_tipo_notificacao']])) {
				continue;
			}//fim verificacao

			$obrigatorio = '';
			
			if($tipo['TipoNotificacaoValor']['codigo_tipo_notificacao'] != 4):
				$disabled = '';
				
				//verifica se item está na lista de relatórios obrigatórios
				if(isset($notificacao_itens_obrigatorios[$tipo['TipoNotificacaoValor']['codigo_tipo_notificacao']])):
					$obrigatorio = 'obrigatorio';
				endif;

			else:
				if(isset($dados_itens_pedido[0]['PedidoExame']['portador_deficiencia']) && $dados_itens_pedido[0]['PedidoExame']['portador_deficiencia'] == 1):
					$disabled = ''; 
					$obrigatorio = 'obrigatorio';
				else:
					$disabled = 'disabled'; 
				endif;
			endif;

			if($tipo['TipoNotificacaoValor']['codigo_tipo_notificacao'] == 8):
				if($piscossocial == 0){
					$disabled = 'disabled'; 
					$tipo['TipoNotificacaoValor']['campo_funcionario'] = 0;
					$tipo['TipoNotificacaoValor']['campo_cliente'] = 0;
					$tipo['TipoNotificacaoValor']['campo_fornecedor'] = 0;
				}
			endif;

			if ($tipo['TipoNotificacaoValor']['codigo_tipo_notificacao'] == 6):
				if($audiometria == 0){
					$disabled = 'disabled'; 
					$tipo['TipoNotificacaoValor']['campo_funcionario'] = 0;
					$tipo['TipoNotificacaoValor']['campo_cliente'] = 0;
					$tipo['TipoNotificacaoValor']['campo_fornecedor'] = 0;
				}
			endif;

			if ($tipo['TipoNotificacaoValor']['codigo_tipo_notificacao'] == 2 OR $tipo['TipoNotificacaoValor']['codigo_tipo_notificacao'] == 3):
				if($aso == 0){
					$disabled = 'disabled'; 
					$tipo['TipoNotificacaoValor']['campo_funcionario'] = 0;
					$tipo['TipoNotificacaoValor']['campo_cliente'] = 0;
					$tipo['TipoNotificacaoValor']['campo_fornecedor'] = 0;
				}
			endif;

			if ($tipo['TipoNotificacaoValor']['codigo_tipo_notificacao'] == 7):
				if($ficha_assistencial == 0){
					$disabled = 'disabled'; 
					$tipo['TipoNotificacaoValor']['campo_funcionario'] = 0;
					$tipo['TipoNotificacaoValor']['campo_cliente'] = 0;
					$tipo['TipoNotificacaoValor']['campo_fornecedor'] = 0;
				}
			endif;

			?>   



				<tr <?php if(!empty($obrigatorio)): ?> class="obrigatorio" <?php endif;?> >
					<td class="input-medium">
						<?php echo $dados_tipo_notificacao[$tipo['TipoNotificacaoValor']['codigo_tipo_notificacao']]; ?>
					</td>
					<td class="input-large" style="text-align: center;">
						<?php if($tipo['TipoNotificacaoValor']['codigo_tipo_notificacao'] == 2): ?>
							<input type="text"  class="input-mini" name="data[PedidosExames][vias_aso]" value="<?php echo  !empty($tipo['TipoNotificacaoValor']['vias_aso']) ? $tipo['TipoNotificacaoValor']['vias_aso'] : $vias_aso; ?>">
						<?php endif;?>
					</td>
					<td class="input-large" style="text-align: center;">
						<input type="checkbox" name="data[PedidosExames][funcionario][<?php echo $tipo['TipoNotificacaoValor']['codigo_tipo_notificacao']; ?>]" value="1" multiple="multiple" <?php echo $tipo['TipoNotificacaoValor']['campo_funcionario'] ? ' CHECKED ' : ''; ?><?php echo $disabled;?>  >
					</td>
					<td class="input-large" style="text-align: center;">
						<input type="checkbox" name="data[PedidosExames][cliente][<?php echo $tipo['TipoNotificacaoValor']['codigo_tipo_notificacao']; ?>]" value="1"  multiple="multiple" <?php echo $tipo['TipoNotificacaoValor']['campo_cliente'] ? ' CHECKED ' : ''; ?><?php echo $disabled;?>>
					</td>
					<td class="input-large" style="text-align: center;">
						<input type="checkbox" name="data[PedidosExames][fornecedor][<?php echo $tipo['TipoNotificacaoValor']['codigo_tipo_notificacao']; ?>]" value="1"  multiple="multiple" <?php echo $tipo['TipoNotificacaoValor']['campo_fornecedor'] ? ' CHECKED ' : ''; ?><?php echo $disabled;?> >
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div class="well">
		<table style="width: 100%">
			<tr>
				<td style="width: 350px;"><br /><?php echo $this->BForm->input('EmailFuncionario.email', array('class' => 'form-control js-valida-email', 'label' => false, 'style' => 'width: 95%;')); ?></td>
				<td>Funcionário: (<?php echo $dados_cliente_funcionario['Funcionario']['nome']; ?>)</td>
			</tr>
			<tr>
				<td style="width: 350px;"><br /><?php echo $this->BForm->input('EmailCliente.email', array('class' => 'form-control js-valida-email', 'label' => false, 'style' => 'width: 95%;')); ?></td>	
				<td>Solicitante: (<?php echo $dados_cliente_funcionario['Cliente']['razao_social']; ?>)</td>
			</tr>
			<?php foreach($fornecedores_notificar as $key => $fornecedor) : ?>
				<tr>
					<td style="width: 350px;"><br /><?php echo $this->BForm->input('EmailFornecedor.' . $key . '.fornecedor', array('class' => 'form-control js-valida-email', 'label' => false, 'style' => 'width: 95%;')); ?></td>
					<td>Fornecedor: (<?php echo $fornecedores_disponiveis[$key]['razao_social']; ?>)</td>
				</tr>	    		
			<?php endforeach; ?>
		</table>
	</div>
	<div class='form-actions well'>
		<?php if(isset($tem_sugestao) && $tem_sugestao == '1') : ?>
			<a href="javascript:void(0);" onclick="submit_form();" class="btn btn-primary"><i class="icon-white icon-thumbs-up"></i> Gravar Preferências</a>
		<?php else : ?>
			<a href="javascript:void(0);" onclick="submit_form();" class="btn btn-primary"><i class="icon-white icon-thumbs-up"></i> Notificar</a>
			<a href="javascript:void(0);" onclick="pre_visualizar(this, <?php echo $this->passedArgs[0]; ?>, <?php echo $this->passedArgs[1]; ?>, <?php echo $relatorio_especifico; ?>);" class="btn btn-warning"><i class="icon-white icon-eye-open"></i> Pré Visualizar Relatórios</a>
		<?php endif; ?>
	</div>
	<?php echo $this->BForm->end(); ?>
	
	<div class="modal fade" id="modal_pre_visualizacao">
		<div class="modal-dialog modal-lg" style="position: static;">
			<div class="modal-content">
				<div class="modal-body" style="height: 600px;" id="conteudo_modal_pre_visualizacao">

				</div>
			</div>
		</div>
	</div>
	
	<?php echo $this->Javascript->codeBlock("	
		jQuery(document).ready(function() {

		// seta etapa
			$('#caminho-pao').load('/portal/pedidos_exames/caminho_pao/4');
		});

		function pre_visualizar(element, codigo_cliente_funcionario, codigo_pedido, audiometria) {
			
			var element_origin = $(element).html();			
			
			$.ajax({
				type: 'POST',
				url: '/portal/pedidos_exames/retorna_link_relatorios/',
				dataType: 'html',
				data: 'codigo_pedido=' + codigo_pedido + '&codigo_cliente_funcionario=' + codigo_cliente_funcionario + '&audiometria=' + audiometria,
				beforeSend: function() {
					$(element).html('<img src=\"/portal/img/default.gif\">');
				},
				success: function(retorno) {
					$('#modal_pre_visualizacao').modal('show');
					$('#conteudo_modal_pre_visualizacao').html(retorno);
				},
				complete: function() {
					$(element).html(element_origin);
				}
			});				
		}

		function submit_form(){
			
			var erro = false;

			$('.js-valida-email').css({borderColor: '#ccc'});

			var mensagem_erro = '<div style=\"text-align:center\">O(s) relatório(s) mencionado(s) abaixo deve(m) ser enviado(s) pelo menos para um usuário:<br><br>';

			$('.validacao tr[class=\"obrigatorio\"]').each(function(index, value) {
				
				var exame = $(this).find('td:first-child').text();
				
				var validacao = false;
				
				var i = 0;
				
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
						if($('input[name=\"data[EmailFuncionario][email]\"]').val().trim() == '') {
							funcionario_email = false;
							return;
						}
					}
				});
				
				var cliente_email = true;
				
				$('[name^=\"data[PedidosExames][cliente]\"]').each(function(index, value) {
				
					if($(this).is(':checked') == true) {
				
						if($('input[name=\"data[EmailCliente][email]\"]').val().trim() == '') {
							cliente_email = false;
							return;
						}
					}
				});
				
				var fornecedor_email = true;
				
				var input_elemento = [];
				
				var i = 0;
				
				$('[name^=\"data[PedidosExames][fornecedor]\"]').each(function(index, value) {
				
					if($(this).is(':checked') == true) {
				
						$('input[name^=\"data[EmailFornecedor]\"]').each(function(index2, value2) {
				
							if(value2.value.trim() == '') {
								input_elemento[i] = $(this);
								fornecedor_email = false;
								i++;
							}
						});
					}
				});
				
				if(funcionario_email && cliente_email && fornecedor_email) {
				
					$('#PedidosExamesNotificacaoForm').submit();
				} else {
				
					if(!funcionario_email){
				
						$('input[name=\"data[EmailFuncionario][email]\"]').css({borderColor: 'red'});
					}
				
					if(!cliente_email){
						$('input[name=\"data[EmailCliente][email]\"]').css({borderColor: 'red'});
					}
				
					if(!fornecedor_email){
						$.each(input_elemento, function(index, value) {
							value.css({borderColor: 'red'});
						});
					}
				
					swal({
						type: 'warning',
						title: 'Atenção',
						text: 'Há campos de e-mail obrigatórios que não foram preenchidos, por favor verifique.'
					});
				}
			}
		}
		", false); ?>