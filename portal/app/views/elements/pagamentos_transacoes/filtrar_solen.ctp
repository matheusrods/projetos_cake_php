<div class='well'>
    <div class="row-fluid inline">
        <?php echo $this->BForm->create('Tranpag', array('autocomplete' => 'off', 'url' => array('controller' => 'pagamentos_transacoes', 'action' => $this->action))) ?>
        <?//php echo $this->Buonny->input_periodo($this, 'Tranpag') ?>
        <?php echo $this->BForm->input('mes', array('type' => 'select', 'options' => Comum::listMeses(), 'class' => 'input-small', 'label' => false,'empty' => 'Selecione o Mês')); ?>
        <?php echo $this->BForm->input('ano', array('type' => 'select', 'options' => Comum::listAnos(), 'class' => 'input-small', 'label' => false,'empty' => 'Selecione o ano')); ?>
        <?php echo $this->BForm->hidden('grupo_empresa', array('value' => 4)); ?>
        <?php echo $this->BForm->hidden('empresa'); ?>
        
        <?php echo $this->BForm->input('centro_custo_desc', array('class' => 'input-small', 'label' => false, 'type' => 'hidden'));?>
        <?php echo $this->BForm->input('centro_custo_descricao', array('class' => 'input-small', 'label' => false, 'placeholder' => 'Descrição')); ?>
        <?php echo $this->BForm->input('sub_codigo_desc', array('class' => 'input-small', 'label' => false, 'type' => 'hidden')); ?>
        <?php echo $this->BForm->input('codigo_conta_desc', array('class' => 'input-small', 'label' => false, 'type' => 'hidden')); ?>
        
        <?php if($this->action == 'listar_titulos_pagos_por_centro_custo'){ ?>
            <?php echo $this->BForm->input('ccusto', array('class' => 'input-mini', 'label' => false, 'placeholder' => 'Centro Custo', 'type' => 'text')); ?>
        <?php }elseif($this->action == 'listar_titulos_pagos_por_centro_custo_sub_codigo'){ ?>
            <?php echo $this->BForm->input('ccusto', array('class' => 'input-mini', 'label' => false, 'placeholder' => 'Centro Custo', 'type' => 'text')); ?>
            <?php echo $this->BForm->input('sub_codigo', array('class' => 'input-mini', 'label' => false, 'placeholder' => 'Sub Código', 'type' => 'text')); ?>
        <?php }elseif($this->action == 'listar_titulos_pagos_por_centro_custo_sub_codigo_conta'){ ?>
            <?php echo $this->BForm->input('ccusto', array('class' => 'input-mini', 'label' => false, 'placeholder' => 'Centro Custo', 'type' => 'text')); ?>
            <?php echo $this->BForm->input('sub_codigo', array('class' => 'input-mini', 'label' => false, 'placeholder' => 'Sub Código', 'type' => 'text')); ?>
            <?php echo $this->BForm->input('codigo_conta', array('class' => 'input-mini', 'label' => false, 'placeholder' => 'Conta', 'type' => 'text')); ?>
        <?php } ?>
        
    </div>
    <?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $this->BForm->end() ?>
</div>
