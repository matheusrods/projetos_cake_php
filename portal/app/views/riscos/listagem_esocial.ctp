<?php //debug($riscos)?>

<?php if (!empty($riscos)): ?>
<?php echo $paginator->options(array('update' => 'div.lista')); ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-medium">Códigos</th>
            <th class="input-xxlarge">Nome agente</th>
            <th class="input-xlarge">Código agente nocivo e-Social</th>
            <th class="input-xlarge">Grupo risco</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($riscos as $dados): ?>
        <tr>
            <td class="input-mini"><?php echo $dados['Risco']['codigo'] ?>
            </td>
            <td class="input-xxlarge"><?php echo $dados['Risco']['nome_agente'] ?>
            </td>
            <td class="input-xlarge"><?php echo $dados['Risco']['codigo_agente_nocivo_esocial']; ?>
            </td>
            <td class="input-mini"><?php echo $dados['GruposRiscos']['descricao']; ?>
            </td>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Risco']['count']; ?>
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
    function atualizaListaRiscosEsocial() {   
    
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'riscos_esocial/listagem/' + Math.random());
    }
    
    function atualizaStatusRiscosEsocial(codigo)
    {

        $.ajax({
            type: 'POST',
            url: baseUrl + 'riscos_esocial/editar_status/' + codigo,
            beforeSend: function(){
                bloquearDivSemImg($('div.lista'));  
            },
            success: function(data){           
                if(data == 1){
                    atualizaListaRiscosEsocial();
                    $('div.lista').unblock();
                    viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
                } else {
                    atualizaListaRiscosEsocial();
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
