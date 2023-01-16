 <div class='row-fluid inline'>
	<?php echo $this->BForm->input('descricao', array('label' => 'Descrição (*)', 'class' => 'input-xxlarge')); ?>
	
	<?php if(empty($this->passedArgs)): ?>
		<?php echo $this->BForm->hidden('ativo', array('value' => 1)); ?>
	<?php else: ?>
		<?php echo $this->BForm->input('ativo', array('label' => 'Status (*)', 'class' => 'input', 'default' => '', 'empty' => 'Status', 'options' => array(1 => 'Ativo', 0 => 'Inativo'))); ?>
	<?php endif;  ?>
</div>  
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('exibe_relatorio', array('label' => 'Exibir no Relatorio?','type' => 'checkbox')) ?>
	<?php echo $this->BForm->input('considera_afastamento', array('label' => 'Considerar para o Afastamento?','type' => 'checkbox')) ?>
</div>  

<div class='row-fluid inline'>
	<h5>Quantidade Limite de Dias para o Afastamento</h5>
	<?php echo $this->BForm->input('limite_min_afastamento', array('label' => 'Mínima', 'class' => 'input-mini just-number')); ?>
	<?php echo $this->BForm->input('limite_max_afastamento', array('label' => 'Máxima', 'class' => 'input-mini just-number')); ?>
</div>  
	<?php echo $this->BForm->hidden('codigo', array('value' =>  !empty($this->data['TipoAfastamento']['codigo'])? $this->data['TipoAfastamento']['codigo'] : '')); ?>
  
  
 <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'tipos_afastamento', 'action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('

	$(document).ready(function(){
		setup_mascaras();

	});', false);
?>