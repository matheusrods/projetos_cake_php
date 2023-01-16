<?php echo $this->BForm->create('PontoEletronico',array('url' => array('controller' => 'PontoEletronico','action' => 'editar'))) ?>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('codigo') ?>
    <?php echo $this->BForm->input('motivo_hora_extra', array('label' => 'Motivo da hora extra', 'class' => 'input-xlarge')); ?>
    <?php echo $this->BForm->input('motivo_cliente',array('type' => 'checkbox', 'label' => 'Motivo Cliente', 'div' => array('class' => 'control-group input checkbox checkbox-parent-with-label') )); ?>
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