
<table class="table table-striped" style='width:3000px;max-width:none;'>
    <thead>
        <tr>
            <th>Código do Funcionário</th>
            <th>Nome</th>
            <th>Data Nascimento</th>
            <th>RG</th>
            <th>Orgão</th>
            <th>RG UF</th>
            <th>CPF</th>
            <th>Sexo</th>
            <th>CTPS</th>
            <th>CTPS Serie</th>
            <th>CTPS UF</th>
            <th>CTPS Data Emissão</th>
            <th>GFIP</th>
            <th>CNS</th>
            <th>E-mail</th>
            <th>Estado Civil</th>
            <th>Deficiência</th>
            <th>Nome da Mãe</th>
            <th>Data Inclusão Funcionário</th>
            <th>Data Alteração Funcionário</th>
            <th>Usuário Alteração Funcionário</th>
            <th>Código cliente matrícula</th>
            <th>Nome Cliente matrícula</th>
            <th>Data Admissão matrícula</th>
            <th>Data Demissão matrícula</th>
            <th>Status matrícula</th>
            <th>Matrícula </th>
            <th>Data inclusão matrícula </th>
            <th>Usuário Inclusão matrícula</th>
            <th>Data Alteração matrícula</th>
            <th>Usuário Alteração matrícula</th>
            <th>Código cliente Alocação</th>
            <th>Nome Cliente alocação</th>
            <th>Setor</th>
            <th>Cargo</th>
            <th>Data inicio função</th>
            <th>Data fim função</th>
            <th>Código cliente tomador referência função</th>
            <th>Nome cliente tomador referência função</th>
            <th>Data inclusão função</th>
            <th>Usuário inclusão função</th>
            <th>Data alteração função</th>
            <th>Usuário alteração função</th>
            <th>Ação</th>
        </tr>
    </thead>
    <tbody>
        <?php $total = 0 ?>
        <?php foreach($dados as $key => $value) : ?>
            <?php $total += 1 ?>
            <tr>
                <td><?= $value[0]['codigo_funcionario']; ?></td>
                <td><?= $value[0]['nome']; ?></td>
                <td><?= AppModel::dbDateToDate($value[0]['data_nascimento']); ?></td>
                <td><?= $value[0]['rg']; ?></td>
                <td><?= $value[0]['orgao']; ?></td>
                <td><?= $value[0]['rg_uf']; ?></td>
                <td><?= $buonny->documento($value[0]['cpf']); ?></td>
                <td><?= ($value[0]['sexo'] == 'F' ? 'Feminino' : 'Masculino'); ?></td>
                <td><?= $value[0]['ctps']; ?></td>
                <td><?= $value[0]['ctps_serie']; ?></td>
                <td><?= $value[0]['ctps_uf']; ?></td>
                <td><?= AppModel::dbDateToDate($value[0]['ctps_data_emissao']); ?></td>
                <td><?= $value[0]['gfip']; ?></td>
                <td><?= $value[0]['cns']; ?></td>
                <td><?= $value[0]['email']; ?></td>
                <td>
                    <?php                        
                        switch ($value[0]['estado_civil']) {
                            case '1':
                                print 'Solteiro';
                                break;
                            case '2':
                                print 'Casado';
                                break;
                            case '3':
                                print 'Separado';
                                break;
                            case '4':
                                print 'Divorciado';
                                break;
                            case '5':
                                print 'Viúvo';
                                break;
                            case '6':
                                print 'Outros';
                                break;                            
                        };
                    ?>
                        
                </td>
                <td><?= ($value[0]['deficiencia'] == 1 ? 'Sim' : 'Não'); ?></td>
                <td><?= $value[0]['nome_mae']; ?></td>
                <td><?= AppModel::dbDateToDate($value[0]['data_inclusao']); ?></td>
                <td><?= AppModel::dbDateToDate($value[0]['data_alteracao']); ?></td>
                <td><?= $value[0]['nome_usuario_inclusao']; ?></td>
                <td><?= $value[0]['codigo_cliente_funcionario']; ?></td>
                <td><?= $value[0]['cliente_matricula_nome']; ?></td>
                <td><?= AppModel::dbDateToDate($value[0]['data_admissao']); ?></td>
                <td><?= AppModel::dbDateToDate($value[0]['data_demissao']); ?></td>
                <td>
                    <?php
                     switch ($value[0]['status_matricula']) {
                        case '1':
                             print 'Ativo';
                             break;
                        case '2':
                             print 'Férias';
                             break;
                        case '3':
                             print 'Afastado';
                             break;
                        default:
                            print 'Inativo';
                             break;
                     };                     
                     ?>
                </td>
                <td><?= $value[0]['matricula']; ?></td>
                <td><?= AppModel::dbDateToDate($value[0]['data_inclusao_matricula']); ?></td>
                <td><?= $value[0]['nome_usuario_inclusao_cfl']; ?></td>
                <td><?= AppModel::dbDateToDate($value[0]['data_alteracao_matricula']); ?></td>
                <td><?= $value[0]['nome_usuario_alteracao_cfl']; ?></td>
                <td><?= $value[0]['codiog_fscl']; ?></td>
                <td><?= $value[0]['cliente_alocacao_nome']; ?></td>
                <td><?= $value[0]['setor']; ?></td>
                <td><?= $value[0]['cargo']; ?></td>
                <td><?= AppModel::dbDateToDate($value[0]['data_inicio_funcao']); ?></td>
                <td><?= AppModel::dbDateToDate($value[0]['data_fim_funcao']); ?></td>
                <td><?= $value[0]['codigo_tomador']; ?></td>
                <td><?= $value[0]['tomador_nome_fantasia']; ?></td>
                <td><?= AppModel::dbDateToDate($value[0]['data_inclusao_funcao']); ?></td>
                <td><?= $value[0]['nome_usuario_inclusao_fscl']; ?></td>
                <td><?= AppModel::dbDateToDate($value[0]['data_alteracao_funcao']); ?></td>
                <td><?= $value[0]['nome_usuario_alteracao_fscl']; ?></td>
                <td><?= (!empty($value[0]['acao'])) ? $acoes[$value[0]['acao']] : '';; ?></td>
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