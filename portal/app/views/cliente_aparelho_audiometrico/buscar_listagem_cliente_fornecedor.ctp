<?php if(!empty($dados_fornecedores)):?>
    <?php echo $paginator->options(array('update' => 'div#busca-lista')); ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th class="input-mini">Código</th>
            <th>Atende</th>
            <th>Razão Social</th>
            <th>Nome Fantasia</th>
            <th>CNPJ</th>
            <th>Cidade</th>
			<th class="input-mini">Estado</th>
			<th class="action-icon">Ações</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($dados_fornecedores as $key => $dados): ?>
			<tr style="font-size:12px;">
				<td class="input-mini"><?php echo $dados['Fornecedor']['codigo'] ?></td>
                <td><?php echo $this->Buonny->valida_atendimento_de_servicos($dados[0]['serv_at'], $dados[0]['total_at']) ?></td>
                <td><?php echo $dados['Fornecedor']['razao_social'] ?></td>
                <td><?php echo $dados['Fornecedor']['nome'] ?></td>
                <td><?php echo $buonny->documento($dados['Fornecedor']['codigo_documento']);?></td>
                <td><?php echo $dados['FornecedorEndereco']['cidade'] ?></td>
				<td class="input-mini"><?php echo $dados['FornecedorEndereco']['estado_descricao'] ?></td>
				<td class="action-icon"><?php echo $this->Html->link('', 'javascript:void(0)',array('onclick' => 'insereClienteFornecedor('.$codigo_cliente.','.$dados['Fornecedor']['codigo'].')', 'class' => 'icon-plus ', 'title' => 'Incluir Médico')); ?></td>
			</tr>
		<?php endforeach ?>	 
	</tbody>
	<tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Fornecedor']['count']; ?></td>
            </tr>
        </tfoot>    
    </table>
    <div class='row-fluid'>
        <div class='numbers span7'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span4'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            
        </div>
    </div>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 
<?php echo $this->Js->writeBuffer(); ?>
<?php echo $this->Javascript->codeBlock('

    $(document).ready(function() {
        $("[data-toggle=\"tooltip\"]").tooltip();
    });

    function insereClienteFornecedor(codigo_cliente, codigo_fornecedor){
    	$.ajax({

            type: "POST",        
            url: "/portal/clientes_fornecedores/incluir",        
            dataType : "json",
            data: {
                "codigo_cliente": codigo_cliente, 
            	"codigo_fornecedor": codigo_fornecedor
            },
            success : function(retorno){ 
                if(retorno == 0){
                    alert("Erro! Não é possível cadastrar o Fornecedor!");
                }
                else if(retorno == 1){
                    if($("#cliente-fornecedor-lista div.alert").length > 0){
                        $("#cliente-fornecedor-lista div.alert").remove();
                    }
                    atualizaLista(); 
                    atualizaListaBusca();      
                }
                else{
                    alert("Fornecedor já cadastrado!");
                }
          	},
          	error : function(error){
                alert("Erro! Não é possível cadastrar o Fornecedor!");
          	}
        }); 
    }

    function atualizaLista(){
        var div = jQuery("#cliente-fornecedor-lista");
        // var div = jQuery("#cliente-fornecedor-lista table");
        bloquearDiv(div);
        div.load(baseUrl + "clientes_fornecedores/listagem/'.$codigo_cliente.'/" + Math.random());
    }

    function atualizaListaBusca(){
        var div = jQuery("#busca-lista");
        bloquearDiv(div);
        div.load(baseUrl + "clientes_fornecedores/buscar_listagem_cliente_fornecedor/'.$codigo_cliente.'/" + Math.random());
    }
');
?>
<?php echo $this->Js->writeBuffer(); ?>
