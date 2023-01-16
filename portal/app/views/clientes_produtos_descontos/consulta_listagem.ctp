<?php if( isset($dados) && !empty($dados) ): ?>

    <table class='table table-striped tablesorter'>
        <thead>            
            <th><?php echo $this->Html->link('Codigo', 'javascript:void(0)') ?></th>
            <th><?php echo $this->Html->link('Cliente', 'javascript:void(0)') ?></th>
            <th><?php echo $this->Html->link('Produto', 'javascript:void(0)') ?></th>
            <th><?php echo $this->Html->link('Usuário', 'javascript:void(0)') ?></th>
            <th><?php echo $this->Html->link('Data', 'javascript:void(0)') ?></th>
            <th class="numeric"><?php echo $this->Html->link('Desconto', 'javascript:void(0)') ?></th>
            <th><?php echo $this->Html->link('Observação', 'javascript:void(0)') ?></th>
        </thead>
        <tbody>
            <?php $total_desconto = 0; ?>
            <?php foreach($dados as $value): ?>

                <tr>
                    <td><?php echo $value['Cliente']['codigo'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><?php echo $value['Cliente']['razao_social'] ?></td>
                    <td><?php echo $value['Produto']['descricao'] ?></td>
                    <td><?php echo $value['Usuario']['nome'] ?></td>
                    <td><?php echo $value['ClienteProdutoDesconto']['data_inclusao']; ?></td>
                    <td class="numeric"><?php echo $this->Buonny->moeda($value['ClienteProdutoDesconto']['valor']); ?></td>
                    <td><?php echo $value['ClienteProdutoDesconto']['observacao']; ?></td>
                </tr>
                <?php $total_desconto += $value['ClienteProdutoDesconto']['valor']; ?>
            <?php endforeach; ?>
            
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"><strong>Total:</strong></td>                    
                <td class='numeric'><strong><?php echo $this->Buonny->moeda($total_desconto) ;?></strong></td>
                <td></td>                
            </tr>
        </tfoot>
    </table>
    
    <?php echo $this->Javascript->codeBlock('
        jQuery(document).ready(function(){
           jQuery("table.table").tablesorter({
                sortList: [[2,1]]
            });                     
        });', false);
    ?>

<?php endif; ?>