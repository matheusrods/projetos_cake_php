<?php if(!empty($conselhos)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
            <th class="input-mini">Código</th>
            <th class="input-mini">Descrição</th>
            <th></th>
            <th></th>
            <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($conselhos as $conselho): ?>
            <tr>
                <td class="input-mini"><?php echo $conselho['ConselhoProfissional']['codigo'] ?></td>
                <td class="input-mini"><?php echo $conselho['ConselhoProfissional']['descricao'] ?></td>
                <td></td>
                <td></td>
                <td>
                <?php echo $this->Html->link('', array('action' => 'editar_conselho_classe', $conselho['ConselhoProfissional']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar Conselho Classe')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['ConselhoProfissional']['count']; ?></td>
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

<?php 
echo $this->Javascript->codeBlock("

     function atualizaStatus(codigo, status){
        $.ajax({
            type: 'POST',
            url: baseUrl + 'medicos/atualiza_status/' + codigo + '/' + status + '/' + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($('div.lista'));  
            },
            success: function(data){
                if(data == 1){
                    atualizaLista();
                    $('div.lista').unblock();
                    viewMensagem(1,'Os dados informados foram armazenados com sucesso!');
                } else {
                    atualizaLista();
                    $('div.lista').unblock();
                    viewMensagem(0,'Não foi possível mudar o status!');
                }
            },
            error: function(erro){
            $('div.lista').unblock();
            viewMensagem(0,'Não foi possível mudar o status!');
            }
        });
    }

    function atualizaLista() {
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'medicos/listagem_conselho_classe/' + Math.random());
    }

    
");
?>