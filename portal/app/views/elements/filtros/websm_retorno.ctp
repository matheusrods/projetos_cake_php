<?php if (empty($user)): ?>
<div class='well'>
	<?php echo $this->Bajax->form('WebsmRetorno', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'WebsmRetorno', 'element_name' => 'websm_retorno'), 'divupdate' => '.form-procurar')) ?>
  		<div class='row-fluid inline'>
			
			<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true,'WebsmRetorno') ?>

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
		atualizaListaWebsmRetorno();
		
		$("#limpar-filtro").click(function(){
			bloquearDiv($(".form-procurar"));
			$(".form-procurar").load(baseUrl + "/filtros/limpar/model:WebsmRetorno/element_name:websm_retorno/" + Math.random())
		});
		
	});', false);
?>