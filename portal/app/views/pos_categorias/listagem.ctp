<?php if(!empty($registros)):?>


    <?= $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="input-medium">Código</th>
                <th>Ferramenta</th>
                <th>Tipo de Observação</th>
                <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registros as $dados): ?>
            <tr>
                <td><?= $dados[0]['codigo_pos_categoria'];?></td>
                <td><?= $dados[0]['ferramenta_descricao'];?></td>
                <td><?= $dados[0]['categoria_descricao'];?></td>
                <td>
                    <?php if($dados[0]['codigo_pos_categoria']): ?>

                        <?= $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatus('{$dados[0]['codigo_pos_categoria']}','{$dados[0]['categoria_ativo']}')"));?>

                        <?php if($dados[0]['categoria_ativo']== 0): ?>
                            <span class="badge-empty badge badge-important" title="Desativado"></span>
                        <?php elseif($dados[0]['categoria_ativo']== 1): ?>
                            <span class="badge-empty badge badge-success" title="Ativo"></span>
                        <?php endif; ?>

                        <?= $this->Html->link('', array('action' => 'editar', $dados[0]['codigo_pos_categoria'], $dados[0]['codigo_cliente']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>

                    <?php endif; ?>

                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?= $this->Paginator->params['paging']['Cliente']['count']; ?></td>
            </tr>
        </tfoot>    
    </table>
    <div class='row-fluid'>
        <div class='numbers span6'>
            <?= $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?= $this->Paginator->numbers(); ?>
            <?= $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span6'>
            <?= $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            
        </div>
    </div>
    <?= $this->Js->writeBuffer(); ?>
    <?= $this->Javascript->codeBlock('

    function atualizaLista() {
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "pos_categorias/listagem/'.$codigo_cliente.'/" + Math.random());
    }

    function atualizaStatus(codigo, status){
        $.ajax({
            type: "POST",
            url: baseUrl + "pos_categorias/atualiza_status/" + codigo + "/" + status + "/" + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($("div.lista"));  
            },
            success: function(data){
                if(data == 1){
                    atualizaLista();
                    $("div.lista").unblock();
                    viewMensagem(1,"Os dados informados foram armazenados com sucesso!");
                } else {
                    atualizaLista();
                    $("div.lista").unblock();
                    viewMensagem(0,"Não foi possível mudar o status!");
                }
            },
            error: function(erro){
            $("div.lista").unblock();
            viewMensagem(0,"Não foi possível mudar o status!");
            }
        });
    }

    function fecharMsg(){
        setInterval(
            function(){
                $("div.message.container").css({ "opacity": "0", "display": "none" });
            },
            4000
        );     
    }

    function gerarMensagem(css, mens){
        $("div.message.container").css({ "opacity": "1", "display": "block" });
        $("div.message.container").html("<div class=\"alert alert-"+css+"\"><p>"+mens+"</p></div>");
        fecharMsg();
    }

    function viewMensagem(tipo, mensagem){
        switch(tipo){
            case 1:
                gerarMensagem("success",mensagem);
                break;
            case 2:
                gerarMensagem("success",mensagem);
                break;
            default:
                gerarMensagem("error",mensagem);
                break;
        }    
    } 

');
?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    
