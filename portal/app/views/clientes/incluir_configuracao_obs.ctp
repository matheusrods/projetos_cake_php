<?php echo $this->BForm->create('Cliente', array('url' => array('controller' => 'clientes','action' => 'incluir_configuracao_obs', $this->passedArgs[0]))); ?>
<?php echo $this->element('clientes/fields_configuracao_obs', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>

<style>
    h3 {
        text-decoration: none;
    }
</style>
