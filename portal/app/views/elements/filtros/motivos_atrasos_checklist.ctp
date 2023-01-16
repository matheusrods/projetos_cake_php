<?php $filtrado = (isset($this->data['TCmatChecklistMotivoAtraso']) ? true : false)?>
<div class='well'>
	<div id='filtros'>
		<?php echo $bajax->form('TCmatChecklistMotivoAtraso', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TCmatChecklistMotivoAtraso', 'element_name' => 'motivos_atrasos_checklist'), 'divupdate' => '.form-procurar')) ?>
			<div class='row-fluid inline'>
				<?php echo $this->BForm->input('cmat_descricao', array('class' => 'input-xlarge','label'=>false, 'placeholder' => 'Descrição')) ?>
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
        '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "motivos_atrasos_checklist/listagem/" + Math.random());':'').'
		
		$("#limpar-filtro").click(function(){	
			bloquearDiv($(".form-procurar"));
			$(".form-procurar").load(baseUrl + "/filtros/limpar/model:TCmatChecklistMotivoAtraso/element_name:motivos_atrasos_checklist/" + Math.random());
		});

	});', false);
?>
