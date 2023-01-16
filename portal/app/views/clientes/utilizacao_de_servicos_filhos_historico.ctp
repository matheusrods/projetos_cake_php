<?php if (!$utilizacoes): ?>
    <div class='form-procurar'>	
        <div class='well'>
    	    <?php echo $this->BForm->create('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'clientes', 'action' => 'utilizacao_de_servicos_filhos_historico', $this->passedArgs[0]))) ?>
    	    <div class="row-fluid inline">
                <?php echo $this->Buonny->input_codigo_cliente($this); ?>
    	        <?php echo $this->BForm->input('data_inicial', array('class' => 'input-small data', 'type' => 'text', 'label' => false, 'placeholder' => 'Data Inicial')); ?>
    	        <?php echo $this->BForm->input('data_final', array('class' => 'input-small data', 'type' => 'text', 'label' => false, 'placeholder' => 'Data Final')); ?>
                    
    	    </div>
    	    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
    	    <?php echo $this->BForm->end();?>
    	</div>
    	<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){ setup_datepicker(); });', false); ?>
    </div>
<?php else: ?>
    <div class='well'>
        <strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
        <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
    </div>
    <div class='row-fluid' style='overflow-x:auto'>
        <table class="table table-striped table-bordered" style='width:4000px;max-width:none'>
            <thead>
                <tr>
                    <th class='input-small'><?= $this->Html->link('Cód', 'javascript:void(0)') ?></th>
                    <th><?= $this->Html->link('Razão Social', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Vr.à Pagar', 'javascript:void(0)') ?></th>
                    <th class='input-mini numeric'><?= $this->Html->link('Dias', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Vr.Determinado', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Prêmio Mínimo', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Valor Máximo', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Placa Frota com SMs', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Placa Avulso com SMs', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Qtd.Dia', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Unit.Dia', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Vr.Dia', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Qtd.Km', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Unit.Km', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Vr.Km', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Qtd.Sm Monitorada', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Unit.Sm Monitorada', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Vr.Sm Monitorada', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Vr.Sm Mon Liq', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Qtd.Sm Tele', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Unit.Sm Tele', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Vr.Sm Tele', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Vr.Sm Tele Liq', 'javascript:void(0)') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $total_clientes = 0 ?>
                <?php $total_valor_a_pagar = 0 ?>
                <?php $total_determinado = 0 ?>
                <?php $total_premio_minimo = 0 ?>
                <?php $total_valor_maximo = 0 ?>
                <?php $total_qtd_frota = 0 ?>
                <?php $total_qtd_placa_avulsa = 0 ?>
                <?php $total_qtd_dia = 0 ?>
                <?php $total_valor_dia = 0 ?>
                <?php $total_qtd_km = 0 ?>
                <?php $total_valor_km = 0 ?>
                <?php $total_qtd_sm_monitorada = 0 ?>
                <?php $total_valor_sm_monitorada = 0 ?>
                <?php $total_valor_sm_monitorada_liquido = 0 ?>
                <?php $total_qtd_sm_telemonitorada = 0 ?>
                <?php $total_valor_sm_telemonitorada = 0 ?>
                <?php $total_valor_sm_tele_liquido = 0 ?>
                <?php if ($utilizacoes): ?>
                    <?php foreach($utilizacoes as $utilizacao): ?>
                        <?php if ($utilizacao['DetalheItemPedido']['valor_a_pagar'] > 0): ?>
                            <?php $total_clientes += 1 ?>
                            <?php $total_valor_a_pagar += $utilizacao['DetalheItemPedido']['valor_a_pagar'] ?>
                            <?php $total_determinado += $utilizacao['DetalheItemPedido']['ValDeterminado'] ?>
                            <?php $total_premio_minimo += $utilizacao['DetalheItemPedido']['PremioMinimo'] ?>
                            <?php $total_valor_maximo += $utilizacao['DetalheItemPedido']['ValMaximo'] ?>
                            <?php $total_qtd_frota += $utilizacao['DetalheItemPedido']['qtd_frota'] ?>
                            <?php $total_qtd_placa_avulsa += $utilizacao['DetalheItemPedido']['qtd_placa_avulsa'] ?>
                            <?php $total_qtd_dia += $utilizacao['DetalheItemPedido']['qtd_dia'] ?>
                            <?php $total_valor_dia += $utilizacao['DetalheItemPedido']['valor_dia'] ?>
                            <?php $total_qtd_km += $utilizacao['DetalheItemPedido']['qtd_km'] ?>
                            <?php $total_valor_km += $utilizacao['DetalheItemPedido']['valor_km'] ?>
                            <?php $total_qtd_sm_monitorada += $utilizacao['DetalheItemPedido']['qtd_sm_monitorada'] ?>
                            <?php $total_valor_sm_monitorada += $utilizacao['DetalheItemPedido']['valor_sm_monitorada'] ?>
                            <?php $total_valor_sm_monitorada_liquido += $utilizacao['DetalheItemPedido']['valor_sm_monitorada_liquido'] ?>
                            <?php $total_qtd_sm_telemonitorada += $utilizacao['DetalheItemPedido']['qtd_sm_telemonitorada'] ?>
                            <?php $total_valor_sm_telemonitorada += $utilizacao['DetalheItemPedido']['valor_sm_telemonitorada'] ?>
                            <?php $total_valor_sm_tele_liquido += $utilizacao['DetalheItemPedido']['valor_sm_tele_liquido'] ?>
                            <tr>
                                <td><?= $utilizacao['DetalheItemPedido']['codigo_utilizador'] ?></td>
								<td><?= iconv('ISO-8859-1', 'UTF-8', !empty($utilizacao['Cliente']['raz_social']) ? $utilizacao['Cliente']['raz_social']: $utilizacao['ClientEmpresa']['Raz_Social']) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao['DetalheItemPedido']['valor_a_pagar'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao['DetalheItemPedido']['DiasNoMes'], array('nozero' => true, 'places' => 0)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao['DetalheItemPedido']['ValDeterminado'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao['DetalheItemPedido']['PremioMinimo'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao['DetalheItemPedido']['ValMaximo'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao['DetalheItemPedido']['qtd_frota'], array('nozero' => true, 'places' => 0)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao['DetalheItemPedido']['qtd_placa_avulsa'], array('nozero' => true, 'places' => 0)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao['DetalheItemPedido']['qtd_dia'], array('nozero' => true, 'places' => 0)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao['DetalheItemPedido']['valor_unitario_dia'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao['DetalheItemPedido']['valor_dia'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao['DetalheItemPedido']['qtd_km'], array('nozero' => true, 'places' => 0)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao['DetalheItemPedido']['valor_unitario_km'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao['DetalheItemPedido']['valor_km'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao['DetalheItemPedido']['qtd_sm_monitorada'], array('nozero' => true, 'places' => 0)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao['DetalheItemPedido']['valor_unitario_sm_monitorada'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao['DetalheItemPedido']['valor_sm_monitorada'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao['DetalheItemPedido']['valor_sm_monitorada_liquido'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao['DetalheItemPedido']['qtd_sm_telemonitorada'], array('nozero' => true, 'places' => 0)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao['DetalheItemPedido']['valor_unitario_sm_telemonitorada'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao['DetalheItemPedido']['valor_sm_telemonitorada'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao['DetalheItemPedido']['valor_sm_tele_liquido'], array('nozero' => true)) ?></td>
                            </tr>
                        <?php endif ?>
                    <?php endforeach; ?>
                <?php endif ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class="numeric"><?= $total_clientes; ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="numeric"><?= $total_determinado != 0 ? $this->Buonny->moeda($total_determinado): ''; ?></td>
                    <td class="numeric"><?= $total_premio_minimo != 0 ? $this->Buonny->moeda($total_premio_minimo): ''; ?></td>
                    <td class="numeric"><?= $total_valor_maximo != 0 ? $this->Buonny->moeda($total_valor_maximo): ''; ?></td>
                    <td class="numeric"><?= $total_qtd_frota != 0 ? $total_qtd_frota: ''; ?></td>
                    <td class="numeric"><?= $total_qtd_placa_avulsa != 0 ? $total_qtd_placa_avulsa: ''; ?></td>
                    <td class="numeric"><?= $total_qtd_dia != 0 ? $total_qtd_dia: ''; ?></td>
                    <td></td>
                    <td class="numeric"><?= $total_valor_dia != 0 ? $this->Buonny->moeda($total_valor_dia): ''; ?></td>
                    <td class="numeric"><?= $total_qtd_km != 0 ? $total_qtd_km: ''; ?></td>
                    <td></td>
                    <td class="numeric"><?= $total_valor_km != 0 ? $this->Buonny->moeda($total_valor_km): ''; ?></td>
                    <td class="numeric"><?= $total_qtd_sm_monitorada != 0 ? $total_qtd_sm_monitorada: ''; ?></td>
                    <td></td>
                    <td class="numeric"><?= $total_valor_sm_monitorada != 0 ? $this->Buonny->moeda($total_valor_sm_monitorada): ''; ?></td>
                    <td class="numeric"><?= $total_valor_sm_monitorada_liquido != 0 ? $this->Buonny->moeda($total_valor_sm_monitorada_liquido): ''; ?></td>
                    <td class="numeric"><?= $total_qtd_sm_telemonitorada != 0 ? $total_qtd_sm_telemonitorada: ''; ?></td>
                    <td></td>
                    <td class="numeric"><?= $total_valor_sm_telemonitorada != 0 ? $this->Buonny->moeda($total_valor_sm_telemonitorada): ''; ?></td>
                    <td class="numeric"><?= $total_valor_sm_tele_liquido != 0 ? $this->Buonny->moeda($total_valor_sm_tele_liquido): ''; ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
    <?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>
    <?php if (!empty($utilizacoes)): ?>
        <?php //echo $this->Javascript->codeBlock("jQuery(document).ready(function() { jQuery('table.table').tablesorter() })"); ?>
    <?php endif ?>
<?php endif ?>