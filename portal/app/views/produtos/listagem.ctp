<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-medium">Código</th>
            <th class="input-xxlarge">Descrição</th>
            <th class="input-medium">Código Naveg</th>            
            <th class="acoes" style="width:75px">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($produtos as $produto): ?>
        <tr>
            <td class="input-mini"><?php echo $produto['Produto']['codigo'] ?></td>
            <td class="input-xxlarge"><?php echo $produto['Produto']['descricao'] ?></td>
            <td class="input-medium"><?php echo $produto['Produto']['codigo_naveg'] ?></td>
            <td>
            <?php if(!$produto['Produto']['controla_volume']){ echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatusProduto('{$produto['Produto']['codigo']}','{$produto['Produto']['ativo']}')"));?>
            <?php if($produto['Produto']['ativo']== 0): ?>
                <span class="badge-empty badge badge-important" title="Desativado"></span>
            <?php elseif($produto['Produto']['ativo']== 1): ?>
                <span class="badge-empty badge badge-success" title="Ativo"></span>
            <?php endif; ?>
            <?php } ?>
            <?php echo $this->Html->link('', array('controller' => 'produtos', 'action' => 'incluir_servicos', $produto['Produto']['codigo']), array('escape' => false, 'class' => 'icon-plus evt-incluir-servico', 'title' => 'Incluir serviço')); ?>
            <?php echo $this->Html->link('', array('action' => 'editar', $produto['Produto']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
            <?php if(!$produto['Produto']['controla_volume']) echo $this->Html->link('', array('controller' => 'produtos', 'action' => 'excluir', $produto['Produto']['codigo']), array('escape' => false, 'class' => 'icon-trash', 'title' => 'Excluir'), "Deseja realmente excluir esse produto?"); ?></td>
            
        </tr>
        <?php endforeach; ?>        
    </tbody>
</table>
<?php
echo $this->Javascript->codeBlock("
function atualizaStatusProduto(codigo, status){
    $.ajax({
        type: 'POST',
        url: baseUrl + 'produtos/editar_status_produtos/' + codigo + '/' + status + '/' + Math.random(),
        beforeSend: function(){
            bloquearDivSemImg($('div.lista'));  
        },
        success: function(data){
            if(data == 1){
                atualizaListaProdutos();
                $('div.lista').unblock();
                viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
            } else {
                atualizaListaProdutos();
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
"


);
?>

