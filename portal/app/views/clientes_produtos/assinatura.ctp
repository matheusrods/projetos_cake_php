<div class='form-procurar'> 
    <div class='well'>
        <?php echo $this->BForm->create('ClienteProduto', array('autocomplete' => 'off', 'url' => array('controller' => 'clientes_produtos', 'action' => 'assinatura'))) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this); ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
        <?php echo $this->BForm->end();?>
    </div>
</div>
<?php if (isset($cliente)): ?>
        <div class='well'>
            <strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
            <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
        </div>
    <ul class="nav nav-tabs">
      <li class="active"><a href="#assinatura" data-toggle="tab">Contratos do Cliente</a></li>
      <li><a href="#fornecedores" data-toggle="tab">Prestadores</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="assinatura">
            <div class='actionbar-right'>
                <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('action' => 'incluir2', $cliente['Cliente']['codigo'], rand()), array('title' => 'Incluir Assinatura', 'class' => 'btn btn-success', 'escape' => false)) ?>
            </div>
            <div style="margin-bottom:20px;">
                <h4>Contratos do Cliente</h4>
            </div>
            <div style="margin-bottom:20px;">
                <strong>Legenda, tipo de bloqueio:</strong>&nbsp;
                <span class="badge-empty badge" title="Pendência Comercial"></span>&nbsp;Comercial&nbsp;&nbsp;
                <span class="badge-empty badge badge-important" title="Pendência Financeira"></span>&nbsp;Financeira&nbsp;&nbsp;
                <span class="badge-empty badge badge-warning" title="Pendência Jurídica"></span>&nbsp;Jurídica
            </div>
            <table class='table cliente-produto'>
                <thead>
                    <th>Produto / Serviço</th>
                    <th></th>
                    <th class='input-mini'>Status</th>
                    <th class='numeric'>Cód. Cliente Pagador</th>
                    <th class='numeric'>Taxa Bancária</th>
                    <th class='numeric'>Taxa Corretora</th>
                    <th class='numeric' title="Valor do Prêmio Mínimo">R$ PM</th>
                    <th class='numeric'>Valor</th>
                    <th class='action-icon' colspan='2'>Ações</th>
                </thead>
                <tbody>
                    <?php if (count($produtos)): ?>

                    <?php 
                        $Produto_Toggle_anterior = null;
                        $Produto_Toggle = 0;
                    ?>

                        <?php foreach($produtos as $produto): ?>
                            <?php
                                if ($produto['Produto']['codigo'] == 82 && !in_array($cliente['Cliente']['codigo'], Set::extract('/ClienteProdutoServico2/codigo_cliente_pagador', $produto))) continue;
                            ?>
                            <?php 
                            $pattern = array(
                                '/(.*inativ.*)/i',
                                '/(.*pend.+ncia.*)/i',
                                '/(.*desatualizad.*)/i',
                            );
                            $replacement = array(
                                'INATIVO',
                                'PENDÊNCIA FIN.',
                                'DESATUALIZADO',
                            );
                            $motivo_bloqueio = preg_replace($pattern, $replacement, $produto['MotivoBloqueio']['descricao']);
                            switch ($motivo_bloqueio) {
                                case 'OK':
                                    $class_motivo_bloqueio = 'label label-success';
                                    break;
                                case 'DESATUALIZADO':
                                    $class_motivo_bloqueio = 'label label-warning';
                                    break;
                                case 'PENDÊNCIA FIN.':
                                    $class_motivo_bloqueio = 'label label-important';
                                    break;
                                case 'INATIVO':
                                default:
                                    $class_motivo_bloqueio = 'label';
                                    break;
                            }
                            ?>                                              
                            <tr id="<?php echo $produto['Produto']['codigo']; ?>" class="produto" style="cursor:pointer" ProdutoToggle="<?=++$Produto_Toggle?>" >
                                <td><i class="icon-chevron-down"></i> 
                                    <strong>
                                    <?php echo $produto['Produto']['descricao']; ?>
                                    - Inclusão:<?php echo preg_replace('/\s.*/', '', $produto['ClienteProduto']['data_inclusao']); ?>
                                    - Ativação:<?php echo preg_replace('/\s.*/', '', $produto['ClienteProduto']['data_faturamento'])?>
                                </strong></td>
                                <td>
                                    
                                </td>
                                <td>
                                    <?php if($produto['Produto']['ativo'] == '0' || $produto['Produto']['ativo'] = ''): ?>
                                        <span class="label label-important">Produto Inativo</span>
                                    <?php else: ?>
                                        <span style="margin-bottom:5px;" title="<?= $produto['MotivoBloqueio']['descricao'] ?>" class="<?= $class_motivo_bloqueio ?>"><?= $motivo_bloqueio ?></span><br />
                                    <?php if($produto['ClienteProduto']['pendencia_comercial']): ?>
                                        <span class="badge-empty badge" title="Pendência Comercial"></span>&nbsp;
                                    <?php endif; ?>

                                    <?php if($produto['ClienteProduto']['pendencia_financeira']): ?>
                                        <span class="badge-empty badge badge-important" title="Pendência Financeira"></span>&nbsp;&nbsp;
                                    <?php endif; ?>

                                    <?php if($produto['ClienteProduto']['pendencia_juridica']): ?>
                                        <span class="badge-empty badge badge-warning" title="Pendência Jurídica"></span>
                                    <?php endif; ?>
                                    <?php if(isset($produto['MotivoCancelamento']['codigo'])): ?>
                                        <span><?= $produto['MotivoCancelamento']['descricao']?></span>
                                    <?php endif; ?>
                                    
                                    <?php endif; ?>                            
                                </td>
                                <td class='numeric'>&nbsp</td>
                                <td class='numeric'><?= $this->Buonny->moeda($produto['ClienteProduto']['valor_taxa_bancaria'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($produto['ClienteProduto']['valor_taxa_corretora'], array('nozero' => true)) ?></td>
                                <td class='numeric'><?= $this->Buonny->moeda($produto['ClienteProduto']['valor_premio_minimo'], array('nozero' => true)) ?></td>
                                <td class='numeric'></td>
                                <td class='action-icon'><?php echo $this->Html->link('', array('controller' => 'clientes_produtos', 'action' => 'editar', $produto['ClienteProduto']['codigo'], $cliente['Cliente']['codigo']), array('class' => 'icon-edit evt-editar-cliente-produto', 'title' => 'Editar')); ?></td>
                                <td class='action-icon'><?php echo $this->Html->link('', array('controller' => 'clientes_produtos', 'action' => 'excluir', $produto['ClienteProduto']['codigo']), array('class' => 'icon-trash evt-excluir-cliente-produto', 'title' => 'Excluir')); ?></td>                    
                            </tr>
                            <?php foreach($produto['ClienteProdutoServico2'] as $servico): ?>
                                <tr class="ProdutoToggle-<?=$Produto_Toggle?> produto-servico-detalhe">
                                    <td style='padding-left:27px' class='first'><?= $servico['Servico']['descricao'] ?></td>
                                    <td>
                                        <?php echo (($servico['Servico']['credenciados'] > 0)? '<span class="label label-success">Com credenciado</span>' : '<span class="label label-important">Sem credenciado</span>') ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if($servico['ProdutoServico']['ativo'] == '0' || $produto['Produto']['ativo'] = ''): ?>
                                        <span class="label label-important">Serviço Inativo</span>
                                        <?php endif;?>                     
                                    </td>
                                    <td class='numeric'><?= $servico['codigo_cliente_pagador'] ?></td>
                                    <td></td>
                                    <td></td>
                                    <td class='numeric'><?= $this->Buonny->moeda($servico['valor_premio_minimo'], array('nozero' => true)) ?></td>
                                    <td class='numeric'><?= $this->Buonny->moeda($servico['valor']) ?></td>
                                    <td class='action-icon'><?php echo $this->Html->link('', array('controller' => 'clientes_produtos_servicos', 'action' => 'atualizar_servico_assinatura', $cliente['Cliente']['codigo'], $produto['Produto']['codigo'], $servico['Servico']['codigo'], $servico['codigo']), array('escape' => false, 'class' => 'icon-edit evt-editar-servico', 'title' => 'Editar')); ?>
                                    </td>
                                    <td class='action-icon'><?php echo $this->Html->link('', array('controller' => 'clientes_produtos', 'action' => 'excluir_servico_assinatura', $servico['codigo']), array('onclick' => 'return confirm("Confirma a exclusão de '.$servico['Servico']['descricao'] .'?")' ,'class' => 'icon-trash evt-excluir-servico', 'title' => 'Excluir')); ?></td>
                                </tr>
                            <?php endforeach ?>
                            <?php $Produto_Toggle_anterior = $produto['Produto']['codigo'] ?>
                        <?php endforeach ?>
                    <?php endif ?>
                </tbody>
            </table>

            <!-- dados da assinatura da matriz -->
            <?php if (isset($produto_matriz)): ?>
                <?php if (count($produto_matriz)): ?>
                    
                    <hr>
                    <div style="margin-bottom:20px;">
                        <h4>Liberado Matriz</h4>
                    </div>
                    <div style="margin-bottom:20px;">
                        <strong>Legenda, tipo de bloqueio:</strong>&nbsp;
                        <span class="badge-empty badge" title="Pendência Comercial"></span>&nbsp;Comercial&nbsp;&nbsp;
                        <span class="badge-empty badge badge-important" title="Pendência Financeira"></span>&nbsp;Financeira&nbsp;&nbsp;
                        <span class="badge-empty badge badge-warning" title="Pendência Jurídica"></span>&nbsp;Jurídica
                    </div>
                    <table class='table cliente-produto'>
                        <thead>
                            <th>Produto / Serviço</th>
                            <th></th>
                            <th class='input-mini'>Status</th>
                            <th class='numeric'>Cód. Cliente Pagador</th>
                            <th class='numeric'>Taxa Bancária</th>
                            <th class='numeric'>Taxa Corretora</th>
                            <th class='numeric' title="Valor do Prêmio Mínimo">R$ PM</th>
                            <th class='numeric'>Valor</th>
                        </thead>
                        <tbody>
                            <?php 
                                $Produto_Toggle_anterior_matriz = null;
                                $Produto_Toggle_matriz = 0;
                            ?>

                            <?php foreach($produto_matriz as $produtoMatriz): ?>
                                <?php
                                    if ($produtoMatriz['Produto']['codigo'] == 82 && !in_array($cliente_matriz['Cliente']['codigo'], Set::extract('/ClienteProdutoServico2/codigo_cliente_pagador', $produtoMatriz))) continue;
                                ?>
                                <?php 
                                $pattern = array(
                                    '/(.*inativ.*)/i',
                                    '/(.*pend.+ncia.*)/i',
                                    '/(.*desatualizad.*)/i',
                                );
                                $replacement = array(
                                    'INATIVO',
                                    'PENDÊNCIA FIN.',
                                    'DESATUALIZADO',
                                );
                                $motivo_bloqueio = preg_replace($pattern, $replacement, $produtoMatriz['MotivoBloqueio']['descricao']);
                                switch ($motivo_bloqueio) {
                                    case 'OK':
                                        $class_motivo_bloqueio = 'label label-success';
                                        break;
                                    case 'DESATUALIZADO':
                                        $class_motivo_bloqueio = 'label label-warning';
                                        break;
                                    case 'PENDÊNCIA FIN.':
                                        $class_motivo_bloqueio = 'label label-important';
                                        break;
                                    case 'INATIVO':
                                    default:
                                        $class_motivo_bloqueio = 'label';
                                        break;
                                }
                                ?>                                              
                                <tr id="<?php echo $produtoMatriz['Produto']['codigo']; ?>" class="produto" style="cursor:pointer" ProdutoToggle="<?=++$Produto_Toggle_matriz?>" >
                                    <td><i class="icon-chevron-down"></i> 
                                        <strong>
                                        <?php echo $produtoMatriz['Produto']['descricao']; ?>
                                        - Inclusão:<?php echo preg_replace('/\s.*/', '', $produtoMatriz['ClienteProduto']['data_inclusao']); ?>
                                        - Ativação:<?php echo preg_replace('/\s.*/', '', $produtoMatriz['ClienteProduto']['data_faturamento'])?>
                                    </strong></td>
                                    <td></td>
                                    <td>
                                        <?php if($produtoMatriz['Produto']['ativo'] == '0' || $produtoMatriz['Produto']['ativo'] = ''): ?>
                                            <span class="label label-important">Produto Inativo</span>
                                        <?php else: ?>
                                            <span style="margin-bottom:5px;" title="<?= $produtoMatriz['MotivoBloqueio']['descricao'] ?>" class="<?= $class_motivo_bloqueio ?>"><?= $motivo_bloqueio ?></span><br />
                                        <?php if($produtoMatriz['ClienteProduto']['pendencia_comercial']): ?>
                                            <span class="badge-empty badge" title="Pendência Comercial"></span>&nbsp;
                                        <?php endif; ?>

                                        <?php if($produtoMatriz['ClienteProduto']['pendencia_financeira']): ?>
                                            <span class="badge-empty badge badge-important" title="Pendência Financeira"></span>&nbsp;&nbsp;
                                        <?php endif; ?>

                                        <?php if($produtoMatriz['ClienteProduto']['pendencia_juridica']): ?>
                                            <span class="badge-empty badge badge-warning" title="Pendência Jurídica"></span>
                                        <?php endif; ?>
                                        <?php if(isset($produtoMatriz['MotivoCancelamento']['codigo'])): ?>
                                            <span><?= $produtoMatriz['MotivoCancelamento']['descricao']?></span>
                                        <?php endif; ?>
                                        
                                        <?php endif; ?>                            
                                    </td>
                                    <td class='numeric'>&nbsp</td>
                                    <td class='numeric'><?= $this->Buonny->moeda($produtoMatriz['ClienteProduto']['valor_taxa_bancaria'], array('nozero' => true)) ?></td>
                                    <td class='numeric'><?= $this->Buonny->moeda($produtoMatriz['ClienteProduto']['valor_taxa_corretora'], array('nozero' => true)) ?></td>
                                    <td class='numeric'><?= $this->Buonny->moeda($produtoMatriz['ClienteProduto']['valor_premio_minimo'], array('nozero' => true)) ?></td>
                                    <td class="numeric">&nbsp</td>                                    
                                </tr>
                                <?php foreach($produtoMatriz['ClienteProdutoServico2'] as $servico): ?>
                                    <tr class="ProdutoToggle-<?=$Produto_Toggle_matriz?> produto-servico-detalhe">
                                        <td style='padding-left:27px' class='first'><?= $servico['Servico']['descricao'] ?></td>
                                        <td>
                                            <?php echo (($servico['Servico']['credenciados'] > 0)? '<span class="label label-success">Com credenciado</span>' : '<span class="label label-important">Sem credenciado</span>') ?>
                                        </td>
                                        <td>
                                            <?php 
                                            if($servico['ProdutoServico']['ativo'] == '0' || $produtoMatriz['Produto']['ativo'] = ''): ?>
                                            <span class="label label-important">Serviço Inativo</span>
                                            <?php endif;?>                     
                                        </td>
                                        <td class='numeric'><?= $servico['codigo_cliente_pagador'] ?></td>
                                        <td></td>
                                        <td></td>
                                        <td class='numeric'><?= $this->Buonny->moeda($servico['valor_premio_minimo'], array('nozero' => true)) ?></td>
                                        <td class='numeric'><?= $this->Buonny->moeda($servico['valor']) ?></td>
                                    </tr>
                                <?php endforeach ?>
                                <?php $Produto_Toggle_anterior_matriz = $produtoMatriz['Produto']['codigo'] ?>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                <?php endif ?>
            <?php endif ?>
            <!-- fim dados assinatura matriz -->


            <!-- dados da assinatura dos utilizadores -->
            <?php if (isset($produto_pagador)): ?>
                <?php if (count($produto_pagador)): ?>
                    
                    <hr>
                    <div style="margin-bottom:20px;">
                        <h4>Utilizadores Liberados</h4>
                    </div>
                    <div style="margin-bottom:20px;">
                        <strong>Legenda, tipo de bloqueio:</strong>&nbsp;
                        <span class="badge-empty badge" title="Pendência Comercial"></span>&nbsp;Comercial&nbsp;&nbsp;
                        <span class="badge-empty badge badge-important" title="Pendência Financeira"></span>&nbsp;Financeira&nbsp;&nbsp;
                        <span class="badge-empty badge badge-warning" title="Pendência Jurídica"></span>&nbsp;Jurídica
                    </div>
                    <table class='table cliente-produto'>
                        <thead>
                            <th>Produto / Serviço</th>
                            <th></th>
                            <th class='input-mini'>Status</th>
                            <th class='numeric'>Cód. Cliente Utilizador</th>
                            <th class='numeric'>Cód. Cliente Pagador</th>
                            <th class='numeric'>Taxa Bancária</th>
                            <th class='numeric'>Taxa Corretora</th>
                            <th class='numeric' title="Valor do Prêmio Mínimo">R$ PM</th>
                            <th class='numeric'>Valor</th>
                        </thead>
                        <tbody>
                            <?php 
                                $Produto_Toggle_anterior_matriz = null;
                                $Produto_Toggle_matriz = 0;
                            ?>

                            <?php foreach($produto_pagador as $produtoPagador): ?>
                                <?php
                                    if ($produtoPagador['Produto']['codigo'] == 82 && !in_array($cliente['Cliente']['codigo'], Set::extract('/ClienteProdutoServico2/codigo_cliente_pagador', $produtoPagador))) continue;
                                ?>
                                <?php 
                                $pattern = array(
                                    '/(.*inativ.*)/i',
                                    '/(.*pend.+ncia.*)/i',
                                    '/(.*desatualizad.*)/i',
                                );
                                $replacement = array(
                                    'INATIVO',
                                    'PENDÊNCIA FIN.',
                                    'DESATUALIZADO',
                                );
                                $motivo_bloqueio = preg_replace($pattern, $replacement, $produtoPagador['MotivoBloqueio']['descricao']);
                                switch ($motivo_bloqueio) {
                                    case 'OK':
                                        $class_motivo_bloqueio = 'label label-success';
                                        break;
                                    case 'DESATUALIZADO':
                                        $class_motivo_bloqueio = 'label label-warning';
                                        break;
                                    case 'PENDÊNCIA FIN.':
                                        $class_motivo_bloqueio = 'label label-important';
                                        break;
                                    case 'INATIVO':
                                    default:
                                        $class_motivo_bloqueio = 'label';
                                        break;
                                }
                                ?>                                              
                                <tr id="<?php echo $produtoPagador['Produto']['codigo']; ?>" class="produto" style="cursor:pointer" ProdutoToggle="<?=++$Produto_Toggle_matriz?>" >
                                    <td><i class="icon-chevron-down"></i> 
                                        <strong>
                                        <?php echo $produtoPagador['Produto']['descricao']; ?>
                                        - Inclusão:<?php echo preg_replace('/\s.*/', '', $produtoPagador['ClienteProduto']['data_inclusao']); ?>
                                        - Ativação:<?php echo preg_replace('/\s.*/', '', $produtoPagador['ClienteProduto']['data_faturamento'])?>
                                    </strong></td>
                                    <td></td>
                                    <td>
                                        <?php if($produtoPagador['Produto']['ativo'] == '0' || $produtoPagador['Produto']['ativo'] = ''): ?>
                                            <span class="label label-important">Produto Inativo</span>
                                        <?php else: ?>
                                            <span style="margin-bottom:5px;" title="<?= $produtoPagador['MotivoBloqueio']['descricao'] ?>" class="<?= $class_motivo_bloqueio ?>"><?= $motivo_bloqueio ?></span><br />
                                        <?php if($produtoPagador['ClienteProduto']['pendencia_comercial']): ?>
                                            <span class="badge-empty badge" title="Pendência Comercial"></span>&nbsp;
                                        <?php endif; ?>

                                        <?php if($produtoPagador['ClienteProduto']['pendencia_financeira']): ?>
                                            <span class="badge-empty badge badge-important" title="Pendência Financeira"></span>&nbsp;&nbsp;
                                        <?php endif; ?>

                                        <?php if($produtoPagador['ClienteProduto']['pendencia_juridica']): ?>
                                            <span class="badge-empty badge badge-warning" title="Pendência Jurídica"></span>
                                        <?php endif; ?>
                                        <?php if(isset($produtoPagador['MotivoCancelamento']['codigo'])): ?>
                                            <span><?= $produtoPagador['MotivoCancelamento']['descricao']?></span>
                                        <?php endif; ?>
                                        
                                        <?php endif; ?>                            
                                    </td>
                                    <td class='numeric'>&nbsp</td>
                                    <td class='numeric'><?= $this->Buonny->moeda($produtoPagador['ClienteProduto']['valor_taxa_bancaria'], array('nozero' => true)) ?></td>
                                    <td class='numeric'><?= $this->Buonny->moeda($produtoPagador['ClienteProduto']['valor_taxa_corretora'], array('nozero' => true)) ?></td>
                                    <td class='numeric'><?= $this->Buonny->moeda($produtoPagador['ClienteProduto']['valor_premio_minimo'], array('nozero' => true)) ?></td>
                                    <td class="numeric">&nbsp</td>
                                    <td class="numeric">&nbsp</td>
                                </tr>
                                <?php foreach($produtoPagador['ClienteProdutoServico2'] as $servico): ?>
                                    <tr class="ProdutoToggle-<?=$Produto_Toggle_matriz?> produto-servico-detalhe">
                                        <td style='padding-left:27px' class='first'><?= $servico['Servico']['descricao'] ?></td>
                                        <td>
                                            <?php echo (($servico['Servico']['credenciados'] > 0)? '<span class="label label-success">Com credenciado</span>' : '<span class="label label-important">Sem credenciado</span>') ?>
                                        </td>
                                        <td>
                                            <?php 
                                            if($servico['ProdutoServico']['ativo'] == '0' || $produtoPagador['Produto']['ativo'] = ''): ?>
                                            <span class="label label-important">Serviço Inativo</span>
                                            <?php endif;?>                     
                                        </td>
                                        <td class='numeric'><?= $produtoPagador['ClienteProduto']['codigo_cliente'] ?></td>
                                        <td class='numeric'><?= $servico['codigo_cliente_pagador'] ?></td>
                                        <td></td>
                                        <td></td>
                                        <td class='numeric'><?= $this->Buonny->moeda($servico['valor_premio_minimo'], array('nozero' => true)) ?></td>
                                        <td class='numeric'><?= $this->Buonny->moeda($servico['valor']) ?></td>
                                        &nbsp
                                    </tr>
                                <?php endforeach ?>
                                <?php $Produto_Toggle_anterior_matriz = $produtoPagador['Produto']['codigo'] ?>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                <?php endif ?>
            <?php endif ?>
            <!-- fim dados dos produtos utilizadores -->




        </div>
        <div class="tab-pane" id="fornecedores" style="min-height:120px;">
          <?php echo $this->element('clientes_produtos/clientes_fornecedores') ?>
        </div>
    </div>
    <div class="form-actions"></div>
    
    <div class="embarcador_transportador"></div>
    <?php echo $this->addScript($this->Javascript->codeBlock("listaEmbarcadorTransportador({$cliente['Cliente']['codigo']})"));?>
    <div class="matriz_filial"></div>
    <?php echo $this->addScript($this->Javascript->codeBlock("listaMatrizFilial({$cliente['Cliente']['codigo']})"));?>

    <?php echo $this->Javascript->codeBlock("
        setup_mascaras();
        $(document).on('click', '.evt-excluir-cliente-produto', function(e) {
                e.preventDefault();
                var confirmation = window.confirm('Deseja cancelar o produto para o cliente?');
                if (confirmation === true) {
                    var link = $(this).attr('href');

                    $.ajax({
                        url: link,
                        type: 'get',
                        success: function(data) {
                            location.reload();
                        },
                        error: function(erro) {
                            alert('Não foi possível excluir, tente novamente.');
                        }
                    });
                }
            });

        $(function() {
            $('tr a').click(function(){
                window.location = $(this).attr('href');
                return false;
            });

            $('tr').click(function(){
                $('.ProdutoToggle-'+$(this).attr('ProdutoToggle')).toggle();
                
                if($(this).find('i.icon-chevron-down').length > 0){
                    $(this).find('i').addClass('icon-chevron-right');
                    $(this).find('i').removeClass('icon-chevron-down');
                }else{
                    $(this).find('i').addClass('icon-chevron-down');
                    $(this).find('i').removeClass('icon-chevron-right');
                }

                return false;
            });
        });

    ");  ?>
<?php endif ?>