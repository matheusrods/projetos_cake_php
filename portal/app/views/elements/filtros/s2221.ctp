<div class='well'>
	<?php echo $bajax->form('Esocial', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Esocial', 'element_name' => 's2221'), 'divupdate' => '.form-procurar')) ?>
	<?= $this->element('esocial/fields_filtros_funcionarios') ?>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-s2221', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end() ?>
</div>	
<?php 

echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	
		setup_datepicker(); 
		var div = jQuery(".lista");

		bloquearDiv(div);

		div.load(baseUrl + "esocial/s2221_listagem/" + Math.random());

		jQuery("#limpar-filtro-s2221").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Esocial/element_name:s2221/" + Math.random())            
        });        
        
    });', false);
?>