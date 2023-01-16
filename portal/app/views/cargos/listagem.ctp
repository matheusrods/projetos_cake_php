<?php if(strpos($codigo_cliente, ',') == 0): // se for varios codigo_cliente oculta botão?>
<div class='actionbar-right'>
    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'cargos', 'action' => 'incluir', $codigo_cliente, $referencia, $terceiros_implantacao), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos Cargos'));?>
</div>
<?php endif; ?>
<?php if(!empty($cargos)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-mini">Código</th>
            <th class="input-xlarge">Descrição</th>
            <th class="input-xlarge">CBO</th>
            <th class="acoes input-mini">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cargos as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['Cargo']['codigo'] ?></td>
                <td class="input-xlarge"><?php echo $dados['Cargo']['descricao'] ?></td>               
                <td class="input-xlarge"><?php echo $dados['Cbo']['descricao_cbo'] ?></td>               
                <td class="input-mini">
                    <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatus('{$dados['Cargo']['codigo']}','{$dados['Cargo']['ativo']}', '{$dados['Cargo']['codigo_cliente']}')"));?>

                    <?php if($dados['Cargo']['ativo']== 0): ?>
                        <span class="badge-empty badge badge-important" title="Desativado"></span>
                    <?php elseif($dados['Cargo']['ativo']== 1): ?>
                        <span class="badge-empty badge badge-success" title="Ativo"></span>
                    <?php endif; ?>
                    
                    <?php echo $this->Html->link('', array('action' => 'editar', $dados['Cargo']['codigo_cliente'], $dados['Cargo']['codigo'], $referencia, $terceiros_implantacao), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>

                    <?php if($dados['Cargo']['ativo']== 1): ?>
                        <?php echo $this->Html->link('', array('controller' => 'clientes_setores_cargos', 'action' => 'index', $dados['Cargo']['codigo_cliente'],'implantacao', $dados['Cargo']['codigo'], $terceiros_implantacao), array('class' => 'icon-wrench', 'title' => 'Criar Hierarquia')); ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Cargo']['count']; ?></td>
            </tr>
        </tfoot>    
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
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 
<?php if(strpos($codigo_cliente, ',') == 0): // se for varios codigo_cliente oculta botão?>
<?php if($referencia == "implantacao"): ?>
    <div class='form-actions well'>
        <?php if($terceiros_implantacao == 'terceiros_implantacao'): ?>
            <?php echo $html->link('Voltar para Estrutura', array('controller' => 'clientes_implantacao', 'action' => 'estrutura', $codigo_cliente, $referencia, $terceiros_implantacao), array('class' => 'btn')); ?>
        <?php else: ?>
            <?php echo $html->link('Voltar para Estrutura', array('controller' => 'clientes_implantacao', 'action' => 'estrutura', $codigo_cliente, $referencia), array('class' => 'btn')); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php endif; ?>

    <?php echo $this->Js->writeBuffer(); ?>
    <?php 
    echo $this->Javascript->codeBlock("

    function atualizaStatus(codigo, status, codigo_cliente){
        $.ajax({
            type: 'POST',
            url: baseUrl + 'cargos/atualiza_status/' + codigo + '/' + status + '/' + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($('div.lista'));  
            },
            success: function(data){
                
                if(data == 1){
                    atualizaLista(codigo_cliente);
                    $('div.lista').unblock();
                    viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
                } else {
                    atualizaLista(codigo_cliente);
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

    function atualizaLista(codigo_cliente) {
    var div = jQuery('div.lista');
    bloquearDiv(div);
    div.load(baseUrl + 'cargos/listagem/'+ codigo_cliente + '/".$referencia."/' + Math.random());
}
");
?>