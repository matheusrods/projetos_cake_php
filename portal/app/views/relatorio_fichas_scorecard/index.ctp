<?php
$codigo_cliente = null;
$razao_social   = null;
$cnpj           = null;
if(isset($authUsuario['Usuario']['codigo_cliente']) && !empty($authUsuario['Usuario']['codigo_cliente']) && $authUsuario['Usuario']['codigo_cliente'] != '' ) {
	$codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];
	$razao_social   = $authUsuario['Usuario']['nome'];
	$cnpj           = $authUsuario['Usuario']['codigo_documento'];
}
?>
<div class='form-procurar'>	
    <div class='well'>
	    <?php echo $this->BForm->create('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'RelatorioFichasScorecard', 'action' => 'gera_demonstrativo'))) ?>
	    <div class="row-fluid inline">
		 	<?php echo empty($authUsuario['Usuario']['codigo_cliente']) ? $this->Buonny->input_codigo_cliente($this) : $this->BForm->input('codigo', array('type' => 'hidden','value'=>$authUsuario['Usuario']['codigo_cliente']))  ?>
		 	<?php echo $this->BForm->input('data_inicial', array('id'=>'data_inicial','class' => 'input-small data','placeholder' =>'Data Inicial' , 'label' => false)); ?>
			<?php echo $this->BForm->input('data_final', array('id'=>'data_final','class' => 'input-small data','placeholder' =>'Data Final' , 'label' => false)); ?>			
	    </div>
	    <?php echo $this->BForm->submit('Gerar', array('id'=>'btn-enviar','div' => false, 'class' => 'btn')); ?>
	    <?php echo $this->BForm->end();?>
	</div>
	<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){ setup_datepicker(); });', false); ?>
</div>
<script>

$("#btn-enviar").click(function(){
	var erros = '';
	if(new Date($('#data_final').val()) < new Date($("#data_inicial").val())) {
		erros += "Data Final é maior que Data Inicial\n";
	}
	if(($('#data_final').val() == '' || $('#data_final').val() == undefined) || ($('#data_inicial').val() == undefined || $('#data_inicial').val() == '')) {
		erros += "Data Final e Data Inicial são obrigatórios\n";
	}
	// if($("#ClienteCodigoCliente").val() == '' || $("#ClienteCodigoCliente").val() == undefined) {
	// 	erros += "Cliente é obrigatório!\n";
	// }	
	if(erros != '') {
		alert(erros);
		return false;
	} else {
		return true;
	}
});
</script>
<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>
