<?php echo $this->BForm->create('AplicacaoExame', array('url' => Comum::splitURL( $_SERVER['REQUEST_URI'] ) ) ); ?>
<?php echo $this->element('aplicacao_exames/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>