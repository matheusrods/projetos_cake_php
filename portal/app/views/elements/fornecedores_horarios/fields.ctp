<div class='row-fluid inline'>
 	<?php echo $this->BForm->hidden('FornecedorHorario.codigo_fornecedor', array('value' => $codigo_fornecedor)); ?>

 	<div class="row-fluid inline">
	    <?php echo $this->BForm->input('FornecedorHorario.de_hora', array('class' => 'hora input-small', 'label' => 'De:', 'type' => 'text')); ?>
	    
	    <?php echo $this->BForm->input('FornecedorHorario.ate_hora', array('class' => 'hora input-small', 'label' => 'Hora:', 'type' => 'text')); ?>
</div>
<div class="row-fluid inline">
	<label>Dias</label>
<?php 
    $dias_semana = array('seg' =>'Segunda-Feira', 'ter' =>'Terça-Feira', 'qua'=>'Quarta-Feira', 'qui'=>'Quinta-Feira', 'sex'=>'Sexta-Feira', 'sab'=>'Sábado','dom'=>'Domingo');
    ?>

    <?php echo $this->BForm->input('FornecedorHorario.dias_semana', array('legend' => false, 'options' => $dias_semana, 'multiple'=>'checkbox','before' => '<div class="fornecedor_radio_checkbox" style="width:550px;">','after' => '</div>', 'label' => false)) ?>
</div>

<div class='form-actions'>
	 <?php //echo $html->link('Salvar', 'javascript:void(0)', array('div' => false, 'class' => 'btn btn-primary', 'onclick' => 'salvarDados();')); ?>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?php echo $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div> 
<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
	    setup_time();
	    setup_mascaras();
	});
    ");?>