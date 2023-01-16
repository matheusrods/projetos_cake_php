<div class='well'>
	<?php echo $this->Bajax->form('Regioes', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Regioes', 'element_name' => 'regioes'), 'divupdate' => '.form-procurar')) ?>
  		<div class='row-fluid inline'>
			<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true,'Regioes') ?>
			<?php echo $this->BForm->input('descricao', array('class' => 'input-large', 'label' => 'Descricao', 'placeholder' => 'Descricao', 'type' => 'text')) ?>
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
		atualizaListaRegioes();

		$("#limpar-filtro").click(function(){
			bloquearDiv($(".form-procurar"));
			$(".form-procurar").load(baseUrl + "/filtros/limpar/model:Regioes/element_name:regioes/" + Math.random())
		});
	});', false);
?>