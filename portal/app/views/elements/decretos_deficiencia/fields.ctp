 <div class='row-fluid inline'>
	<?php echo $this->BForm->input('descricao', array('label' => 'Decreto (*)', 'class' => 'input-xxlarge')); ?>
	
	<?php if(empty($this->passedArgs)): ?>
		<?php echo $this->BForm->hidden('ativo', array('value' => 1)); ?>
	<?php else: ?>
		<?php echo $this->BForm->input('ativo', array('label' => 'Status (*)', 'class' => 'input', 'default' => '', 'empty' => 'Status', 'options' => array(1 => 'Ativo', 0 => 'Inativo'))); ?>
	<?php endif;  ?>
</div>  
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('decreto_descricao', array(
	'label' => 'Descrição do Decreto (*)', 'type'  => 'textarea', 'class' => 'input-xxlarge', 'rows' =>4)); ?>
</div>

<div class='row-fluid inline'>
	<h5>Itens do Decreto</h5>
	<h6>Informe os itens que deverão aparecer no Decreto.</h6>
	
	<?php echo $this->Html->link('Nome do Funcionário', "javascript:void(0)", array('class' => 'input btn',  'onclick' => 'insereCampos("NOME");', 'title' => 'Inserir Nome do Funcionário')); ?>
	<?php echo $this->Html->link('RG', "javascript:void(0)", array('class' => 'input btn',  'onclick' => 'insereCampos("RG");', 'title' => 'Inserir RG')); ?>
	<?php echo $this->Html->link('CPF', "javascript:void(0)", array('class' => 'input btn',  'onclick' => 'insereCampos("CPF");', 'title' => 'Inserir CPF')); ?>
	<?php echo $this->Html->link('Data de Nascimento', "javascript:void(0)", array('class' => 'input btn',  'onclick' => 'insereCampos("DT_NASC");', 'title' => 'Inserir Data de Nascimento')); ?>
	<?php echo $this->Html->link('CTPS', "javascript:void(0)", array('class' => 'input btn',  'onclick' => 'insereCampos("CTPS");', 'title' => 'Inserir CTPS')); ?>
	<?php echo $this->Html->link('Setor', "javascript:void(0)", array('class' => 'input btn',  'onclick' => 'insereCampos("SETOR");', 'title' => 'Inserir Setor')); ?>
	<?php echo $this->Html->link('Cargo', "javascript:void(0)", array('class' => 'input btn',  'onclick' => 'insereCampos("CARGO");', 'title' => 'Inserir Cargo')); ?>
</div>  

	<?php echo $this->BForm->hidden('codigo', array('value' =>  !empty($this->data['DecretoDeficiencia']['codigo'])? $this->data['DecretoDeficiencia']['codigo'] : '')); ?>
  
  
 <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'decretos_deficiencia', 'action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php 
echo $this->Javascript->codeBlock("

    function insereCampos(campo){
    	var decreto_descricao = $('#DecretoDeficienciaDecretoDescricao').val();
    	
    	decreto_descricao = decreto_descricao + '#'+campo+'#';
    	$('#DecretoDeficienciaDecretoDescricao').val(decreto_descricao);
    }
");
?>