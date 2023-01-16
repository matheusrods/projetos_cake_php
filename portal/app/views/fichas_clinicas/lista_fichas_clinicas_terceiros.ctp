<?php if(isset($fichas_clinicas) && count($fichas_clinicas)) : ?>

    <?php echo $paginator->options(array('update' => 'div.lista')); ?>

    <div class='well'>
        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array('controller' => $this->name, 'action' => $this->action, 'destino','export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-medium">Código Ficha</th>
                <th class="input-medium">Código Pedido</th>
               <th class="input-medium">Cliente</th>
               <th class="input-medium">Unidade</th>
               <th class="input-medium">Funcionário</th>
               <th class="input-medium">CPF</th>
               <th class="input-medium">Médico</th>
               <th class="input-medium">Data Inclusão</th>
            	<!-- <th class="acoes" style="width:75px">Ações</th> -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fichas_clinicas as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['FichaClinica']['codigo'] ?></td>
                <td class="input-mini"><?php echo $dados['FichaClinica']['codigo_pedido_exame'] ?></td>
                <td class="input-mini"><?php echo $dados['Cliente']['razao_social'] ?></td>
                <td class="input-mini"><?php echo $dados['Unidade']['nome_fantasia'] ?></td>
                <td class="input-mini"><?php echo $dados['Funcionario']['nome'] ?></td>
                <td class="input-mini"><?php echo $dados[0]['cpf'] ?></td>
                <td class="input-mini"><?php echo $dados['Medico']['nome'] ?></td>
                <td class="input-mini"><?php echo $dados['FichaClinica']['data_inclusao'] ?></td>
                <!-- <td>                 -->
                    <?php //echo $this->Html->link('', array('action' => 'editar', $dados['FichaClinica']['codigo']), array('data-toggle' => 'tooltip', 'class' => 'icon-edit ', 'title' => 'Editar')); ?>
                    <?php //echo $this->Html->link('', array('action' => 'imprimir_relatorio', $dados['FichaClinica']['codigo'], $dados['PedidoExame']['codigo'], $dados['Funcionario']['codigo']), array('data-toggle' => 'tooltip', 'title' => 'Imprimir relatório', 'class' => 'icon-print ')); ?>
                    <!-- <a href="javascript:void(0);" onclick="log_ficha_clinica('<?php //echo $dados['PedidoExame']['codigo']; ?>', '<?php //echo $dados['FichaClinica']['codigo']; ?>');"><i class="icon-eye-open" title="Log da Ficha Clinica"></i></a> -->
                <!-- </td> -->
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['FichaClinica']['count']; ?></td>
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
<?php //echo $javascript->link('comum.js'); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<?php echo $this->Javascript->codeBlock("
    $('[data-toggle=\"tooltip\"]').tooltip();
    function atualizaStatusFontesGeradoras(codigo, status){
        $.ajax({
            type: 'POST',
            url: baseUrl + 'fichas_clinicas/atualiza_status/' + codigo + '/' + status + '/' + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($('div.lista'));  
            },
            success: function(data){
                if(data == 1){
                    atualizaListaFichasClinicas();
                    $('div.lista').unblock();
                    viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
                } else {
                    atualizaListaFichasClinicas();
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

    function fecharMsg(){
        setInterval(
            function(){
                $('div.message.container').css({ 'opacity': '0', 'display': 'none' });
            },
            4000
        );     
    }

    function gerarMensagem(css, mens){
        $('div.message.container').css({ 'opacity': '1', 'display': 'block' });
        $('div.message.container').html('<div class=\"alert alert-'+css+'\"><p>'+mens+'</p></div>');
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

    function atualizaListaFichasClinicas() {
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'fichas_clinicas/lista_fichas_clinicas_terceiros/' + Math.random());
    }

    function log_ficha_clinica(codigo_pedido_exame, codigo_ficha){
        var janela = window_sizes();
        window.open(baseUrl + 'fichas_clinicas/lista_log_ficha_clinica/' + codigo_pedido_exame + '/' + codigo_ficha + '/' + Math.random(), janela, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
    }
");
?>