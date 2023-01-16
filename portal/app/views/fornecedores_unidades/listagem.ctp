<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();

    
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini">Código</th>
            <th>Razão Social</th>          
            <th>Nome Fantasia</th>          
            <th class="acoes" style="width:75px">
        </tr>
    </thead>
    <tbody>
    	
        <?php 
         foreach ($fornecedores as $dados): ?>
        <tr>
            <td class="input-mini">
				<?php echo $dados['Fornecedor']['codigo'];?>
			</td>
            <td>
                <?php echo $dados['Fornecedor']['razao_social'];?>
            </td>
            <td>
				<?php echo $dados['Fornecedor']['nome'];?>
			</td>			
			<td>
                <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatus('{$dados['FornecedorUnidade']['codigo']}','{$dados['FornecedorUnidade']['ativo']}', '{$codigo_fornecedor_matriz}')"));?>
                <?php if($dados['FornecedorUnidade']['ativo']== 0): ?>
                    <span class="badge-empty badge badge-important" title="Desativado"></span>
                <?php elseif($dados['FornecedorUnidade']['ativo']== 1): ?>
                    <span class="badge-empty badge badge-success" title="Ativo"></span>
                <?php endif; ?>
                
				<?php echo $html->link('', array('action' => 'editar', $dados['FornecedorUnidade']['codigo_fornecedor_matriz'].'/'.$dados['FornecedorUnidade']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
				<?php //echo $html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => "javascript:excluir_unidade({$dados['FornecedorUnidade']['codigo']}, {$dados['FornecedorUnidade']['codigo_fornecedor_matriz']});")) ?>
			</td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
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
<?php 
echo $this->Javascript->codeBlock("
	function excluir_unidade(codigo, codigo_fornecedor_matriz) {
		
		if (confirm('Deseja excluir esta unidade?'))
			location.href = '/portal/fornecedores_unidades/excluir/' + codigo +'/'+ codigo_fornecedor_matriz;
	}

    function atualizaStatus(codigo, status, codigo_fornecedor_matriz){
        $.ajax({
            type: 'POST',
            url: baseUrl + 'fornecedores_unidades/atualiza_status/' + codigo + '/' + status + '/' + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($('div.lista'));  
            },
            success: function(data){
                
                if(data == 1){
                    atualizaLista(codigo_fornecedor_matriz);
                    $('div.lista').unblock();
                    viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
                } else {
                    atualizaLista(codigo_fornecedor_matriz);
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

    function atualizaLista(codigo_fornecedor_matriz) {
    var div = jQuery('div.lista');
    bloquearDiv(div);
    div.load(baseUrl + 'fornecedores_unidades/listagem/' + codigo_fornecedor_matriz + '/' + Math.random());
}
   

	"); ?>