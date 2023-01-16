<div id="fornecedor-historico" class="fieldset" style="display: block;">
	<h3>Histórico</h3>

	<div class='actionbar-right'>
	    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'fornecedores_historicos', 'action' => 'incluir', $codigo_fornecedor), array('escape' => false, 'class' => 'btn btn-success dialog_historico', 'title' =>'Cadastrar Anexo'));?>

	    <?php //debug($codigo_fornecedor); ?>
	    <?php //debug($this->data); ?>
	</div>
	<div id="fornecedor-historico-lista" class="grupo"></div>
</div>

<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        setup_time();
        setup_mascaras();
        atualizaFornecedorHistorico();
    });

    function atualizaFornecedorHistorico(){
		var div = jQuery('#fornecedor-historico-lista');
		bloquearDiv(div);
		div.load(baseUrl + 'fornecedores_historicos/lista_historico/".$codigo_fornecedor."/' + Math.random());
	}
")
?>