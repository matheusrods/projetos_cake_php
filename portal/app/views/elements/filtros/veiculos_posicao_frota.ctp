<div class='well'>
	<h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
	    <?php echo $this->Bajax->form('VeiculoPosicaoFrota', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'VeiculoPosicaoFrota', 'element_name' => 'veiculos_posicao_frota'), 'divupdate' => '.form-procurar')) ?>
		<div class="row-fluid inline">
			<?php echo $this->Buonny->input_codigo_cliente_base($this, 'codigo_cliente', 'Cliente', false, 'VeiculoPosicaoFrota') ?>
		</div>
		<div id="div-tipo-alvo" class="row-fluid inline">
			<?php echo $this->element('/filtros/alvos_origem', array('model'=>'VeiculoPosicaoFrota')); ?>
		</div>
	    <div class="row-fluid inline">
	        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
	        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
	        <?php echo $this->BForm->end() ?>
	    </div>
	    <?php echo $this->Javascript->codeBlock('
		    jQuery(document).ready(function(){
		        init_combo_event_alvos_origem("VeiculoPosicaoFrota", "#div-tipo-alvo", "#VeiculoPosicaoFrotaCodigoCliente");
		    });', false);
		?>
	</div>
</div>
<?php if(!empty($filtrado)): ?>
	<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){ function atualizaListaVeiculoPosicaoFrota() {
			var div = jQuery("div.lista");
			bloquearDiv(div);
			div.load(baseUrl + "relatorios_sm/listagem_posicao_frota/" + Math.random());
		} 
		atualizaListaVeiculoPosicaoFrota();
	});', false); ?>
<?php endif; ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	$.placeholder.shim();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:VeiculoPosicaoFrota/element_name:veiculos_posicao_frota/" + Math.random())
            jQuery(".lista").empty();
        });  
		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });
        
        setup_mascaras(); 
    });', false);
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>