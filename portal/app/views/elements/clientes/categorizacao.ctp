<div class="row-fluid inline">
    <?php echo $this->BForm->input('codigo_corretora', array('label' => 'Corretora', 'options' => $corretoras, 'empty' => 'Selecione uma opção')); ?>
    <?php echo $this->BForm->input('codigo_corporacao', array('label' => 'Corporação', 'options' => $corporacoes, 'empty' => 'Selecione uma opção')); ?>
</div>