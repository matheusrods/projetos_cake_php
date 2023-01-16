<?php echo $this->BForm->create('Cliente', array('url' => array('controller' => 'clientes','action' => 'incluir_config_criticidade', $this->passedArgs[0]), 'type' => 'post')); ?>
<?php echo $this->element('clientes/fields_config_criticidade'); ?>
<?php echo $this->BForm->end(); ?>

<style>
    h3 {
        text-decoration: none;
    }
</style>
