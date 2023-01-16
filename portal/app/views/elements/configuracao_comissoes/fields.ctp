<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo_endereco_regiao', array('label' => 'Filial', 'options' => $filiais, 'empty' => 'selecione uma filial', 'class' => 'input-large')); ?>
	<?php echo $this->BForm->input('codigo_produto_naveg', array('label' => 'Produto', 'options' => $produtos, 'empty' => 'selecione um produto', 'class' => 'input-large')); ?>
	<?php echo $this->BForm->input('regiao_tipo_faturamento', array('label' => 'Faturamento', 'options' => array(1 => 'Total', 0 => 'Parcial'), 'class' => 'text-small')); ?>
	<?php echo $this->BForm->input('percentual', array('label' => 'Percentual (%)', 'type' => 'text', 'class' => 'input-small moeda numeric', 'maxlength' => 6)); ?>
</div>

<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?php echo $this->Html->link('Voltar',array('controller' => 'ConfiguracaoComissoes','action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		setup_mascaras("000,##");
    });', false);
?>