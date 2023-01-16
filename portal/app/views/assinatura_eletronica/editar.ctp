<?php echo $this->BForm->create('AnexoAssinaturaEletronica', array('type' => 'file', 'enctype' => 'multipart/form-data', 'url' => array('controller' => 'assinatura_eletronica', 'action' => 'editar'))); ?>
<?php echo $this->element('assinatura_eletronica/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>