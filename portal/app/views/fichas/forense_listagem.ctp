<?php 
    echo $this->Paginator->options(array('update' => '.lista')); 
?>
<?php if( isset($dados) && !empty($dados) ): ?>

    <table class='table table-striped'>
        <thead>
            <th class="input-mini">Código</th>
            <th class="input-medium">Seguradora</th>
            <th class="input-large">Cliente</th>
            <th class="input-large">Motorista</th>
            <th class="input-mini">CPF</th>
            <th class="numeric input-mini">Ações</th>
        </thead>
        <tbody>
            
            <?php foreach($dados as $value): ?>

                <tr>
                    <td><?php echo $value['Ficha']['codigo']; ?></td>
                    <td><?php echo $value['Seguradora']['nome']; ?></td>
                    <td><?php echo $value['Cliente']['razao_social']; ?></td>
                    <td><?php echo $value['ProfissionalLog']['nome']; ?></td>                    
                    <td><?php echo substr($buonny->documento($value['ProfissionalLog']['codigo_documento']), 0, 7); ?>...</td>
                    <td class="numeric">
                        <?php  
                            echo $html->link('', array('action' => 'forense_editar', $value['FichaForense']['codigo'],$value['Ficha']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar'));
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
    <?php echo $this->Js->writeBuffer(); ?>    

<?php endif; ?>