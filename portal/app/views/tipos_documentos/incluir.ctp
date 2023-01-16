<?php echo $this->BForm->create('TiposDocumentos', array('type' => 'file', 'action' => 'incluir')); ?>

<?php echo $this->BForm->input('TipoDocumento.descricao', array('class' => 'input-xlarge', 'label' => 'Descrição do Documento')); ?>
<?php echo $this->BForm->input('TipoDocumento.obrigatorio', array('label' => false, 'class' => 'input-xlarge', 'default' => 1,'options' => array(1 => 'Sim',0=> 'Não'), 'label' => 'É Obrigatório?')); ?>

<div class='form-actions'>
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
</div>
<?php echo $this->BForm->end(); ?>