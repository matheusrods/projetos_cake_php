<div class="form-procurar">
	<?php echo $this->BForm->hidden('type', array('value' => $type, 'id' => 'js-type')); ?> 
	<?php echo $this->element('/filtros/questoes_buscar_codigo', array('type' => $type)) ?>
</div>
<div class="lista"></div>

<?php echo $this->Javascript->codeBlock("
	jQuery(document).ready(function(){
	atualizaQuestoes();
		jQuery('#limpar-filtro').click(function(){
			bloquearDiv(jQuery('.form-procurar'));
			jQuery('.form-procurar').load(baseUrl + '/filtros/limpar/model:Questao/element_name:questoes_buscar_codigo/' + Math.random())
		});
	});

		function atualizaQuestoes() {
         var div = jQuery('div.lista');
         bloquearDiv(div);
         div.load(baseUrl + 'questoes/listagem/' + $('#js-type').val() + '/' + Math.random());
     }

	", false);
	?>