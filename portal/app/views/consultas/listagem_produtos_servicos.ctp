<?php if(!empty($listagem)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('Produto', 'produto');?></th>            
                <th><?php echo $this->Paginator->sort('Serviço', 'servico');?></th>            
                <th><?php echo $this->Paginator->sort('Prestador', 'razao_social');?></th>            
                <th><?php echo $this->Paginator->sort('Estado', 'estado');?></th>
                <th><?php echo $this->Paginator->sort('Cidade', 'cidade');?></th>
                <th><?php echo $this->Paginator->sort('Valor', 'documento');?></th>            
                <th><?php echo $this->Paginator->sort('Média Cidade', 'ListaDePreco__valor_medio_cida');?></th>            
                <th><?php echo $this->Paginator->sort('Média UF', 'valor_medio_uf');?></th>            
                <th><?php echo $this->Paginator->sort('Menor Valor UF', 'valor_minimo_uf');?></th>            
                <th><?php echo $this->Paginator->sort('Maior Valor UF', 'valor_maximo_uf');?></th>            
                <th><?php echo $this->Paginator->sort('Média Brasil', 'ListaDePreco__valor_medio_bras');?></th>            
                <th><?php echo $this->Paginator->sort('Status', 'status');?></th>            
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listagem as $dados):             
                if(empty($dados['ListaDePrecoProdutoServico']['valor'])):
                    $valor = '0,00';
                else:
                    $valor = $this->Buonny->moeda($dados['ListaDePrecoProdutoServico']['valor'], array('nozero' => true));
                endif;

                //medias, minimas e maximas dos estados
                $calculos_uf = Consulta::CalculosUF($lista_codigos, $dados['ListaDePreco']['estado']);
                //media da cidade
                $mediaCidade = Consulta::MediaCidade($lista_codigos, $dados['ListaDePreco']['cidade'], $list_cods_lpp);                
                //media brasil
                $media_brasil = Consulta::CalculoMédiaBrasil($lista_codigos);                 
            ?>
            <tr>
                <td><?php echo $dados['ListaDePreco']['produto'];?></td>
                <td><?php echo $dados['ListaDePreco']['servico'];?></td>
                <td><?php echo $dados['ListaDePreco']['razao_social'];?></td>
                <td><?php echo $dados['ListaDePreco']['estado'];?></td>
                <td><?php echo mb_strtoupper($dados['ListaDePreco']['cidade'], mb_internal_encoding());?></td>               
                <td class="numeric"><?php echo $valor;?></td>
                <td class="numeric"><?php echo empty($mediaCidade['valor_media_cidade']) ? '0,00' : $this->Buonny->moeda($mediaCidade['valor_media_cidade'], array('nozero' => true));?></td>
                <td class="numeric">
                    <?php echo empty($calculos_uf['valor_medio_uf']) ? '0,00' : $this->Buonny->moeda($calculos_uf['valor_medio_uf'], array('nozero' => true));?>    
                </td>
                <td class="numeric"><?php echo empty($calculos_uf['valor_min_uf']) ? '0,00' : $this->Buonny->moeda($calculos_uf['valor_min_uf'], array('nozero' => true));?></td>
                <td class="numeric"><?php echo empty($calculos_uf['valor_max_uf']) ? '0,00' : $this->Buonny->moeda($calculos_uf['valor_max_uf'], array('nozero' => true));?></td>
                <td class="numeric">
                    <?php                                          
                        echo empty($media_brasil['valor_medio_brasil']) ? '0,00' : $this->Buonny->moeda($media_brasil['valor_medio_brasil'], array('nozero' => true));
                    ?>                   
                </td>
                <td>
                    <?php if($dados['Fornecedor']['ativo'] == '1') : ?>
                        <span class="badge-empty badge badge-success" title="Ativo"></span>    
                    <?php else : ?>
                        <span class="badge-empty badge badge-important" title="Inativo"></span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['ListaDePreco']['count']; ?></td>
            </tr>
        </tfoot>    
    </table>
    <div class='row-fluid'>
        <div class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span6'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            
        </div>
    </div>
    <?php echo $this->Js->writeBuffer(); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 

<?php echo $this->Javascript->codeBlock("
    function atualizaLista() {
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'consultas/listagem_produtos_servicos/' + Math.random());
    }
");
?>