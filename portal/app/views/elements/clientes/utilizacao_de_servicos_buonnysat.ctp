<h5>BuonnySat</h5> 
<div class='row-fluid'>
    <table class="table table-striped table-bordered" style='width:2500px;max-width:none'>
        <thead>
            <tr>
                <th class='input-small'><?= $this->Html->link('Cód', 'javascript:void(0)') ?></th>
                <th><?= $this->Html->link('Razão Social', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Vr.à Pagar', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Vr.Prêmio Mínimo', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Desconto', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Vr.Determinado', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Qtd.Frota', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Vr.Frota', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Qtd.Placa', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Vr.Placa', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Qtd.Dia', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Vr.Dia', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Qtd.Km', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Vr.Km', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Qtd.Sm Monitorada', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Vr.Sm Monitorada', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Qtd.Sm Tele', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Vr.Sm Tele', 'javascript:void(0)') ?></th>
                <th class='input-mini'></th>
            </tr>
        </thead>
        <tbody>
            <?php $total_clientes = 0 ?>
            <?php $total_valor_a_pagar = 0 ?>
            <?php $total_valor_desconto = 0 ?>
            <?php $total_determinado = 0 ?>
            <?php $total_qtd_frota = 0 ?>
            <?php $total_valor_frota = 0 ?>
            <?php $total_qtd_placa_avulsa = 0 ?>
            <?php $total_valor_placa_avulsa = 0 ?>
            <?php $total_qtd_dia = 0 ?>
            <?php $total_valor_dia = 0 ?>
            <?php $total_qtd_km = 0 ?>
            <?php $total_valor_km = 0 ?>
            <?php $total_qtd_sm_monitorada = 0 ?>
            <?php $total_valor_sm_monitorada = 0 ?>
            <?php $total_qtd_sm_telemonitorada = 0 ?>
            <?php $total_valor_sm_telemonitorada = 0 ?>
            <?php if ($utilizacoes): ?>
                <?php foreach($utilizacoes as $utilizacao): ?>
                    <?php $total_clientes += 1 ?>
                    <?php $total_valor_a_pagar += $utilizacao[0]['valor_a_pagar'] ?>
                    <?php $total_valor_desconto += $utilizacao[0]['valor_desconto'] ?>
                    <?php $total_determinado += $utilizacao[0]['ValDeterminado'] ?>
                    <?php $total_qtd_frota += $utilizacao[0]['qtd_frota'] ?>
                    <?php $total_valor_frota += $utilizacao[0]['valor_frota'] ?>
                    <?php $total_qtd_placa_avulsa += $utilizacao[0]['qtd_placa_avulsa'] ?>
                    <?php $total_valor_placa_avulsa += $utilizacao[0]['valor_placa_avulsa'] ?>
                    <?php $total_qtd_dia += $utilizacao[0]['qtd_dia'] ?>
                    <?php $total_valor_dia += $utilizacao[0]['valor_dia'] ?>
                    <?php $total_qtd_km += $utilizacao[0]['qtd_km'] ?>
                    <?php $total_valor_km += $utilizacao[0]['valor_km'] ?>
                    <?php $total_qtd_sm_monitorada += $utilizacao[0]['qtd_sm_monitorada'] ?>
                    <?php $total_valor_sm_monitorada += $utilizacao[0]['valor_sm_monitorada'] ?>
                    <?php $total_qtd_sm_telemonitorada += $utilizacao[0]['qtd_sm_telemonitorada'] ?>
                    <?php $total_valor_sm_telemonitorada += $utilizacao[0]['valor_sm_telemonitorada'] ?>
                    <?php $alerta = (empty($utilizacao['0']['codigo_endereco']) ? "<span title='Sem Endereço Comercial' class='icon-exclamation-sign'> </span>": "") ?>
                    <?php $falha = (!empty($alerta) || $utilizacao[0]['valor_a_pagar']<0) ? 'falha' : '' ?>
                    <?php $razao_social = (!empty($alerta) && $tem_permissao_edicao_cliente) ? $this->Html->link($utilizacao['0']['razao_social'], array('controller' => 'clientes', 'action' => 'editar', $utilizacao['0']['cliente_pagador'])) : $utilizacao['0']['razao_social'] ?>
                    <tr class="<?= $falha ?>" >
                        <td><?= $this->Html->link($utilizacao['0']['cliente_pagador'], "javascript:utilizacao_de_servicos_filhos('{$utilizacao['0']['cliente_pagador']}', '{$this->data['Cliente']['data_inicial']}', '{$this->data['Cliente']['data_final']}')").$alerta ?></td>
                        <td><?= $razao_social ?></td>
                        <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_a_pagar'], array('nozero' => true)) ?></td>
						<td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_premio_minimo'], array('nozero' => true)) ?></td>
                        <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_desconto'], array('nozero' => true)) ?></td>
                        <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['ValDeterminado'], array('nozero' => true)) ?></td>
                        <td class='numeric'><?= (($utilizacao[0]['valor_frota']) == 0) ? '' : $this->Html->link($this->Buonny->moeda($utilizacao[0]['qtd_frota'], array('nozero' => true, 'places' => 0)), 'javascript:void(0)', array('onclick' => "visualizar_relatorio_frota('{$utilizacao['0']['cliente_pagador']}', '{$this->data['Cliente']['data_inicial']}', '{$this->data['Cliente']['data_final']}')")); ?></td>
                        <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_frota'], array('nozero' => true)) ?></td>
                        <td class='numeric'><?= (($utilizacao[0]['valor_placa_avulsa']) == 0) ? '' : $this->Html->link($this->Buonny->moeda($utilizacao[0]['qtd_placa_avulsa'], array('nozero' => true, 'places' => 0)), 'javascript:void(0)', array('onclick' => "visualizar_relatorio_placas('{$utilizacao['0']['cliente_pagador']}', '{$this->data['Cliente']['data_inicial']}', '{$this->data['Cliente']['data_final']}')")); ?></td>
                        <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_placa_avulsa'], array('nozero' => true)) ?></td>
                        <td class='numeric'><?= (($utilizacao[0]['valor_dia']) == 0) ? '' : $this->Html->link($this->Buonny->moeda($utilizacao[0]['qtd_dia'], array('nozero' => true, 'places' => 0)), 'javascript:void(0)', array('onclick' => "visualizar_relatorio_sm('{$utilizacao['0']['cliente_pagador']}', '{$this->data['Cliente']['data_inicial']}', '{$this->data['Cliente']['data_final']}', null)")); ?></td>
                        <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_dia'], array('nozero' => true)) ?></td>
                        <td class='numeric'><?= (($utilizacao[0]['valor_km']) == 0) ? '' : $this->Html->link($this->Buonny->moeda($utilizacao[0]['qtd_km'], array('nozero' => true, 'places' => 0)), 'javascript:void(0)', array('onclick' => "visualizar_relatorio_sm('{$utilizacao['0']['cliente_pagador']}', '{$this->data['Cliente']['data_inicial']}', '{$this->data['Cliente']['data_final']}', null)")); ?></td>
                        <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_km'], array('nozero' => true)) ?></td>
                        <td class='numeric'><?= (($utilizacao[0]['valor_sm_monitorada']) == 0) ? '' : $this->Html->link($this->Buonny->moeda($utilizacao[0]['qtd_sm_monitorada'], array('nozero' => true, 'places' => 0)), 'javascript:void(0)', array('onclick' => "visualizar_relatorio_sm('{$utilizacao['0']['cliente_pagador']}', '{$this->data['Cliente']['data_inicial']}', '{$this->data['Cliente']['data_final']}', ".Recebsm::TIPO_MONITORAMENTO_MONITORADO.")")); ?></td>
                        <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_sm_monitorada'], array('nozero' => true)) ?></td>
                        <td class='numeric'><?= (($utilizacao[0]['valor_sm_telemonitorada']) == 0) ? '' : $this->Html->link($this->Buonny->moeda($utilizacao[0]['qtd_sm_telemonitorada'], array('nozero' => true, 'places' => 0)), 'javascript:void(0)', array('onclick' => "visualizar_relatorio_sm('{$utilizacao['0']['cliente_pagador']}', '{$this->data['Cliente']['data_inicial']}', '{$this->data['Cliente']['data_final']}', ".Recebsm::TIPO_MONITORAMENTO_TELEMONITORADO.")")); ?></td>
                        <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_sm_telemonitorada'], array('nozero' => true)) ?></td>
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
				<td></td>
                <td class="numeric"><?= ($total_valor_desconto != 0) ? $this->Buonny->moeda($total_valor_desconto):''; ?></td>
                <td class="numeric"><?= ($total_determinado != 0) ? $this->Buonny->moeda($total_determinado):''; ?></td>
                <td class="numeric"><?= ($total_qtd_frota != 0 && $utilizacao[0]['valor_frota'] != 0) ? $total_qtd_frota: ''; ?></td>
                <td class="numeric"><?= ($total_valor_frota != 0) ? $this->Buonny->moeda($total_valor_frota): ''; ?></td>
                <td class="numeric"><?= ($total_qtd_placa_avulsa != 0 && $utilizacao[0]['valor_placa_avulsa'] != 0) ? $total_qtd_placa_avulsa: ''; ?></td>
                <td class="numeric"><?= ($total_valor_placa_avulsa != 0) ? $this->Buonny->moeda($total_valor_placa_avulsa): ''; ?></td>
                <td class="numeric"><?= ($total_qtd_dia != 0 && $utilizacao[0]['valor_dia'] != 0) ? $total_qtd_dia: ''; ?></td>
                <td class="numeric"><?= ($total_valor_dia) ? $this->Buonny->moeda($total_valor_dia): ''; ?></td>
                <td class="numeric"><?= ($total_qtd_km != 0 && $utilizacao[0]['valor_km'] != 0) ? $total_qtd_km: ''; ?></td>
                <td class="numeric"><?= ($total_valor_km != 0) ? $this->Buonny->moeda($total_valor_km): ''; ?></td>
                <td class="numeric"><?= ($total_qtd_sm_monitorada != 0 && $utilizacao[0]['valor_sm_monitorada'] != 0) ? $total_qtd_sm_monitorada:''; ?></td>
                <td class="numeric"><?= ($total_valor_sm_monitorada != 0) ? $this->Buonny->moeda($total_valor_sm_monitorada):''; ?></td>
                <td class="numeric"><?= ($total_qtd_sm_telemonitorada != 0 && $utilizacao[0]['valor_sm_telemonitorada'] != 0) ? $total_qtd_sm_telemonitorada:''; ?></td>
                <td class="numeric"><?= ($total_valor_sm_telemonitorada != 0) ? $this->Buonny->moeda($total_valor_sm_telemonitorada):''; ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>
