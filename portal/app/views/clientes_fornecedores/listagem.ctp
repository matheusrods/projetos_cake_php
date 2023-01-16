<?php 
    echo $paginator->options(array('update' => 'div#cliente-fornecedor-lista')); 
    $total_paginas = $this->Paginator->numbers();
?>
    <?php if(!empty($fornecedores)):?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="input-mini">Código</th>
                    <th class="input-mini">Código Fornecedor</th>
                    <th class="input-mini">Atende</th>
                    <th>Razão Social</th>
                    <th>Nome Fantasia</th>
                    <th>CNPJ</th>
                    <th>Cidade</th>
                    <th>Estado</th>
                    <th class='input-mini'>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fornecedores as $fornecedor): ?>
                <tr>
                    <td class="input-mini"><?= $fornecedor['ClienteFornecedor']['codigo'] ?></td>
                    <td class="input-mini"><?= $fornecedor['Fornecedor']['codigo'] ?></td>
                    <td>
                        <?php echo $this->Buonny->valida_atendimento_de_servicos($fornecedor[0]['serv_at'], $fornecedor[0]['total_at']) ?>
                    </td>
                    <td><?php echo $fornecedor['Fornecedor']['razao_social'] ?></td>
                    <td><?php echo $fornecedor['Fornecedor']['nome'] ?></td>
                    <td><?php echo $buonny->documento($fornecedor['Fornecedor']['codigo_documento']) ?></td>
                    <td><?php echo $fornecedor['FornecedorEndereco']['cidade'] ?></td>
                    <td><?php echo $fornecedor['FornecedorEndereco']['estado_descricao'] ?></td>
                    <td>
                        <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-trash', 'escape' => false, 'title'=>'Remover fornecedor','onclick' => "atualizaStatus('{$fornecedor['ClienteFornecedor']['codigo']}','{$fornecedor['ClienteFornecedor']['ativo']}')"));?>
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
    <?php else:?>
        <div class="alert">Nenhum dado foi encontrado.</div>
    <?php endif;?> 

<?php echo $this->Javascript->codeBlock('
jQuery(document).ready(function(){
    $("[data-toggle=\"tooltip\"]").tooltip();
    $(document).on("click", ".dialog_cliente_fornecedor", function(e) {
        e.preventDefault();
        open_dialog(this, "Fornecedores", 990);
    });
});

    function atualizaStatus(codigo, status){
        $.ajax({
            type: "POST",
            url: baseUrl + "clientes_fornecedores/atualiza_status/" + codigo + "/" + status + "/" + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($("div#cliente-fornecedor-lista"));  
            },
            success: function(data){
                if(data == 1){
                    atualizaLista();
                    $("div#cliente-fornecedor-lista").unblock();
                    viewMensagem(2,"Os dados informados foram armazenados com sucesso!");
                } else {
                    atualizaLista();
                    $("div#cliente-fornecedor-lista").unblock();
                    viewMensagem(0,"Não foi possível mudar o status!");
                }
            },
            error: function(erro){
            $("div#cliente-fornecedor-lista").unblock();
            viewMensagem(0,"Não foi possível mudar o status!");
            }
        });
    }

    function fecharMsg(){
        setInterval(
            function(){
                $("div.message.container").css({ "opacity": "0", "display": "none" });
            },
            4000
        );     
    }

    function gerarMensagem(css, mens){
        $("div.message.container").css({ "opacity": "1", "display": "block" });
        $("div.message.container").html("<div class=\'alert alert-"+css+"\'><p>"+mens+"</p></div>");
        fecharMsg();
    }

    function viewMensagem(tipo, mensagem){
        switch(tipo){
            case 1:
                gerarMensagem("success",mensagem);
                break;
            case 2:
                gerarMensagem("success",mensagem);
                break;
            default:
                gerarMensagem("error",mensagem);
                break;
        }    
    } 

    function atualizaLista(){
    var div = jQuery("div#cliente-fornecedor-lista");
    bloquearDiv(div);
    div.load(baseUrl + "clientes_fornecedores/listagem/'.$codigo_cliente.'/" + Math.random());
}   
');
?>
<?php echo $this->Js->writeBuffer(); ?>