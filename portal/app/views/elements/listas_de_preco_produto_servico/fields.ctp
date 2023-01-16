<div class='row-fluid inline'>
	<?php echo $this->BForm->hidden('codigo'); ?>
	<?php echo $this->BForm->input('codigo_produto', array('label' => 'Produto', 'class' => 'input-xlarge', 'options' => $produtos, 'empty' => '')); ?>
</div>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('codigo_servico', array('label' => 'Servico', 'class' => 'input-xlarge', 'options' => $servicos, 'empty' => '')); ?>
	<?php echo $this->BForm->input('valor', array('label' => 'Valor', 'maxlength' => 14, 'class' => 'input-medium numeric moeda')); ?>
	<?php echo $this->BForm->input('valor_maximo', array('label' => 'Valor Máximo', 'class' => 'input-medium numeric moeda', 'maxlength' => 14)); ?>
	<?php echo $this->BForm->input('valor_venda', array('label' => 'Valor Venda', 'class' => 'input-medium numeric moeda', 'maxlength' => 14)); ?>
	
</div>

<div class="row-fluid inline">
	<label>Tipo de Atendimento:</label>
	<?php echo $this->BForm->input('ListaDePrecoProdutoServico.tipo_atendimento', array('div' => true, 'legend' => false, 'options' => array('0' => 'Ordem de Chegada', '1' => 'Hora Marcada', '2' => 'Não se aplica'), 'type' => 'radio', 'value' => $lista_de_preco['Fornecedor']['tipo_atendimento'])); ?>
</div>

<div id='controle-de-volume' style='<?= ($tem_controle_de_volume) ? '' : 'display:none' ?>'>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('tipo_premio_minimo', array('legend' => false, 'type' => 'radio', 'label' => array('class' => 'radio inline'), 'options' => array(1 => 'por Produto', 2 => 'por Serviço'))); ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('valor_premio_minimo', array('label' => 'Prêmio Mínimo (R$)', 'class' => 'input-medium numeric moeda')); ?>
		<?php echo $this->BForm->input('qtd_premio_minimo', array('label' => 'Prêmio Mínimo (Qtd)', 'class' => 'input-medium numeric')); ?>
	</div>
</div>
<div class='form-actions'>
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?= $html->link('Voltar', array('controller' => 'listas_de_preco_produto', 'action' => 'index', $this->passedArgs[0]), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php $this->addScript($this->Javascript->codeBlock("
	setup_mascaras();
	function atualizaComboServicos(codigo_produto) {
		jQuery.ajax({
			url: baseUrl + 'produtos_servicos/servicos_por_produto/'+codigo_produto+'/'+Math.random(), 
			success: function(data){
				jQuery('#ListaDePrecoProdutoServicoCodigoServico').html(data);
			}
		});
	}

	function verificaControleDeVolume(codigo_produto) {
		jQuery.ajax({
			url: baseUrl + 'produtos/tem_controle_de_volume/'+codigo_produto+'/'+Math.random(), 
			dataType: 'json',
			success: function(tem_controle_de_volume){
				if (tem_controle_de_volume) {
					jQuery('div#controle-de-volume').show();
				} else {
					jQuery('div#controle-de-volume').hide();
				}
			}
		});
	}

	jQuery('#ListaDePrecoProdutoServicoCodigoProduto').change(function() {
		var combo = jQuery(this);
		$('#ListaDePrecoProdutoServicoCodigoServico option:selected').text('Aguarde, carregando...');
		atualizaComboServicos(combo.val());
		verificaControleDeVolume(combo.val());
	});
")) ?>