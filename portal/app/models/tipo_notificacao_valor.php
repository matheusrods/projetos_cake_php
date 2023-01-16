<?php
class TipoNotificacaoValor extends AppModel {

	var $name		   = 'TipoNotificacaoValor';
	var $tableSchema   = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable	   = 'tipo_notificacao_valores';
	var $primaryKey	   = 'codigo';
	var $actsAs		   = array('Secure');
	
}