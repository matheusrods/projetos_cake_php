<div class='actionbar-right'>
  <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'grupos_homogeneos_exames', 'action' => 'incluir', $codigo_cliente, $referencia), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos Grupos Homogêneos Exames'));?>
</div>

<?php if(!empty($grupos_homogeneos)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-medium">Código</th>
            <th class="input-xlarge">Descrição</th>
            <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($grupos_homogeneos as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['GrupoHomogeneoExame']['codigo'] ?></td>
                <td class="input-xxlarge"><?php echo $dados['GrupoHomogeneoExame']['descricao'] ?></td>
                <td>
                <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatus('{$dados['GrupoHomogeneoExame']['codigo']}','{$dados['GrupoHomogeneoExame']['ativo']}')"));?>

                <?php if($dados['GrupoHomogeneoExame']['ativo']== 0): ?>
                    <span class="badge-empty badge badge-important" title="Desativado"></span>
                <?php elseif($dados['GrupoHomogeneoExame']['ativo']== 1): ?>
                    <span class="badge-empty badge badge-success" title="Ativo"></span>
                <?php endif; ?>
                
                <?php echo $this->Html->link('', array('action' => 'editar', $codigo_cliente, $dados['GrupoHomogeneoExame']['codigo'], $referencia), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['GrupoHomogeneoExame']['count']; ?></td>
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
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    
<?php if($referencia != 'principal') : ?>
<div class='form-actions well'>
    <?php echo $html->link('Voltar', array('controller' => 'clientes_implantacao', 'action' => 'gerenciar_pcmso', $matriz['GrupoEconomicoCliente']['matriz']), array('class' => 'btn')); ?>
</div>
<?php endif; ?>
    <?php echo $this->Js->writeBuffer(); ?>
    <?php 
$this->addScript($this->Buonny->link_js('comum.js'));
echo $this->Javascript->codeBlock("

    function atualizaStatus(codigo, status){
        $.ajax({
            type: 'POST',
            url: baseUrl + 'grupos_homogeneos_exames/atualiza_status/' + codigo + '/' + status + '/' + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($('div.lista'));  
            },
            success: function(data){
                if(data == 1){
                    atualizaLista();
                    $('div.lista').unblock();
                    viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
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

    function atualizaLista(){
    var div = jQuery('div.lista');
    bloquearDiv(div);
    div.load(baseUrl + 'grupos_homogeneos_exames/listagem/".$codigo_cliente."/".$referencia."/' + Math.random());
}
   
");
?>