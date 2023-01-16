<?php if(!empty($dados_nfs)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>

    <div class='well'>
    <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array('controller' => $this->name, 'action' => $this->action, 'destino','export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
    </div>

    <table class="table table-striped" style='width:1464px;max-width:none;'>
        <thead>
            <tr>
            <th class="input-mini" style="width:35px;"><?= $this->Paginator->sort('Código Credenciado', 'codigo_fornecedor')?></th>
            <!-- <th><?= $this->Paginator->sort('Nome','nome')?></th> -->
            <th class="input-medium"><?= $this->Paginator->sort('CNPJ Credenciado', 'codigo_fornecedor')?></th>
            <th class="input-medium"><?= $this->Paginator->sort('Razão Social Credenciado', 'razao_social')?></th>
            <th class="input-mini"><?= $this->Paginator->sort('Número NF','numero_nota_fiscal')?></th>
            <th class="input-mini"><?= $this->Paginator->sort('Código Rastreamento', 'chave_rastreamento')?></th>
            <th class="input-mini"><?= $this->Paginator->sort('Tipo Recebimento', 'codigo_tipo_recebimento')?></th>
            <th class="input-mini"><?= $this->Paginator->sort('Forma de Pagamento', 'codigo_formas_pagto')?></th>

            <th class="input-mini"><?= $this->Paginator->sort('Data da Emissão', 'data_emissao')?></th>
            <th class="input-mini"><?= $this->Paginator->sort('Data de Vencimento','data_vencimento')?></th>
            <th class="input-mini"><?= $this->Paginator->sort('Data de Recebimento','data_recebimento')?></th>
            <th class="input-mini"><?= $this->Paginator->sort('Data de Pagamento','data_pagamento')?></th>
            <th class="input-mini"><?= $this->Paginator->sort('Status','codigo_nota_fiscal_status')?></th>
            <th class="input-medium"><?= $this->Paginator->sort('Valor NFS', 'valor')?></th>
            <th class="input-mini"><?= $this->Paginator->sort('Acréscimo', 'codigo_fornecedor')?></th>
            <th></th>
            <th class="acoes" style="width:120px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dados_nfs as $dados): ?>
                <?php $texto = 'R$ '; ?>
            <tr>

                <td><?php echo $dados['Fornecedor']['codigo'] ?></td>
                <!-- <td><?php //echo $dados['Fornecedor']['nome'] ?></td> -->
                <td><?php echo Comum::formatarDocumento($dados['Fornecedor']['codigo_documento']) ?></td>
                <td><?php echo $dados['Fornecedor']['razao_social'] ?></td>
                <td><?php echo $dados['NotaFiscalServico']['numero_nota_fiscal'] ?></td>
                <td><?php echo $dados['NotaFiscalServico']['chave_rastreamento'] ?></td>
                <td><?php echo $dados['TipoRecebimento']['descricao'] ?></td>
                <td><?php echo $dados['FormaPagto']['descricao'] ?></td>
                <td><?php echo $dados['NotaFiscalServico']['data_emissao'] ?></td>
                <td><?php echo $dados['NotaFiscalServico']['data_vencimento'] ?></td>
                <td><?php echo $dados['NotaFiscalServico']['data_recebimento'] ?></td>
                <td><?php echo $dados['NotaFiscalServico']['data_pagamento'] ?></td>
                <td><?php echo $dados['NotaFiscalStatus']['descricao'] ?></td>
                <td style="width:83px;"><?php echo  $texto.$this->Ithealth->moeda($dados['NotaFiscalServico']['valor'], array('nozero' => true)) ?></td>
                <td>
                <?php if($dados['NotaFiscalServico']['flag_acrescimo'] == 0): ?>
                    Não
                <?php elseif($dados['NotaFiscalServico']['flag_acrescimo'] == 1): ?>
                    Sim
                <?php endif; ?>    
                </td>

                <td></td>
                <td style="width:50px;">    
                    <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status',' onclick' => "atualizaStatus('{$dados['NotaFiscalServico']['codigo']}','{$dados['NotaFiscalServico']['ativo']}')"));?>

                    <?php if($dados['NotaFiscalServico']['ativo'] == 0): ?>
                        <span class="badge-empty badge badge-important" title="Desativado"></span>
                    <?php elseif($dados['NotaFiscalServico']['ativo'] == 1): ?>
                        <span class="badge-empty badge badge-success" title="Ativo"></span>
                    <?php endif; ?>

                    <?php
                    //trecho comentado a pedido pela demanda PC-811
                    
                    //if(!empty($dados['NotaFiscalServico']['codigo_nota_fiscal_status']) && $dados['NotaFiscalServico']['codigo_nota_fiscal_status'] != 5 ){
                        echo $this->Html->link('', array(
                            'controller' => 'notas_fiscais_servico', 
                            'action' => 'editar', 
                            $dados['NotaFiscalServico']['codigo']
                        ), 
                        array(
                                'class' => 'icon-edit ', 
                                'data-toggle' => 'tooltip', 
                                'title' => 'Editar'
                            )
                        ); 

                    // }
                        if($dados['NotaFiscalServico']['codigo_nota_fiscal_status'] == 5 || $dados['NotaFiscalServico']['codigo_nota_fiscal_status'] == 3 ){
                            echo $this->Html->link('', array(
                                'controller' => 'notas_fiscais_servico', 
                                'action' => 'visualizar', 
                                $dados['NotaFiscalServico']['codigo'],
                            ), 
                            array(
                                    'class' => 'icon-search ', 
                                    'data-toggle' => 'tooltip', 
                                    'title' => 'Visualizar'
                                )
                            );  
                        }
                        
                    ?>

                    <?php  echo $this->Html->link('', array('controller' => 'notas_fiscais_servico', 'action' => 'lista_glosas', $dados['NotaFiscalServico']['codigo_fornecedor'], $dados['NotaFiscalServico']['codigo']), array('class' => 'icon-tags', 'title' => 'Glosas')); ?>                 

                    <?php 
                    if(!isset($dados['AnexoNotaFiscalServico']['codigo_nota_fiscal_servico']) && !empty($dados['AnexoNotaFiscalServico']['codigo_nota_fiscal_servico'])): 
                    ?>
                        <a href="https://api.rhhealth.com.br<?php echo $dados['AnexoNotaFiscalServico']['caminho_arquivo']; ?>" target="_blank" class="icon-file btn-anexos visualiza_anexo" title='Visualizar Digitalização'></a>
                    <?php    
                        // echo $this->Html->link('', array(
                        //     'controller' => 'notas_fiscais_servico', 
                        //     'action' => 'visualizar_anexo_nota_fiscal',  $dados['AnexoNotaFiscalServico']['codigo_nota_fiscal_servico']), 
                        //     array('class' => 'icon-tags', 'target'=>'_blank', 'title' => 'Visualizar Anexo')); 
                    
                    endif; ?>

                    <a href="javascript:void(0);" onclick="log_pedido(<?php echo $dados['NotaFiscalServico']['codigo']; ?>);"><i class="icon-eye-open" title="Log da Nota Fiscal"></i></a>

                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "15"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['NotaFiscalServico']['count']; ?></td>
            </tr>
        </tfoot>    
    </table>
    <div class='row-fluid'>
        <div class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span7'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            
        </div>
    </div>

    <?php echo $this->Js->writeBuffer(); ?>

    <?php echo $this->Javascript->codeBlock("

        function atualizaStatus(codigo, status){
            $.ajax({
                type: 'POST',
                url: baseUrl + 'notas_fiscais_servico/atualiza_status/' + codigo + '/' + status + '/' + Math.random(),
                beforeSend: function(){
                    bloquearDivSemImg($('div.lista'));  
                },
                success: function(data){
                    if(data == 1){
                        atualizaLista();
                        $('div.lista').unblock();
                    } else{
                        atualizaLista();
                        $('div.lista').unblock();
                   }
                },
                error: function(erro){
                    $('div.lista').unblock();
                }
            });
        }

        function atualizaLista() {
            var div = jQuery('div.lista');
            bloquearDiv(div);
            div.load(baseUrl + 'notas_fiscais_servico/listagem/' + Math.random());
        }
        
        function log_pedido(codigo_nfs){
            var janela = window_sizes();
            window.open(baseUrl + 'notas_fiscais_servico/log_nfs/' + codigo_nfs + '/' + Math.random(), janela, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
        }
    ");
?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

