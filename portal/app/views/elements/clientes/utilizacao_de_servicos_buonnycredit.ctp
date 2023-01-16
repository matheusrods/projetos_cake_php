<h5>BuonnyCredit</h5>
<div class='row-fluid' style='overflow-x:auto'>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th class='input-small'><?= $this->Html->link('Cód', 'javascript:void(0)') ?></th>
                <th><?= $this->Html->link('Razão Social', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Vr.à Pagar', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Desconto', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Quantidade', 'javascript:void(0)') ?></th>
                <th class='input-mini'></th>
            </tr>
        </thead>
        <tbody>
            <?php $total_clientes = 0 ?>
            <?php $total_qtd_cobrado = 0 ?>
            <?php $total_valor_a_pagar = 0 ?>
            <?php $total_valor_desconto = 0 ?>
            <?php if ($utilizacoes_bcredit): ?>
                <?php foreach($utilizacoes_bcredit as $utilizacao): ?>
                    <?php $total_clientes += 1 ?>
                    <?php $total_qtd_cobrado += $utilizacao[0]['qtd_cobrado'] ?>
                    <?php $total_valor_a_pagar += $utilizacao[0]['valor_a_pagar'] ?>
                    <?php $total_valor_desconto += $utilizacao[0]['valor_desconto'] ?>
                    <?php $alerta = (empty($utilizacao['0']['codigo_endereco']) ? "<span title='Sem Endereço Comercial' class='icon-exclamation-sign'> </span>": "") ?>
                    <?php $falha = (!empty($alerta) || $utilizacao[0]['valor_a_pagar']<0) ? 'falha' : '' ?>
                    <?php $razao_social = (!empty($alerta) && $tem_permissao_edicao_cliente) ? $this->Html->link($utilizacao['0']['razao_social'], array('controller' => 'clientes', 'action' => 'editar', $utilizacao['0']['codigo_cliente_pagador'])) : $utilizacao['0']['razao_social'] ?>
                    <tr class="<?= $falha ?>" >
                        <td><?= $this->Html->link($utilizacao['0']['codigo_cliente_pagador'], "javascript:utilizacao_de_servicos_bcredit_filhos('{$utilizacao['0']['codigo_cliente_pagador']}', '{$this->data['Cliente']['data_inicial']}', '{$this->data['Cliente']['data_final']}')").$alerta ?></td>
                        <td><?= $razao_social ?></td>
                        <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_a_pagar'], array('nozero' => true)) ?></td>
                        <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_desconto'], array('nozero' => true)) ?></td>
                        <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['qtd_cobrado'], array('nozero' => true, 'places' => 0)) ?></td>
                        <td><?= (!empty($alerta) || $utilizacao[0]['valor_a_pagar']<0) ? 'falha' : '' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="numeric"><?= $total_clientes; ?></td>
                <td></td>
                <td class="numeric"><?= ($total_valor_a_pagar != 0) ? $this->Buonny->moeda($total_valor_a_pagar):''; ?></td>
                <td class="numeric"><?= ($total_valor_desconto != 0) ? $this->Buonny->moeda($total_valor_desconto):''; ?></td>
                <td class="numeric"><?= ($total_qtd_cobrado != 0) ? $this->Buonny->moeda($total_qtd_cobrado, array('places' => 0)):''; ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>
<?php if (!empty($utilizacoes_bcredit)): ?>
    <?php echo $this->Javascript->codeBlock("
    function utilizacao_de_servicos_bcredit_filhos( codigo_cliente, data_inicial, data_final ) {   
        var form = document.createElement('form');
        var form_id = ('formresult' + Math.random()).replace('.','');
        form.setAttribute('method', 'post');
        form.setAttribute('action', '/portal/clientes/utilizacao_de_servicos_bcredit_filhos/1');
        form.setAttribute('target', form_id);
        field = document.createElement('input');
        field.setAttribute('name', 'data[Cliente][codigo_cliente]');
        field.setAttribute('value', codigo_cliente);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[Cliente][data_inicial]');
        field.setAttribute('value', data_inicial);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[Cliente][data_final]');
        field.setAttribute('value', data_final);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        document.body.appendChild(form);
        var janela = window_sizes();
        window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-100)+',width='+(janela.width-80).toString()+',resizable=yes,toolbar=no,status=no');
        form.submit();
    }
    jQuery(document).ready(function() { 
        // jQuery('table.table').tablesorter();

    })"); ?>
<?php endif ?>