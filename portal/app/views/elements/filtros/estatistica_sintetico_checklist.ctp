<div class='well'>
	<h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
	    <?php echo $this->Bajax->form('TCveiChecklistVeiculo', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TCveiChecklistVeiculo', 'element_name' => 'estatistica_sintetico_checklist'), 'divupdate' => '.form-procurar')) ?>
	        <div class="row-fluid inline">
    			<?php echo $this->BForm->hidden('tipo', array('value'=>'sintetico')); ?>
    			<?php echo $this->Buonny->input_periodo($this,'TCveiChecklistVeiculo') ?>
    			<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', False,'TCveiChecklistVeiculo') ?>
    			<?php echo $this->BForm->input('placa', array('class' => 'placa-veiculo input-mini', 'placeholder' => 'Placa', 'label' => false)); ?>
    		</div>
			<div class="row-fluid inline">
				<span class="label label-info">Agrupar por:</span>
	            <div id='agrupamento'>
					<?php echo $this->BForm->input('agrupamento', array('type' => 'radio', 'options' => array('operador' =>'Operador','transportador'=>'Transportador', /* 'placa'=>'Placa',*/'data'=>'Data','CD'=>'CD','Aprovado/Reprovado'=>'Aprovado/Reprovado'), 'default' => 'operador', 'legend' => false, 'label' => array('class' => 'radio inline input-medium'))) ?>
				</div>
			</div>
			
	    <div class="row-fluid inline">
	        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
	        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
	        <?php echo $this->BForm->end() ?>
	    </div>
	    <?php echo $this->Javascript->codeBlock('
		    jQuery(document).ready(function(){
	    		init_combo_event_tipo_alvo("RelatorioSm", "#div-tipo-alvo", "#RelatorioSmCodigoCliente");
		    });', false);
		?>
	</div>
</div>

	<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){ atualizaListaEstatisticaSinteticoChecklist(); });', false); ?>


<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	setup_datepicker();
    	$.placeholder.shim();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TCveiChecklistVeiculo/element_name:estatistica_sintetico_checklist/" + Math.random())
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
<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>