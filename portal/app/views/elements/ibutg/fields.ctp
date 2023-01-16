<div class='well'>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('nome_atividade', array('label' => 'Nome da Atividade (*)', 'class' => 'input-xxlarge')); ?>
	</div>
	<div class='row-fluid inline'>
		<?php 
			$array_tipo = array(
				'1' => 'Trabalho Leve',
				'2' => 'Trabalho Moderado',
				'3' => 'Trabalho Pesado',
				'4' => 'Repouso'
			);
			
		?>
	</div>
	<div class='row-fluid inline'>	
		<?php echo $this->BForm->input('tipo_atividade', array('options' => $array_tipo, 'empty' => '--- Selecione ---', 'label' => 'Tipo de Atividade', 'class' => 'input-xlarge')); ?>
		<?php echo $this->BForm->input('valor_kcal', array('label' => 'Valor Kcal', 'maxlength' => '10', 'class' => 'input-xlarge just-number')); ?>	 
	</div> 
</div>
<div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'ibutg', 'action' => 'index'), array('class' => 'btn')); ?>
</div>


<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();
	});
'); ?>