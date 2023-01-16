<?php if(!empty($fichas_assistenciais)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
             <th class="input-medium">Código Ficha</th>
             <th class="input-medium">Código Pedido</th>
             <th class="input-medium">Cliente</th>
             <th class="input-medium">Funcionário</th>
             <th class="input-medium">Médico</th>
             <th class="acoes" style="width:75px">Ações</th>
         </tr>
     </thead>
     <tbody>
        <?php foreach ($fichas_assistenciais as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['FichaAssistencial']['codigo'] ?></td>
                <td class="input-mini"><?php echo $dados['FichaAssistencial']['codigo_pedido_exame'] ?></td>
                <td class="input-mini"><?php echo $dados['Cliente']['razao_social'] ?></td>
                <td class="input-mini"><?php echo $dados['Funcionario']['nome'] ?></td>
                <td class="input-mini"><?php echo $dados['Medico']['nome'] ?></td>
                <td>
                    <?php // echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatusFontesGeradoras('{$dados['FichaAssistencial']['codigo']}','{$dados['FichaAssistencial']['ativo']}')"));?>

                    <?php // if($dados['FichaAssistencial']['ativo']== 0): ?>
                    <!-- <span class="badge-empty badge badge-important" title="Desativado"></span> -->
                    <?php // elseif($dados['FichaAssistencial']['ativo']== 1): ?>
                    <!-- <span class="badge-empty badge badge-success" title="Ativo"></span> -->
                    <?php // endif; ?>

                    <?php echo $this->Html->link('', array('action' => 'editar', 
                                                     $dados['FichaAssistencial']['codigo']), 
                                                     array('data-toggle' => 'tooltip', 
                                                           'class' => 'icon-edit ', 
                                                           'title' => 'Editar',
                                                            'style' => 'text-decoration: none; border-radius: 100%; padding: 3px; background-position: -93px -68px;')
                                                ); 
                    ?>&nbsp;
                    <?php echo $this->Html->link('', array('action' => 'imprimir_ficha_assistencial', 
                                                           $dados['FichaAssistencial']['codigo'], 
                                                           $dados['PedidoExame']['codigo'], 
                                                           $dados['Funcionario']['codigo']), 
                                                     array('data-toggle' => 'tooltip', 
                                                           'title' => 'Imprimir Ficha Assitencial', 
                                                            'class' => 'icon-print ',
                                                            'style' => 'border: 1px solid; text-decoration: none; border-radius: 100%; padding: 3px; background-position: -93px -45px;')
                                                ); 
                    ?>&nbsp;
                    <?php 
                    if($dados['FichaAssistencialResposta']['resposta'] == 1){
                        echo $this->Html->link('', array('action' => 'imprimir_receita_medica', 
                                                               $dados['FichaAssistencial']['codigo'],
                                                               $dados['PedidoExame']['codigo'], 
                                                               $dados['Funcionario']['codigo']),
                                                         array('data-toggle' => 'tooltip', 
                                                               'title' => 'Imprimir Receita Médica', 
                                                               'class' => 'icon-print ',
                                                               'style' => 'background-color: #33CCFF; text-decoration: none; border: 1px solid; border-radius: 100%; padding: 3px; background-position: -93px -45px;')
                                                    ); 
                        echo('&nbsp;');
                    }

                    if($dados['Atestado']['exibir_ficha_assistencial'] == 1){

                        echo $this->Html->link('', array('action' => 'imprimir_atestado_medico', 
                                                               $dados['FichaAssistencial']['codigo'], 
                                                               $dados['PedidoExame']['codigo'], 
                                                               $dados['Funcionario']['codigo']), 
                                                         array('data-toggle' => 'tooltip', 
                                                               'title' => 'Imprimir Atestado Médico', 
                                                               'class' => 'icon-print ',
                                                               'style' => 'background-color: #FF9933; text-decoration: none; border: 1px solid; border-radius: 100%; padding: 3px; background-position: -93px -45px;')
                                                    ); 
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['FichaAssistencial']['count']; ?></td>
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
	$('[data-toggle=\"tooltip\"]').tooltip();
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