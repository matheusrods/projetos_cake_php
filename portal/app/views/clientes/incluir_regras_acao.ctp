<?php echo $this->BForm->create('Cliente', array('url' => array('controller' => 'clientes','action' => 'incluir_regras_acao', $this->passedArgs[0]))); ?>
<?php echo $this->element('clientes/fields_regras_acao', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>

<style>
    h3 {
        text-decoration: none;
    }
</style>
