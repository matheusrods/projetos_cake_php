<div class='well'>
    <div class="row-fluid inline">
        <?php echo $this->BForm->create('Tranpag', array('autocomplete' => 'off', 'url' => array('controller' => 'pagamentos_transacoes', 'action' => $this->action))) ?>
        <?php echo $this->BForm->input('data_inicial', array('class' => 'data input-small', 'placeholder' => 'Início', 'label' => false)) ?>
        <?php echo $this->BForm->input('data_final', array('class' => 'data input-small', 'placeholder' => 'Fim', 'label' => false)) ?>        
        <?php echo $this->Buonny->input_grupo_empresas($this,$grupos_empresas,$empresas); ?>
        
        <?php echo $this->BForm->input('centro_custo_desc', array('class' => 'input-small', 'label' => false, 'type' => 'hidden')); ?>
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