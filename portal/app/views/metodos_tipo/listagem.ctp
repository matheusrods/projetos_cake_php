<?php if(!empty($metodos_tipo)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="input-medium">Código</th>
                <th class="input-xxlarge">Descrição</th>               
                <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($metodos_tipo as $dados): ?>
                <tr>
                    <td class="input-mini"><?php echo $dados['MetodosTipo']['codigo'] ?></td>
                    <td class="input-xxlarge"><?php echo $dados['MetodosTipo']['descricao'] ?></td>
                    <td>
    	                <?php echo $this->Html->link('', array('action' => 'editar', $dados['MetodosTipo']['codigo'], $dados['MetodosTipo']['codigo_cliente']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
                        <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-trash', 'escape' => false, 'title'=>'Excluir','onclick' => "excluir_permissao('{$dados['MetodosTipo']['codigo']}')"));?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['MetodosTipo']['count']; ?></td>
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
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

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
                url: baseUrl + 'metodos_tipo/excluir/' + codigo + '/',
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    bloquearDivSemImg($('div.lista'));  
                },
            })
            .done(function(response) {
                if(response == 1) {
                    swal('Sucesso!', 'Excluido.', 'success');
                    atualizaLista();                    
                    $('div.lista').unblock();
                } else {
                    swal('Erro!', 'Não foi excluido :)', 'error');

                    atualizaLista();
                    $('div.lista').unblock();
                }
            });
        });
    }

    function atualizaLista() {
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'metodos_tipo/listagem/' + Math.random());
    }
");
?>