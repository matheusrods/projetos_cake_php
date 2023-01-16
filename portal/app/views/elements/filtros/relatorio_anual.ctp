<div class='well'>
	<h5><?= $this->Html->link((!empty($this->data['Exame']['codigo_cliente']) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
	<div id='filtros'>
		<?php echo $bajax->form('Exame', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Exame', 'element_name' => 'relatorio_anual'), 'divupdate' => '.form-procurar')) ?>
		<?= $this->element('exames/relatorio_anual_fields_filtros') ?>

		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-posicao', 'class' => 'btn')) ;?>

		<?php echo $this->BForm->end() ?>
	</div>
</div>	
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		setup_datepicker(); 
		var div = jQuery(".lista");
		bloquearDiv(div);
		div.load(baseUrl + "exames/relatorio_anual_listagem/" + Math.random());

		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });

		jQuery("#limpar-filtro-posicao").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Exame/element_name:relatorio_anual/" + Math.random())
        });
        
    });', false);
?>
<?php if (!empty($this->data['Exame']['codigo_cliente'])): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>

 