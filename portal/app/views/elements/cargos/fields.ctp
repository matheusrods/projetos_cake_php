<div class='well'>
    <strong>Código: </strong><?php echo $this->Html->tag('span', $this->data['Cliente']['codigo']); ?>
    <strong>Cliente: </strong><?php echo $this->Html->tag('span', $this->data['Cliente']['razao_social']); ?>
    <?php echo $this->BForm->hidden('Cargo.codigo_cliente', array('value' => $this->data['Cliente']['codigo'])); ?>
</div>

 <div class='row-fluid inline'>
	<?php echo $this->BForm->hidden('Cargo.codigo', array('value' => !empty($this->data['Cargo']['codigo']) ? $this->data['Cargo']['codigo'] : '')); ?>
	 <?php echo $this->BForm->input('Cargo.descricao', array('label' => 'Descrição (*)', 'class' => 'input-xxlarge', 'required' => true)); ?>
    <?php echo $this->BForm->input('Cargo.codigo_rh', array('label' => 'Código Cargo Externo', 'class' => 'input-large')); ?>

    <?php if (empty($this->passedArgs[2])): ?>
		<?php echo $this->BForm->hidden('Cargo.ativo', array('value' => 1)); ?>
	<?php
else: ?>
		<?php echo $this->BForm->input('Cargo.ativo', array('label' => 'Status (*)', 'class' => 'input', 'default' => '', 'empty' => 'Status', 'options' => array(1 => 'Ativo', 0 => 'Inativo'), 'required' => true)); ?>
	<?php
endif; ?>
	
</div> 

<div class='row-fluid inline'>
	<?php echo $this->BForm->input('Cargo.descricao_ppp', array('label' => 'Nome Legal <span style="font-size: 10px;">(Nome que será impresso no PPP)</span>', 'class' => 'input-xxlarge')); ?>
	<?php echo $this->BForm->input('Cargo.codigo_funcao', array('label' => 'Função', 'class' => 'input-xxlarge', 'default' => '', 'empty' => 'Selecione', 'options' => $funcoes)); ?>
		
</div> 

<?php if (!empty($atribuicoes_cargos)): ?>
	<div class='row-fluid inline'>
		<label class="inline input-xxlarge">Esta função se enquadra em alguma das opções abaixo?</label>
		<?php
	foreach ($atribuicoes_cargos as $id => $atribuicao):
		$checked = ($edit_mode == 1 && isset($cargo_atribuicoes_cargos[$id])) ? true : false;
		echo $this->BForm->label($id, $this->BForm->checkbox('atribuicoes_cargos.' . $id, array('value' => $id, 'hiddenField' => false, 'checked' => $checked)) . $atribuicao, array('class' => 'checkbox inline input-xxlarge', 'escape' => 'pull-left margin-left-20'));
	endforeach;
?>
	</div>
	<br> 
<?php
endif; ?>

<div class='row-fluid inline'>
	<?php echo $this->Buonny->input_codigo_cbo($this, 'codigo_cbo', 'CBO', 'CBO', 'Cargo', null, 'descricao_cargo'); ?>

	<?php echo $this->BForm->input('Cargo.codigo_cargo_similar', array('label' => 'Existe algum cargo similar a este nesta estrutura?', 'class' => 'input-xxlarge', 'default' => '', 'empty' => 'Selecione', 'options' => $cargos_similares)); ?>
</div> 

<div class='row-fluid inline'>
	<?php echo $this->BForm->input('Cargo.requisito', array('label' => 'Requisitos da Função', 'class' => 'input-xxlarge', 'type' => 'textarea')); ?>
	<?php echo $this->BForm->input('Cargo.descricao_cargo', array('label' => 'Descrição de atividades (*)', 'class' => 'input-xxlarge', 'type' => 'textarea', 'required' => true )); ?>
</div> 

<div class='row-fluid inline'>
	<?php echo $this->BForm->input('Cargo.educacao', array('label' => 'Educação', 'class' => 'input-xxlarge', 'type' => 'textarea')); ?>
	<?php echo $this->BForm->input('Cargo.treinamento', array('label' => 'Treinamento', 'class' => 'input-xxlarge', 'type' => 'textarea')); ?>
</div> 

<div class='row-fluid inline'>
	<?php echo $this->BForm->input('Cargo.habilidades', array('label' => 'Habilidades', 'class' => 'input-xxlarge', 'type' => 'textarea')); ?>
	<?php echo $this->BForm->input('Cargo.experiencias', array('label' => 'Experiência', 'class' => 'input-xxlarge', 'type' => 'textarea')); ?>
</div> 

<div class='row-fluid inline'>
	<?php echo $this->BForm->input('Cargo.descricao_local', array('label' => 'Descrição do Local', 'class' => 'input-xxlarge', 'type' => 'textarea')); ?>
	<?php echo $this->BForm->input('Cargo.observacao_aso', array('label' => 'Orientação (ASO)', 'class' => 'input-xxlarge', 'type' => 'textarea')); ?>
</div> 

<div class='row-fluid inline'>
	<?php echo $this->BForm->input('Cargo.material_utilizado', array('label' => 'Material utilizado <span style="font-size: 10px;">(Descrição do material utilizado)</span>', 'class' => 'input-xxlarge', 'type' => 'textarea')); ?>
	<?php echo $this->BForm->input('Cargo.mobiliario_utilizado', array('label' => 'Mobiliário utilizado <span style="font-size: 10px;">(Descrição do mobiliário utilizado)</span>', 'class' => 'input-xxlarge', 'type' => 'textarea')); ?>
</div> 

<div class='row-fluid inline'>
	<?php echo $this->BForm->input('Cargo.local_trabalho', array('label' => 'Local de Trabalho <span style="font-size: 10px;">(Descrição do local de trabalho)</span>', 'class' => 'input-xxlarge', 'type' => 'textarea')); ?>
</div>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('Cargo.codigo_gfip', array('label' => 'GFIP', 'class' => 'input-xxlarge', 'default' => '', 'empty' => 'Selecione', 'options' => $gfip)); ?>	
</div> 

 <div class='form-actions'>
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?= $html->link('Voltar', array('controller' => 'cargos', 'action' => 'index', $this->data['Cliente']['codigo'], $referencia, $terceiros_implantacao), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
$(document).ready(function(){
	setup_mascaras();
});
'); ?>
