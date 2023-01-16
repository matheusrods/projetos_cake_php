
<?php if(!empty($dados)):?>
    <?php 
        echo $paginator->options(array('update' => 'div.lista')); 
        $total_paginas = $this->Paginator->numbers();
    ?>
    <table class="table table-striped" style="width: 1500px;" >
        <thead>
            <tr>
                <th>Código Credenciado</th>
                <th>Numero NFS</th>
                <th>Código Glosa</th>
                <th>Pedido de Exames</th>
                <th>Exame</th>
                <th>Valor</th>
                <th>Data Glosa</th>
                <th>Data Pagamento</th>
                <th>Status</th>
                <th>Motivo da Glosa</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dados as $dado): ?>
                <tr>
                    <td><?php echo $dado[0]['codigo_fornecedor'] ?></td>
                    <td><?php echo $dado[0]['numero_nota_fiscal'] ?></td>
                    <td><?php echo $dado[0]['codigo_glosa'] ?></td>
                    <td><?php echo $dado[0]['codigo_pedido_exame'] ?></td>
                    <td><?php echo $dado[0]['exame'] ?></td>                    
                    <td><?php echo $dado[0]['valor'] ?></td>
                    <td><?php echo AppModel::dbDateToDate($dado[0]['data_glosa']) ?></td>
                    <td><?php echo AppModel::dbDateToDate($dado[0]['data_pagamento']) ?></td>                    
                    <td><?php echo $dado[0]['status_glosa']; ?></td>
                    <td><?php echo $dado[0]['motivo_glosa']; ?></td>                    
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

