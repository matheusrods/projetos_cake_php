<?php if(!empty($aplicacao)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-medium">Código</th>
            <th class="input-xxlarge">Exame</th>
            <th class="input-xxlarge">Cargo</th>
            <th class="input-xxlarge">Setor</th>
            <th class="acoes" style="width:100px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php 

            foreach ($aplicacao as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['AplicacaoExame']['codigo'] ?></td>
                <td class="input-xxlarge"><?php echo $dados['Exame']['descricao'] ?></td>
                <td class="input-xxlarge"><?php echo $dados['Cargo']['descricao'] ?></td>
                <td class="input-xxlarge"><?php echo $dados['Setor']['descricao'] ?></td>
                <td>
                <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatus('{$dados['AplicacaoExame']['codigo']}','{$dados['AplicacaoExame']['ativo']}', '{$dados['AplicacaoExame']['codigo_cliente']}')"));?>

                <?php if($dados['AplicacaoExame']['ativo']== 0): ?>
                    <span class="badge-empty badge badge-important" title="Desativado"></span>
                <?php elseif($dados['AplicacaoExame']['ativo']== 1): ?>
                    <span class="badge-empty badge badge-success" title="Ativo"></span>
                <?php endif; ?>
                
                <?php echo $this->Html->link('', array('action' => 'editar', $dados['AplicacaoExame']['codigo_cliente'], $dados['AplicacaoExame']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['AplicacaoExame']['count']; ?></td>
            </tr>
        </tfoot>    
    </table>
    <div class='row-fluid'>
        <div class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        
		<div class='form-actions well'>
		    <?php if( $this->Paginator->params['paging']['AplicacaoExame']['count'] > 0): ?>
		        <?php echo $html->link('Concluido', array('controller' => 'clientes_implantacao','action' => 'atualiza_status_pcmso',  $this->data['Cliente']['codigo'], 3), array('class' => 'btn btn-primary')); ?>
		    <?php endif;?>
		    <?php echo $html->link('Voltar', array('controller' => 'clientes_implantacao', 'action' => 'gerenciar_pcmso', $this->data['GrupoEconomicoCliente']['matriz']), array('class' => 'btn')); ?>
		</div>
    </div>
    <?php echo $this->Js->writeBuffer(); ?>
    <?php 
	echo $this->Javascript->codeBlock("

    function atualizaStatus(codigo, status, codigo_cliente){
        $.ajax({
            type: 'POST',
            url: baseUrl + 'aplicacao_exames/atualiza_status/' + codigo + '/' + status + '/' + Math.random(),
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
    div.load(baseUrl + 'aplicacao_exames/listagem/' + codigo_cliente + '/' + Math.random());
}
");
?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    