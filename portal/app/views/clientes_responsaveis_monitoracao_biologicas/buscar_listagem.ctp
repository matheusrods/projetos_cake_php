<?php if(!empty($registros_ambientais)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
            <th class="span2">Código</th>
            <th class="span7">Nome do responsável</th>
            <th class="acoes span3">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registros_ambientais as $dado): ?>
            <tr>
                <td class="input-mini"><?php echo $dado['Crra']['codigo'] ?></td>
                <td class="input-xlarge"><?php echo $dado['Medico']['nome'] ?></td>

                <td>                
                <?php echo $this->Html->link('', array('action' => 'editar', $dado['Crra']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar Médico')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Crra']['count']; ?></td>
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
    <?php echo $this->Js->writeBuffer(); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    
<?php echo $this->Javascript->codeBlock('

    function atualizaFornecedorMedico(){
        var div = jQuery("#fornecedor-medico-lista");
        bloquearDiv(div);
        div.load(baseUrl + "fornecedores_medicos/listagem/'.$codigo_fornecedor.'/" + Math.random());
    }
');
?>
<?php echo $this->Js->writeBuffer(); ?>
