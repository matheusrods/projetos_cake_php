<div class='well'>
    <div class="row-fluid inline">
        <?php echo $this->BForm->create('Sistema', array('url' => array('controller' => 'sistemas', 'action' => 'criptografar'))) ?>
            <?php echo $this->BForm->input('texto', array('class' => 'input-xxlarge', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
            <?php echo $this->BForm->submit('Criptografar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $this->BForm->end() ?>
    </div>  
    <div class="row-fluid inline">
        <?php echo $criptografado ?>
    </div>
</div>