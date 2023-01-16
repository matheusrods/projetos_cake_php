<div class='row-fluid inline'>
    <?php echo $this->BForm->hidden('codigo'); ?>
    <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true) ?>
    <?php echo $this->BForm->input('tipo_mensagem', array('label' => false, 'label' => 'Tipo Mensagem', 'class' => 'input-large')); ?>
    <?php echo $this->BForm->input('soap_tipo_disparador', array('label' => false, 'label' => 'Tipo Disparador', 'class' => 'input-small','options' => array('E' => 'Eventos', 'T' => 'Tempo'))); ?>
    <?php echo $this->BForm->input('soap_tipo_valor', array('label' => false, 'label' => 'Tipo Valor', 'class' => 'input-small just-number')); ?>
</div>
<div class='row-fluid inline'>
    <?php echo $this->BForm->input('soap_url', array('label' => false, 'label' => 'SOAP URL', 'class' => 'input-xxlarge')); ?>
    <?php echo $this->BForm->input('soap_funcao', array('label' => false, 'label' => 'SOAP Função', 'class' => 'input-medium')); ?>
</div>
<div class='form-actions'>
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>