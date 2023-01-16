<?php 
    echo $this->Paginator->options(array('update' => '.lista')); 
?>
<?php if( isset($dados) && !empty($dados) ): ?>

    <table class='table table-striped'>
        <thead>
            <th class="input-mini">Codigo</th>
            <th class="input-xlarge">Cid. Origem</th>
            <th class="input-medium">Cid. Destino</th>
            <th class="numeric input-medium">KM</th>
            <th class="input-medium">Descrição</th>
            <th class="numeric input-mini">Ações</th>
        </thead>
        <tbody>
            
            <?php foreach($dados as $value): ?>

                <tr>
                    <td><?php echo $value['Rota']['codigo']; ?></td>
                    <td><?php echo $value['CidadeOrigem']['descricao'].' - '.$value['CidadeOrigem']['estado']; ?></td>
                    <td><?php echo $value['CidadeDestino']['descricao'].' - '.$value['CidadeDestino']['estado']; ?></td>
                    <td class="numeric"><?php echo $value['Rota']['km']; ?></td>                    
                    <td><?php echo $value['Rota']['descricao']; ?></td>                                        
                    <td class="numeric">
                        <?php  
                            echo $html->link('', array('action' => 'editar', $value['Rota']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar'));
                            echo '&nbsp;&nbsp;';
                            echo $html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => "javascript:excluir_rota('{$value['Rota']['codigo']}')"));
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

<?php 
echo $this->Javascript->codeBlock("
function excluir_rota(codigo) {    
    if (confirm('Deseja realmente excluir essa rota?'))    
        location.href = '/portal/rotas/excluir/' + codigo;
}
"); ?>