<?php echo $this->BForm->create('TipoRetorno', array('type' => 'post' ,'url' => array('controller' => 'tipos_retornos','action' => 'editar')));?>
  <?php echo $this->BForm->hidden('codigo') ?>

  <div class='row-fluid inline means'>
    <?php echo $this->BForm->input('descricao',array('class' => 'input-xlarge', 'label' => 'Descrição' )) ?>
  </div>
  <div class='row-fluid inline parent'>
    <?php echo $this->BForm->label('proprietario', $this->BForm->checkbox('proprietario').'Proprietario', array('class' => 'checkbox inline input-xlarge', 'escape'=>false)); ?> 
    <?php echo $this->BForm->label('profissional', $this->BForm->checkbox('profissional').'Profissional', array('class' => 'checkbox inline input-xlarge', 'escape'=>false)); ?> 
    <?php echo $this->BForm->label('usuario_interno', $this->BForm->checkbox('usuario_interno').'Usuario Interno', array('class' => 'checkbox inline input-xlarge', 'escape'=>false)); ?> 
  </div> 
  <div class="form-actions">
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?php echo $html->link('Voltar',array('controller' => 'tipos_retornos', 'action' => 'index'), array('class' => 'btn')) ;?>
  </div>

<?php echo $this->BForm->end() ?>

<?php echo $this->Javascript->codeBlock('
  $(document).ready(function() {
    setup_mascaras();
  });
');
?>