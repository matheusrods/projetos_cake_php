<?php echo $this->BForm->create('PontoEletronico',array('url' => array('controller' => 'PontoEletronico','action' => 'incluir'))) ?>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('codigo_usuario', array('label' => 'Selecione o Usuário', 'class' => 'input-medium', 'options' => $lista,'empty' => 'Selecione o usuário')) ?>
    <?php echo $this->BForm->input('codigo_gestor', array('type' => 'hidden')) ?>
    <?php echo $this->BForm->input('data_hora_extra_de', array('label' => 'De', 'class' => 'input-small data')); ?>
    <?php echo $this->BForm->input('data_hora_extra_ate', array('label' => 'Até', 'class' => 'input-small data')); ?>
    <?php echo $this->BForm->input('motivo_cliente',array('type' => 'checkbox', 'label' => 'Motivo Cliente', 'div' => array('class' => 'control-group input checkbox checkbox-parent-with-label') )); ?>
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('motivo_hora_extra', array('label' => 'Motivo da hora extra', 'class' => 'input-xlarge')); ?>
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div> 
<?php echo $this->BForm->end(); ?>  
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	 setup_datepicker();
    });', false);
?>