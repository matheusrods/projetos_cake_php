<?php if(!empty($usuarios)): ?>
    <?php
        echo $paginator->options(array('update' => 'div.lista'));
        $total_paginas = $this->Paginator->numbers();
    ?>

    <?php if (isset($minha_configuracao) && $minha_configuracao == "minha_configuracao") : ?>

        <?php if (!empty($codigo_cliente) && is_array($codigo_cliente)) :?>
            <div class='actionbar-right'>
                <a class="btn btn-success dialog_incluir" title="Cadastrar Novos Usuarios"><i class="icon-plus icon-white"></i> Incluir</a>
            </div>
        <?php else : ?>
            <div class='actionbar-right'><?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir_minha_configuracao', $codigo_cliente), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos Usuarios'));?></div>
        <?php endif; ?>

    <?php endif; ?>

    <?php if(isset($authUsuario['Usuario']['codigo_empresa']) && is_null($authUsuario['Usuario']['codigo_empresa'])) : ?>
        <div class='well'>
            <div class='pull-right'>
                <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'exportar'), array('escape' => false, 'title' =>'Exportar para Excel'));?>
            </div>
        </div>
    <?php endif; ?>

    <table class="table table-striped">
        <thead>
        <tr>
            <th class="input-small"><?= $this->Paginator->sort('Código', 'codigo') ?></th>
            <th class="input-small"><?= $this->Paginator->sort('Login', 'apelido') ?></th>
            <th class="input-medium"><?= $this->Paginator->sort('Nome', 'nome') ?></th>
            <th class="input-small"><?= $this->Paginator->sort('Email', 'email') ?></th>
            <th class="input-small"><?= $this->Paginator->sort('Cliente', 'codigo_cliente') ?></th>
            <th class="input-small"><?= $this->Paginator->sort('Perfil', 'descricao') ?></th>
            <th style='width:75px'>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?= $usuario['Usuario']['codigo'] ?></td>
                <td><?= $usuario['Usuario']['apelido'] ?></td>
                <td><?= $usuario['Usuario']['nome'] ?></td>
                <td><?= str_replace(';', '; ', $usuario['Usuario']['email']) ?></td>
                <td><?= $usuario['Usuario']['codigo_cliente'] ?></td>
                <td><?= $usuario['Uperfil']['descricao'] ?></td>
                <td>
                    <?= $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatusUsuarios('{$usuario['Usuario']['codigo']}','{$usuario['Usuario']['ativo']}')"));?>
                    <?php if($usuario['Usuario']['ativo']== 0): ?>
                        <span class="badge-empty badge badge-important" title="Desativado"></span>
                    <?php elseif($usuario['Usuario']['ativo']== 1): ?>
                        <span class="badge-empty badge badge-success" title="Ativo"></span>
                    <?php endif; ?>

                    <?php

                    if (isset($minha_configuracao) && $minha_configuracao == "minha_configuracao") {
                        echo $html->link('', array('action' => "editar_minha_configuracao", $usuario['Usuario']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar'));
                    } else {
                        echo $html->link('', array('action' => $action, $usuario['Usuario']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar'));
                    }

                    ?>

                    <?= $html->link('', array('controller' => 'usuarios_historicos', 'action' => 'ultimos_acessos', $usuario['Usuario']['codigo']), array('class' => 'icon-eye-open', 'title' => 'Logs')) ?>

                    <?php 
                        if (isset($minha_configuracao) && $minha_configuracao == "minha_configuracao") {
                            echo $html->link('', array('action' => 'envia_acesso_cliente', $usuario['Usuario']['codigo'], 'minha_configuracao'), array('class' => 'icon-envelope', 'title' => 'Enviar e-mail com login e senha do usuário'));
                        } else {
                            echo $html->link('', array('action' => 'envia_acesso_cliente', $usuario['Usuario']['codigo']), array('class' => 'icon-envelope', 'title' => 'Enviar e-mail com login e senha do usuário'));
                        }
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

<?php else : ?>
    <div class="alert">Nenhum dado foi encontrado!</div>
<?php endif; ?>

<script type="text/javascript">
    function fecharMsg(){
        setInterval(
            function(){
                $('div.message.container').css({ "opacity": "0", "display": "none" });
            },
            4000
        );
    }
    function gerarMensagem(css, mens){
        $('div.message.container').css({ "opacity": "1", "display": "block" });
        $('div.message.container').html('<div class="alert alert-'+css+'"><p>'+mens+'</p></div>');
        fecharMsg();
    }
    function viewMensagem(tipo, mensagem){
        switch(tipo){
            case 1:
                gerarMensagem('success',mensagem);
                break;
            case 2:
                gerarMensagem('success',mensagem);
                break;
            default:
                gerarMensagem('error',mensagem);
                break;
        }
    }

    function excluir_usuarios(codigo) {
        if (confirm('Deseja realmente excluir ?')){
            $.ajax({
                type: 'POST',
                url: baseUrl + 'usuarios/excluir_usuario/' + codigo + '/' + Math.random(),
                beforeSend: function(){
                    bloquearDivSemImg($('div.lista'));
                },
                success: function(data) {
                    atualizaListaUsuarios();
                    $('div.lista').unblock();
                    viewMensagem(1,'Excluído com sucesso!');
                },
                error: function(erro){
                    $('div.lista').unblock();
                    viewMensagem(0,'Não foi possível excluir!');
                }
            });
        }
    }
    function atualizaStatusUsuarios(codigo, status){
        $.ajax({
            type: 'POST',
            url: baseUrl + 'usuarios/editar_status_usuarios/' + codigo + '/' + status + '/' + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($('div.lista'));
            },
            success: function(data){
                if(data == 1){
                    atualizaListaUsuarios();
                    $('div.lista').unblock();
                    viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
                } else {
                    atualizaListaUsuarios();
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

    <?php if (isset($minha_configuracao) && $minha_configuracao == "minha_configuracao") : ?>
        $( ".dialog_incluir" ).on( "click", function() {
            
            <?php if (!empty($codigo_cliente) && is_array($codigo_cliente)) :?>//seleciona o cliente
                swal({//mostra mensagem de confirmação
                    type: 'warning',
                    title: 'Atenção',
                    text: 'Necessário filtrar um dos clientes.'
                });
                return;//para a execução do script
            <?php endif; ?>//fim do if
        });
    <?php endif; ?>
</script>