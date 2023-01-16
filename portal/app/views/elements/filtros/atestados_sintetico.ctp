<div class='well'>
	<h5><?= $this->Html->link((!empty($this->data['Atestado']['codigo_cliente']) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
	<div id='filtros'>
		<?php echo $bajax->form('Atestado', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Atestado', 'element_name' => 'atestados_sintetico'), 'divupdate' => '.form-procurar')) ?>
		<?= $this->element('atestados/fields_filtros') ?>
		<div class="row-fluid inline">
	        <span class="label label-info">Agrupar por:</span>
	        <div id='agrupamento'>
	            <?php echo $this->BForm->input('agrupamento', array('type' => 'radio', 'options' => $tipos_agrupamento, 'default' => 5, 'legend' => false, 'label' => array('class' => 'radio inline input-small'))) ?>
	        </div>
	    </div>
	    <div class="row-fluid inline">
	    	<?php echo $this->BForm->input('tipo_atestado', array('options' => array('1' => 'Atestado Saúde', '2' => 'Afastamento Temporário'), 'empty' => 'Todos', 'class' => 'input-medium', 'label' => 'Tipo')); ?>
	    </div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-atestados', 'class' => 'btn')) ;?>
		<?php echo $this->BForm->end() ?>
	</div>
</div>	
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		setup_datepicker(); 
		var div = jQuery(".lista");
		bloquearDiv(div);
		div.load(baseUrl + "atestados/sintetico_listagem/" + Math.random());

		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });

		jQuery("#limpar-filtro-atestados").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Atestado/element_name:atestados_sintetico/" + Math.random())
        });
    });', false);
?>
<?php if (!empty($this->data['Atestado']['codigo_cliente'])): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>