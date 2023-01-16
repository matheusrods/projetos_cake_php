<?php

class StatusPropostaCred extends AppModel {
	
	var $name = 'StatusPropostaCred';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'status_proposta_credenciamento';
	var $primaryKey = 'codigo';
	var $displayField = 'descricao';
	var $actsAs = array('Secure');
	        
//	lista de status antigos

//	const EM_ABERTO = 1;	
//  const DOCUMENTACAO_SOLICITADA = 2;
//  const AGUARDANDO_APROVACAO = 3;
//  const RECUSADA = 4;
//  const APROVADA = 5;    
//  const PRECADASTRO = 6;

	const PRECADASTRO = 1;
	const AGUARDANDO_ANALISE_VALORES = 2;
	const AGUARDANDO_AVALIACAO_CONTRA_PROPOSTA = 3;
	const AGUARDANDO_RETORNO_CONTRA_PROPOSTA = 4;

	const RENEGOCIAR_VALOR_MINIMO = 16;
	const VALOR_MINIMO_NEGOCIADO = 17;
	
	#const VALORES_APROVADOS = 5;
	const VALORES_APROVADOS = 13;
	const PROPOSTA_ACEITA = 6;
	const DOCUMENTACAO_SOLICITADA = 7;
	const AGUARDANDO_ANALISE_DOCUMENTOS = 8;
	const APROVADO = 9;
	const REPROVADO = 10;
	const TERMO_RECUSADO = 11;
	const AGUARDANDO_ANALISE_PROPOSTA = 12;
	const AGUARDANDO_ENVIO_TERMO = 13;
	const CONTRATO_ASSINADO_ENVIADO = 14;
}

?>