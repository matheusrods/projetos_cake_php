<div class='well'>
	<h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
	<div id='filtros'>
		<?php echo $bajax->form('ChecklistViagem', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ChecklistViagem', 'element_name' => 'checklist_viagem_analitico'), 'divupdate' => '.form-procurar')) ?>
			<?php echo $this->BForm->hidden('refe_codigo') ?>
			<?php echo $this->BForm->hidden('tipo_checklist') ?>
			<?= $this->element('filtros/checklist_viagem') ?>
			<div class='row-fluid inline'>
				<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
				<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
			</div>
		<?php echo $this->BForm->end() ?>
	</div>
	
</div>
<?php if(!empty($filtrado)): ?>
	<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){ checklistViagemAnalitico(); });', false); ?>
<?php endif; ?>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();

		$("#limpar-filtro").click(function(){	
			bloquearDiv($(".form-procurar"));
			$(".form-procurar").load(baseUrl + "/filtros/limpar/model:ChecklistViagem/element_name:checklist_viagem_analitico/" + Math.random());
		});

		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });	
	
	});', false);
?>
<?php if (!empty($filtrado)): ?>
 	<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>