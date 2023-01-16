<div class='well'>
    <?php echo $this->BForm->create('Recebsm', array('autocomplete' => 'off', 'url' => array('controller' => 'solicitacoes_monitoramento', 'action' => 'acompanhar_notas_valores'))) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_periodo($this, 'Recebsm') ?>
            <?php echo $this->Buonny->input_cliente_tipo($this,0, $clientes_tipos)?>
        </div>
        <?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')); ?>
    <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaAcompanhamentoNotasEValores();
    });', false);
?>