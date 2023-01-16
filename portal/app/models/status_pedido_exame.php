<?php

class StatusPedidoExame extends AppModel {

	var $name = 'StatusPedidoExame';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'status_pedidos_exames';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	
    const PENDENTE_BAIXA = 1;
    const PARCIALMENTE_BAIXADO = 2;
    const TOTALMENTE_BAIXADO = 3;
    const PENDENTE_AGENDAMENTO = 4;
	CONST CANCELADO = 5;
	CONST CONCLUIDO_PARCIAL = 6;
	
}

?>