
<table class="table table-striped" style='width:3000px;max-width:none;'>
    <thead>
        <tr>
            <th>Codigo Fornecedor</th>
            <th>Código Documento</th>
            <th>Nome Fantasia</th>           
            <th>Razão Social</th>
            <th>Ativo</th>
            <th>Responsável Administrativo</th>
            <th>Tipo Atendimento</th>
            <th>Banco</th>
            <th>Agência</th>
            <th>Numero Conta</th>
            <th>Tipo Conta</th>
            <th>Favorecido</th>
            <th>Atendente</th>
            <th>Data Contratação</th>
            <th>Data Cancelamento</th>
            <th>Codigo Externo</th>
            <th>Responsável Técnico</th>
            <th>Tipo Conselho</th>
            <th>Numero Conselho</th>
            <th>Uf Conselho</th>
            <th>Tipo Unidade</th>
            <th>Código Fornecedor Fiscal</th>
            <th>Código Documento Real</th>
            <th>Utiliza Sistema Agendamento</th>
            <th>Possibilidade de acesso ao Portal RHHealth</th>
            <th>Fornecedor Interno</th>
            <th>Data Inclusão</th>
            <th>Usuário Inclusão</th>
            <th>Data Alteração</th>
            <th>Usuário Alteração</th>
            <th>Ação</th>
        </tr>
    </thead>
    <tbody>
        <?php $total = 0 ?>
        <?php foreach($dados as $key => $value) : ?>
            <?php $total += 1 ?>
            <tr>
                <td><?= $value['FornecedorLog']['codigo_fornecedor']; ?></td>
                <td><?= $buonny->documento($value['FornecedorLog']['codigo_documento']); ?></td>
                <td><?= $value['FornecedorLog']['nome']; ?></td>
                <td><?= $value['FornecedorLog']['razao_social']; ?></td>
                <td><?= (!empty($value['FornecedorLog']['ativo'])) ? ($value['FornecedorLog']['ativo'] == 1) ? 'Ativo' : 'Inativo' : ''; ?></td>
                <td><?= $value['FornecedorLog']['responsavel_administrativo']; ?></td>
                <td><?= $value['FornecedorLog']['tipo_atendimento']; ?></td>
                <td><?= (!empty($value['Banco']['codigo_banco'])) ? $value['Banco']['codigo_banco']." - ".$value['Banco']['descricao'] : ''; ?></td>
                <td><?= $value['FornecedorLog']['agencia']; ?></td>
                <td><?= $value['FornecedorLog']['numero_conta']; ?></td>
                <td><?= ($value['FornecedorLog']['tipo_conta'] == 1) ? 'Conta Corrente' : 'Conta Poupança'; ?></td>
                <td><?= $value['FornecedorLog']['favorecido']; ?></td>
                <td><?= $value['FornecedorLog']['atendente']; ?></td>
                <td><?= AppModel::dbDateToDate($value['FornecedorLog']['data_contratacao']); ?></td>
                <td><?= AppModel::dbDateToDate($value['FornecedorLog']['data_cancelamento']); ?></td>
                <td><?= $value['FornecedorLog']['codigo_soc']; ?></td>
                <td><?= $value['FornecedorLog']['responsavel_tecnico']; ?></td>
                <td><?= $value['FornecedorLog']['codigo_conselho_profissional']; ?></td>
                <td><?= $value['FornecedorLog']['responsavel_tecnico_conselho_numero']; ?></td>
                <td><?= $value['FornecedorLog']['responsavel_tecnico_conselho_uf']; ?></td>
                <td><?= (!empty($value['FornecedorLog']['tipo_unidade'])) ? ($value['FornecedorLog']['tipo_unidade'] == 'F') ? 'Fiscal' : 'Operacional' : ''; ?></td>

                <td><?= $value['FornecedorLog']['codigo_fornecedor_fiscal']; ?></td>
                <td><?= $value['FornecedorLog']['codigo_documento_real']; ?></td>
                <td><?= ($value['FornecedorLog']['utiliza_sistema_agendamento'] == 1) ? 'Sim' : 'Não'; ?></td>

                <td><?= ($value['FornecedorLog']['acesso_portal'] == 1) ? 'Sim' : 'Não'; ?></td>
                <td><?= ($value['FornecedorLog']['interno'] == 1) ? 'Sim' : 'Não'; ?></td>
                
                <!-- exames_local_unico                
                contrato_ativo
                dia_do_pagamento
                disponivel_para_todas_as_empresas
                especialidades
                tipo_de_pagamento
                texto_livre
                codigo_status_contrato_fornecedor -->



                <td><?= AppModel::dbDateToDate($value['FornecedorLog']['data_inclusao']); ?></td>
                <td><?= $value['UsuarioInclusao']['nome']; ?></td>
                <td><?= AppModel::dbDateToDate($value['FornecedorLog']['data_alteracao']); ?></td>
                <td><?= $value['UsuarioAlteracao']['nome']; ?></td>
                <td><?= (!empty($value['FornecedorLog']['acao_sistema'])) ? $acoes[$value['FornecedorLog']['acao_sistema']] : ''; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td><?= $total ?></td>
            <td colspan="35"></td>
        </tr>
    </tfoot>
</table>