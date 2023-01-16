<div class='well'>
    <div class="row-fluid inline">
        <?php echo $this->BForm->create('Sistema', array('url' => array('controller' => 'sistemas', 'action' => 'zerar_senhas'))) ?>
            <?php echo $this->BForm->input('senha', array('class' => 'input-small', 'placeholder' => 'Senha', 'label' => false, 'type' => 'password')) ?>
            <?php echo $this->BForm->submit('Zerar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $this->BForm->end() ?>
    </div>  
</div>