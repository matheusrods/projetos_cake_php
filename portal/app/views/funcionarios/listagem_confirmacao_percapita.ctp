
<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
?>

<?php if(!empty($disparos_links)):?>

    <table class='table table-striped tablesorter'>
        <thead>
            <tr>
                <th>Código</th>
                <th>Cliente</th>
                <th>Link Enviado</th>
                <th>E-mail Enviado</th>
                <th>Data Envio Link</th>
                <th>Data Validação</th>
                <th>Usuario Validação</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($disparos_links as $dado): ?>
            <tr>
                <td><?php echo $dado['DisparoLink']['codigo_cliente']; ?></td>
                <td><?php echo $dado['Cliente']['nome_fantasia']; ?></td>
                <td>
                    <?php 
                    if(!is_null($dado['DisparoLink']['link'])) {
                        echo "<a href='".$dado['DisparoLink']['link']."' title='Link' target='blank'>Link</a>";
                    }
                    ?>
                </td>
                <td><?php echo $this->Buonny->leiamais($dado['DisparoLink']['email'],25); ?> </td>
                <td><?php echo $dado['DisparoLink']['data_inclusao']; ?></td>
                <td><?php echo $dado['DisparoLink']['data_validacao']; ?></td>
                <td><?php echo $dado['Usuario']['nome']; ?></td>
                <td>
                    <?php 
                    echo ($dado['DisparoLink']['status_validacao'] == 1) ? '<span class="badge-empty badge badge-success" title="Validado"></span>' : '<span class="badge-empty badge badge-important" title="Pendente"></span>'; 
                    ?>
                </td>
            </tr>
            <?php endforeach; ?> 
        </tbody>        
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

<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>
<?php echo $this->Js->writeBuffer(); ?>
