<?php echo $this->BForm->create('LogFaturamentoTeleconsult', array('type' => 'post' ,'url' => array('controller' => 'fichas_scorecard', 'action' => 'exclusao_log_faturamento', $codigo_log_faturamento)));?>
    <?php echo $this->BForm->hidden('codigo', array('value'=>$codigo_log_faturamento))?>
    <div class='row-fluid inline parent'>        
        <label>Data da exclusão: <?php echo date("d/m/Y H:i");?></label>
    </div>
    <div class='row-fluid inline parent'>
        <label>Responsável: <?php echo $authUsuario['Usuario']['apelido'];?></label>
    </div>
    <div class='control-group input textarea'>
        <label>Motivo da exclusão</label>
        <?php echo $this->BForm->input('LogFaturamentoExcluido.motivo_exclusao',array( 'type'=>'textarea','class' => 'input-xxlarge', 'label' => false ))?>
    </div>
    <div class="form-actions">
        <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
        <?php echo $html->link('Cancelar', array('controller' => 'fichas_scorecard', 'action' => 'log_faturamento'), array('class' => 'btn')) ;?>
    </div>
<?php echo $this->BForm->end() ?>