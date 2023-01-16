<?php if(!empty($listagem)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-medium">Código do Cliente/Unidade</th>
                <th>Nome do Cliente</th>
                <th>Login do usuário</th>
                <th>Nome do Usuário</th>
                <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listagem as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['Unidade']['codigo'] ?></td>
                <td><?php echo $dados['Unidade']['nome_fantasia'] ?></td>
                <td><?php echo $dados['Usuario']['apelido'] ?></td>
                <td><?php echo $dados['Usuario']['nome'] ?></td>
                <td>
                    <?php echo $this->Html->link('', array('action' => 'editar_c_c_v', $dados['ClienteValidador']['codigo'], $dados['ClienteValidador']['codigo_cliente_matriz']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
                    <?php //echo $html->link('', array('controller' => 'clientes', 'action' => 'delete_ccv', $dados['ClienteValidador']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir'), 'Confirma exclusão?'); ?>
                     <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-trash', 'escape' => false, 'title'=>'Excluir','onclick' => "excluir_permissao('{$dados['ClienteValidador']['codigo']}')"));?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['ClienteValidador']['count']; ?></td>
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

<?php echo $this->Js->writeBuffer(); ?>
<?php echo $this->Javascript->codeBlock("

    function excluir_permissao(codigo){
        swal({
            type: 'warning',
            title: 'Atenção',
            text: 'Tem certeza que deseja excluir?',
            showCancelButton: true,
            confirmButtonColor: '#FF0000',
            cancelButtonColor: '#ADD8E6',
            cancelButtonText: 'Não',
            confirmButtonText: 'Sim',
            showLoaderOnConfirm: true
        }, 
        function(){
            $.ajax({
                url: baseUrl + 'clientes/delete_ccv/' + codigo + '/',
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    bloquearDivSemImg($('div.lista'));  
                },
            })
            .done(function(response) {
                if(response == 1) {
                    swal('Sucesso!', 'A permissao deste usuario foi excluida.', 'success');
                    atualizaLista();                    
                    $('div.lista').unblock();
                } else {
                    swal('Erro!', 'A permissao deste usuario não foi excluida :)', 'error');

                    atualizaLista();
                    $('div.lista').unblock();
                }
            });
        });
    }

    function atualizaLista() {
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'clientes/lista_cliente_validadores/' + Math.random());
    }
");
?>