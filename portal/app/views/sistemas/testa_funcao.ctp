<div class='form-procurar well'>
    <?php echo $this->BForm->create('Model', array('autocomplete' => 'off', 'url' => array('controller' => 'sistemas', 'action' => 'testa_funcao'))); ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('name', array('label' => 'Model', 'class' => 'input-large', 'placeholder' => 'Model')); ?>
            <?php echo $this->BForm->input('function', array('label' => 'Função', 'class' => 'input-large', 'placeholder' => 'Função')); ?>
            <?php echo $this->BForm->input('parameters', array('label' => 'Parâmetros', 'class' => 'input-large', 'placeholder' => 'Parâmetros')); ?>
        </div>
        <?php echo $this->BForm->submit('Run', array('div' => false)); ?>
    <?php echo $this->BForm->end(); ?>
</div>
<?php if (isset($result)): ?>
    <?php pr($result) ?>
<?php endif ?>