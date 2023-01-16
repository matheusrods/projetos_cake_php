<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
    

    <table class="table table-striped" style='table-layout:fixed'>
        
        <thead>
            <tr>
                <th class='input-large'>Seguradora</th>
                <th>Razão Social</th>
                <th class='input-medium'>CPF</th>
                <th class='input-mini'>Tempo</th>
            <!--<th class='input-large'>Produto</th> Caso  seja  necessario utilizar novamente -->
                <th class='input-medium'>Data do Cadastro</th>
                <th class='input-medium'>Categoria</th>
                <th style="width:13px"></th>
            </tr>
        </thead>
        <tbody>

            <?php foreach($listar as $listar):?>
                <?php foreach($listar as $lista):  ?>
                    <tr>
                        <td class='input-large' style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?php echo $lista['nome_seguradora'];?></td>
                        <td style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?php echo $lista['razao_social']; ?></td>
                        <td class='input-medium'><?php echo $buonny->documento($lista['codigo_documento']); ?></td>
                        <td class='input-mini'><?php echo $lista['tempo_restante']; ?></td>
                    <!--<td><?//php echo $lista['produto_descricao']; ?></td> Caso  seja  necessario utilizar novamente -->
                        <td class='input-medium'><?php echo $lista['data_inclusao']; ?></td>
                        <td class='input-medium'><?php echo $lista['profissional_descricao']; ?></td>
                        <td><?php echo $html->link('', array('controller' => 'fichas_status_criterios', 'action' => 'editar', $lista['codigo_ficha']), array('class' => 'icon-eye-open', 'title' => 'Pesquisar'));?></td>
                    </tr>
                <?php endforeach; ?>    
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




    
    
