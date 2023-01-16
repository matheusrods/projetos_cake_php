<h2>Consultar CNH</h2>
<?php echo $this->BForm->create('ExtratorCnh', array('url'=>array('controller'=>$this->params['controller'], 'action'=> $this->params['action']))); ?>
    <div class='row-fluid inline'>
        <?php echo $this->BForm->input('cpf', array('label' => 'CPF:', 'class' => 'input-medium')) ?>
        <?php echo $this->BForm->input('registro', array('label' => 'Número do Registro:', 'class' => 'input-medium')) ?>
        <?php echo $this->BForm->input('seguranca', array('label' => 'Número de Segurança:', 'class' => 'input-medium')) ?>
    </div>
    <div class='row-fluid inline'>
      <?php echo $this->BForm->submit('Executar', array('div' => false, 'class' => 'btn')); ?>
    </div>
<?php echo $this->BForm->end(); ?>

<h2>Consultar Veículo</h2>
<?php echo $this->BForm->create('ExtratorVeiculo', array('url'=>array('controller'=>$this->params['controller'], 'action'=> $this->params['action']))); ?>
    <div class='row-fluid inline'>
        <?php echo $this->BForm->input('cpf', array('label' => 'CPF:', 'class' => 'input-medium')) ?>
        <?php echo $this->BForm->input('renavam', array('label' => 'Renavam:', 'class' => 'input-medium')) ?>
    </div>
    <div class='row-fluid inline'>
      <?php echo $this->BForm->submit('Executar', array('div' => false, 'class' => 'btn')); ?>
    </div>
<?php echo $this->BForm->end(); ?>

<h2>Resultado da Consulta</h2>
<?php foreach ($respostas as $key => $resposta): ?>
    <div class="well">
        <strong><?php echo $key ?>:</strong> <?php echo $resposta; ?>
    </div>
<?php endforeach; ?>