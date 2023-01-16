<div class = 'well form-procurar'>
	<?= $this->element('/filtros/metas_centro_custo') ?>
</div>
<div class='actionbar-right'>
	<p>
		<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success'));?>
	</p>
</div>
<?php echo $javascript->codeblock("jQuery(document).ready(function() {
	setup_mascaras();	
	$(document).on('change','#MetaCentroCustoCodigoFluxo',function(){
		if( $(this).val() ){
			carrega_sub_fluxo( $('#MetaCentroCustoCentroCusto').val(), $(this).val() );
		}
	});
	
	$(document).on('change','#MetaCentroCustoCentroCusto',function(){
		if( $(this).val() ){
			carrega_sub_fluxo( $(this).val(), $('#MetaCentroCustoCodigoFluxo').val() );
		}
	});
	
	function carrega_sub_fluxo( centro_custo, codigo_sub_fluxo ){
		var sub_fluxo = $('#MetaCentroCustoCodigoSubFluxo');
		if( centro_custo && codigo_sub_fluxo ){
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
<div class='lista'></div>