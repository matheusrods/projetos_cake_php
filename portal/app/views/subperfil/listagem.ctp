<?php if(!empty($codigo_cliente)) : ?>

    <?php 
    //verificacao para saber se multicliente onde precisa selecionar uma empresa
    $disabled = '';
    $msg = 'Cadastrar Formulário';
    $href = "href='/portal/subperfil/incluir/{$codigo_cliente}'";
    if(strpos($codigo_cliente,",")) {
        $disabled = " disabled='disabled'";
        $href = '';
        $msg = "Necessário filtrar um dos clientes liberados.";
    }
    ?>
    <div class='actionbar-right'>
        <a <?php echo $href; ?> class="btn btn-success"  title="<?php echo $msg; ?>" <?php echo $disabled; ?>>
            <i class="icon-plus icon-white"></i> Incluir
        </a>
    </div>
<?php endif; ?>



<?php if (!empty($subperfil)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th class="input-medium">Códigos</th>
            <th class="input-xxlarge">Descrição</th>
            <th class="input-xxlarge">Tipo usuário</th>
            <th class="input-xlarge">Código cliente</th>
            <th class="acoes" style="width:75px">Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($subperfil as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['Subperfil']['codigo'] ?>
                </td>
                <td class="input-xxlarge"><?php echo $dados['Subperfil']['descricao'] ?>
                </td>
                <td class="input-xxlarge"><?php echo $dados['Subperfil']['interno'] == 1 ? "Interno" : "Externo" ?>
                </td>
                <td class="input-mini"><?php echo $dados['Subperfil']['codigo_cliente']; ?>
                </td>
                <td>
                    <?php
                    echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatusSubperfil('{$dados['Subperfil']['codigo']}')"));?>
                    <?php if($dados['Subperfil']['ativo'] == 0): ?>
                        <span class="badge-empty badge badge-important" title="Desativado" style="margin-right: 5px"></span>
                    <?php elseif($dados['Subperfil']['ativo']== 1): ?>
                        <span class="badge-empty badge badge-success" title="Ativo" style="margin-right: 5px"></span>
                    <?php endif; ?>

                    <?php echo $this->Html->link('', array('action' => 'editar', $dados['Subperfil']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar'));?>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Subperfil']['count']; ?>
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
    <?php echo $this->Js->writeBuffer(); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado! Verificar se a configuração da assinatura está ativa.</div>
<?php endif;?>

<?php
echo $this->Javascript->codeBlock("
    function atualizaListaSubperfil() {   
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'subperfil/listagem/' + Math.random());
    }
    
    function atualizaStatusSubperfil(codigo)
    {

        $.ajax({
            type: 'POST',
            url: baseUrl + 'subperfil/editar_status/' + codigo,
            beforeSend: function(){
                bloquearDivSemImg($('div.lista'));  
            },
            success: function(data){           
                if(data == 1){
                    atualizaListaSubperfil();
                    $('div.lista').unblock();
                    viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
                } else {
                    atualizaListaSubperfil();
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
