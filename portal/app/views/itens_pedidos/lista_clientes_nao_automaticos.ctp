<?php if(!empty($dados_liberacao)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>

    <div class="row-fluid inline">
        <?php echo $this->BForm->create('ItemPedido', array('type' => 'post' ,'url' => array('controller' => 'itens_pedidos','action' => 'integracao'))); ?>

        <div class='actionbar-right'>
            <?php echo $this->BForm->submit('Carregar Clientes Selecionados', array('div' => false, 'class' => 'btn btn-primary', 'name' => 'data[Submit][type]')); ?>
             <?php echo $this->BForm->submit('Reverter Clientes Selecionados', array('div' => false, 'class' => 'btn btn-warning', 'name' => 'data[Submit][type]')); ?>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                <th><?= $this->Paginator->sort('Código Cliente', 'codigo')?></th>
                <th><?= $this->Paginator->sort('Nome Fantasia','nome_fantasia')?></th>
                <th><?= $this->Paginator->sort('CNPJ/CPF','codigo_documento')?></th>
                <th>Status</th>
                <th class="acoes" style="width:89px">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dados_liberacao as $dados): ?>

                    <?php 
                        if($dados[0]['status_pedido'] == 1)
                        {
                            $status = "<span class='label label-important'>Pendente</span>";
                        }
                        if($dados[0]['status_pedido'] == 2)
                        {
                            $status = "<span class='label label-success'>Carregado</span>";
                        }
                        if($dados[0]['status_pedido'] == 3 )
                        {
                            $status = "<span class='label label-info'>Integrado</span>";
                        }                        
                    ?>

                    <tr>
                        <td><?php echo $dados['Cliente']['codigo'] ?></td>
                        <td><?php echo $dados['Cliente']['nome_fantasia'] ?></td>
                        <td><?php echo $buonny->documento($dados['Cliente']['codigo_documento']) ?></td>
                        <td><?php echo $status; ?></td>
                        <td style="width:50px;">
                            <?php echo $this->BForm->input('Cliente.codigo', array('name' => "data[Cliente][codigo][]", 'type' => 'checkbox', 'label' => false, 'value' => $dados['Cliente']['codigo'], 'multiple', 'hiddenField' => false)); ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan = "15"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Cliente']['count']; ?></td>
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
        <?php echo $this->BForm->end(); ?>
    </div>

    <?php echo $this->Js->writeBuffer(); ?>

    <?php echo $this->Javascript->codeBlock("

    ");
?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<script type="text/javascript">
    
    // SOLUCAO PARA EVITAR DUPLO CLIQUES NA HORA DE INTEGRAR, CARREGAR E REVERTER
    jQuery.fn.preventDoubleSubmit = function() {
        jQuery(this).submit(function() {
            if (this.beenSubmitted){                
                return false;
            } else {
                this.beenSubmitted = true;              
            }
        });
    };

    $(document).ready(function() { 
        jQuery('#ItemPedidoListaClientesNaoAutomaticosForm').preventDoubleSubmit();
    });
</script>

