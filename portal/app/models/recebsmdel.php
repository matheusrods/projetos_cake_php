<?php
class Recebsmdel extends AppModel {
    var $name = 'Recebsmdel';
    var $tableSchema = 'dbo';
    var $databaseTable = 'Monitora';
    var $useTable = 'recebsmdel';
    var $primaryKey = 'SM';
    var $actsAs = array('Secure');
	
	function listaSmsCanceladas($filtros){
		$conditions = array('RecebsmDel.DataDel BETWEEN ? AND ?' => array($filtros['ano'].'-01-01 00:00:00', $filtros['ano'].'-12-31 23:59:59'));
		if (isset($filtros['codigo_cliente_monitora']) && !empty($filtros['codigo_cliente_monitora'])) {
                    $filtros['codigo_cliente_monitora'] = str_pad($filtros['codigo_cliente_monitora'], 6, 0, STR_PAD_LEFT);
                    $conditions['RecebsmDel.cliente'] = $filtros['codigo_cliente_monitora'];
                }
                if (isset($filtros['cliente_transportador'])) {
                    $conditions['RecebsmDel.cliente_transportador'] = $filtros['cliente_transportador'];
                }
                if (isset($filtros['cliente_embarcador'])) {
                    $conditions['RecebsmDel.cliente_embarcador'] = $filtros['cliente_embarcador'];
                }
		$fields = array(
			"left(CONVERT(varchar, DataDel, 102), 7) as anomes",
			"count(distinct SM) as sms",
		);
		$group = array("left(CONVERT(varchar, DataDel, 102), 7)",);
		$results = $this->find('all', array('fields' => $fields, 'group' => $group, 'conditions' => $conditions));
		return $results;
	}

	public function carregarPorPedidoAberto($cliente,$pedido){
		$conditions = array('Recebsmdel.Cliente' => $cliente, 'pedido_cliente' => $pedido, 'pedido_cliente <>' => NULL);
		$fields 	= array('Recebsmdel.SM','pedido_cliente','CONVERT(VARCHAR, Recebsmdel.Dta_Inc, 120) AS DATA','RecebsmDel.Hora_Inc');

		return $this->find('first',compact('conditions','joins','fields'));
	}
}

?>