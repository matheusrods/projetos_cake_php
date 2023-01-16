<?php echo $this->BForm->create('Cliente', array('url' => array('controller' => 'clientes','action' => 'editar_cliente_gestao_de_risco', $this->passedArgs[0]))); ?>
<?php echo $this->element('clientes/fields_gestao_de_risco', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>

<style>
    h3 {
        text-decoration: none;
    }
</style>

