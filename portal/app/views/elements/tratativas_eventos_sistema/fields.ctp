<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('tesi_codigo'); ?>
    <?php echo $this->BForm->input('tesi_espa_codigo', array('class' => 'input-xxlarge', 'options' => $eventos, 'label' => 'Evento', 'empty' => 'Selecione o Evento' )); ?>    
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('tesi_descricao', array('class' => 'input-xxlarge','label' => 'Descrição' )); ?> 
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>    
<?php echo $this->BForm->end(); ?>