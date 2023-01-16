<?php if(!empty($codigo_cliente)) : ?>

    <?php 
    //verificacao para saber se multicliente onde precisa selecionar uma empresa
    $disabled = '';
    $msg = 'Cadastrar nova Origem Ferramenta';
    $href = "href='/portal/origem_ferramenta/incluir/{$codigo_cliente}'";
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

<?php if (!empty($origem_ferramenta)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th class="input-medium">Códigos</th>
            <th class="input-xxlarge">Descrição</th>
            <th class="input-xlarge">Código cliente</th>
            <th class="input-xlarge">Produto</th>
            <th class="acoes" style="width:75px">Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($origem_ferramenta as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['OrigemFerramenta']['codigo'] ?>
                </td>
                <td class="input-xxlarge"><?php echo $dados['OrigemFerramenta']['descricao'] ?>
                </td>
                <td class="input-mini"><?php echo $dados['OrigemFerramenta']['codigo_cliente']; ?></td>
                <td class="input-mini"><?php echo $dados['Produto']['descricao']; ?></td>
                <td>
                    <?php
                    echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatusOrigemFerramenta('{$dados['OrigemFerramenta']['codigo']}')"));?>
                    <?php if($dados['OrigemFerramenta']['ativo'] == 0): ?>
                        <span class="badge-empty badge badge-important" title="Desativado" style="margin-right: 5px"></span>
                    <?php elseif($dados['OrigemFerramenta']['ativo']== 1): ?>
                        <span class="badge-empty badge badge-success" title="Ativo" style="margin-right: 5px"></span>
                    <?php endif; ?>

                    <?php echo $this->Html->link('', array('action' => 'editar', $dados['OrigemFerramenta']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar'));?>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['OrigemFerramenta']['count']; ?>
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
    <div class="alert">Nenhum dado foi encontrado! Verificar se a configuração da assinatura está ativa.</div>
<?php endif;?>

<?php
//echo $this->Js->writeBuffer();

echo $this->Javascript->codeBlock("
    function atualizaListaOrigemFerramenta() {   
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'origem_ferramenta/listagem/' + Math.random());
    }
    
    function atualizaStatusOrigemFerramenta(codigo)
    {

        $.ajax({
            type: 'POST',
            url: baseUrl + 'origem_ferramenta/editar_status/' + codigo,
            beforeSend: function(){
                bloquearDivSemImg($('div.lista'));  
            },
            success: function(data){           
                if(data == 1){
                    atualizaListaOrigemFerramenta();
                    $('div.lista').unblock();
                    viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
                } else {
                    atualizaListaOrigemFerramenta();
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
