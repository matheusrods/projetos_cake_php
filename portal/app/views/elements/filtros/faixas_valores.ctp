<div class='well'>
	<?php echo $this->Bajax->form('TCdfvCriterioFaixaValor', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TCdfvCriterioFaixaValor', 'element_name' => 'faixas_valores'), 'divupdate' => '.form-procurar')) ?>
  		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('cdfv_descricao', array('class' => 'input-large', 'label' => 'Descricao', 'placeholder' => 'Descricao', 'type' => 'text')) ?>
		</div>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
			<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
		</div>
	<?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();		
		atualizaListaFaixasValores();

		$("#limpar-filtro").click(function(){
			bloquearDiv($(".form-procurar"));
			$(".form-procurar").load(baseUrl + "/filtros/limpar/model:TCdfvCriterioFaixaValor/element_name:faixas_valores/" + Math.random())
		});
	});', false);
?>