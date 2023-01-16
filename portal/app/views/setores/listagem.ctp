<?php if(strpos($codigo_cliente, ',') == 0): // se for varios codigo_cliente oculta botão?>
    <div class='actionbar-right'>
        <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'setores', 'action' => 'incluir', $codigo_cliente, $referencia, $terceiros_implantacao), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos Setores'));?>
    </div>
<?php endif; ?>

<?php if(!empty($setores)):?>
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
            <?php foreach ($setores as $setor): ?>
            <tr>
                <td class="input-mini"><?php echo $setor['Setor']['codigo'] ?></td>
                <td class="input-xxlarge"><?php echo $setor['Setor']['descricao'] ?></td>
                <td>
                <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatus('{$setor['Setor']['codigo']}','{$setor['Setor']['ativo']}', '{$codigo_cliente}')"));?>

                <?php if($setor['Setor']['ativo']== 0): ?>
                    <span class="badge-empty badge badge-important" title="Desativado"></span>
                <?php elseif($setor['Setor']['ativo']== 1): ?>
                    <span class="badge-empty badge badge-success" title="Ativo"></span>
                <?php endif; ?>
                
                <?php echo $this->Html->link('', array('action' => 'editar',  $codigo_cliente, $setor['Setor']['codigo'], $referencia, $terceiros_implantacao), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Setor']['count']; ?></td>
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
setup_mascaras();
    function atualizaStatus(codigo, status, codigo_cliente){
        $.ajax({
            type: 'POST',
            url: baseUrl + 'setores/atualiza_status/' + codigo + '/' + status + '/' + Math.random(),
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
    div.load(baseUrl + 'setores/listagem/' + codigo_cliente + '/".$referencia."/' + Math.random());
}
   
");
?>   