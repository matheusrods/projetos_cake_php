<?php echo $this->Bajax->form('MetaCentroCusto', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'MetaCentroCusto', 'element_name' => 'metas_centro_custo'), 'divupdate' => '.form-procurar')) ?>
	<div class="row-fluid inline">
	    <?php echo $this->BForm->input('mes', array('type' => 'select', 'options' => $meses, 'class' => 'input-small', 'label' => false,'empty' => 'Selecione o Mês')); ?>
	    <?php echo $this->BForm->input('ano', array('type' => 'select', 'options' => $anos, 'class' => 'input-small', 'label' => false,'empty' => 'Selecione o ano')); ?>
	    <?php echo $this->Buonny->input_grupo_empresas($this,$grupos_empresas,$empresas); ?>
	    
	</div>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('centro_custo', array('label' => false, 'placeholder' => FALSE, 'class' => 'input-large', 'options' => $centro_custo, 'empty' => 'Selecione um Centro de Custo'));?>
		<?php echo $this->BForm->input('codigo_fluxo', array('label' => false, 'placeholder' => FALSE, 'class' => 'input-large', 'options' => $fluxo, 'empty' => 'Selecione um Fluxo'));?>
		<?php echo $this->BForm->input('codigo_sub_fluxo', array('label' => false, 'placeholder' => FALSE, 'class' => 'input-large', 'options' => $sub_fluxo, 'empty' => 'Selecione um Sub Fluxo'));?>
	</div>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
<?php echo $this->BForm->end();?>

<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function() {
	var div = jQuery("div.lista");
	bloquearDiv(div);
	div.load(baseUrl + "/metas_centro_custo/listagem/" + Math.random());	
	jQuery("#limpar-filtro").click(function(){
		bloquearDiv(jQuery(".form-procurar"));
		jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:MetaCentroCusto/element_name:metas_centro_custo/" + Math.random())
	});
})') ?>