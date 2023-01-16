<div class="span9">
    <div class="row-fluid inline">
        <?php echo $this->BForm->hidden('codigo'); ?>
        <?php echo $this->BForm->hidden('codigo_sm'); ?>
        <?php echo $this->BForm->hidden('codigo_passo_atendimento'); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('placa', array('readonly' => true, 'label' => 'Placa', 'type' => 'text', 'class' => 'input-small')); ?>
        <?php echo $this->BForm->input('empresa', array('readonly' => true, 'class' => 'input-xlarge')); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('telefone_empresa', array('readonly' => true, 'class' => 'input-medium')); ?>
        <?php echo $this->BForm->input('Equipamento.Descricao', array('label' => 'Tecnologia', 'readonly' => true, 'class' => 'input-medium')); ?>
        <?php echo $this->BForm->hidden('codigo_tecnologia'); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('motorista', array('readonly' => true, 'class' => 'input-xxlarge')); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('telefone_motorista', array('readonly' => true, 'class' => 'input-medium')); ?>
        <?php echo $this->BForm->input('celular_motorista', array('readonly' => true, 'class' => 'input-medium')); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('local', array('label' => 'Local', 'class' => 'input-xxlarge')); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('codigo_tipo_evento', array('label' => 'Tipo de Evento', 'class' => 'input-xxlarge', 'options' => $tipos_eventos, 'empty' => 'Selecione o Evento')); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('origem', array('readonly' => true, 'class' => 'input-large')); ?>
        <?php echo $this->BForm->input('destino', array('readonly' => true, 'class' => 'input-large')); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('texto', array('label' => 'Observações', 'type' => 'textarea', 'class' => 'input-xxlarge')) ?>
    </div>
    <div class="row-fluid inline">
        <?php if(isset($latitude) && isset($longitude)):?>
            <?php echo $this->BForm->input('latitude', array('readonly' => true, 'label' => 'Latitude', 'maxlength' => 11, 'class' => 'latitude input-small', 'value' => $latitude)); ?>
            <?php echo $this->BForm->input('longitude', array('readonly' => true, 'label' => 'Longitude', 'maxlength' => 11, 'class' => 'longitude input-small', 'value' => $longitude)); ?>
        <?php else: ?>
            <?php echo $this->BForm->input('latitude', array('label' => 'Latitude', 'maxlength' => 11, 'class' => 'latitude input-small')); ?>
            <?php echo $this->BForm->input('longitude', array('label' => 'Longitude', 'maxlength' => 11, 'class' => 'longitude input-small')); ?>
        <?php endif;?>
    </div>
    <div class="row-fluid">
        <?php echo $this->BForm->submit('Incluir', array('class' => 'btn')); ?>
        <?php echo $this->BForm->end(); ?>
    </div>
</div>