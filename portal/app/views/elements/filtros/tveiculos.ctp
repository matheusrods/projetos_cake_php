<div class="well">
	<h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
	<div id="filtros">
		<?php echo $this->Bajax->form('Tveiculos', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Tveiculos', 'element_name' => 'tveiculos'), 'divupdate' => '.form-procurar')) ?>
		<div class="row-fluid inline">
			<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente','Tveiculos') ?>
			<?php echo $this->Buonny->input_periodo($this, 'Tveiculos','data_inicial', 'data_final', true) ?>
			<?php echo $this->BForm->input('chassi', array('class' => 'input-medium chassi','label' => 'Chassi' )); ?>
			<?php echo $this->BForm->input('local', array('class' => 'input-medium local','label' => 'Local da Vistoria' )); ?>		
		</div>
		<div class="row-fluid inline">
			<span class="label label-info">Agrupar por:</span>
	        <div id='agrupamento'>
				<?php echo $this->BForm->input('agrupamento', array('type' => 'radio', 'options' => $agrupamento, 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline', 'style' => 'width:110px;'))) ?>
			</div>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn', 'id'=>'filtrar')); ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
		<?php echo $this->BForm->end();?>
	</div>
</div>

<?php 
	if(!empty($filtrado)): 
		echo $this->Javascript->codeBlock('
			function atualizaListaTveiculos() {
				var div = jQuery("div.lista");
				bloquearDiv(div);	
				div.load(baseUrl + "tveiculos/listagem/" + Math.random());
			}
			jQuery(document).ready(function(){ atualizaListaTveiculos(); });', false); 
	endif; 
?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){     	
    	setup_mascaras();  		
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Tveiculos/element_name:tveiculos/" + Math.random())
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
<?php 
	if (isset($filtrado) && $filtrado):
    	echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');
    endif; ?>