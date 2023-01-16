<?php if(!empty($cnae)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-small">CNAE</th>
               <th class="input-small">Seção</th>
            <th class="input-xxlarge">Descrição</th>
            <th class="input-small">Grau de Risco</th>
            <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php 

            foreach ($cnae as $dados): ?>
            <tr>
                <td class="input-small"><?php echo $dados['Cnae']['cnae'];?></td>
                <td class="input-small"><?php echo $dados['Cnae']['secao'];?></td>
                <td class="input-xxlarge"><?php echo $dados['Cnae']['descricao'];?></td>
                <td class="input-small"><?php echo $dados['Cnae']['grau_risco'];?></td>
                <td>
                <?php echo $this->Html->link('', array('action' => 'editar', $dados['Cnae']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
                <?php echo $html->link('', array('controller' => 'cnae', 'action' => 'excluir', $dados['Cnae']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir Cnae'), 'Confirma exclusão?'); ?>

                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Cnae']['count']; ?></td>
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
    div.load(baseUrl + 'cnae/listagem/' + Math.random());
}
");
?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    