<?php echo $this->BForm->create('ObjetivoExcecaoFaturamento', array('type' => 'post' ,'url' => array('controller' => 'objetivo_excecao_faturamento','action' => 'incluir')));?>
<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'ObjetivoExcecaoFaturamento' ); ?>
	<?php echo $this->BForm->input('mes', array('type' => 'select', 'options' => $meses, 'class' => 'input-medium', 'label' => 'Mês', 'empty' => 'Selecione o Mês')); ?>
    <?php echo $this->BForm->input('ano', array('type' => 'select', 'default'=> date('Y'),'options' => $anos, 'class' => 'input-medium', 'label' => 'Ano', 'empty' => 'Selecione o Ano')); ?>
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('faturamento_medio', array('type' => 'text','class' => 'input-large numeric moeda', 'label' => 'Faturamento')); ?>
    <?php echo $this->BForm->input('codigo_produto', array('type' => 'select', 'options' => $produtos, 'class' => 'input-large', 'label' => 'Produtos','empty' => 'Selecione o produto')); ?>
</div>
<div class="form-actions">
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
    $(document).ready(function() {
        setup_mascaras();
    });
');
?>