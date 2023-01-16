<?php echo $this->BForm->create('Usuario', array('url' => array('action' => 'recuperar_senha'), 'autocomplete' => 'off'))?> 
    <?php echo $this->BForm->input('apelido', array('label' => 'Login', 'class' => 'input-medium')) ?>

    
    <?php //echo $this->BForm->input('senha', array('label' => 'Senha', 'class' => 'input-medium', 'readonly' => true)) ?>

    <?php
    if(isset($this->data['Usuario']['dados'])) {
        foreach($this->data['Usuario']['dados'] AS $dados) {
            echo '<b>Perfil:</b> '. $dados['perfil'] . " <b>Senha:</b> " . $dados['senha']."<br><br>";
        }
    }

    ?>

    <div class="form-actions">
      <?php echo $this->BForm->submit('Recuperar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    </div>    
<?php echo $this->BForm->end() ?>