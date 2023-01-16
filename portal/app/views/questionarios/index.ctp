<div class = 'form-procurar'>
	<div class='well'>
		<div id='filtros'>
			<?php echo $bajax->form('Questionario', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Questionario', 'element_name' => 'questionarios'), 'divupdate' => '.form-procurar')) ?>

			<div class="row-fluid inline">
				<?php echo $this->BForm->input('codigo', array('class' => 'input-mini just-number', 'label' => 'Código', 'type' => 'text')) ?>
				<?php echo $this->BForm->input('descricao', array('class' => 'input-large', 'label' => 'Descrição', 'type' => 'text')) ?>
			</div>     

			<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
			<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
			<?php echo $this->BForm->end() ?>
		</div>
	</div>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'questionarios', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar novo questionário'));?>
</div>
<div class='lista'></div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
	setup_mascaras();
	jQuery(document).ready(function(){
		atualizaQuestionarios();
		jQuery("#limpar-filtro").click(function(){
			bloquearDiv(jQuery(".form-procurar"));
			jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Questionario/element_name:questionarios/" + Math.random())
		});

		function atualizaQuestionarios() {
			var div = jQuery("div.lista");
			bloquearDiv(div);
			div.load(baseUrl + "questionarios/listagem/" + Math.random());
		}

	});', false);
	?>