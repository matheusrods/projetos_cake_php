<div id="fornecedor-lista">
    <div class='actionbar-right' style='margin-bottom: 10px;  min-height: 50px'>
        <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'clientes_fornecedores', 'action' => 'buscar_cliente_fornecedor', $this->data['ClienteProduto']['codigo_cliente']), array('escape' => false, 'class' => 'btn btn-success dialog_cliente_fornecedor', 'title' =>'Cadastrar Novos fornecedores'));?>
    </div>

    <div id="cliente-fornecedor-lista"></div>
</div>

<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        setup_time();
        setup_mascaras();
        atualizaLista();
    });

    function atualizaLista(){
        var div = jQuery('#cliente-fornecedor-lista');
        bloquearDiv(div);
        div.load(baseUrl + 'clientes_fornecedores/listagem/".$this->data['ClienteProduto']['codigo_cliente']."/' + Math.random());
    }
")
?>