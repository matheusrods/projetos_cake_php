<?php echo $this->BForm->create('TCtecContaTecnologia', array('url' => array('controller' => 'StatusTecnologias','action' => 'editar_configuracao',$this->data['TCtecContaTecnologia']['ctec_codigo']), 'type' => 'POST')); ?>

    <div class='row-fluid inline'>
      <?php echo $this->BForm->hidden('ctec_codigo') ?>
      <?php echo $this->BForm->input('ctec_percentual_posicionando', array('label' => 'Percentual Posicionando', 'class' => 'input-medium')); ?>
      <?php echo $this->BForm->input('ctec_minimo_monitoramento', array('label' => 'Quantidade Minima', 'class' => 'input-medium')); ?>
    </div>  

<div class='form-actions'>
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('controller' => 'StatusTecnologias', 'action' => 'conta_tecnologias'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>