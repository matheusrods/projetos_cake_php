<div class="row-fluid inline">
  <?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
  <?php echo $this->BForm->input('descricao', array('class' => 'input-xxlarge', 'placeholder' => 'Descrição', 'label' => false)) ?>
  <?php echo $this->BForm->input('codigo_externo', array('class' => 'input-medium', 'placeholder' => 'Código Personalizado', 'label' => false)) ?>
</div>
<div class="row-fluid inline">
  <?php echo $this->BForm->input('tipo_servico', array('class' =>'input', 'label' => false, 'options' => array('E' => 'Exames Complementares', 'G' => 'Engenharia', 'C' => 'Consultorias e Palestras','S'=> 'Saúde'), 'empty' => 'Tipo de Serviço', 'default' => ' ')); ?>
  <?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => false, 'options' => array('0' => 'Inativos', '1' => 'Ativos'), 'empty' => 'Status', 'default' => ' ')); ?>
  
</div>        