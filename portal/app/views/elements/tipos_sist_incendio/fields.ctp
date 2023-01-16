<div class="well">

	<div class="span4">
		<div class='row-fluid inline'>
			 <?php echo $this->BForm->input('nome', array('label' => 'Nome(*)', 'class' => 'input-xlarge')); ?>
		</div>
		<div class='row-fluid inline'>
			 <?php echo $this->BForm->input('descricao_detalhada', array('type' => 'textarea', 'label' => 'Descrição Detalhada', 'class' => 'input-xlarge')); ?>
		</div>
		<div class='row-fluid inline'>	 
			 <?php echo $this->BForm->input('fabricante', array('type' => 'text', 'label' => 'Fabricante', 'class' => 'input-xlarge')); ?>
		</div>	
		<div class='row-fluid inline'>
			<b> Classe do Fogo: </b><br /><br />
			 <?php echo $this->BForm->input('classe_fogo.0.classe', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => '(A)', 'class' => 'input-xlarge', 'multiple'=>'multiple')); ?> 
			 <?php echo $this->BForm->input('classe_fogo.1.classe', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => '(B)', 'class' => 'input-xlarge', 'multiple'=>'multiple')); ?> 
			 <?php echo $this->BForm->input('classe_fogo.2.classe', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => '(C)', 'class' => 'input-xlarge', 'multiple'=>'multiple')); ?> 
			 <?php echo $this->BForm->input('classe_fogo.3.classe', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => '(D)', 'class' => 'input-xlarge', 'multiple'=>'multiple')); ?> 
		</div>
		<div class='row-fluid inline'>	 
			 <?php echo $this->BForm->input('composicao', array('label' => 'Composição', 'class' => 'input-xlarge')); ?>
		</div>
		<div class='row-fluid inline'>	 
			 <?php echo $this->BForm->input('peso_liquido', array('label' => 'Peso Líquido', 'class' => 'input-large just-number')); ?>
		</div>	
		<div class='row-fluid inline'>	 
			 <?php echo $this->BForm->input('periodicidade_recarga', array('label' => 'Periodicidade da Recarga', 'class' => 'input-large just-number')); ?>
		</div>		
	</div>
	
	<div class="span4">
		<div class='row-fluid inline'>	 
			 <?php echo $this->BForm->input('periodicidade_verificacao', array('label' => 'Periodicidade da verificação', 'class' => 'input-large just-number')); ?>
		</div>	
		<div class='row-fluid inline'>	 
			 <?php echo $this->BForm->input('tempo_restante', array('label' => 'Tempo Restante', 'class' => 'input-xlarge just-number')); ?>
		</div>	
		<div class='row-fluid inline'>	 
			 <?php echo $this->BForm->input('tipo_esguico', array('type' => 'textarea', 'label' => 'Tipo Esguiço', 'class' => 'input-xlarge')); ?>
		</div>
		<div class='row-fluid inline'>	 
			 <?php echo $this->BForm->input('tipo_engate', array('type' => 'textarea', 'label' => 'Tipo Engate', 'class' => 'input-xlarge')); ?>
		</div>
		<div class='row-fluid inline'>
			 <?php echo $this->BForm->input('medida_engate', array('type' => 'textarea', 'label' => 'Medida Engate', 'class' => 'input-xlarge')); ?>
		</div>		
	</div>
	
	<div class="span3">
		<div class='row-fluid inline'>	 
			 <?php echo $this->BForm->input('diametro_mangueira', array('label' => 'Diametro da Mangueira', 'class' => 'input-xlarge')); ?>
		</div>
		<div class='row-fluid inline'>	 
			 <?php echo $this->BForm->input('tipo_acionamento_sprinklers', array('type' => 'textarea', 'label' => 'Tipo Acionamento Sprinklers', 'class' => 'input-xlarge')); ?>
		</div>
		<div class='row-fluid inline'>	 
			 <?php echo $this->BForm->input('acessorios', array('type' => 'textarea', 'label' => 'Acessórios', 'class' => 'input-xlarge')); ?>
		</div>
		<div class='row-fluid inline'>	 
			 <?php echo $this->BForm->input('observacao', array('type' => 'textarea', 'label' => 'Observação', 'class' => 'input-xlarge')); ?>	 
		</div>
	</div>
	<div class="clear"></div>
</div>

<div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'tipos_sist_incendio', 'action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();
	});
'); ?>