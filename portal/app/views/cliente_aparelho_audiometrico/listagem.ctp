<?php if(isset($lista) && count($lista)) : ?>
    <?php 
        echo $paginator->options(array('update' => 'div.lista')); 
        $total_paginas = $this->Paginator->numbers(); 
    ?>
        
    <table class="table table-striped">
        <thead>
            <tr>
            <th><?= $this->Paginator->sort('Código', 'codigo') ?></th>
            <th><?= $this->Paginator->sort('Descrição', 'descricao') ?></th>
            <th><?= $this->Paginator->sort('Fabricante', 'fabricante') ?></th>
            <th><?= $this->Paginator->sort('Cod. Usuário Inativação', 'codigo_usuario_inativacao') ?></th>
            <th><?= $this->Paginator->sort('Cod. Prestador', 'codigo_fornecedor') ?></th>
            <th><?= $this->Paginator->sort('Prestador', 'nome_prestador') ?></th>
            <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($lista as $dados): ?>
                <?php 
                    $texto = ' - '; 
                ?>
            <tr>
                <td><?php echo $dados['AparelhoAudiometrico']['codigo'] ?></td>
                <td><?php echo $dados['AparelhoAudiometrico']['descricao'] ?></td>
                <td><?php echo $dados['AparelhoAudiometrico']['fabricante'] ?></td>
                <td>
                    <?php if (empty($dados['UsuarioInativacao']['apelido'])): ?>
                        <?php echo $texto; ?>
                    <?php else: ?>
                        <?php echo $dados['UsuarioInativacao']['apelido'] ?>   
                    <?php endif; ?>     
                </td>
                <td><?php echo $dados['ApAudioFornecedor']['codigo_fornecedor'] ?></td>
                <td><?php echo $dados[0]['nome_prestador'] ?></td>
                <td>
                <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatus('{$dados['AparelhoAudiometrico']['codigo']}','{$dados['AparelhoAudiometrico']['ativo']}')"));?>

                <?php if($dados['AparelhoAudiometrico']['ativo']== 0): ?>
                    <span class="badge-empty badge badge-important" title="Desativado"></span>
                <?php elseif($dados['AparelhoAudiometrico']['ativo']== 1): ?>
                    <span class="badge-empty badge badge-success" title="Ativo"></span>
                <?php endif; ?>
                
                <?php echo $this->Html->link('', array('action' => 'editar', $dados['AparelhoAudiometrico']['codigo'], $dados['AparelhoAudiometrico']['codigo_cliente'], $dados['ApAudioFornecedor']['codigo'] ), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['AparelhoAudiometrico']['count']; ?></td>
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
    function atualizaStatus(codigo, status){
        $.ajax({
            type: "POST",
            url: baseUrl + "cliente_aparelho_audiometrico/atualiza_status/" + codigo + "/" + status + "/" + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($("div.lista"));   
            },
            success: function(data){
                if(data == 1){
                    atualizaLista();
                    $("div.lista").unblock();
                    } else {
                    atualizaLista();
                   $("div.lista").unblock();
                }
            },
            error: function(erro){
            $("div.lista").unblock();
            }
        });
    }

    function atualizaLista(){
        var div = jQuery("div#cliente-fornecedor-lista");
        bloquearDiv(div);
        div.load(baseUrl + "cliente_aparelho_audiometrico/listagem/" + Math.random());
    }   
    '); 
?>