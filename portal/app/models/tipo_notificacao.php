<?php
class TipoNotificacao extends AppModel {

	var $name		   = 'TipoNotificacao';
	var $tableSchema   = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable	   = 'tipo_notificacao';
	var $primaryKey	   = 'codigo';
	var $actsAs		   = array('Secure');
	
}