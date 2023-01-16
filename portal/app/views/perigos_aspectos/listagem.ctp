<?php //debug($riscos_tipo)?>

<?php if (!empty($perigos_aspectos)):?>
<?php echo $paginator->options(array('update' => 'div.lista')); ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-medium">Códigos</th>
            <th class="input-xxlarge">Descrição</th>
            <th class="input-xlarge">Risco tipo</th>
            <th class="input-xlarge">Código cliente</th>
            <th class="acoes" style="width:75px">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($perigos_aspectos as $dados): ?>
        <tr>
            <td class="input-mini"><?php echo $dados['PerigosAspectos']['codigo'] ?>
            </td>
            <td class="input-xxlarge"><?php echo $dados['PerigosAspectos']['descricao'] ?>
            </td>
            <td class="input-xlarge"><?php echo $dados['RiscosTipo']['descricao']; ?>
            </td>
            <td class="input-mini"><?php echo $dados['PerigosAspectos']['codigo_cliente']; ?>
            </td>
            <td>
                <?php
                echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatusPerigosAspectos('{$dados['PerigosAspectos']['codigo']}','{$dados['PerigosAspectos']['descricao']}','{$dados['PerigosAspectos']['codigo_risco_tipo']}','{$dados['PerigosAspectos']['ativo']}','{$dados['PerigosAspectos']['codigo_cliente']}')"));?>
                <?php if($dados['PerigosAspectos']['ativo'] == 0): ?>
                    <span class="badge-empty badge badge-important" title="Desativado" style="margin-right: 5px"></span>
                <?php elseif($dados['PerigosAspectos']['ativo']== 1): ?>
                    <span class="badge-empty badge badge-success" title="Ativo" style="margin-right: 5px"></span>
                <?php endif; ?>

                <?php echo $this->Html->link('', array('action' => 'editar', $dados['PerigosAspectos']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar'));?>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['PerigosAspectos']['count']; ?>
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
    <div class='counter span6'>
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
    </div>
</div>

<?php else:?>
<div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<?php
echo $this->Js->writeBuffer();

echo $this->Javascript->codeBlock("
    function atualizaListaPerigosAspectos() {   
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'perigos_aspectos/listagem/' + Math.random());
    }
    
    function atualizaStatusPerigosAspectos(codigo, descricao, codigo_risco_tipo, status, codigo_cliente)
    {

        $.ajax({
            type: 'POST',
            url: baseUrl + 'perigos_aspectos/editar_status/' + codigo + '/' + descricao + '/' + codigo_risco_tipo + '/' + status + '/' + '/' + codigo_cliente + '/' + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($('div.lista'));  
            },
            success: function(data){           
                if(data == 1){
                    atualizaListaPerigosAspectos();
                    $('div.lista').unblock();
                    viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
                } else {
                    atualizaListaPerigosAspectos();
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
    
    function fecharMsg()
    {
        setInterval(
            function(){
                $('div.message.container').css({ 'opacity': '0', 'display': 'none' });
            },
            4000
        );     
    }
    
    function gerarMensagem(css, mens)
    {
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
");
