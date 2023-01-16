<?php $this->addScript($this->Buonny->link_js('search')); ?>
<?php $this->addScript($this->Buonny->link_js('comum')); ?>
<div class='dados'>
	<? if(!empty($dados_sm)): ?>
        <div class="row-fluid inline">
            <?= $this->BForm->input('Recebsm', array('readonly' => true, 'label' => 'SM', 'title' => 'SM', 'class' => 'input-small', 'value' => $dados['TViagViagem']['viag_codigo_sm'])) ?>
            <?= $this->BForm->input('Recebsm.Placa', array('readonly' => true, 'label' => 'Placa', 'title' => 'Placa', 'class' => 'input-small', 'value' => $dados_sm['Recebsm']['Placa'])) ?>
            <?= $this->BForm->input('Motorista.Nome', array('readonly' => true, 'label' => 'Motorista', 'title' => 'Motorista', 'class' => 'input-large', 'value' => $dados_sm['Motorista']['Nome'])) ?>
            <?= $this->BForm->input('Motorista.DDDTelefone', array('readonly' => true, 'label' => 'DDD Telefone', 'title' => 'DDD Telefone', 'class' => 'input-medium', 'value' => $dados_sm['Motorista']['DDDTelefone'])) ?>
            <?= $this->BForm->input('Motorista.Telefone', array('readonly' => true, 'label' => 'Telefone Motorista', 'title' => 'Telefone', 'class' => 'input-medium', 'value' => $dados_sm['Motorista']['telefone'])) ?>
        </div>
        <div class="row-fluid inline">
            <?= $this->BForm->input('ClientEmpresa.Raz_social', array('readonly' => true, 'label' => 'Razão Social', 'title' => 'Razão Social', 'class' => 'input-xlarge', 'value' => $dados_sm['ClientEmpresa']['Raz_social'])) ?>
            <?= $this->BForm->input('ClientEmpresa.Telefone', array('readonly' => true, 'label' => 'Telefone Empresa', 'title' => 'Telefone', 'class' => 'input-medium', 'value' => $dados_sm['ClientEmpresa']['Telefone'])) ?>
            <?= $this->BForm->input('CidadeOrigem.Descricao', array('readonly' => true, 'label' => 'Origem', 'title' => 'Origem', 'class' => 'input-medium', 'value' => $dados_sm['CidadeOrigem']['Descricao'])) ?>
            <?= $this->BForm->input('CidadeDestino.Descricao', array('readonly' => true, 'label' => 'Destino', 'title' => 'Destino', 'class' => 'input-medium', 'value' => $dados_sm['CidadeDestino']['Descricao'])) ?>
        </div>
	<? endif; ?>
        <div class="row-fluid inline">
            <?= $this->BForm->input('AtendimentoSm.prioridade', array('readonly' => true, 'label' => 'Prioridade do Atendimento', 'title' => 'Prioridade', 'class' => 'input-medium', 'value' => $dados_atendimento_sm['AtendimentoSm']['prioridade'])) ?>
            <?= $this->BForm->input('AtendimentoSm.status', array('readonly' => true, 'label' => 'Status', 'title' => 'Status', 'class' => 'input-medium', 'value' => $dados_atendimento_sm['AtendimentoSm']['status'])) ?>
            <?= $this->BForm->input('HistoricoSm.codigo_usuario_monitora', array('readonly' => true, 'label' => 'Operador', 'title' => 'Operador', 'class' => 'input-medium', 'value' => $usuario_operador)) ?>
            <?= $this->BForm->input('TEspaEventoSistemaPadrao.espa_descricao', array('readonly' => true, 'label' => 'Tipo de Evento', 'title' => 'Tipo de Evento', 'class' => 'input-xlarge', 'value' => $tipo_evento['TEspaEventoSistemaPadrao']['espa_descricao'])) ?>
        </div>
        <div class="row-fluid inline">
            <?= $this->BForm->input('Equipamento.Descricao', array('readonly' => true, 'label' => 'Tecnologia', 'title' => 'Tecnologia', 'class' => 'input-medium', 'value' => $dados['TTecnTecnologia']['tecn_descricao'])) ?>
            <?= $this->BForm->input('HistoricoSm.local', array('readonly' => true, 'label' => 'Local', 'title' => 'Local', 'class' => 'input-xxlarge', 'value' => $dados_historico_sm['HistoricoSm']['local'])) ?>
        </div>
</div>
        
<?php if(empty($dados_passo_atendimento_sm['PassoAtendimentoSm']['data_fim']) && !isset($bloquear_campos)): ?>
    <div class="form-historico">
        <?php echo $this->element('historicos_sms/fields'); ?>
    </div>
<?php endif;
        echo $javascript->codeBlock('jQuery(document).ready(function(){
            atualizaListaHistoricosSms('.$codigo_sm.');
        });');
?>
<div class='listagem'></div>