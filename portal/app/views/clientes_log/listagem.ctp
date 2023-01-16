<div class="text-group">
    <table class="table table-condensed table-striped">
        <tbody>
            <?php foreach ($clientes_log as $cliente_log): ?>
                <tr>
                    <td>
                        <div class='row-fluid'>
                            <?= $this->Html->tag('label', 'Cliente:', array('class' => 'span2 title')) ?><?= $this->Html->tag('label', $cliente_log['ClienteLog']['codigo_cliente']) ?>
                        </div>
                        <div class='row-fluid'>
                            <?= $this->Html->tag('label', 'CPF/CNPJ:', array('class' => 'span2 title')) ?> <?= $this->Html->tag('label', $cliente_log['ClienteLog']['codigo_documento']) ?>
                            <?= $this->Html->tag('label', 'Inscrição Estadual:', array('class' => 'span2 title')) ?> <?= $this->Html->tag('label', $cliente_log['ClienteLog']['inscricao_estadual']) ?>
                            <?= $this->Html->tag('label', 'CCM:', array('class' => 'span1 title')) ?> <?= $this->Html->tag('label', $cliente_log['ClienteLog']['ccm']) ?>
                            <?= $this->Html->tag('label', 'ISS:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente_log['ClienteLog']['iss']) ?>
                        </div>
                        <div class='row-fluid'>
                            <?= $this->Html->tag('label', 'Razão Social:', array('class' => 'span2 title')) ?> <?= $this->Html->tag('label', $cliente_log['ClienteLog']['razao_social']) ?>
                            <?= $this->Html->tag('label', 'Nome Fantasia:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente_log['ClienteLog']['nome_fantasia']) ?>
                        </div>
                        <div class='row-fluid'>
                            <?= $this->Html->tag('label', 'Subtipo:', array('class' => 'span2 title')) ?> <?= $this->Html->tag('label', $cliente_log['ClienteSubTipo']['descricao']) ?>
                            <?= $this->Html->tag('label', 'Corporação:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente_log['Corporacao']['descricao']) ?>
                        </div>
                        <div class='row-fluid'>
                            <?= $this->Html->tag('label', 'Corretora:', array('class' => 'span2 title')) ?> <?= $this->Html->tag('label', $cliente_log['Corretora']['nome']) ?>
                            <?= $this->Html->tag('label', 'Seguradora:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente_log['Seguradora']['nome']) ?>
                        </div>
                        <div class='row-fluid'>
                            <?= $this->Html->tag('label', 'Região:', array('class' => 'span2 title')) ?> <?= $this->Html->tag('label', $cliente_log['EnderecoRegiao']['descricao']) ?>
                            <?= $this->Html->tag('label', 'Faturamento:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente_log['ClienteLog']['regiao_tipo_faturamento'] ? 'TOTAL' : 'PARCIAL') ?>
                            <?= $this->Html->tag('label', 'Ativo:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente_log['ClienteLog']['ativo'] ? 'SIM' : 'NÂO' ); ?>
                            <?= $this->Html->tag('label', 'Ação:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente_log['ClienteLog']['acao_sistema'] == 0 ? 'INSERCAO':($cliente_log['ClienteLog']['acao_sistema'] == 1 ? 'ALTERACAO':'EXCLUSAO')) ?>
                            <?= $this->Html->tag('label', 'Data:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente_log['ClienteLog']['data_inclusao']) ?>
                            <?= $this->Html->tag('label', 'Usuário:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente_log['UsuarioAlteracao']['apelido']) ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>        
        </tbody>
    </table>
</div>

