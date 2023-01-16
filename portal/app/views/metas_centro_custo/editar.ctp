<?php echo $this->BForm->create('MetaCentroCusto',array('url' => array('controller' => 'metas_centro_custo','action' => 'editar', $this->passedArgs[0]), 'type' => 'POST')) ?>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo'); ?>
    <?php echo $this->BForm->input('mes', array('type' => 'select', 'options' => $meses, 'class' => 'input-small', 'label' => 'Mês','empty' => 'Selecione o Mês')); ?>
    <?php echo $this->BForm->input('ano', array('type' => 'select', 'options' => $anos, 'class' => 'input-small', 'label' => 'Ano','empty' => 'Selecione o ano')); ?>
    <?php echo $this->Buonny->input_grupo_empresas($this,$grupos_empresas,$empresas); ?>
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('centro_custo', array('label' => 'Centro de Custo', 'placeholder' => FALSE, 'class' => 'input-large', 'options' => $centro_custo, 'empty' => 'Selecione'));?>
	<?php echo $this->BForm->input('codigo_fluxo', array('label' => 'Fluxo', 'placeholder' => FALSE, 'class' => 'input-large', 'options' => $fluxo, 'empty' => 'Selecione'));?>
	<?php echo $this->BForm->input('codigo_sub_fluxo', array('label' => 'Sub Fluxo', 'placeholder' => FALSE, 'class' => 'input-large', 'options' => $sub_fluxo, 'empty' => 'Selecione'));?>
	<?php echo $this->BForm->input('valor_meta', array('label' => 'Valor da Meta', 'placeholder' => 'Valor', 'type' => 'text', 'class' => 'input-small moeda numeric','default' => '0,00' )); ?>
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