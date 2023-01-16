<div class="well">
<?php echo $this->BForm->create('TRefeReferencia', array('autocomplete' => 'off', 'url' => array('controller' => 'referencias', 'action' => 'historico_alvo_veiculos'))) ?>

	<div class="row-fluid inline">
		<?php echo $this->BForm->input('data_inicial', array('class' => 'input-small data', 'label' => false, 'placeholder' => 'Data', 'type' => 'text')) ?>
		<?php echo $this->Buonny->input_codigo_cliente_base($this) ?>
	</div>
	<div class="row-fluid inline">
		<?php echo $this->Buonny->input_referencia($this, '#TRefeReferenciaCodigoCliente', 'TRefeReferencia') ?>
	</div>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
<?php echo $this->BForm->end();?>
</div>
<?php echo $this->Javascript->codeBlock("$(document).ready(function(){setup_datepicker()});") ?>
<div class='lista'></div>

<?php echo $this->Javascript->codeBlock("$(function() 	{	
	var conteiner = $('div.lista');
	
	$.ajax({
        url: baseUrl + 'referencias/historico_alvo_veiculos_listagem/' + Math.random(),
        cache: false, 
        beforeSend : function(){ bloquearDiv(conteiner); },
        success : function(data){ conteiner.html(data); }  
    });
})") ?>