<div class="row-fluid inline">
    <?php echo $this->BForm->input('codigo_sm', array('class' => 'input-mini just-number', 'label' => 'SM')); ?>
    <?php echo $this->BForm->input('codigo_motivo_atendimento', array('label' => 'Motivo da ligação', 'class' => 'input-large', 'options' => $motivos, 'empty' => 'Selecione um motivo')); ?>
    <?php echo $this->BForm->input('nome_atendente', array('class' => 'input-large', 'label' => 'Atendente')); ?>
    <?php echo $this->BForm->input('motorista', array('class' => 'input-large', 'label' => 'Motorista')); ?>
    <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_embarcador','Embarcador', 'Embarcador', 'AtendimentoSac', null, false) ?>
    <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_transportador','Transportador', 'Transportador', 'AtendimentoSac', null, false) ?>
</div>
<div class="row-fluid inline">
    <?php echo $this->Buonny->input_periodo($this, 'AtendimentoSac', 'data_inicial', 'data_final', true) ?>
    <?php echo $this->BForm->input('hora_inicial', array('label' => 'Hora inicial', 'class' => 'hora input-mini')); ?>
    <?php echo $this->BForm->input('hora_final', array('label' => 'Hora final', 'class' => 'hora input-mini')); ?>
    <?php echo $this->BForm->input('placa', array('class' => 'input-small placa-veiculo required', 'label' => 'Placa', 'placeholder' => false,)) ?>
    <?php echo $this->BForm->input('tecnologia', array('label' => 'Tecnologia', 'class' => 'input-large', 'options' => $tecnologia, 'empty' => 'Selecione a Tecnologia')); ?>
</div>