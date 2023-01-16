<?php  
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();");
        exit;
    }
?>
<?php echo $this->Buonny->flash(); ?>
<div class="lista">
<?php echo $this->Bajax->form('TOveiOcorrenciaVeiculo', array('autocomplete' => 'off', 'url' => array('controller' => 'veiculos_ocorrencias', 'action' => 'recusar_checklist'))) ?>

    <div class="well">
        <?php echo $this->BForm->hidden("TOveiOcorrenciaVeiculo.ovei_codigo",array('value' =>$this->data['TOveiOcorrenciaVeiculo']['ovei_codigo']))?>
        <?php echo $this->BForm->hidden("TOveiOcorrenciaVeiculo.ovei_pess_oras_codigo",array('value' =>$this->data['TOveiOcorrenciaVeiculo']['ovei_pess_oras_codigo']))?>
        <?php echo $this->BForm->hidden("TOveiOcorrenciaVeiculo.ovei_veic_oras_codigo",array('value' =>$this->data['TOveiOcorrenciaVeiculo']['ovei_veic_oras_codigo']))?>
        <?php echo $this->BForm->hidden("TOveiOcorrenciaVeiculo.ovei_svoc_codigo",array('value' => 5))?>
        <div class="row-fluid inline">
            <strong>Data do Cancelamento:</strong> <?php echo $this->BForm->hidden("TOveiOcorrenciaVeiculo.ovei_data_recusa", array('value' =>$this->data['TOveiOcorrenciaVeiculo']['ovei_data_recusa'])) ?><?php echo $this->data['TOveiOcorrenciaVeiculo']['ovei_data_recusa']; ?>
        </div>
        <div class="row-fluid inline">
            <strong>Usuário Cancelamento:</strong> <?php echo $this->BForm->hidden("TOveiOcorrenciaVeiculo.ovei_usuario_recusa", array('value' =>$this->data['TOveiOcorrenciaVeiculo']['ovei_usuario_recusa'] )) ?><?php echo $this->data['TOveiOcorrenciaVeiculo']['ovei_usuario_recusa']; ?>
        </div>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('ovei_mcch_codigo', array('label' => false, 'class' => 'input-xxlarge', 'options' => $motivo, 'empty' => 'Selecione'));?>
    </div>
    <div class="form-actions">
        <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success', 'id'=>'botao-submit')); ?>
    </div>
</div>
