<?php echo $this->BForm->create('AnexoDigitalizacao', array('type' => 'file', 'enctype' => 'multipart/form-data', 'url' => array('controller' => 'TipoDigitalizacao','action' => 'incluir_digitalizacao'))); ?>

<?php echo $this->element('tipo_digitalizacao/fields_digitalizacao_terceiros', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>