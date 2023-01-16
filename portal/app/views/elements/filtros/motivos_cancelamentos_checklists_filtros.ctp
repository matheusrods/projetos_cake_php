<?php echo $this->BForm->create('TMcchMotivoCancelChecklist', array('url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TMcchMotivoCancelChecklist', 'return' => 'motivos_cancelamentos_checklists'), 'type' => 'POST')) ?>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('mcch_descricao', array('class' => 'input-medium', 'placeholder' => 'Descrição', 'label' => false)) ?>
</div>        
<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
<?php echo $this->BForm->end(); ?>