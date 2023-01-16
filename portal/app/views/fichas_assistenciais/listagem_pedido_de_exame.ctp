<?php if(!empty($pedidosExames)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
             <th class="input-medium">Código Pedido de Exame</th>
             <th class="input-medium">Cliente</th>
             <th class="input-medium">Funcionário</th>
             <th class="acoes" style="width:75px">Ações</th>
         </tr>
     </thead>
     <tbody>
        <?php foreach ($pedidosExames as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['PedidoExame']['codigo'] ?></td>
                <td class="input-mini"><?php echo $dados['Cliente']['razao_social'] ?></td>
                <td class="input-mini"><?php echo $dados['Funcionario']['nome'] ?></td>
                <td>
                    <?php echo $this->Html->link('Criar Ficha Assistencial', array('action' => 'incluir', 
                                                                              $dados['PedidoExame']['codigo']), 
                                                                        array('class' => 'btn btn-default btn-small', 
                                                                              'title' => 'Editar')
                                                ); 
                    ?>
            </td>
        </tr>
    <?php endforeach ?>
</tbody>
<tfoot>
    <tr>
        <td colspan = "4">
            <strong>Total</strong> <?php echo $this->Paginator->params['paging']['PedidoExame']['count']; ?>
        </td>
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
<?php 
echo $this->Javascript->codeBlock("

    function atualizaStatusFontesGeradoras(codigo, status){
        $.ajax({
            type: 'POST',
            url: baseUrl + 'fichas_assistenciais/atualiza_status/' + codigo + '/' + status + '/' + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($('div.lista'));  
            },
            success: function(data){
                if(data == 1){
                    atualizaListaFichasAssistenciais();
                    $('div.lista').unblock();
                    viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
                } else {
                    atualizaListaFichasAssistenciais();
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

    function atualizaListaFichasAssistenciais() {
       var div = jQuery('div.lista');
       bloquearDiv(div);
       div.load(baseUrl + 'fichas_assistenciais/listagem/' + Math.random());
   }
   ");
   ?>
   <?php //echo $javascript->link('comum.js'); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    