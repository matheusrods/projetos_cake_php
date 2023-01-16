<?php //debug($this->data); ?>

<div class="well">
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('Medico.nome', array('label' => 'Nome', 'class' => 'input-xxlarge', 'readonly' => 'readonly')); ?>
		<?php echo $this->BForm->hidden('Medico.codigo', array('value' =>  !empty($this->data['Medico']['codigo'])? $this->data['Medico']['codigo'] : '')); ?>
	</div>
			
	<b>Upload da Assinatura eletrônica</b>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('caminho_arquivo', array('type' => 'file', 'label' => false)); ?>
	</div>

	<div class='row-fluid inline'>
		<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
		<?php echo $this->BForm->button('Limpar', array('type' => 'button', 'label' => 'Limpar', 'id' => 'LimparCaminhoArquivo', 'class' => 'btn btn-anexos')); ?>
	 	<?= $html->link('Voltar', array('controller' => 'assinatura_eletronica', 'action' => 'index'), array('class' => 'btn')); ?>
	</div>
</div>

<?php echo $this->Javascript->codeBlock('
	$(function() { 
		setup_mascaras(); 
	
		$("#LimparCaminhoArquivo").click(function(){
			$("#AnexoAssinaturaEletronicaCaminhoArquivo").val("");                
		});
	});	
'); ?>
