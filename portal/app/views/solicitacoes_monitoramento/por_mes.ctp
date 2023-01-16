<div class='form-procurar'> 
    <div class='well'>
        <?php echo $this->BForm->create('RecebsmPorAno', array('autocomplete' => 'off', 'url' => array('controller' => 'solicitacoes_monitoramento', 'action' => 'por_mes'))) ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('ano', array('options' => $anos, 'class' => 'input-small', 'label' => false)); ?>
            <?php //echo $this->Buonny->input_cliente_tipo($this, 0, $clientes_tipos, 'RecebsmPorAno'); ?>
            <?php echo $this->Buonny->input_codigo_cliente_base($this) ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
        <?php echo $this->BForm->end();?>
    </div>
</div>
<?php if(isset($meses_ano_anterior) && count($meses_ano_anterior)>0): ?>
    <?php if(isset($cliente['Cliente']['nome_fantasia'])): ?>
        <div class="well">
            <strong>Cliente: </strong><?php echo $cliente['Cliente']['nome_fantasia']; ?>
        </div>
    <?php endif; ?> 
    <div id="grafico" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
    <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th title="Mês" rowspan="2">Mês</th>
                    <th class='pagination-centered' colspan="2" title="Em aberto">Em aberto</th>
                    <th class='pagination-centered' colspan="2" title="em Monitoramento">Em Monitoramento</th>
                    <th class='pagination-centered' colspan="4" title="Encerradas">Encerradas</th>
                    <th class='pagination-centered' colspan="2" title="Canceladas">Canceladas</th>
                </tr>
                <tr>
                    <td class='numeric'><?= $meses_ano_anterior[0]['ano']; ?></td>
                    <td class='numeric'><?= $meses_ano_selecionado[0]['ano']; ?></td>
                    <td class='numeric'><?= $meses_ano_anterior[0]['ano']; ?></td>
                    <td class='numeric'><?= $meses_ano_selecionado[0]['ano']; ?></td>
                    <td class='numeric'><?= $meses_ano_anterior[0]['ano']; ?></td>
                    <td class='numeric'><?= $meses_ano_selecionado[0]['ano']; ?></td>
                    <td class='numeric'><?= $meses_ano_anterior[0]['ano'].' R$'; ?></td>
                    <td class='numeric'><?= $meses_ano_selecionado[0]['ano'].' R$'; ?></td>
                    <td class='numeric'><?= $meses_ano_anterior[0]['ano']; ?></td>
                    <td class='numeric'><?= $meses_ano_selecionado[0]['ano']; ?></td>
                </tr>
            </thead>
            <tbody>
                <?php
                        $numero_mes = 1;
                        $qtd_total_ano_anterior_abertas = 0;
                        $qtd_total_ano_selecionado_abertas = 0;
                        $qtd_total_ano_anterior_andamento = 0;
                        $qtd_total_ano_selecionado_andamento = 0;
                        $qtd_total_ano_anterior_encerradas = 0;
                        $qtd_total_ano_selecionado_encerradas = 0;
                        $qtd_total_ano_anterior_canceladas = 0;
                        $qtd_total_ano_selecionado_canceladas = 0;
                        $vl_total_ano_anterior_encerradas = 0;
                        $vl_total_ano_selecionado_encerradas = 0;
                ?>
                <?php for($i = 0; $i < 12; $i++): ?>
                <tr>
                    <td><?php echo $this->Buonny->mes_extenso($numero_mes); ?></td>
                    <td class="numeric ">
                            <?php
                                    if($meses_ano_anterior[$i]['qtds']['abertas'] != 0){
                                            echo $meses_ano_anterior[$i]['qtds']['abertas'];
                                            $qtd_total_ano_anterior_abertas += $meses_ano_anterior[$i]['qtds']['abertas'];
                                    }
                            ?>
                    </td>
                    <td class="numeric">
                            <?php
                                    if($meses_ano_selecionado[$i]['qtds']['abertas'] != 0){
                                            echo $meses_ano_selecionado[$i]['qtds']['abertas'];
                                            $qtd_total_ano_selecionado_abertas += $meses_ano_selecionado[$i]['qtds']['abertas'];
                                    }
                            ?>
                    </td>
                    <td class="numeric">
                            <?php
                                    if($meses_ano_anterior[$i]['qtds']['andamento'] != 0){
                                            echo $meses_ano_anterior[$i]['qtds']['andamento'];
                                            $qtd_total_ano_anterior_andamento += $meses_ano_anterior[$i]['qtds']['andamento'];
                                    }
                            ?>
                    </td>
                    <td class="numeric">
                            <?php
                                    if($meses_ano_selecionado[$i]['qtds']['andamento'] != 0){
                                            echo $meses_ano_selecionado[$i]['qtds']['andamento'];
                                            $qtd_total_ano_selecionado_andamento += $meses_ano_selecionado[$i]['qtds']['andamento'];
                                    }
                            ?>
                    </td>
                    <td class="numeric">
                            <?php
                                    if($meses_ano_anterior[$i]['qtds']['encerradas'] != 0){
                                            echo $meses_ano_anterior[$i]['qtds']['encerradas'];
                                            $qtd_total_ano_anterior_encerradas += $meses_ano_anterior[$i]['qtds']['encerradas'];
                                    }
                            ?>
                    </td>
                    <td class="numeric">
                            <?php
                                    if($meses_ano_selecionado[$i]['qtds']['encerradas'] != 0){
                                            echo $meses_ano_selecionado[$i]['qtds']['encerradas'];
                                            $qtd_total_ano_selecionado_encerradas += $meses_ano_selecionado[$i]['qtds']['encerradas'];
                                    }
                            ?>
                    </td>
                    <td class="numeric">
                            <?php
                                    if($meses_ano_anterior[$i]['valores']['encerradas'] != 0){
                                            echo $this->Buonny->moeda($meses_ano_anterior[$i]['valores']['encerradas']);
                                            $vl_total_ano_anterior_encerradas += $meses_ano_anterior[$i]['valores']['encerradas'];
                                    }
                            ?>
                    </td>
                    <td class="numeric">
                            <?php
                                    if($meses_ano_selecionado[$i]['valores']['encerradas'] != 0){
                                            echo $this->Buonny->moeda($meses_ano_selecionado[$i]['valores']['encerradas']);
                                            $vl_total_ano_selecionado_encerradas += $meses_ano_selecionado[$i]['valores']['encerradas'];
                                    }
                            ?>
                    </td>
                    <td class="numeric">
                            <?php
                                    if($meses_ano_anterior[$i]['qtds']['canceladas'] != 0){
                                            echo $meses_ano_anterior[$i]['qtds']['canceladas'];
                                            $qtd_total_ano_anterior_canceladas += $meses_ano_anterior[$i]['qtds']['canceladas'];
                                    }
                            ?>
                    </td>
                    <td class="numeric">
                            <?php
                                    if($meses_ano_selecionado[$i]['qtds']['canceladas'] != 0){
                                            echo $meses_ano_selecionado[$i]['qtds']['canceladas'];
                                            $qtd_total_ano_selecionado_canceladas += $meses_ano_selecionado[$i]['qtds']['canceladas'];
                                    }
                            ?>
                    </td>
                </tr>
                <?php $numero_mes++; ?>
                <?php endfor; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td>
                        <strong>Total</strong>
                    </td>
                    <td class="numeric">
                        <?php echo $qtd_total_ano_anterior_abertas; ?>
                    </td>
                    <td class="numeric">
                        <?php echo $qtd_total_ano_selecionado_abertas; ?>
                    </td>
                    <td class="numeric">
                        <?php echo $qtd_total_ano_anterior_andamento; ?>
                    </td>
                    <td class="numeric">
                        <?php echo $qtd_total_ano_selecionado_andamento; ?>
                    </td>
                    <td class="numeric">
                        <?php echo $qtd_total_ano_anterior_encerradas; ?>
                    </td>
                    <td class="numeric">
                        <?php echo $qtd_total_ano_selecionado_encerradas; ?>
                    </td>
                    <td class="numeric">
                        <?php echo $this->Buonny->moeda($vl_total_ano_anterior_encerradas); ?>
                    </td>
                    <td class="numeric">
                        <?php echo $this->Buonny->moeda($vl_total_ano_selecionado_encerradas); ?>
                    </td>
                    <td class="numeric">
                        <?php echo $qtd_total_ano_anterior_canceladas; ?>
                    </td>
                    <td class="numeric">
                        <?php echo $qtd_total_ano_selecionado_canceladas; ?>
                    </td>
                </tr>
            </tfoot>
    </table>
    <?php
    echo $this->Javascript->codeBlock($this->Highcharts->render($eixo_x, $series, array(
        'renderTo' => 'grafico',
        'chart' => array('type' => 'line'),
        'yAxis' => array('title' => ''),
        'xAxis' => array('labels' => array('rotation' => 0, 'y' => 20), 'gridLineWidth' => 1),
        'tooltip' => array('formatter' => 'this.y'),
    )));
    ?>
<?php endif ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>