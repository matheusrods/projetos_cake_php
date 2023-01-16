<style type="text/css">.message.container {top:40px !important;}</style>
<?php echo $this->BForm->create('Usuario', array('url' => array('action' => 'recuperar_senha_cliente'), 'autocomplete' => 'off'))?>
    <?php echo $this->BForm->input('apelido', array('label' => 'Usuário', 'class' => 'input-medium')); ?>
    <div class="form-actions">
      <?php echo $this->BForm->submit('Recuperar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    </div>    
<?php echo $this->BForm->end(); ?>