<table class="table table-striped" style='width:3000px;max-width:none;'>
    <thead>
        <tr>
            <th>Código Cat</th>
            <th>Unidade</th>
            <th>Funcionário</th>
            <th>CPF</th>
            <th>Identificação do evento</th>
            <th>Número do recibo</th>
            <th>Motivo da retificação</th>
            <th>Emitente</th>
            <th>Tipo de CAT</th>
            <th>Motivo Emissão CAT</th>       
            <th>Remuneração mensal</th>
            <th>Filiação à Previdência Social</th>
            <th>Aposentado</th>
            <th>Áreas</th>
            <th>Data do acidente</th>
            <th>Hora do acidente</th>
            <th>Após quantas horas de trabalho?</th>
            <th>Tipo</th>
            <th>Houve afastamento?</th>
            <th>Último dia trabalhado</th>
            <th>Local do acidente</th>
            <th>Especificação do local do acidente</th>
            <th>CNPJ</th>
            <th>UF</th>
            <th>Parte do corpo</th>
            <th>Parte do corpo atingida</th>
            <th>Agente causador</th>
            <th>Descrição da situação geradora do acidente ou doença</th>
            <th>Houve registro policial?</th>
            <th>Houve morte?</th>
            <th>Data Óbito</th>
            <th>Observação CAT</th>
            <th>Cep acidente</th>
            <th>Código País</th>
            <th>Tipo de Inscrição</th>
            <th>CAEPF</th>
            <th>CNO</th>
            <th>Endereço acidente</th>
            <th>Número acidente</th>
            <th>Complemento acidente</th>
            <th>Bairro acidente</th>
            <th>Cidade acidente</th>
            <th>Estado acidente</th>
            <th>Nome Testemunha 1</th>
            <th>Endereço</th>
            <th>Número</th>
            <th>Complemento</th>
            <th>Bairro</th>
            <th>CEP</th>
            <th>Cidade</th>
            <th>Estado</th>
            <th>Telefone</th>
            <th>Nome Testemunha 2</th>
            <th>Endereço</th>
            <th>Número</th>
            <th>Complemento</th>
            <th>Bairro</th>
            <th>CEP</th>
            <th>Cidade</th>
            <th>Estado</th>
            <th>Telefone</th>
            <th>Local</th>
            <th>Data</th>
            <th>CNES (Cadastro Nacional de Estabelecimento de Saúde)</th>
            <th>Data Atendimento</th>
            <th>Hora Atendimento</th>
            <th>Indicação de Internação</th>
            <th>Duração estimada do Tratamento (em dias)</th>
            <th>Natureza da Lesão</th>
            <th>Descrição Complementar Lesão</th>
            <th>Diagnóstico Provável</th>
            <th>CID10</th>
            <th>Observação</th>
            <th>Médico</th>
            <th>CRM</th>
            <th>UF</th>
            <th>Nome do Médico</th>
            <th>CPF do Médico</th>
            <th>Data CAT Origem</th>
            <th>Número CAT Origem</th>
            <th>Ação Sistema</th>
        </tr>
    </thead>
    <tbody>
        <?php $total = 0 ?>
        <?php foreach($dados as $key => $value) : ?>
            <?php $total += 1 ?>
            <?//= debug($value); ?>
            <tr>
                <td><?= $value['CatLog']['codigo_cat']; ?></td>
                <td><?= $value['Cliente']['razao_social']; ?></td>
                <td><?= $value['Funcionario']['nome']; ?></td>
                <td><?= empty($value['Funcionario']['cpf']) ? '' : AppModel::formataCpf($value['Funcionario']['cpf']); ?></td>                
                <td><?= $value[0]['id_evento']; ?></td>
                <td><?= $value[0]['numero_recibo']; ?></td>
                <td><?= $value[0]['motivo_retificacao']; ?></td> 
                <td><?= $value[0]['emitente']; ?></td>
                <td><?= $value[0]['tipo_cat']; ?></td>
                <td><?= $value[0]['motivo_emissao']; ?></td>
                <td><?= empty($value['CatLog']['remuneracao_mensal']) ? '' : $this->Ithealth->moeda($value['CatLog']['remuneracao_mensal'], array('nozero' => true)); ?></td>
                <td><?= $value[0]['filiacao_previdencia']; ?></td>
                <td><?= $value[0]['aposentado']; ?></td>
                <td><?= $value[0]['areas']; ?></td>
                <td><?= $value[0]['data_acidente']; ?></td>
                <td><?= $value[0]['hr_acidente']; ?></td>
                <td><?= $value[0]['apos_horas_trab']; ?></td>
                <td><?= $value[0]['tipo']; ?></td>
                <td><?= $value[0]['h_afastamento']; ?></td>
                <td><?= $value[0]['ultimo_dia']; ?></td>
                <td><?= $value[0]['loca_acidente']; ?></td>
                <td><?= $value[0]['espec_lc_acidente']; ?></td>
                <td><?= Comum::formatarDocumento($value[0]['cnpj']); ?></td>
                <td><?= $value[0]['uf']; ?></td>
                <td><?= $value[0]['parte_corpo']; ?></td>
                <td><?= $value[0]['parte_atingida']; ?></td>
                <td><?= $value[0]['agente_causador']; ?></td>
                <td><?= $value[0]['descricao_acidente_gerador']; ?></td>
                <td><?= $value[0]['registro_policial']; ?></td>
                <td><?= $value[0]['morte']; ?></td>
                <td><?= $value[0]['data_obito']; ?></td>
                <td><?= $value[0]['observacao_cat']; ?></td>
                <td><?= $value[0]['cep_acidentado']; ?></td>
                <td><?= $value[0]['codigo_pais']; ?></td>
                <td><?= $value[0]['tipo_inscricao']; ?></td>
                <td><?= $value[0]['caepf']; ?></td>
                <td><?= $value[0]['cno']; ?></td>
                <td><?= $value[0]['endereco_acidente']; ?></td>
                <td><?= $value[0]['numero_acidente']; ?></td>
                <td><?= $value[0]['complemento_acidente']; ?></td>
                <td><?= $value[0]['bairro_acidente']; ?></td>
                <td><?= $value[0]['cidade_acidente']; ?></td>
                <td><?= $value[0]['estado_acidente']; ?></td>
                <td><?= $value[0]['nome_test1']; ?></td>
                <td><?= $value[0]['endereco_test1']; ?></td>
                <td><?= $value[0]['numero_test1']; ?></td>
                <td><?= $value[0]['complemento_test1']; ?></td>
                <td><?= $value[0]['bairro_test1']; ?></td>
                <td><?= $value[0]['cep_test1']; ?></td>
                <td><?= $value[0]['cidade_test1']; ?></td>
                <td><?= $value[0]['estado_test1']; ?></td>
                <td><?= $value[0]['telefone_test1']; ?></td>
                <td><?= $value[0]['nome_test2']; ?></td>
                <td><?= $value[0]['endereco_test2']; ?></td>
                <td><?= $value[0]['numero_test2']; ?></td>
                <td><?= $value[0]['complemento_test2']; ?></td>
                <td><?= $value[0]['bairro_test2']; ?></td>
                <td><?= $value[0]['cep_test2']; ?></td>
                <td><?= $value[0]['cidade_test2']; ?></td>
                <td><?= $value[0]['estado_test2']; ?></td>
                <td><?= $value[0]['telefone_test2']; ?></td>
                <td><?= $value[0]['local']; ?></td>
                <td><?= $value[0]['data']; ?></td>
                <td><?= $value[0]['cnes']; ?></td>
                <td><?= $value[0]['data_atendimento_atestado']; ?></td>
                <td><?= $value[0]['hora_atendimento']; ?></td>
                <td><?= $value[0]['indic_internacao']; ?></td>
                <td><?= $value[0]['duracao_tratamento']; ?></td>
                <td><?= $value[0]['natureza_lesao']; ?></td>
                <td><?= $value[0]['descricao_complementar_lesao']; ?></td>
                <td><?= $value[0]['diagnostico_provavel']; ?></td>
                <td><?= $value[0]['cid10']; ?></td>
                <td><?= $value[0]['obs_atestado']; ?></td>
                <td><?= $value[0]['codigo_medico']; ?></td>
                <td><?= $value[0]['crm']; ?></td>
                <td><?= $value[0]['uf_medico']; ?></td>
                <td><?= $value[0]['nome_medico']; ?></td>
                 <td><?= empty($value[0]['cpf_medico']) ? '' : AppModel::formataCpf($value[0]['cpf_medico']); ?></td>
                <td><?= $value[0]['data_cat_origem']; ?></td>
                <td><?= $value[0]['numero_cat_origem']; ?></td>
                <td><?= $value[0]['acao_sistema']; ?></td>
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