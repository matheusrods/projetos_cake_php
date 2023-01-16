<?php if(!empty($empresas)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
            <th class="input-mini">Código</th>
            <th>Razão Social</th>
            <th>Nome Fantasia</th>
            <th>CNPJ</th>
            <th class="input-mini"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($empresas as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['MultiEmpresa']['codigo'] ?></td>
                <td><?php echo $dados['MultiEmpresa']['razao_social'] ?></td>
                <td><?php echo $dados['MultiEmpresa']['nome_fantasia'] ?></td>
                <td><?php echo $buonny->documento($dados['MultiEmpresa']['codigo_documento']) ?></td>
                <td class="input-mini">
	                <?php echo $this->Html->link('Emular', array('action' => 'mudar_empresa', $dados['MultiEmpresa']['codigo']), array('title' => 'Emular', 'class' => 'btn btn-success')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['MultiEmpresa']['count']; ?></td>
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
    <?php echo $this->Js->writeBuffer(); ?>
    <?php 
$this->addScript($this->Buonny->link_js('comum.js'));
echo $this->Javascript->codeBlock("

    function atualizaStatus(codigo, status){
        $.ajax({
            type: 'POST',
            url: baseUrl + 'multi_empresas/atualiza_status/' + codigo + '/' + status + '/' + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($('div.listaSelecionar'));  
            },
            success: function(data){
                if(data == 1){
                    atualizaListaMultiEmpresa();
                    $('div.listaSelecionar').unblock();
                    viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
                } else {
                    atualizaListaMultiEmpresa();
                    $('div.listaSelecionar').unblock();
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

    function atualizaListaMultiEmpresa(){
    var div = jQuery('div.listaSelecionar');
    bloquearDiv(div);
    div.load(baseUrl + 'multi_empresas/selecionar_empresa_listagem/' + Math.random());
}
   
");
?>