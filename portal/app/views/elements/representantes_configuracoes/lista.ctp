<table>
    <thead>
        <tr>
            <th>Representante</th>
            <th>Produto</th>
            <th>Serviço</th>
            <th>Vigência</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($configuracoes as $configuracao) : ?>
        <?php $tooltip = "".
                "<table>".
                    "<thead>".
                        "<tr>".
                            "<td class='numeric'>Tipo Soma</td>".
                            "<td class='numeric'>% Mesma Região</td>".
                            "<td class='numeric'>% Outra Região</td>".
                            "<td class='numeric'>Qtd. de</td>".
                            "<td class='numeric'>Qtd. até</td>";
                            if ($configuracao['RepresentanteConfiguracao']['faturamento_ate'] > 0):
                                $tooltip .= "<td class='numeric'>Faturamento de</td>".
                                            "<td class='numeric'>Faturamento até</td>";
                            endif;
                            if ($configuracao['RepresentanteConfiguracao']['valor_unitario_ate'] > 0):
                                $tooltip .= "<td class='numeric'>Vr.Unitário de</td>".
                                            "<td class='numeric'>Vr.Unitário até</td>";
                            endif;
                        $tooltip .= "</tr>".
                    "</thead>".
                        "<tr>".
                            "<td class='numeric'>".(($configuracao['RepresentanteConfiguracao']['faturamento_ate'] > 0) ? "TOTAL FATURAMENTO" : "VALOR UNITÁRIO")."</td>".
                            "<td class='numeric'>".$buonny->moeda($configuracao['RepresentanteConfiguracao']['porcentagem_mesma_regiao'], array('edit' => true))."</td>".
                            "<td class='numeric'>".$buonny->moeda($configuracao['RepresentanteConfiguracao']['porcentagem_outra_regiao'], array('edit' => true))."</td>".
                            "<td class='numeric'>".$configuracao['RepresentanteConfiguracao']['quantidade_de']."</td>".
                            "<td class='numeric'>".$configuracao['RepresentanteConfiguracao']['quantidade_ate']."</td>";
                            if ($configuracao['RepresentanteConfiguracao']['faturamento_ate'] > 0):
                                $tooltip .= "<td class='numeric'>".$buonny->moeda($configuracao['RepresentanteConfiguracao']['faturamento_de'])."</td>".
                                            "<td class='numeric'>".$buonny->moeda($configuracao['RepresentanteConfiguracao']['faturamento_ate'])."</td>";
                            endif;
                            if ($configuracao['RepresentanteConfiguracao']['valor_unitario_ate'] > 0):
                                $tooltip .= "<td class='numeric'>".$buonny->moeda($configuracao['RepresentanteConfiguracao']['valor_unitario_de'])."</td>".
                                            "<td class='numeric'>".$buonny->moeda($configuracao['RepresentanteConfiguracao']['valor_unitario_ate'])."</td>";
                            endif;
                        $tooltip .= "</tr>".
                "</table>";
        ?>
        <tr title = "<?= nl2br($tooltip) ?>">
            <td><?= $configuracao['Representante']['nome'] ?></td>
            <td><?= $configuracao['Produto']['descricao'] != null ? $configuracao['Produto']['descricao'] : 'Todos produtos' ?></td>
            <td><?= $configuracao['Servico']['descricao'] != null ? $configuracao['Servico']['descricao'] : 'Todos serviços' ?></td>
            <td><?php echo substr($configuracao['RepresentanteConfiguracao']['vigencia'], 0, 10) ?></td>
            <td><?php
            $cod_representante = $configuracao['RepresentanteConfiguracao']['codigo'];
             echo $html->link("Excluir", "/representantes_configuracoes/excluir/$cod_representante");
            ?></td>
        </tr>        
    <?php endforeach; ?>
    </tbody>
</table>
<?= $javascript->link('jquery-tooltip/jquery.tooltip.js') ?>
<?= $html->css('jquery.tooltip.css') ?>
<?= $javascript->codeBlock('jQuery("tr").tooltip()') ?>