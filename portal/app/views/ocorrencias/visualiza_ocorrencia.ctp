<div id="ocorrencia-acao">
    <?= $bajax->form('Ocorrencia') ?>
    <?= $this->BForm->hidden('codigo') ?>
    <div class="row-fluid inline">
        <span class="span1">SM</span>
        <span class="span11">
            <?= $this->BForm->input('data_ocorrencia', array('type' => 'text', 'readonly' => true, 'label' => false, 'title' => 'Data da Ocorrência', 'class' => 'input-medium')) ?>
            <?= $this->BForm->input('codigo_sm', array('readonly' => true, 'label' => false, 'title' => 'SM', 'class' => 'input-small')) ?>
            <?= $this->BForm->input('Equipamento.Descricao', array('readonly' => true, 'label' => false, 'title' => 'Tecnologia', 'class' => 'input-medium')) ?>
            <?= $this->BForm->input('placa', array('readonly' => true, 'label' => false, 'title' => 'Placa', 'class' => 'input-small')) ?>
        </span>
    </div>
    <div class="row-fluid inline">
        <span class="span1">Empresa</span>
        <span class="span11">
            <?= $this->BForm->input('empresa', array('readonly' => true, 'label' => false, 'title' => 'Empresa', 'class' => 'input-xxlarge')) ?>
            <?= $this->BForm->input('telefone_empresa', array('readonly' => true, 'label' => false, 'title' => 'Telefone', 'class' => 'input-medium')) ?>
        </span>
    </div>
    <div class="row-fluid inline">
        <span class="span1">Motorista</span>
        <span class="span11">
            <?= $this->BForm->input('motorista', array('readonly' => true, 'label' => false, 'title' => 'Motorista', 'class' => 'input-xlarge')) ?>
            <?= $this->BForm->input('telefone_motorista', array('readonly' => true, 'label' => false, 'title' => 'Telefone', 'class' => 'input-medium')) ?>
            <?= $this->BForm->input('celular_motorista', array('readonly' => true, 'label' => false, 'title' => 'Celular', 'class' => 'input-medium')) ?>
        </span>
    </div>
    <div class="row-fluid inline">
        <?= $this->BForm->input('local', array('readonly' => true, 'label' => 'Local', 'class' => 'input-large')) ?>
        <?= $this->BForm->input('rodovia', array('readonly' => true, 'label' => 'Rodovia', 'class' => 'input-large')) ?>
        <?= $this->BForm->input('origem', array('readonly' => true, 'label' => 'Origem', 'class' => 'input-large')) ?>
        <?= $this->BForm->input('destino', array('readonly' => true, 'label' => 'Destino', 'class' => 'input-large')) ?>
    </div>
    <div class="row-fluid inline">
        <?php debug($tipos_ocorrencia); ?>
        <?= $this->BForm->input('OcorrenciaTipo.codigo_tipo_ocorrencia', array('disabled' => 'disabled', 'options' => $tipos_ocorrencia, 'multiple' => 'checkbox', 'label' => false, 'class' => 'checkbox inline input-large')) ?>
        <?= $this->BForm->input('descricao_tipo_ocorrencia', array('readonly' => true, 'label' => false, 'class' => 'input-medium', 'title' => 'Descrição para tipo outros')) ?>
        <?= $this->BForm->input('AssinaturaLiberacao.apelido', array('readonly' => true, 'label' => false, 'class' => 'input-medium', 'title' => 'Liberação pelo Responsável')) ?>
        <?php echo $this->BForm->input('codigo_prioridade', array('class' => 'input-small', 'div' => array('class' => 'form-inline'), 'label' => 'Prioridade', 'options' => array('1' => 'Baixa', '2' => 'Média', '3' => 'Alta'), 'disabled' => true)); ?>
    </div>
    <div class="row-fluid inline">
        <h4>Histórico</h4>
        <table class="table table-striped">
            <thead>
                <th style="width:125px">Data</th>
                <th style="width:160px">Usuário</th>
                <th style="width:157px">Status</th>
                <th>Descrição</th>
            </thead>
        <?php foreach ($ocorrencia['OcorrenciaHistorico'] as $ocorrencia_historico): ?>
            <tr>
                <td><?= $ocorrencia_historico['OcorrenciaHistorico']['data_inclusao'] ?></td>
                <td><?= $ocorrencia_historico['Funcionario']['Apelido'] != null ? $ocorrencia_historico['Funcionario']['Apelido'] : $ocorrencia_historico['Usuario']['Apelido'] ?></td>
                <td><?= $tipos_status_geral[$ocorrencia_historico['OcorrenciaHistorico']['codigo_status_ocorrencia']] ?></td>
                <td><?= $ocorrencia_historico['OcorrenciaHistorico']['descricao'] ?></td>
            </tr>
        <?php endforeach; ?>
        </table>
    </div>
    <?= $this->BForm->end() ?>
</div>
<?= $javascript->codeBlock("jQuery('div#ocorrencia-acao :checkbox').attr('disabled', true)") ?>