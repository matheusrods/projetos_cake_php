<?php $filtrado = (isset($this->data['TTveiTipoVeiculo']) ? true : false)?>
<div class='well'>
	<div id='filtros'>
		<?php echo $bajax->form('TTveiTipoVeiculo', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TTveiTipoVeiculo', 'element_name' => 'tipos_veiculos'), 'divupdate' => '.form-procurar')) ?>
			<div class='row-fluid inline'>
				<?php echo $this->BForm->input('tvei_descricao', array('class' => 'input-xlarge','label'=>false, 'placeholder' => 'Descrição')) ?>
			</div>
			<div class='row-fluid inline'>
				<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
				<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
			</div>
		<?php echo $this->BForm->end() ?>
	</div>
	
</div>

<?php echo $this->Javascript->codeBlock('

	$(document).ready(function(){
		setup_mascaras();
        '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "tipos_veiculos/listagem/" + Math.random());':'').'
		
		$("#limpar-filtro").click(function(){	
			bloquearDiv($(".form-procurar"));
			$(".form-procurar").load(baseUrl + "/filtros/limpar/model:TTveiTipoVeiculo/element_name:tipos_veiculos/" + Math.random());
		});

	});', false);
?>
