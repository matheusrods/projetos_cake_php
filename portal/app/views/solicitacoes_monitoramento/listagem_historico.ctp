<table id="listaOcorrencias" class="table table-striped">
    <thead>
        <tr>
            <th class='input-small' title="Código SM">SM</th>
            <th title="Cliente">Cliente</th>
            <th class='input-medium' title="Operador">Operador</th>
            <th class='input-medium' title="Operação">Operação</th>
            <th class='input-medium' title="Equipamento">Tecnologia</th>
        </tr>
    </thead>
    <tbody>
        <?php $linhas = 0; ?>
        <?php if (count($solicitacoes_monitoramento_historico) > 0): ?>
            <?php foreach ($solicitacoes_monitoramento_historico as $solicitacao_monitoramento_historico) : ?>
                <tr>
                    <td>
                        <?= 
                            $this->Html->link( $solicitacao_monitoramento_historico['EstatisticaSm']['codigo_sm'], 'javascript:void(0)', array( 'onclick' => "consulta_sm('{$solicitacao_monitoramento_historico['EstatisticaSm']['codigo_sm']}')" )) 
                        ?>
                    </td>
                    <td><?= $solicitacao_monitoramento_historico['ClientEmpresa']['raz_social'] ?></td>
                    <td><?= $solicitacao_monitoramento_historico['Funcionario']['apelido'] ?></td>
                    <td><?= $solicitacao_monitoramento_historico['OperacaoMonitora']['descricao'] ?></td>
                    <td><?= $solicitacao_monitoramento_historico['Equipamento']['descricao'] ?></td>
                </tr>
                <?php $linhas++ ?>
            <?php endforeach ?>
        <?php endif ?>
    </tbody>
     <tfoot>                        
            <td colspan="5"><div class="actionbar-right"><strong>Total:</strong> <?= $linhas ?></td>
      </tfoot>
</table>
<?php echo $this->Javascript->codeBlock("
    $(document).ajaxStart(function() {
        var div = jQuery('div.lista');
        bloquearDiv(div);
    }).ajaxComplete(function(e, xmlhttprequest, ajaxoptions) {
        var div = jQuery('div.lista');
        div.unblock();
    });
")
?>