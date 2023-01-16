<?php echo $this->BForm->create('Crmb', array('url' => array('controller' => 'clientes_responsaveis_monitoracao_biologicas', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('clientes_responsaveis_monitoracao_biologicas/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>