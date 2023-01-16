<div class='well'>
	<?php echo $bajax->form('FichaPsicossocial', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'FichaPsicossocial', 'element_name' => 'ficha_psicossocial'), 'divupdate' => '.form-procurar')) ?>
	
		<?php echo $this->element('ficha_psicossocial/fields_filtros_ficha_psicossocial'); ?>

		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-ficha_psicossocial', 'class' => 'btn')) ;?>

	<?php echo $this->BForm->end() ?>
</div>	
<?php
	echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		
		var div = jQuery(".lista");

		bloquearDiv(div);

		div.load(baseUrl + "ficha_psicossocial/listagem/" + Math.random());

		jQuery("#limpar-filtro-ficha_psicossocial").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:FichaPsicossocial/element_name:ficha_psicossocial/" + Math.random())
		});		
    });', false);
?>