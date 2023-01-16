<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');

		echo $this->Javascript->codeBlock("
    	alert('aqui');
        		close_dialog();
        		//carrega_fornecedor_horario('".$this->passedArgs[0]."')      
        ");
        exit;
    }
?>
<div class='row-fluid inline'>
	<?php echo $this->Buonny->input_codigo_medico($this, 'codigo_medico', 'Código','Código','FornecedorMedico');?>
</div>
<div class="row-fluid inline">

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

function salvarDados(){
	alert('33');
	//$('#FornecedorHorarioIncluirForm').submit();
}
    ");?>