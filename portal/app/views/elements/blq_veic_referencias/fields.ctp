<div class="well">
	<div class='row-fluid inline'>
		<?php if($readonly): ?>
			<?php echo $this->BForm->hidden('codigo_cliente',Array('value'=>$cliente['Cliente']['codigo'])) ?>
			<?php echo $this->BForm->input('codigo_cliente_visual',array('readonly'=>true,'label'=>'Cliente','value'=>$cliente['Cliente']['razao_social'])) ?>
		<?php else: ?>
			<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true,'TBvreBlqVeicReferencia',(isset($cliente['Cliente']['codigo']) ? $cliente['Cliente']['codigo'] : '') ) ?>
		<?php endif; ?>
		<?php echo $this->BForm->hidden('bvre_veic_oras_codigo') ?>
		<?php echo $this->BForm->input('TVeicVeiculo.veic_placa', array('class' => 'input-small placa-veiculo', 'label' => 'Placa Veículo', 'placeholder' => 'Placa','onkeyup'=>(!$readonly ? "consulta_placa()" : "return false;"),'after'=>'<img src="/portal/img/loading.gif" id="imgCarregarPlaca" style="display: none" />')) ?>
		
	</div>
	<div class='row-fluid inline'>
		<?php if($readonly): ?>
			<?php echo $this->BForm->hidden('bvre_refe_codigo') ?>
			<?php echo $this->BForm->input('bvre_refe_codigo_visual',array('readonly'=>true,'label'=>'Alvo','value'=>$this->data['TRefeReferencia']['refe_descricao'])) ?>
		<?php else: ?>
			<?php echo $this->Buonny->input_referencia($this, '#TBvreBlqVeicReferenciaCodigoCliente', 'TBvreBlqVeicReferencia', 'bvre_refe_codigo', false, 'Alvo', 'Alvo'); ?>
		<?php endif; ?>
	</div>
</div>

<?php echo $this->Javascript->codeBlock('

	function consulta_placa() {

	    var placa = $("#TVeicVeiculoVeicPlaca").val();
	    if (placa.length > 0 && placa.indexOf(\'_\') < 0) {
    	    $.ajax({
    	        url: baseUrl + \'veiculos/dados_por_placa/placa:\' + placa + \'/\' + Math.random(),
    	        dataType: \'json\',
    	        beforeSend: function() {
    	        	$("#imgCarregarPlaca").show();
    	        },
    	        success: function(data) {
    	        	$("#imgCarregarPlaca").hide();
    	            if (data){
    	            	$("#TBvreBlqVeicReferenciaBvreVeicOrasCodigo").val(data.TVeicVeiculo.veic_oras_codigo);
	                	$("#TVeicVeiculoVeicPlaca").removeClass("form-error").parent().removeClass("error").find("#lbl-error").remove();
        	        } else {
		                $("#TVeicVeiculoVeicPlaca").removeClass("form-error").parent().removeClass("error").find("#lbl-error").remove();                
		                $("#TVeicVeiculoVeicPlaca").addClass("form-error").parent().addClass("error").append("<div id=\"lbl-error\" class=\"help-block error-message\">Veículo não encontrado</div>");
        	        }
                },
                error: function() {
    	        	$("#imgCarregarPlaca").hide();
	                $("#TVeicVeiculoVeicPlaca").removeClass("form-error").parent().removeClass("error").find("#lbl-error").remove();                
	                $("#TVeicVeiculoVeicPlaca").addClass("form-error").parent().addClass("error").append("<div id=\"lbl-error\" class=\"help-block error-message\">Veículo não encontrado</div>");
                }
    	    });
    	}	    
	}', false);
?>