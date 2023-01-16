
<?php if(!empty($dados)):?>
    <?php 
        echo $paginator->options(array('update' => 'div.lista')); 
        $total_paginas = $this->Paginator->numbers();
    ?>
    <div class='well'>
        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
    </div>
    <table class="table table-striped" style="width: 1500px;" >
        <thead>
            <tr>
                <th>Pedido de Exame</th>
                <th>Exame</th>
                <th>Código Credenciado</th>
                <th>Código Cliente</th>
                <th>Cliente</th>
                <th>Valor Custo</th>                
            </tr>
        </thead>
        <tbody>

            <?php foreach ($dados as $dado): ?>
                <tr>
                    <td><?php echo $dado[0]['codigo_pedido_exame'] ?></td>
                    <td><?php echo $dado[0]['exame'] ?></td>
                    <td><?php echo $dado[0]['codigo_credenciado'] ?></td>
                    <td><?php echo $dado[0]['codigo_cliente'] ?></td>
                    <td><?php echo $dado[0]['nome_cliente'] ?></td>
                    <td><?php echo $dado[0]['valor_custo'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br />

    <?php echo $this->Js->writeBuffer(); ?>
    <?php echo $this->Javascript->codeBlock('
        jQuery(document).ready(function() {
            setup_mascaras(); setup_time(); setup_datepicker();
        });
    '); ?>  
<?php else:?>    
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 

