<div id="fornecedor-contato-agendamento" class="fieldset" style="display: block; padding-top: 0px;">
	<h3 style="text-decoration: none; margin: 0 !important;">Dados para Agendamento</h3>

	<div id="fornecedor_contato_agendamento_lista" class="grupo"></div>
</div>

<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        setup_time();
        setup_mascaras();
        atualizaFornecedorContatoAgendamento();
    });

    function atualizaFornecedorContatoAgendamento(){
		var div = jQuery('#fornecedor_contato_agendamento_lista');
		bloquearDiv(div);
		div.load(baseUrl + 'fornecedores_contatos/contatos_por_fornecedores_agendamento/".$codigo_fornecedor."/' + Math.random());
	}
")
?>