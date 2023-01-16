<?php if(($this->params['action'] == 'incluir') ? $readonly = FALSE :  $readonly = TRUE);?>
<div class="row-fluid inline">
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('ptve_tvei_codigo', array('readonly' => $readonly ,'type' => 'select', 'options' => $tipo_veiculo,'class' => 'input-meddium','label' =>'Tipo do Veículo','empty' => 'Selecione o tipo veiculo')); ?>
    </div>
    <div class="row-fluid inline">
       <span class="label label-info">Periféricos</span>
       <?php echo $this->BForm->input('ptve_ppad_codigo', array('multiple' => 'checkbox', 'options' => $periferico, 'label' => '', 'class' => 'checkbox inline input-large')); ?>
    </div>
</div>
<div class="form-actions">
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>