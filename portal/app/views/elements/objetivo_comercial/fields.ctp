<div class="row-fluid inline">
	<?php echo $this->BForm->input('mes', array('type' => 'select', 'options' => $meses, 'class' => 'input-medium', 'label' => 'Mês', 'empty' => 'Selecione o Mês')); ?>
    <?php echo $this->BForm->input('ano', array('type' => 'select', 'default'=> date('Y'),'options' => $anos, 'class' => 'input-medium', 'label' => 'Ano', 'empty' => 'Selecione o Ano')); ?>
    <?php echo $this->BForm->input('codigo_endereco_regiao', array('type' => 'select', 'options' => $filiais, 'class' => 'input-large', 'label' => 'Filial','empty' => 'Selecione a filial')); ?>
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('codigo_gestor', array('type' => 'select', 'options' => $gestores, 'class' => 'input-large', 'label' => 'Gestor','empty' => 'Selecione o getor')); ?>
    <?php echo $this->BForm->input('codigo_produto', array('type' => 'select', 'options' => $produtos, 'class' => 'input-large', 'label' => 'Produtos','empty' => 'Selecione o produto')); ?>
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('visitas_objetivo', array('type' => 'text','class' => 'input-large numeric just-number', 'label' => 'Visitas')); ?>
    <?php echo $this->BForm->input('faturamento_objetivo', array('type' => 'text','class' => 'input-large numeric moeda', 'label' => 'Faturamento')); ?>
    <?php echo $this->BForm->input('novos_clientes_objetivo', array('type' => 'text','class' => 'input-large numeric just-number', 'label' => 'Novos Clientes')); ?>
</div>
<div class="form-actions">
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->Javascript->codeBlock('
    $(document).ready(function() {
        setup_mascaras();
    });
');
?>