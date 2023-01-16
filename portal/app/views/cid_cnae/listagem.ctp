<?php if(!empty($cid_cnae)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-mini">Código</th>
            <th class="input-small">CID</th>
            <th class="input-xlarge">Descrição</th>
            <th class="input-small">CNAE</th>
            <th class="input-xlarge">Ramo de atividade</th>
            <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cid_cnae as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['CidCnae']['codigo'] ?></td>
                <td class="input-small"><?php echo $dados['Cid']['codigo_cid10'] ?></td>
                <td class="input-xlarge"><?php echo (strlen($dados['Cid']['descricao']) > 50 ) ? substr($dados['Cid']['descricao'],0,49)."..." :  $dados['Cid']['descricao'] ?></td>
                <td class="input-small"><?php echo $dados['Cnae']['cnae'] ?></td>
                <td class="input-xlarge"><?php echo (strlen($dados['Cnae']['descricao']) > 50 ) ? substr($dados['Cnae']['descricao'],0,49)."..." :  $dados['Cnae']['descricao']  ?></td>
                <td>
                <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatus('{$dados['CidCnae']['codigo']}','{$dados['CidCnae']['ativo']}')"));?>

                <?php if($dados['CidCnae']['ativo']== 0): ?>
                    <span class="badge-empty badge badge-important" title="Desativado"></span>
                <?php elseif($dados['CidCnae']['ativo']== 1): ?>
                    <span class="badge-empty badge badge-success" title="Ativo"></span>
                <?php endif; ?>
                
                <?php echo $this->Html->link('', array('action' => 'editar', $dados['CidCnae']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['CidCnae']['count']; ?></td>
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

     function atualizaStatus(codigo, status){
        $.ajax({
            type: 'POST',
            url: baseUrl + 'cid_cnae/atualiza_status/' + codigo + '/' + status + '/' + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($('div.lista'));  
            },
            success: function(data){
                if(data == 1){
                    atualizaLista();
                    $('div.lista').unblock();
                    viewMensagem(1,'Os dados informados foram armazenados com sucesso!');
                } else {
                    atualizaLista();
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

    function atualizaLista() {
    var div = jQuery('div.lista');
    bloquearDiv(div);
    div.load(baseUrl + 'cid_cnae/listagem/' + Math.random());
    }
");
?>
<?php //echo $javascript->link('comum.js'); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    