<div class='well'>
    <?php echo $bajax->form('Questao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Questao', 'element_name' => 'questoes_buscar_codigo'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('pergunta', array('class' => 'input-medium', 'placeholder' => 'Pergunta', 'label' => false)) ?>
    </div>        
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock("
	jQuery(document).ready(function(){

		jQuery('#limpar-filtro').click(function(){
			bloquearDiv(jQuery('.form-procurar'));
			jQuery('.form-procurar').load(baseUrl + '/filtros/limpar/model:Questao/element_name:questoes_buscar_codigo/' + Math.random())
		});
	});


	", false);
	?>