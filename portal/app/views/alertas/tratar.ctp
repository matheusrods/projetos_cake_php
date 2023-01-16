<?php echo $this->Buonny->flash(); ?>
<?php echo $this->Bajax->form('Alerta', array('autocomplete' => 'off', 'url' => array('controller' => 'alertas', 'action' => 'tratar'), 'callback'=>'close_dialog_alerta')) ?>
	<?php echo $this->BForm->input('codigo', array('type' => 'hidden')) ?>
	<?php echo $this->BForm->input('descricao', array('class' => 'input-xxlarge', 'placeholder' => 'Observação', 'label' => 'Descrição', 'type' => 'text', 'readonly'=>'readonly')) ?>
	<?php echo $this->BForm->input('observacao_tratamento', array('class' => 'input-xxlarge', 'placeholder' => 'Observação do tratamento', 'label' => 'Observação do tratamento', 'type' => 'textarea')) ?>
<div class='form-actions'>
    <?php echo $this->BForm->submit('Finalizar alerta', array('div' => false, 'class' => 'btn btn-success')); ?>
    <?php echo $html->link('Deixar de tratar', 'javascript:parar_de_tratar(' . $this->BForm->value('codigo') . ')', array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>