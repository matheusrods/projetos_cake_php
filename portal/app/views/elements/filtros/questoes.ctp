<div class='well'>
	<div id='filtros'>
	<?php echo $bajax->form('Questao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Questao', 'element_name' => 'questoes'), 'divupdate' => '.form-procurar')) ?>

		<div class="row-fluid inline">
			<?php echo $this->BForm->input('codigo', array('class' => 'input-mini just-number', 'label' => 'Código', 'type' => 'text')) ?>
			<?php echo $this->BForm->input('descricao', array('class' => 'input-large', 'label' => 'Descrição', 'type' => 'text')) ?>
		</div>     

		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
		<?php echo $this->BForm->end() ?>
	</div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){
		atualizaQuestoes();
		jQuery("#limpar-filtro").click(function(){
			bloquearDiv(jQuery(".form-procurar"));
			jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Questao/element_name:questoes/" + Math.random())
		});

		function atualizaQuestoes() {
			var div = jQuery("div.lista");
			bloquearDiv(div);
			div.load(baseUrl + "questoes/listagem/" + Math.random());
		}

	});', false);
	?>