<?php if (!empty($utilizacoes)): ?>


    <?php echo $this->Javascript->codeBlock("
    function utilizacao_de_servicos_filhos( codigo_cliente, data_inicial, data_final ) {   
        var form = document.createElement('form');
        var form_id = ('formresult' + Math.random()).replace('.','');
        form.setAttribute('method', 'post');
        form.setAttribute('action', '/portal/clientes/utilizacao_de_servicos_filhos/1');
        form.setAttribute('target', form_id);
        field = document.createElement('input');
        field.setAttribute('name', 'data[ClientEmpresa][codigo_cliente]');
        field.setAttribute('value', codigo_cliente);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[ClientEmpresa][data_inicial]');
        field.setAttribute('value', data_inicial);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[ClientEmpresa][data_final]');
        field.setAttribute('value', data_final);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        document.body.appendChild(form);
        var janela = window_sizes();
        window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-100)+',width='+(janela.width-80).toString()+',resizable=yes,toolbar=no,status=no');
        form.submit();
    }

    function visualizar_relatorio_placas(codigo_cliente, data_inicial, data_final){
		var form = document.createElement('form');
        var form_id = ('formresult' + Math.random()).replace('.','');
        form.setAttribute('method', 'post');
        form.setAttribute('action', '/portal/relatorios_bsat/relatorio_bsat_placas');
        form.setAttribute('target', form_id);
        field = document.createElement('input');
        field.setAttribute('name', 'data[RelatorioBsat][codigo_cliente]');
        field.setAttribute('value', codigo_cliente);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[RelatorioBsat][data_inicial]');
        field.setAttribute('value', data_inicial);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[RelatorioBsat][data_final]');
        field.setAttribute('value', data_final);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        document.body.appendChild(form);
        var janela = window_sizes();
        window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-100)+',width='+(janela.width-80).toString()+',resizable=yes,toolbar=no,status=no');
        form.submit();
	}

	function visualizar_relatorio_frota(codigo_cliente, data_inicial, data_final){
		var form = document.createElement('form');
        var form_id = ('formresult' + Math.random()).replace('.','');
        form.setAttribute('method', 'post');
        form.setAttribute('action', '/portal/relatorios_bsat/relatorio_bsat_frota');
        form.setAttribute('target', form_id);
        field = document.createElement('input');
        field.setAttribute('name', 'data[RelatorioBsat][codigo_cliente]');
        field.setAttribute('value', codigo_cliente);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[RelatorioBsat][data_inicial]');
        field.setAttribute('value', data_inicial);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[RelatorioBsat][data_final]');
        field.setAttribute('value', data_final);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        document.body.appendChild(form);
        var janela = window_sizes();
        window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-100)+',width='+(janela.width-80).toString()+',resizable=yes,toolbar=no,status=no');
        form.submit();
	}
	

	function visualizar_relatorio_sm(codigo_cliente, data_inicial, data_final, tipo_monitoramento){
		var form = document.createElement('form');
        var form_id = ('formresult' + Math.random()).replace('.','');
        form.setAttribute('method', 'post');
        form.setAttribute('action', '/portal/relatorios_bsat/relatorio_bsat_sm');
        form.setAttribute('target', form_id);
        field = document.createElement('input');
        field.setAttribute('name', 'data[RelatorioBsat][cliente_pagador]');
        field.setAttribute('value', codigo_cliente);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[RelatorioBsat][data_inicial]');
        field.setAttribute('value', data_inicial);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[RelatorioBsat][data_final]');
        field.setAttribute('value', data_final);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        if (tipo_monitoramento != null) {
            field = document.createElement('input');
            field.setAttribute('name', 'data[RelatorioBsat][tipo_monitoramento]');
            field.setAttribute('value', tipo_monitoramento);
            field.setAttribute('type', 'hidden');
            form.appendChild(field);
        }
        document.body.appendChild(form);
        var janela = window_sizes();
        window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-100)+',width='+(janela.width-80).toString()+',resizable=yes,toolbar=no,status=no');
        form.submit();
	}
"); ?>
<?php endif ?>