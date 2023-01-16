<?php echo $this->BForm->create('LogFaturamentoTeleconsult', array('type' => 'post' ,'url' => array('controller' => 'fichas_scorecard', 'action' => 'recuperar_numero_liberacao', $codigo_log_faturamento)));?>
    <?php echo $this->BForm->hidden('codigo', array('value'=>$codigo_log_faturamento))?>
    <div class='row-fluid inline parent'>
        <label>Data da recuperação: <?php echo date("d/m/Y H:i");?></label>
    </div>
    <div class='row-fluid inline parent'>
        <label>Usuário: <?php echo $authUsuario['Usuario']['apelido'];?></label>
    </div>
    
    <div class='row-fluid inline parent'>
        <?if( !empty($numero_liberacao) ):?>
        <h3> Número de Liberação: <?=$numero_liberacao;?></h3>        
        <?endif;?>
    </div>

    <div class="form-actions">
        <?php echo $this->BForm->submit('Recuperar', array('div' => false, 'class' => 'btn btn-primary')); ?>
        <?php echo $html->link('Voltar', array('controller' => 'fichas_scorecard', 'action' => 'log_faturamento'), array('class' => 'btn')) ;?>
    </div>
<?php echo $this->BForm->end() ?>