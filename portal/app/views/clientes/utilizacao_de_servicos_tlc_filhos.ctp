<?php if (!$utilizacoes_tlc): ?>
    <div class='form-procurar'> 
        <div class='well'>
            <?php echo $this->BForm->create('LogFaturamentoTeleconsult', array('autocomplete' => 'off', 'url' => array('controller' => 'clientes', 'action' => 'utilizacao_de_servicos_filhos'))) ?>
            <div class="row-fluid inline">
                <?php echo $this->Buonny->input_codigo_cliente($this); ?>
                <?php echo $this->BForm->input('data_inicial', array('class' => 'input-small data', 'label' => false, 'placeholder' => 'InÃ­cio')); ?>
                <?php echo $this->BForm->input('data_final', array('class' => 'input-small data', 'label' => false, 'placeholder' => 'InÃ­cio')); ?>
                    
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
        <table class="table table-striped table-bordered" style='width:1500px;max-width:none'>
            <thead>
                <tr>
                    <th class='input-small'><?= $this->Html->link('Cód', 'javascript:void(0)') ?></th>
                    <th><?= $this->Html->link('Razão Social', 'javascript:void(0)') ?></th>
                    <th><?= $this->Html->link('Produto', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Vr.à Pagar', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Qtd.Cobrado', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Valor Cobrado', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Qtd.não Cobrado', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Valor não Cobrado', 'javascript:void(0)') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $total_clientes = 0 ?>
                <?php $total_qtd_cobrado = 0 ?>
                <?php $total_valor_cobrado = 0 ?>
                <?php $total_qtd_nao_cobrado = 0 ?>
                <?php $total_valor_nao_cobrado = 0 ?>
                <?php $total_valor_a_pagar = 0 ?>
                <?php if ($utilizacoes_tlc): ?>
                    <?php foreach($utilizacoes_tlc as $utilizacao): ?>
                        <?php $total_clientes += 1 ?>
                        <?php $total_qtd_cobrado += $utilizacao[0]['qtd_cobrado'] ?>
                        <?php $total_valor_cobrado += $utilizacao[0]['valor_cobrado'] ?>
                        <?php $total_valor_a_pagar += $utilizacao[0]['valor_a_pagar'] ?>
                        <?php $total_qtd_nao_cobrado += $utilizacao[0]['qtd_nao_cobrado'] ?>
                        <?php $total_valor_nao_cobrado += $utilizacao[0]['valor_nao_cobrado'] ?>
                        <tr>
                            <td><?= $utilizacao['0']['codigo_cliente'] ?></td>
                            <td><?= $utilizacao['0']['razao_social'] ?></td>
                            <td><?= $utilizacao['0']['descricao'] ?></td>
                            <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_a_pagar'], array('nozero' => true)) ?></td>
                            <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['qtd_cobrado'], array('nozero' => true, 'places' => 0)) ?></td>
                            <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_cobrado'], array('nozero' => true)) ?></td>
                            <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['qtd_nao_cobrado'], array('nozero' => true, 'places' => 0)) ?></td>
                            <td class='numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_nao_cobrado'], array('nozero' => true)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class="numeric"><?= $total_clientes; ?></td>
                    <td></td>
                    <td></td>
                    <td class="numeric"><?= ($total_valor_a_pagar != 0) ? $this->Buonny->moeda($total_valor_a_pagar):''; ?></td>
                    <td class="numeric"><?= ($total_qtd_cobrado != 0) ? $this->Buonny->moeda($total_qtd_cobrado, array('places' => 0)):''; ?></td>
                    <td class="numeric"><?= ($total_valor_cobrado != 0) ? $this->Buonny->moeda($total_valor_cobrado):''; ?></td>
                    <td class="numeric"><?= ($total_qtd_nao_cobrado != 0) ? $this->Buonny->moeda($total_qtd_nao_cobrado, array('places' => 0)):''; ?></td>
                    <td class="numeric"><?= ($total_valor_nao_cobrado != 0) ? $this->Buonny->moeda($total_valor_nao_cobrado):''; ?></td>
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