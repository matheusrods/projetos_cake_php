<div id="fornecedor-contato" class="fieldset" style="display: block;padding-top: 0px;">
	<h3 style="text-decoration: none; margin: 0 !important;">Contatos</h3>

	<div class='actionbar-right'>
	    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'fornecedores_contatos', 'action' => 'incluir', $codigo_fornecedor), array('escape' => false, 'class' => 'btn btn-success dialog_contato', 'title' =>'Cadastrar Contato'));?>
	</div>

	<div id="fornecedor-contato-lista" class="grupo" style="margin-top:10px;"></div>
</div>

<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        setup_time();
        setup_mascaras();
        atualizaFornecedorContato();
    });

    function atualizaFornecedorContato(){
		var div = jQuery('#fornecedor-contato-lista');
		bloquearDiv(div);
		div.load(baseUrl + 'fornecedores_contatos/contatos_por_fornecedores/".$codigo_fornecedor."/' + Math.random());
	}
	
")
?>