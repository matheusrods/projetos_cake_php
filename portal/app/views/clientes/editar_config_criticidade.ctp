<?php echo $this->BForm->create('Cliente', array('url' =>
    array('controller' => 'clientes','action' => 'editar_config_criticidade', $this->passedArgs[0], $this->passedArgs[1]), 'type' => 'put')); ?>
<?php echo $this->element('clientes/fields_editar_config_criticidade', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>

<style>
    h3 {
        text-decoration: none;
    }
</style>
