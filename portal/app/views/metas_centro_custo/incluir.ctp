<?php echo $this->BForm->create('MetaCentroCusto',array('url' => array('controller' => 'metas_centro_custo','action' => 'incluir'), 'type' => 'POST')) ?>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('mes', array('type' => 'select', 'options' => $meses, 'class' => 'input-small', 'label' => false,'empty' => 'Mês')); ?>
    <?php echo $this->BForm->input('ano', array('type' => 'select', 'options' => $anos, 'class' => 'input-small', 'label' => false,'empty' => 'Ano')); ?>
    <?php echo $this->Buonny->input_grupo_empresas($this,$grupos_empresas,$empresas); ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('centro_custo', array('label' => false, 'placeholder' => FALSE, 'class' => 'input-large', 'options' => $centro_custo, 'empty' => 'Selecione um Centro de Custo'));?>
	<?php echo $this->BForm->input('codigo_fluxo', array('label' => false, 'placeholder' => FALSE, 'class' => 'input-large', 'options' => $fluxo, 'empty' => 'Selecione um Fluxo'));?>
	<?php echo $this->BForm->input('codigo_sub_fluxo', array('label' => false, 'placeholder' => FALSE, 'class' => 'input-large', 'options' => $sub_fluxo, 'empty' => 'Selecione um Sub Fluxo'));?>
	<?php echo $this->BForm->input('valor_meta', array('label' => false, 'class' => 'input-small numeric moeda', 'placeholder' => 'Valor Meta')); ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('repetir_meta', array('label' => 'Repetir Meta', 'type' => 'checkbox', 'label' => 'Deseja Repetir a Meta para outros mêses?' )) ?>	
</div>
<div id="repetir_meta" style="display:<?=(!empty($this->data['MetaCentroCusto']['repetir_meta'])? '':'none' )?>">
	<?php echo $this->BForm->input('quantidade_repetir_meta', array('class' => 'input-mini just-number', 'placeholder' => FALSE, 'label' => 'Quantidade de Mêses', 'type' => 'text', 'maxlength'=> 2)) ?>
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>    
<?php echo $this->BForm->end(); ?>
<?php echo $javascript->codeblock("jQuery(document).ready(function() {
	setup_mascaras();	
	
	$(document).on('change','#MetaCentroCustoCodigoFluxo',function(){
		if( $(this).val() ){
			carrega_sub_fluxo( $(this).val() );
		}
	});
	
	$(document).on('click','#MetaCentroCustoRepetirMeta',function(){
		if( $(this).is(':checked') ){
			$('#repetir_meta').show();
		} else {
			$('#repetir_meta').hide();
		}    	
	});

	function carrega_sub_fluxo( codigo_sub_fluxo ){
		var sub_fluxo = $('#MetaCentroCustoCodigoSubFluxo');
		if( codigo_sub_fluxo ){
			sub_fluxo.html('<option value=\'\'>Aguarde...</option>');
			$.ajax({
		        'url': baseUrl + 'metas_centro_custo/carrega_sub_fluxo/' + codigo_sub_fluxo + '/' + Math.random(),
		        dataType: 'json',
		        'success': function(data) {
		            sub_fluxo.html(data.html);
		    	}
			});
		}
	}
});"); ?>