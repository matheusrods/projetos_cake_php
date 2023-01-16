<?php if (!$utilizacoes): ?>
    <div class='form-procurar'>	
        <div class='well'>
    	    <?php echo $this->BForm->create('Recebsm', array('autocomplete' => 'off', 'url' => array('controller' => 'clientes', 'action' => 'utilizacao_de_servicos_filhos'))) ?>
    	    <div class="row-fluid inline">
                <?php echo $this->Buonny->input_codigo_cliente($this); ?>
    	        <?php echo $this->BForm->input('data_inicial', array('class' => 'input-small data', 'label' => false, 'placeholder' => 'Início')); ?>
    	        <?php echo $this->BForm->input('data_final', array('class' => 'input-small data', 'label' => false, 'placeholder' => 'Início')); ?>
                    
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
                    <th class='input-small numeric'><?= $this->Html->link('Qtd.Placa', 'javascript:void(0)') ?></th>
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
                    <th class='input-small numeric'><?= $this->Html->link('Vr.Max.Monitorada por Placa', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Qtd.Sm Tele', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Unit.Sm Tele', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Vr.Sm Tele', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Vr.Max.Tele', 'javascript:void(0)') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $total_clientes = 0 ?>
                <?php $total_valor_a_pagar = 0 ?>
                <?php $total_determinado = 0 ?>
                <?php $total_premio_minimo = 0 ?>
                <?php $total_valor_maximo = 0 ?>
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
                        <?php if ($utilizacao[0]['valor_a_pagar'] > 0 || $utilizacao[0]['qtd_placa_frota'] || $utilizacao[0]['qtd_placa_avulsa']): ?>
                            <?php $total_clientes += 1 ?>
                            <?php $total_valor_a_pagar += $utilizacao[0]['valor_a_pagar'] ?>
                            <?php $total_qtd_dia += (($utilizacao[0]['valor_dia'] == 0) ? 0 : $utilizacao[0]['qtd_dia']) ?>
                            <?php $total_valor_dia += (($utilizacao[0]['valor_dia'] == 0) ? 0 : $utilizacao[0]['valor_dia']) ?>
                            <?php $total_qtd_km += (($utilizacao[0]['valor_km'] == 0) ? 0 : $utilizacao[0]['qtd_km']) ?>
                            <?php $total_valor_km += (($utilizacao[0]['valor_km'] == 0) ? 0 : $utilizacao[0]['valor_km']) ?>
                            <?php $total_qtd_sm_monitorada += (($utilizacao[0]['valor_sm_monitorada'] == 0) ? 0 : $utilizacao[0]['qtd_sm_monitorada']) ?>
                            <?php $total_valor_sm_monitorada += (($utilizacao[0]['valor_sm_monitorada'] == 0) ? 0 : $utilizacao[0]['valor_sm_monitorada']) ?>
                            <?php $total_qtd_sm_telemonitorada += (($utilizacao[0]['valor_sm_telemonitorada'] == 0) ? 0 : $utilizacao[0]['qtd_sm_telemonitorada']) ?>
                            <?php $total_valor_sm_telemonitorada += (($utilizacao[0]['valor_sm_telemonitorada'] == 0) ? 0 : $utilizacao[0]['valor_sm_telemonitorada']) ?>
                            <tr>
                                <td><?= $utilizacao['0']['codigo'] ?></td>
                                <td><?= iconv('ISO-8859-1', 'UTF-8', $utilizacao['0']['raz_social']) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_a_pagar'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['DiasNoMes'], array('nozero' => true, 'places' => 0)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['qtd_placa'], array('nozero' => true, 'places' => 0)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['qtd_placa_frota'], array('nozero' => true, 'places' => 0)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['qtd_placa_avulsa'], array('nozero' => true, 'places' => 0)) ?></td>
                                <td class='numeric'><?= (($utilizacao[0]['valor_dia']) == 0) ? '' : $this->Buonny->moeda($utilizacao[0]['qtd_dia'], array('nozero' => true, 'places' => 0)) ?></td>
                                <td class='numeric'><?= (($utilizacao[0]['valor_dia']) == 0) ? '' : $this->Buonny->moeda($utilizacao[0]['valor_unitario_dia'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_dia'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= (($utilizacao[0]['valor_km']) == 0) ? '' : $this->Buonny->moeda($utilizacao[0]['qtd_km'], array('nozero' => true, 'places' => 0)) ?></td>
                                <td class='numeric'><?= (($utilizacao[0]['valor_km']) == 0) ? '' : $this->Buonny->moeda($utilizacao[0]['valor_unitario_km'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_km'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= (($utilizacao[0]['valor_sm_monitorada']) == 0) ? '' : $this->Buonny->moeda($utilizacao[0]['qtd_sm_monitorada'], array('nozero' => true, 'places' => 0)) ?></td>
                                <td class='numeric'><?= (($utilizacao[0]['valor_sm_monitorada']) == 0) ? '' : $this->Buonny->moeda($utilizacao[0]['valor_unitario_sm_monitorada'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_sm_monitorada'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_maximo_sm'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= (($utilizacao[0]['valor_sm_telemonitorada']) == 0) ? '' : $this->Buonny->moeda($utilizacao[0]['qtd_sm_telemonitorada'], array('nozero' => true, 'places' => 0)) ?></td>
                                <td class='numeric'><?= (($utilizacao[0]['valor_sm_telemonitorada']) == 0) ? '' : $this->Buonny->moeda($utilizacao[0]['valor_unitario_sm_telemonitora'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_sm_telemonitorada'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_maximo_sm_telemonitorada'], array('nozero' => true)) ?></td>
                            </tr>
                        <?php endif ?>
                    <?php endforeach; ?>
                <?php endif ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class="numeric"><?= $total_clientes; ?></td>
                    <td></td>
                    <td class="numeric"><?= $total_valor_a_pagar != 0 ? $this->Buonny->moeda($total_valor_a_pagar): ''; ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="numeric"><?= $total_qtd_dia != 0 ? $total_qtd_dia: ''; ?></td>
                    <td></td>
                    <td class="numeric"><?= $total_valor_dia != 0 ? $this->Buonny->moeda($total_valor_dia): ''; ?></td>
                    <td class="numeric"><?= $total_qtd_km != 0 ? $total_qtd_km: ''; ?></td>
                    <td></td>
                    <td class="numeric"><?= $total_valor_km != 0 ? $this->Buonny->moeda($total_valor_km): ''; ?></td>
                    <td class="numeric"><?= $total_qtd_sm_monitorada != 0 ? $total_qtd_sm_monitorada: ''; ?></td>
                    <td></td>
                    <td class="numeric"><?= $total_valor_sm_monitorada != 0 ? $this->Buonny->moeda($total_valor_sm_monitorada): ''; ?></td>
                    <td></td>
                    <td class="numeric"><?= $total_qtd_sm_telemonitorada != 0 ? $total_qtd_sm_telemonitorada: ''; ?></td>
                    <td></td>
                    <td class="numeric"><?= $total_valor_sm_telemonitorada != 0 ? $this->Buonny->moeda($total_valor_sm_telemonitorada): ''; ?></td>
                    <td></td>
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