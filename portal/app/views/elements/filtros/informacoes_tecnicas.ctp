<?php if(empty($authUsuario['Usuario']['codigo_cliente'])):?>
<div class='well'>
	<?php echo $bajax->form('ITecnicas', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ITecnicas', 'element_name' => 'informacoes_tecnicas'), 'divupdate' => '.form-procurar')) ?>
	<div class='row-fluid inline'>
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false, 'ITecnicas') ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	</div>
	<?php echo $this->BForm->end() ?>
</div>
<?php endif; ?>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();
		atualizaInformacoesTecnicas();

		$("#limpar-filtro").click(function(){
			bloquearDiv($(".form-procurar"));
			$(".form-procurar").load(baseUrl + "/filtros/limpar/model:ITecnicas/element_name:informacoes_tecnicas/" + Math.random())
		});
	});', false);
?>