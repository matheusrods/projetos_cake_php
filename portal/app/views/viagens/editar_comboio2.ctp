<div class="well">
    <?php echo $this->BForm->create('TViagViagem', array('autocomplete' => 'off', 'url' => array('controller' => 'viagens', 'action' => 'editar_comboio2'))) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', FALSE, 'TViagViagem' ); ?>
            <?php echo $this->BForm->input('viag_codigo_sm',array('label' => false, 'class' => 'viag_codigo_sm just-number', 'placeholder'=>'SM')) ?>
        </div>
        <?php echo $this->BForm->submit('Avançar', array('div' => false, 'class' => 'btn')); ?>
    <?php echo $this->BForm->end();?>
</div>