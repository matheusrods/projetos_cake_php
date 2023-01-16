<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('id'); ?>
    <?php echo $this->BForm->hidden('objeto_id'); ?>
    <?php echo $this->BForm->input('aco_string', array('class' => 'input-xlarge', 'label' => 'Aco String')); ?>
    <?php echo $this->BForm->input('codigo_tarefa_desenvolvimento', array('class' => 'input-large', 'label' => 'Controle Tarefa','empty' => 'Selecionar Tarefa' ,'options' => $tarefas)); ?>
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index', $this->data['DependenciaObjAcl']['objeto_id']), array('class' => 'btn')); ?>
</div>    
<?php echo $this->BForm->end(); ?>