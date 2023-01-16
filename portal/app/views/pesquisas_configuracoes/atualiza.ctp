<h4><?php echo $descricao_produto; ?></h4>
<?php echo $this->BForm->create('PesquisaConfiguracao', array('url' => array('action' => 'atualiza'))); ?>
    <div class="well">
        <?php echo $this->BForm->hidden('codigo') ?>
        <?php echo $this->BForm->hidden('codigo_produto') ?>
        <div class='row-fluid inline'>
            <?= $this->BForm->input('quantidade_cheque', array('class' => 'input-mini numeric', 'label' => 'Limite de cheques', 'maxlength' => 2)) ?>
            <?= $this->BForm->input('codigo_status_anterior', array('class' => 'input-large', 'label' => 'Status (Profissional)', 'options' => $lista_status, 'empty' => 'Selecione o status')) ?>

            <?= $this->BForm->input('codigo_status_anterior_proprietario', array('class' => 'input-medium', 'label' => 'Status (Proprietário)', 'options' => array('Não', 'Sim') , 'empty' => 'SELECIONE')) ?>

            <?= $this->BForm->input('valor_serasa', array('class' => 'input-small numeric moeda', 'label' => 'SERASA', 'type' => 'text', 'value' => $buonny->moeda($this->data['PesquisaConfiguracao']['valor_serasa'], array('edit' => true) ))) ?>
            <?= $this->BForm->input('quantidade_minutos_espera_envio_email', array('class' => 'input-mini numeric', 'label' => 'Tempo de espera para envio do retorno (minutos)', 'maxlength' => 2)) ?>
        </div>
        <div class='row-fluid inline'>
            <?= $this->BForm->input('verificar_profissional_negativado', array('class' => 'input-medium ', 'label' => 'Verificar Profissional Negativado', 'options' => array('Não', 'Sim'), 'empty' => 'SELECIONE')) ?>
            <?= $this->BForm->input('verificar_validade_cnh', array('class' => 'input-medium ', 'label' => 'Verificar Validade CNH', 'options' => array('Não', 'Sim') , 'empty' => 'SELECIONE')) ?>
            <?= $this->BForm->input('verificar_veiculo_ocorrencia', array('class' => 'input-medium ', 'label' => 'Verificar Veículo Ocorrência', 'options' => array('Não', 'Sim') , 'empty' => 'SELECIONE')) ?>
        </div>
        <div class='row-fluid inline'>
            <?= $this->BForm->input('historico_quantidade_viagem', array('class' => 'input-mini numeric', 'label' => 'Quantidade de viagens (Histórico)', 'maxlength' => 2)) ?>
            <?= $this->BForm->input('historico_quantidade_meses', array('class' => 'input-mini numeric', 'label' => 'Quantidade de meses (Histórico)', 'maxlength' => 2)) ?>
            <?= $this->BForm->input('historico_quantidade_viagem_ren_atu', array('class' => 'input-mini numeric', 'label' => 'Quantidade de viagens (Ren. Automática)', 'maxlength' => 2)) ?>            
        </div>
        <div class='row-fluid inline'>
            <?= $this->BForm->input('historico_quantidade_meses_ren_atu', array('class' => 'input-mini numeric', 'label' => 'Quantidade de meses (Ren. Automática)', 'maxlength' => 2)) ?>
            <?= $this->BForm->input('historico_quantidade_meses_agregado', array('class' => 'input-mini numeric', 'label' => 'Quantidade de meses (agregado)', 'maxlength' => 2)) ?>
        </div>
    </div>
    <div class="form-actions">
        <?= $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
        <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
    </div>
<?php echo $this->BForm->end() ?>
<?php echo $javascript->link('pesquisa.js'); ?>
<?php echo $javascript->codeblock('setup_mascaras();'); ?>