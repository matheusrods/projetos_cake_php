<div class='row-fluid inline'>
	<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'Veiculo'); ?>
	<?php echo $this->BForm->input('veic_placa', array('label' => 'Placa','type' => 'text','class' => 'placa-veiculo input-small')) ?>
	<?php echo $this->BForm->input('tip_cliente', array('label' => 'Veiculo do Cliente','type' => 'text','class' => 'input-small')) ?>
	<?php echo $this->BForm->input('tvei_codigo', array('label' => 'Tipo', 'empty' => 'Tipos','class' => 'input-small', 'options' => $veiculos_tipos)) ?>
	<?php echo $this->BForm->input('mvei_codigo', array('label' => 'Fabricante', 'empty' => 'Fabricante','class' => 'input-medium', 'options' => $veiculos_fabricantes)) ?>
	<?php echo $this->BForm->input('mvec_codigo', array('label' => 'Modelo', 'empty' => 'Modelo','class' => 'input-medium', 'options' => $veiculos_modelos)) ?>
	<?php echo $this->BForm->input('tecn_codigo', array('label' => 'Tecnologia', 'empty' => 'Tecnologia','class' => 'input-medium', 'options' => $teconlogia)) ?>
	<?php echo $this->BForm->input('term_numero_terminal', array('label' => 'Terminal','type' => 'text','class' => 'input-small')) ?>
	<?php echo $this->BForm->input('veic_status', array('label' => 'Status', 'empty' => 'Status','class' => 'input-small', 'options' => $veiculos_status)) ?>
	<?php echo $this->BForm->input('tvco_codigo', array('label' => 'Cobrança','class' => 'input-small', 'options' => array('' => 'Cobrança','1' => 'FIXO', '3' => 'TERCEIRO'))) ?>
	<?php echo $this->Buonny->input_referencia($this, '#VeiculoCodigoCliente', 'Veiculo','refe_codigo',FALSE,'Alvo Origem',TRUE) ?>
	<?php echo $this->BForm->input('upos_data_comp_bordo', array('label' => 'Posicionando','class' => 'input-small', 'options' => array('0' => 'Todos','1' => 'SIM', '2' => 'NAO'))) ?>
	<? if ($exibe_fields_checklist):?>
	<?php echo $this->BForm->input('ucve_posicao', array('label' => 'Posição Checklist', 'empty' => 'Posição Checklist','class' => 'input-medium', 'options' => $checklist_posicao)) ?>
	<?php //echo $this->BForm->input('ucve_status', array('label' => 'Checklist', 'empty' => 'Status','class' => 'input-small', 'options' => $checklist_status)) ?>
	<?php //echo $this->BForm->input('ucve_validade', array('label' => 'Validade', 'empty' => 'Todos','class' => 'input-small', 'options' => $checklist_validade)) ?>
	<? endif;?>
	<?php echo $this->BForm->input('dias_sem_viagem', array('label' => 'Sem viagem a mais de','type' => 'text','placeholder' => 'Dias' , 'class' => 'just-number input-medium', 'maxlength' => 5)) ?>

</div>
<? if ($exibe_fields_checklist):?>
<div class='row-fluid inline'>
	<?php echo $this->Buonny->input_validade_checklist($this, $regras_aceite_sm, 'Veiculo','racs_validade_checklist','Regra Aceite SM','Selecione',true) ?>
	<?php echo $this->BForm->input('checklist_dias_validos', array('label' => 'Qtd. Dias Regra','type' => 'text','class' => 'input-small numeric just-number', 'placeholder' => 'dias', 'readonly'=>true)) ?>
</div>
<? endif;?>
<div class='row-fluid inline'>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
</div>

<?php echo $this->addScript($this->Buonny->link_js( array('fichas_scorecard', 'solicitacoes_monitoramento') )) ?>
<?php echo $this->Javascript->codeBlock('
	$("#VeiculoCodigoCliente").blur();
/*	
	jQuery("#VeiculoRacsValidadeChecklist").change(function() {
		var selected = jQuery(this).val();
		var div = jQuery("#VeiculoChecklistDiasValidos").parent();
		if (selected != "") {
			div.show();
		} else {
			div.hide();
		}
	});
	jQuery("#VeiculoRacsValidadeChecklist").change(function() {
		var value = $(this).val();
		if (value != "Selecione") {
			$.ajax({
				url: baseUrl + "regras_aceite_sm/carregar_racs_json/" + value,
				async:false,
				type:"post",
				dataType: "json",
				success: function( data ){					
					$("#VeiculoChecklistDiasValidos").val(data.TRacsRegraAceiteSm.racs_validade_checklist);
				}
			});
		} else {
			$("#VeiculoChecklistDiasValidos").val("");
		}
	});
*/
    jQuery(document).ready(function(){
        setup_mascaras();
        setup_codigo_cliente();
        $("#VeiculoMveiCodigo").change(function(){
        	buscar_t_modelo("#VeiculoMveiCodigo","#VeiculoMvecCodigo");
        });
		jQuery("#VeiculoRacsValidadeChecklist").change();
    });', false);
?>