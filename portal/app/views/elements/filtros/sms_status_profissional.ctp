<div class='well'>
	<h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
	    <?php echo $this->Bajax->form('RelatorioSmTeleconsult', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'RelatorioSmTeleconsult', 'element_name' => 'sms_status_profissional'), 'divupdate' => '.form-procurar')) ?>
    	    <div class="row-fluid inline">
    	    	<?php echo $this->Buonny->input_periodo($this,null,'data_previsao_de','data_previsao_ate','Período') ?>
    			<?php echo $this->Buonny->input_codigo_cliente_base($this,'codigo_cliente','Cliente','Cliente') ?>
            </div>
            <div class="row-fluid inline">
                <?php echo $this->Buonny->input_referencia($this, '#RelatorioSmTeleconsultCodigoCliente', 'RelatorioSmTeleconsult', 'refe_codigo', false, 'Alvo Origem', 'Alvo Origem'); ?>
    		</div>
    	    <div class="row-fluid inline">
    	        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn filtrar')) ?>
    	        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
    	    </div>
        <?php echo $this->BForm->end() ?>
	    <?php echo $this->Javascript->codeBlock('
		    jQuery(document).ready(function(){
	    		//init_combo_event_tipo_alvo("RelatorioSm", "#div-tipo-alvo", "#RelatorioSmCodigoCliente");
		    });', false);
		?>
	</div>
</div>

<?php if(!empty($filtrado)): ?>
	<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){ atualizaListaRelatorioSmStatusProfissional(); });', false); ?>
<?php endif; ?>

<?php echo $this->Javascript->codeBlock('

    jQuery(document).ready(function(){
    	$.placeholder.shim();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:RelatorioSmTeleconsult/element_name:sms_status_profissional/" + Math.random())
            jQuery(".lista").empty();
        });  
		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });
		' . (isset($is_post) && $is_post ? 'jQuery(".btn.filtrar").submit()' : '') . '
		setup_mascaras(); 
    });', false);
?>
<?php if (!empty($filtrado)): ?>
 	<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>