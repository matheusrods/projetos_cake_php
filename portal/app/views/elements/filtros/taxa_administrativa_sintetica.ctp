<div class='well'>
	<?php echo $this->Bajax->form('ItemPedido', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ItemPedido', 'element_name' => 'taxa_administrativa_sintetica'), 'divupdate' => '.form-procurar')) ?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('ano_faturamento', array('options' => $ano_faturamento, 'class' => 'input-small', 'label' => false, 'default' => date('Y'))); ?>
	</div>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end();?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	atualizaListaTaxaAdministrativaSintetico();
		jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ItemPedido/element_name:taxa_administrativa_sintetica/" + Math.random())
        });
    });', false);
?>