<?php echo $this->BForm->create('Resultado') ?>
<?php echo $this->BForm->hidden('codigo_questionario', array('value' => $codigo_questionario, 'name' => 'data[codigo_questionario]')) ?>
<div class='well'>
	<div class='actionbar-right'>
		<button type="button" class="btn btn-success btn-small js-add-resultado" data-toggle="tooltip" title="Adicionar novo resultado"><i class="icon-plus icon-white"></i></button>
	</div>
	<hr>
	<div class="js-memoria hide">
		<div class="row-fluid inline margin-bottom-15 app-begin not">
			<div class="bordered span12">
				<div class="span12">
					<?php echo $this->BForm->input('xx.Resultado.descricao', array('class' => 'input-large desc', 'disabled' => true,  'div' => 'span5', 'label' => 'Descrição', 'type' => 'text', 'id' => 'ResultadoxxDescricao')) ?>
					<?php echo $this->BForm->hidden('xx.Resultado.codigo_questionario', array('value' => $codigo_questionario, 'disabled' => true, 'id' => false)) ?>
					<div class="span5">
					<label>Pontos</label>
						<span class="pull-left margin-right-15 margin-top-5">de <strong><span class="js-valor-anterior js-contentxx">yy</span></strong> até </span> 
						<?php echo $this->BForm->input('xx.Resultado.valor', array('class' => 'input-small contentFieldxx val', 'disabled' => true, 'label' => false, 'type' => 'text')) ?>
					</div>

					<div class="span2 margin-top-25 text-right">
						<button type="button" class="btn btn-danger btn-small js-remover-entrada" data-toggle="tooltip" title="Remover resultado"><i class="icon-minus icon-white"></i></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php if(!empty($this->data)) { 
	$cont = 0;
	foreach ($this->data as $key => $dados) { ?>
	<div class="row-fluid inline margin-bottom-15 app-begin">
		<div class="bordered span12">
			<div class="span12">
				<?php echo $this->BForm->input('descricao', array('name' => 'data['.$key.'][Resultado][descricao]', 'class' => 'input-large desc', 'div' => 'span5', 'label' => 'Descrição', 'value' => $dados['Resultado']['descricao'], 'id' => false)) ?>
				<?php echo $this->BForm->hidden('codigo_questionario', array('name' => 'data['.$key.'][Resultado][codigo_questionario]', 'value' => $dados['Resultado']['codigo_questionario'], 'id' => false)) ?>
				<div class="span5">
				<label>Pontos</label>
					<span class="pull-left margin-right-15 margin-top-5">de <strong><span class="js-valor-anterior js-content<?php echo $key?>"><?php echo $cont ?></span></strong> até </span> 
					<?php echo $this->BForm->input('valor', array('name' => 'data['.$key.'][Resultado][valor]', 'class' => 'input-small val contentField'.$key, 'label' => false, 'value' => $dados['Resultado']['valor'], 'id' => false)) ?>
				</div>

				<div class="span2 margin-top-25 text-right">
					<button type="button" class="btn btn-danger btn-small js-remover-entrada" data-toggle="tooltip" title="Remover resultado"><i class="icon-minus icon-white"></i></button>
				</div>
			</div>
		</div>
	</div>
	<?php $cont = $dados['Resultado']['valor']+1; } } ?>
	<div class="js-entrada"></div>
</div>	
<div class="form-actions">
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?> &nbsp;
	<?php echo $html->link('Voltar', array('controller' => 'questionarios'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end() ?>

<?php echo $this->Javascript->codeBlock("
	$(document).ready(function() {
	alteraResultado(".((isset($key))? $key+1 : 0 ).");
	});
", false); ?>