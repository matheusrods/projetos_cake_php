<table width="500" style="font-family:verdana;">
    <tr>
        <td style="font-size:12px;">
            Prezado Credenciado, boa tarde!,
        	<br /><br />
        </td>
    </tr>
    <tr>
        <td style="font-size:12px;">
            A RH HEALTH visando a busca pela qualidade em seus processos, mantém uma rígida auditoria em suas contas médicas, esperando cada vez mais o cumprimento das nossas rotinas tanto no atendimento quanto no preenchimento e envio da documentação obrigatória.
        </td>
    </tr>

    
        <tr>
        <td style="font-size:12px;">
            Informo que sua NF <strong><?= $dados['nota_fiscal_numero']; ?></strong> no valor de <strong>R$<?= $dados['nota_fiscal_valor']; ?></strong> será paga com glosa conforme detalhado abaixo: 
        </td>
    </tr>
    <tr>
        <td style="font-size:12px;">
            
            <table width="500">
                <tr>
                    <td><strong>Nota Fiscal</strong></td>
                    <td><?= $dados['nota_fiscal_numero']; ?></td>
                </tr>
                <tr>
                    <td><strong>Valor Bruto</strong></td>
                    <td><?= $dados['nota_fiscal_valor']; ?></td>
                </tr>
                <tr>
                    <td><strong>Previsão de Pagamento</strong></td>
                    <td><?= $dados['previsao_pagamento']; ?></td>
                </tr>
                <tr>
                    <td><strong>Valor da Glosa</strong></td>
                    <td><?= $dados['valor_glosa']; ?></td>
                </tr>
                <tr>
                    <td><strong>Motivo</strong></td>
                    <td><?= $dados['motivo']; ?></td>
                </tr>
            </table>

        </td>
    </tr>
    <?php if(isset($dados['detalhamento']) && !empty($dados['detalhamento'])):?>
    <tr>
        <td style="font-size:12px;">
            Detalhamento
            <table style="margin-bottom: 20px; width: 100%; background-color: transparent; border-collapse: collapse; border-spacing: 0; max-width: 100%;">
                <thead>
                    <tr style="font-weight: bold; border: 1px solid #ddd; line-height: 20px; padding: 8px;text-align: left; vertical-align: middle;  background-color: #eee;">
                        <th style="font-weight:bold; font-size: 12px; width: 100px;">Exame</th>
                        <th style="font-weight:bold; font-size: 12px">Quantidade</th>
                        <th style="font-weight:bold; font-size: 12px; width: 100px;">Valor cobrado</th>
                        <th style="font-weight:bold; font-size: 12px; width: 100px;">Tabela IT. Health</th>
                        <th style="font-weight:bold; font-size: 12px; width: 100px;">Diferença Unitária</th>
                        <th style="font-weight:bold; font-size: 12px; width: 100px;">Diferença Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dados['detalhamento'] as $k => $detalhe): ?>
                        <tr style="border: 1px solid #ddd; line-height: 20px; padding: 8px;text-align: left; vertical-align: middle;  background-color: #f9f9f9; font-size: 12px">
                            <td style="font-size: 12px;"><?= $detalhe['exame'];?></td>
                            <td style="font-size: 12px;"><?= $detalhe['quantidade'];?></td>
                            <td style="font-size: 12px;"><?= $detalhe['valor_cobrado'];?></td>
                            <td style="font-size: 12px;"><?= $detalhe['valor_tabela'];?></td>
                            <td style="font-size: 12px;"><?= $detalhe['diferenca_unitaria'];?></td>
                            <td style="font-size: 12px;"><?= $detalhe['diferenca_total'];?></td>
                        </tr>
                        <?php endforeach;?>
                </tbody>
                <tfoot>
                    <tr>
                    <td colspan="3">Total da Glosa - Divergência de valores</td>
                    <td colspan="3">R$ <?= $dados['valor_glosa_divergencia']; ?></td>
                    </tr>
                </tfoot>
            </table>

        </td>
    </tr> 
    <?php endif; ?>   
    <?php if(isset($dados['procedimentos']) && !empty($dados['procedimentos'])):?>
    <tr>
        <td style="font-size:12px;">
            <br>
            <table style="margin-bottom: 20px; width: 100%; background-color: transparent; border-collapse: collapse; border-spacing: 0; max-width: 100%;">
                <thead>
                    <tr style="font-weight: bold; border: 1px solid #ddd; line-height: 20px; padding: 8px;text-align: left; vertical-align: middle;  background-color: #eee;">
                        <th style="font-weight:bold; font-size: 12px; width: 100px;">Empresa</th>
                        <th style="font-weight:bold; font-size: 12px">Pedido</th>
                        <th style="font-weight:bold; font-size: 12px;">Colaborador</th>
                        <th style="font-weight:bold; font-size: 12px; width: 100px;">Data</th>
                        <th style="font-weight:bold; font-size: 12px; width: 100px;">Tipo de Exame</th>
                        <th style="font-weight:bold; font-size: 12px; width: 100px;">Procedimento</th>
                        <th style="font-weight:bold; font-size: 12px; ">Motivo</th>
                        <th style="font-weight:bold; font-size: 12px; width: 100px;">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dados['procedimentos'] as $k => $procedimento): ?>
                        <tr style="border: 1px solid #ddd; line-height: 20px; padding: 8px;text-align: left; vertical-align: middle;  background-color: #f9f9f9; font-size: 12px">
                            <td style="font-size: 12px;"><?= $procedimento['empresa'];?></td>
                            <td style="font-size: 12px;"><?= $procedimento['pedido'];?></td>
                            <td style="font-size: 12px;"><?= $procedimento['colaborador'];?></td>
                            <td style="font-size: 12px;"><?= $procedimento['data'];?></td>
                            <td style="font-size: 12px;"><?= $procedimento['tipo_exame'];?></td>
                            <td style="font-size: 12px;"><?= $procedimento['procedimento'];?></td>
                            <td style="font-size: 12px;"><?= $procedimento['motivo'];?></td>
                            <td style="font-size: 12px;"><?= $procedimento['valor'];?></td>
                        </tr>
                        <?php endforeach;?>
                </tbody>
                <tfoot>
                    <tr>
                    <td colspan="7">Total da Glosa - Documentação</td>
                    <td >R$ <?= $dados['valor_glosa_divergencia']; ?></td>
                    </tr>
                </tfoot>
            </table>

        </td>
    </tr>
    <?php endif;?>   
    <tr>
        <td style="font-size:12px;">
        	<br />
        	Att,<br />
        	<b>Equipe RH Health</b><br />
            <a href="<?= $dados['url_website']; ?>" target="_blank">www.rhhealth.com.br</a><br />
        </td>
    </tr>
</table>