<div class='row-fluid'>
<!-- <?php debug($utilizacoes_assinatura); ?> -->
<?php foreach ($utilizacoes_assinatura as $produto => $assinaturas){ ?>
<div class='row-fluid'><h5><?=$produto;?></h5>
    <table class="table table-striped table-bordered">
        <thead>
            <tr> 
                <th class="input-mini">Código</th>
                <th class="input-xlarge">Razão Social</th>                
                <th class="input-medium numeric">Vr.à Pagar</th>
                <th class="input-medium numeric">Desconto</th>
                <th class="input-medium numeric">Valor</th>
            </tr>
        </thead>
        <tbody>
            <?php             
            $total_quandidade = 0;
            $total_valor = 0;
            $total = 0;
            $total_desconto = 0;
            $count = 0;

            //debug($assinaturas);
            foreach($assinaturas as $cliente => $dados):
                $desconto = $dados['manual'] == 1? $dados['desconto_manual'] : $dados['desconto_automatico'];      
                $valor    = $dados['total'] + $desconto;
                $total            +=  $dados['total'];
                $total_desconto   +=  $desconto;
                $total_valor      +=  $valor;
                $count++;
                $exibe_unitario = true;

                //Se o produto é exame complementar ou per capita
                //não exibe valor unitário
                if(in_array($dados['codigo_produto'], array(59,117)) && $dados['manual'] == '0'){
                    // $exibe_unitario = false;
                }
                ?>
                <tr>
                  <td>
                    <?= 
                        $this->Html->link($cliente, "javascript:utilizacao_de_servicos_assinatura_filhos('{$cliente}', '{$this->data['Cliente']['mes_referencia']}', '{$this->data['Cliente']['ano_referencia']}', '{$dados['codigo_produto']}')")
                    ?>
                  </td>
                  <td><?=$dados['nome'] ?></td>              
                  <td class="numeric"><?= $dados['total'] > 0 ? number_format($dados['total'],2,',','.') : '0,00' ?></td>
                  <td class="numeric"><?= number_format($desconto,2,',','.') ?></td>
                  <td class="numeric"><?= $valor > 0 ? number_format($valor,2,',','.') : '' ?></td>
                </tr>
                <?php if($dados['manual'] == 1): ?>
                    <?php if(!empty($dados['detalhes']) && isset($this->data['Cliente']['detalhar_servicos'])): ?>
                    <tr>
                        <td colspan="5" style="padding-left: 9%">
                            <div class="pull-left margin-bottom-10" style="width: 40%">
                                <label class="pull-left"><strong>Serviço</strong></label>
                            </div>
                            <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                <label><strong>Quantidade</strong></label>
                            </div>

                         <?php if($exibe_unitario):?>   
                            <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                <label><strong>Vr. unitário</strong></label>
                            </div>
                         <?php endif;?>
                            <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                <label><strong>Vr. total</strong></label>
                            </div>
                            <div class="clear"></div>
                        <?php foreach ($dados['detalhes'] as $key => $dado): ?>
                            <span <?php if(!empty($dado[0]['nota_cancelada'])): ?> style='font-style: italic;' title= 'nota cancelada: <?=$dado[0]['nota_cancelada']?>' <? endif;?>>
                                <div class="pull-left margin-bottom-10" style="width: 40%">
                                    <div class="clear"></div>
                                    <span><?php echo $dado[0]['servico']; ?></span>
                                </div>
                                <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                    <div class="clear"></div>
                                    <span><?php echo number_format($dado[0]['quantidade'], 0); ?></span>
                                </div>
                                <?php if($exibe_unitario): ?>
                                    <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                        <div class="clear"></div>
                                        <span><?php echo number_format($dado[0]['valor'], 2, ',', '.'); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                    <div class="clear"></div>
                                    <span><?php echo $exibe_unitario ? number_format($dado[0]['valor'] * $dado[0]['quantidade'], 2, ',', '.') : number_format($dado[0]['valor'], 2, ',', '.'); ?></span>
                                </div>
                                 <?php if(!empty($dado[0]['nota_cancelada'])): ?>
                                    <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                        <div class="clear"></div>
                                        <span>CANCELADO</span>
                                    </div>
                                 <?php endif; ?>   
                                </span>
                            <div class="clear"></div>
                        <?php endforeach; // end foreach detalhes ?>
                        </td>
                    </tr>        
                    <?php endif; ?>
                <?php elseif(($dados['manual'] == 0) && (count($dados['detalhes_pro_rata']['per_capita_parcial']) == 0)): ?>
                    <?php if(!empty($dados['detalhes']) && isset($this->data['Cliente']['detalhar_servicos'])): ?>
                    <tr>
                        <td colspan="5" style="padding-left: 9%">
                            <div class="pull-left margin-bottom-10" style="width: 40%">
                                <label class="pull-left"><strong>Serviço</strong></label>
                            </div>
                            <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                <label><strong>Quantidade</strong></label>
                            </div>
                         <?php if($exibe_unitario):?>   
                            <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                <label><strong>Vr. unitário</strong></label>
                            </div>
                         <?php endif;?>
                            <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                <label><strong>Vr. total</strong></label>
                            </div>
                            <div class="clear"></div>
                        <?php foreach ($dados['detalhes'] as $key => $dado): ?>
                            <span <?php if(!empty($dado[0]['nota_cancelada'])): ?> style='font-style: italic;' title= 'nota cancelada: <?=$dado[0]['nota_cancelada']?>' <? endif;?>>
                                <div class="pull-left margin-bottom-10" style="width: 40%">
                                    <div class="clear"></div>
                                    <span><?php echo $dado[0]['servico']; ?></span>
                                </div>
                                <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                    <div class="clear"></div>
                                    <span><?php echo number_format($dado[0]['quantidade'], 0); ?></span>
                                </div>
                                <?php if($exibe_unitario): ?>
                                    <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                        <div class="clear"></div>
                                        <span><?php echo number_format($dado[0]['valor'], 2, ',', '.'); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                    <div class="clear"></div>
                                    <span><?php echo $exibe_unitario ? number_format($dado[0]['valor'] * $dado[0]['quantidade'], 2, ',', '.') : number_format($dado[0]['valor'], 2, ',', '.'); ?></span>
                                </div>
                            </span>
                            <div class="clear"></div>
                        <?php endforeach; // end foreach detalhes ?>
                        </td>
                    </tr>        
                    <?php endif; ?>
                <?php elseif ($dados['manual'] == 0): ?>
                    <?php if(isset($dados['detalhes_pro_rata']) && count($dados['detalhes_pro_rata'] > 0) && isset($this->data['Cliente']['detalhar_servicos'])):
                    ?>
                    <tr>
                        <td colspan="5" style="padding-left: 9%">
                            <div class="pull-left margin-bottom-10" style="width: 40%">
                                <label class="pull-left"><strong>Serviço</strong></label>
                            </div>
                            <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                <label><strong>Quantidade</strong></label>
                            </div>
                            <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                <label><strong>Vr. unitário</strong></label>
                            </div>
                            <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                <label><strong>Vr. total</strong></label>
                            </div>
                            <div class="clear"></div>
                            <span <?php if(!empty($dados['detalhes'][0][0]['nota_cancelada'])): ?> style='font-style: italic;' title= 'nota cancelada: <?=$dados['detalhes'][0][0]['nota_cancelada']?>' <? endif;?>>
                                <?php if(count($dados['detalhes_pro_rata']['per_capita_parcial']) > 0):  
                                        foreach($dados['detalhes_pro_rata']['per_capita_parcial'] as $per_capita_parcial):  

                                            $qtd_vidas          = $per_capita_parcial['qtd_vidas'];
                                            $valor_assinatura   = number_format($per_capita_parcial['valor_assinatura'], 2, ',', '.');
                                            $total_parcial      = number_format($per_capita_parcial['valor'], 2, ',', '.');
                                ?>
                                            <div class="pull-left margin-bottom-10" style="width: 40%">
                                                <div class="clear"></div>
                                                <span>PACOTE PER CAPITA</span>
                                            </div>
                                            <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                                <div class="clear"></div>
                                                <span><?= $qtd_vidas ?></span>
                                            </div>
                                            <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                                <div class="clear"></div>
                                                <span><?= $valor_assinatura ?></span>
                                            </div>
                                            <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                                <div class="clear"></div>
                                                <span><?= $total_parcial ?></span>
                                            </div>
                                             <?php if(!empty($dado[0]['nota_cancelada'])): ?>
                                                <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                                    <div class="clear"></div>
                                                    <span>CANCELADO</span>
                                                </div>
                                            <?php endif; ?>
                                    <?php endforeach;  ?>
                                <?php endif;  ?>
                           
                                <?php if(count($dados['detalhes_pro_rata']['pro_rata']) > 0):  ?>
                                    <?php foreach ($dados['detalhes_pro_rata']['pro_rata'] as $pro_rata):  

                                            $qtd_vidas              = $pro_rata['qtd_vidas'];
                                            $valor_pro_rata         = $pro_rata['valor_pro_rata'];
                                            $total_parcial_pro_rata = number_format($qtd_vidas * $valor_pro_rata, 2, ',', '.');
                                            $valor_pro_rata         = number_format($pro_rata['valor_pro_rata'], 2, ',', '.');
                                            
                                    ?>
                                        <div class="pull-left margin-bottom-10" style="width: 40%">
                                            <div class="clear"></div>
                                            <span>PACOTE PER CAPITA (PRO RATA)</span>
                                        </div>
                                        <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                            <div class="clear"></div>
                                            <span><?= $qtd_vidas ?></span>
                                        </div>
                                        <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                            <div class="clear"></div>
                                            <span><?= $valor_pro_rata ?></span>
                                        </div>
                                        <div class="pull-left margin-bottom-10 text-center" style="width: 10%">
                                            <div class="clear"></div>
                                            <span><?= $total_parcial_pro_rata ?></span>
                                        </div>
                                    <div class="clear"></div>
                                    <?php endforeach; // end foreach detalhes_pro_rata ?>
                                <?php endif; // end if ['detalhes_pro_rata']['pro_rata'] ?>
                         </span>
                        </td>
                    </tr>  
                    <?php  endif; ?>
                <?php  endif; //FINAL IF $dados['manual'] ?>
            <?php endforeach; // end foreach $assinaturas?>
        </tbody>        
        <tfoot>
            <tr>
                <td class="numeric"><b><?=$count?></b></td>
                <td class="numeric"><b>TOTAL</b></td>                
                <td class="numeric"><b><?= $total > 0 ? number_format($total, 2,',','.') : '' ?></td>
                <td class="numeric"><b><?= $total_desconto > 0 ? number_format($total_desconto, 2,',','.') : '' ?></td>
                <td class="numeric"><b><?= $total_valor > 0 ? number_format($total_valor, 2,',','.') : '' ?></td>
            </tr>
        </tfoot>
        
    </table>
    <?php  } ?>
</div>
</div>
 <?php echo $this->Javascript->codeBlock("
    function utilizacao_de_servicos_assinatura_filhos( codigo_cliente, mes_referencia, ano_referencia, codigo_produto ) {     
        var form = document.createElement('form');
        var form_id = ('formresult' + Math.random()).replace('.','');
        form.setAttribute('method', 'post');
        form.setAttribute('action', '/portal/clientes/utilizacao_de_servicos_assinatura_filhos/1');
        form.setAttribute('target', form_id);
        field = document.createElement('input');
        field.setAttribute('name', 'data[Cliente][codigo_cliente]');
        field.setAttribute('value', codigo_cliente);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[Cliente][codigo_produto]');
        field.setAttribute('value', codigo_produto);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[Cliente][mes_referencia]');
        field.setAttribute('value', mes_referencia);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[Cliente][ano_referencia]');
        field.setAttribute('value', ano_referencia);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        document.body.appendChild(form);
        var janela = window_sizes();
        window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-100)+',width='+(janela.width-80).toString()+',resizable=yes,toolbar=no,status=no');
        form.submit();
    }
"); ?>