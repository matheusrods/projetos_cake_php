<?php echo $this->BForm->create('Cliente', array('action' => 'dados_padrao_sm', $codigo_cliente, rand())); ?>
<div class="row-fluid inline">
    <h6>Temperatura Padrão na SM</h6>
    <?php echo $this->BForm->hidden('Cliente.codigo'); ?>
    <?php echo $this->BForm->hidden('Cliente.codigo_documento'); ?>
    <?php echo $this->BForm->hidden('TVppjValorPadraoPjur.vppj_codigo'); ?>
    <?php echo $this->BForm->input('TVppjValorPadraoPjur.vppj_temperatura_de',array('maxlength'=>'3','label' => 'De', 'class' => 'input-small numeric')); ?>
    <?php echo $this->BForm->input('TVppjValorPadraoPjur.vppj_temperatura_ate',array('maxlength'=>'3','label' => 'Até', 'class' => 'input-small numeric')); ?>
    <?php echo $this->BForm->input('TVppjValorPadraoPjur.vppj_monitorar_retorno',array('type' => 'checkbox', 'label' => 'Monitorar retorno', 'div' => array('class' => 'control-group input checkbox checkbox-parent-with-label') )); ?>
    <?php echo $this->BForm->input('TVppjValorPadraoPjur.vppj_rota_sm',array('type' => 'checkbox', 'label' => 'Não permitir geração de Rota na Inclusão da SM', 'div' => array('class' => 'control-group input checkbox checkbox-parent-with-label') )); ?>
    <?php echo $this->BForm->input('TVppjValorPadraoPjur.vppj_bloquear_sem_rota',array('type' => 'checkbox', 'label' => 'Bloquear inclusão de SMs sem Rota', 'div' => array('class' => 'control-group input checkbox checkbox-parent-with-label') )); ?>
    <?php echo $this->BForm->input('TVppjValorPadraoPjur.vppj_inicio_checklist',array('type' => 'checkbox', 'label' => 'Inicio checklist saida automático', 'div' => array('class' => 'control-group input checkbox checkbox-parent-with-label') )); ?>
</div>
<h4>Verificações</h4>
<div class='row-fluid inline'>
    <?php echo $this->BForm->input('TVppjValorPadraoPjur.vppj_monitorar_isca',array('type' => 'checkbox', 'label' => 'Monitorar isca como terminal principal', 'div' => array('class' => 'control-group input checkbox checkbox-parent-with-label') )); ?>
    <?php echo $this->BForm->input('TVppjValorPadraoPjur.vppj_bloq_sem_sinal',array('type' => 'checkbox', 'label' => 'Bloquear Veiculo sem sinal (Acima de '.$horas_sem_sinal.' horas sem envio)', 'value'=>'S',  'div' => array('class' => 'control-group input checkbox checkbox-parent-with-label') )); ?>
    <?php echo $this->BForm->input('TVppjValorPadraoPjur.vppj_nao_permite_sm_concorrente',array('type' => 'checkbox', 'label' => 'Não permite SM concorrente', 'div' => array('class' => 'control-group input checkbox checkbox-parent-with-label') )); ?>     
</div>
<h4>Checklist Online</h4>
<div class='row-fluid inline'>
    <?php echo $this->BForm->input('TVppjValorPadraoPjur.vppj_minutos_atraso_checklist',array('maxlength'=>'4','label' => 'Definir atraso em', 'class' => 'input-small numeric just-number', 'placeholder' => 'Minutos')); ?>
</div>
<h4>Apólice</h4>
<div class='row-fluid inline'>
    <?php echo $this->BForm->input("TVppjValorPadraoPjur.vppj_validade_apolice", array('label' => 'Data Validade Apólice', 'class' => 'input-small data', 'type'=>'text')) ?>
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'editar_configuracao',$codigo_cliente,rand()), array('class' => 'btn')); ?>
</div>    
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
    setup_time();
    setup_mascaras();
    setup_datepicker();
');?>