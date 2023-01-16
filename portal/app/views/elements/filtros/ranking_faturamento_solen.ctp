<div class='well'>
	<?php echo $this->Bajax->form('Notaite', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Notaite', 'element_name' => 'ranking_faturamento2'), 'divupdate' => '.form-procurar')) ?>
	<?php echo $this->element('itens_notas_fiscais/fields_filtros_solen', array('mes_ano' => true, 'grupo_empresas' => in_array($this->data['Notaite']['agrupamento'], array(Notaite::AGRP_CLIENTES, Notaite::AGRP_PRODUTOS)) )); ?>
    <?php echo $this->BForm->hidden('agrupamento') ?>
    <?php echo $this->BForm->hidden('level') ?>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $this->BForm->end();?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	var div = jQuery("div.lista");
		bloquearDiv(div);
		div.load(baseUrl + "itens_notas_fiscais/ranking_faturamento_listagem_solen/" + Math.random());
    });', false);
?>