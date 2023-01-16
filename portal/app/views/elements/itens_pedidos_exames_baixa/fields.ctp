

<style>
	legend {font-size: 13px; margin-bottom: 0;}
	.control-group {padding:0; margin: 0}
	input[type="radio"] {margin: 6px;}
	.row-fluid .error-message {color:#b94a48;}
</style>

<?php echo $this->BForm->hidden('PedidoExame.codigo', array('value' => $codigo_pedidos_exames)); ?>
<?php if(!empty($itens_pedidos_exames)):?>
    
    <table class="table table-striped">
        <thead>
            <tr>
	            <th>Pedido</th>
	            <th>Cliente</th>
	            <th>Funcionário</th>
	            <th>Exame</th>
	            <th>Fornecedor</th>
	            <th>Resultado <br>do Exame</th>
	            <th>Data de Resultado <br>do Exame</th>
	            <th>Fornecedor Particular</th>
	            <th>Descrever as Anormalidades <br>do Exame (se necessário)</th>
	            <th class="acoes">Status</th>
            </tr>
        </thead>
        <tbody>
        	<?php 
            foreach ($itens_pedidos_exames as $linha => $item): ?>
	            <tr>
					<?php echo $this->BForm->hidden('ItemPedidoExameBaixa.'.$linha.'.codigo_itens_pedidos_exames', array('value' => $item['ItemPedidoExame']['codigo'])); ?>
					<?php echo $this->BForm->hidden('ItemPedidoExameBaixa.'.$linha.'.status_item', array('value' => $item['ItemPedidoExame']['status_item'])); ?>

					<?php
						if( $edit ){
							echo $this->BForm->hidden('ItemPedidoExameBaixa.'.$linha.'.codigo', array('value' => $item['ItemPedidoExameBaixa']['codigo']));	
						}
					?>

	                <td><?php echo $item['PedidoExame']['codigo'] ?></td>
	                <td><?php echo $item['Cliente']['razao_social'] ?></td>
	                <td><?php echo $item['Funcionario']['nome'] ?></td>
	                <td><?php echo $item['Exame']['descricao'] ?></td>
	                <td><?php echo $item['Fornecedor']['razao_social'] ?></td>
	                <td><?php 

	                	if( $edit ){
	                		$disabled = '';
							$class_data = 'data datepicker';	
	                	} else {
	                		if($item['ItemPedidoExame']['status_item'] == 1):
								$disabled = true;
								$class_data = '';
							else:
								$disabled = '';
								$class_data = 'data datepicker';
							endif;	
	                	}
												
						?>	

	               		<?php echo $this->BForm->input('ItemPedidoExameBaixa.'.$linha.'.resultado', array('label' => false, 'class' => 'input-small resultado', 'options' => $item['TiposResultados'], 'type' => 'select', 'onchange' => 'mostraDescricao(this);', 'empty' => 'Selecione', 'default' => ' ', 'value' => $item['ItemPedidoExameBaixa']['resultado'], 'disabled' => $disabled)) ?>
	                </td>
	                <td class='input-medium'>
	                	 <?php echo $this->BForm->input('ItemPedidoExameBaixa.'.$linha.'.data_realizacao_exame', array('type' => 'text', 'class' => 'input-small '.$class_data, 'label' => false, 'value' => $item['ItemPedidoExameBaixa']['data_realizacao_exame'], 'disabled' => $disabled)); ?>
	                </td>
	                <td>
					<?php 
						if($item['ItemPedidoExameBaixa']['fornecedor_particular'] == 1):
							$checked = true;
						else:
							$checked = false;
						endif;?>

			   			<?php echo $this->BForm->checkbox('ItemPedidoExameBaixa.'.$linha.'.fornecedor_particular', array('hiddenField' => false, 'style' => 'width:25px;height:15px; vertical-align:bottom','checked' => $checked, 'disabled' => true) ); ?>
	                </td>
	                <td>
	                	<?php echo $this->Form->input('ItemPedidoExameBaixa.'.$linha.'.descricao', array('type' => 'textarea', 'class' => 'input-small', 'label' => false, 'style' => 'height: 60px; width: 220px; font-size: 11px;', 'value' => $item['ItemPedidoExameBaixa']['descricao'],  'disabled' => $disabled)); ?>
	                </td>
	                <td style='text-align:center'>              	
	                	<?php if($item['ItemPedidoExame']['status_item']== 0): ?>
		                    <span class="badge-empty badge badge-important" title="Pendente"></span>
		                <?php elseif($item['ItemPedidoExame']['status_item']== 1): ?>
		                    <span class="badge-empty badge badge-success" title="Baixado"></span>
		                <?php endif; ?>
	                </td>
	            </tr>
	        <?php endforeach ?>
	    </tbody>
    </table>
  <div class='form-actions'>
  	<a href="javascript:void(0);" class="btn btn-primary" onclick="enviarDados(this);" >Salvar</a>
  	<?php //if($lista_pedidos == "lista_pedidos_exames"): ?>
  		<?//= //$html->link('Voltar', array('controller' => 'pedidos_exames', 'action' => 'lista_pedidos'), array('class' => 'btn')); ?>
  	<?php //else:?>
  		<?= $html->link('Voltar', array('controller' => 'itens_pedidos_exames_baixa', 'action' => 'index'), array('class' => 'btn')); ?>
  	<?php //endif; ?>
  </div>
<?php endif;?>
<?php echo $this->Javascript->codeBlock('
		$(document).ready(function() {
	    	setup_mascaras(); setup_time(); setup_datepicker();
	    	mostraDescricao($(".resultado"));
		});
		
		function mostraDescricao(element) {
			if($(element).val() == "2") {
				$(element).parent().parent().next().next().find("textarea").attr("disabled",false)
			} else {
				$(element).parent().parent().next().next().find("textarea").attr("disabled",true)
			}
		}

		/**
		* Funcao para enviar os dados para a controller colocando o botão de loading
		*/
		function enviarDados(element) {
			$(\'body\').append($(\'<div>\', {class: \'ajax-loader\'}));
			$(\'#ItemPedidoExameBaixaBaixaForm\').submit();
		}//fim enviarDados

'); ?>