<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => 'clientes_produtos_descontos', 'action' => 'incluir', $codigo_cliente), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir novo desconto'));?>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Mês</th>
            <th>Produto</th>
            <th>Observação</th>
            <th colspan="2">Valor</th>
        </tr>
    </thead>
    <tbody>
        <?php 
            foreach($descontos as $desconto): 
                $mes_ano    = date("Y/m", strtotime(preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})(\w*)/", "$3$2$1$4", $desconto['ClienteProdutoDesconto']['mes_ano'])));            
                $descricao  = $desconto['Produto']['descricao'];
                $observacao = $desconto['ClienteProdutoDesconto']['observacao'];
                $valor      = $this->Buonny->moeda($desconto['ClienteProdutoDesconto']['valor']);
        ?>
            <tr>
                <td><?= $mes_ano ?></td>
                <td><?= $descricao ?></td>
				<td><?= $observacao ?></td>
                <td><?= $valor ?></td>
                <td>
                    <?php 
                        $mes_ano            = str_replace('/', '', $mes_ano);
                        $mes_ano_anterior   = date("Ym", strtotime('-1 month')); 
                        
                        //debug($mes_ano . ' >= ' . $mes_ano_anterior);

                        //debug($desconto['Pedido']);

                        $exibe_excluir = true;
                        
                        foreach($desconto['Pedido'] as $mes_ano_pedido){
                            if($mes_ano == $mes_ano_pedido){
                                $exibe_excluir = false;
                                break;
                            }
                        }

                        if($mes_ano >= $mes_ano_anterior && ($exibe_excluir)): ?>
                            <?php echo $html->link('', array('controller' => 'clientes_produtos_descontos', 
                                                             'action' => 'excluir', 
                                                             $desconto['ClienteProdutoDesconto']['codigo']), 
                                                             array('class' => 'icon-trash', 
                                                                   'title' => 'Excluir desconto'), 
                                                      'Confirma exclusão?'); 
                            ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table> 