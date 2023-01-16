<?php
class PedidoNotificacao extends AppModel {

	var $name		   = 'PedidoNotificacao';
	var $tableSchema   = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable	   = 'pedidos_notificacao';
	var $primaryKey	   = 'codigo';
	var $actsAs		   = array('Secure');
	
}