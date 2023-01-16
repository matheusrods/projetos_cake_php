<!--<div class="usuarios_fields">-->
    <div class="row-fluid inline">
        <?php echo $this->BForm->hidden('codigo'); ?>
        <?php echo $this->BForm->input('descricao', array('type' => 'text', 'class' => 'input-xxlarge', 'label' => 'Descrição *')); ?>
        <?php echo $this->BForm->input('classificacao', array('class' => 'input-small', 'label' => 'Classificação *', 'options' => array(0 => 'PGR', 1 => 'PCMSO'), 'empty' => 'Selecione..')); ?>
        <?php //echo $this->BForm->input('status', array('class' => 'input-small', 'label' => 'Status *', 'options' => array(1 => 'Ativo', 0 => 'Inativo'), 'empty' => 'Selecione..')); ?>
        <?php echo $this->BForm->input('status', array('type' => 'hidden', 'label' => false, 'value' => '1')); ?>
    </div>
<!--</div>-->

