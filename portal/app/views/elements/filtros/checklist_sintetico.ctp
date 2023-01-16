<div class='well'>
	<h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
	    <?php echo $this->Bajax->form('TCveiChecklistVeiculo', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TCveiChecklistVeiculo', 'element_name' => 'checklist_sintetico'), 'divupdate' => '.form-procurar')) ?>
	        <?= $this->element('checklists/fields_filtros') ?>
			<div class="row-fluid inline">
                <span class="label label-info">Agrupar por:</span>
                <div id='agrupamento'>
                    <?php echo $this->BForm->input('agrupamento', array('type' => 'radio', 'options' => $agrupamentos, 'default' => 'operador', 'legend' => false, 'label' => array('class' => 'radio inline input-medium'))) ?>
                </div>
            </div>
		    <div class="row-fluid inline">
		        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
		        <?php echo $this->BForm->end() ?>
		    </div>
		<?php echo $this->BForm->end() ?>
	</div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	$.placeholder.shim();
        setup_mascaras(); 
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "checklists/sintetico_listagem/" + Math.random());

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TCveiChecklistVeiculo/element_name:checklist_sintetico/" + Math.random())
            jQuery(".lista").empty();
        });  

        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });

        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });

    });', false);
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php else: ?>    
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").show()})');?> 
 <?php endif; ?>