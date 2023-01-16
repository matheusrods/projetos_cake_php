<table class='table table-bordered'>
    <thead>
        <tr>
            <td>Produto</td>
            <td>Limite Cheques</td>
            <td>Status Última Pesquisa (Profissional)</td>
            <td>Status Última Pesquisa (Proprietário)</td>
            <td>Valor Serasa</td>
            <td>Tempo de Espera</td>
            <td>Ver Profissional Negativado</td>
            <td>Ver Validade CNH</td>
            <td>Ver Veículo Ocorrência</td>
            <td>Quantidade de viagens (Histórico)</td>
            <td>Quantidade de meses (Histórico)</td>
            <td>Quantidade de viagens (Ren. Automática)</td>
            <td>Quantidade de meses (Ren. Automática)</td>
            <td>Quantidade de meses (agregado)</td>
            <td></td>
        </tr>
    </thead>
    <?php foreach ($configuracoes as $configuracao): ?>
        <tr>
            <td><?= $configuracao['PesquisaConfiguracao']['codigo_produto'] == 1 ? 'Standard' : ($configuracao['PesquisaConfiguracao']['codigo_produto'] == 2 ? 'Plus' : 'Scorecard' ) ?></td>
            <td class='numeric'><?= $configuracao['PesquisaConfiguracao']['quantidade_cheque'] ?></td>
            <td align='center'><?= $configuracao['Status']['descricao'] ?></td>
            <td align='center'><?= $configuracao['PesquisaConfiguracao']['codigo_status_anterior_proprietario'] ? 'Sim' : 'Não' ?></td>
            <td class='numeric'><?= $buonny->moeda($configuracao['PesquisaConfiguracao']['valor_serasa']) ?></td>
            <td class='numeric'><?= $configuracao['PesquisaConfiguracao']['quantidade_minutos_espera_envio_email'] ?></td>
            <td align='center'><?= $configuracao['PesquisaConfiguracao']['verificar_profissional_negativado'] ? 'Sim' : 'Não' ?></td>
            <td align='center'><?= $configuracao['PesquisaConfiguracao']['verificar_validade_cnh'] ? 'Sim' : 'Não' ?></td>
            <td align='center'><?= $configuracao['PesquisaConfiguracao']['verificar_veiculo_ocorrencia'] ? 'Sim' : 'Não' ?></td>
            <td align='center'><?= $configuracao['PesquisaConfiguracao']['historico_quantidade_viagem'] ?></td>
            <td align='center'><?= $configuracao['PesquisaConfiguracao']['historico_quantidade_meses'] ?></td>
            <td align='center'><?php echo $configuracao['PesquisaConfiguracao']['historico_quantidade_viagem_ren_atu'] ?></td>
            <td align='center'><?php echo $configuracao['PesquisaConfiguracao']['historico_quantidade_meses_ren_atu'] ?></td>
            <td align='center'><?php echo $configuracao['PesquisaConfiguracao']['historico_quantidade_meses_agregado'] ?></td>
            <td><?= $html->link('', array('action' => 'atualiza', $configuracao['PesquisaConfiguracao']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?></td>
        </tr>
    <?php endforeach ?>
</table>