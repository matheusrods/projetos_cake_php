<div class='form-procurar'>	
    <div class='well'>
	    <?php echo $this->BForm->create('FichasScorecard', array('autocomplete' => 'off', 'url' => array('controller' => 'fichas_scorecard', 'action' => 'log_faturamento_sm'))) ?>
	    <div class="row-fluid inline">
	    	<?php echo $this->BForm->input('num_consulta', array('class' => 'input-medium', 'label' => false, 'placeholder' => 'Num Consulta')); ?>
			<?php echo $this->BForm->input('cpf', array('class' => 'input-medium', 'label' => false, 'placeholder' => 'CPF')); ?>
			<?php echo $this->BForm->input('placa', array('class' => 'input-small', 'label' => false, 'placeholder' => 'Placa')); ?>
			<?php echo $this->BForm->input('usuario', array('class' => 'input-small', 'label' => false, 'placeholder' => 'Usuário')); ?>
			<?php echo $this->BForm->input('tipos', array('class' => 'input-small', 'label' => false, 'type'=>'select','options'=>array('1'=>'Sem custos','2'=>'Com Custos'),'empty'=>'Tipos')); ?>
	    </div>
	    <div class="row-fluid inline">
	    	<?php echo $this->BForm->input('tipo_operacao', array('class' => 'input-xlarge', 'label' => false, 'type'=>'select','options'=>$tipo_operacao,'empty'=>'Operação')); ?>
	    	<?php echo $this->BForm->input('data_inicial', array('class' => 'input-medium data', 'label' => false, 'placeholder' => 'Data Inicial')); ?>
	    	<?php echo $this->BForm->input('data_final', array('class' => 'input-medium data', 'label' => false, 'placeholder' => 'Data Final')); ?>
	    </div>
	    <div class="row-fluid inline">
	    	
	    </div>
	    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	    <?php echo $this->BForm->end();?>
	</div>
</div>
<?php echo isset($dados) ? $this->element('fichas_scorecard/log_faturamento',array('dados'=>$dados)) : ''; ?>
<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
	    setup_datepicker();

  
	});', false);

?>