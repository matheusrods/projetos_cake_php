<?php echo $this->BForm->create('Cliente', array('url' => array('controller' => 'embarcadores_transportadores', 'action' => 'editar_embarcador_transportador')) );?>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('razao_social', array('class' => 'input-xxlarge', 'label' => 'Razão Social')); ?>
	<?php echo $this->BForm->input('nome_fantasia', array('class' => 'input-xlarge', 'label' => 'Nome Fantasia')); ?>
	<?php echo $this->BForm->hidden('codigo_cliente_cadastrante'); ?>
</div>
<div class="tab-content">
	<div class="tab-pane active" id="gerais">
		<?php echo $this->element('embarcadores_transportadores/fields_embarcador_transportador', array('edit_mode' => $edit_mode)) ?>
	</div>
</div>
<div class="form-actions">
	<?=$this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?=$html->link('Voltar', array('controller'=>'embarcadores_transportadores', 'action' => 'embarcador_transportador'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock("window.vizualizar = true;"); ?>
<?php $this->addScript($this->Buonny->link_js('clientes.js')); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras();setup_datepicker(); });'); ?>