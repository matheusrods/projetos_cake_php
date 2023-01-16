<h2>Consultar STJ</h2>
<?php echo $this->BForm->create('Stj', array('url'=>array('controller'=>$this->params['controller'], 'action'=> $this->params['action']))); ?>
    <div class='row-fluid inline'>
        <?php echo $this->BForm->input('nome', array('label' => 'Nome:', 'class' => 'input-medium')) ?>
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