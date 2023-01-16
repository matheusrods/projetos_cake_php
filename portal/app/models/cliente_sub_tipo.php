<?php
class ClienteSubTipo extends AppModel {
	var $name = 'ClienteSubTipo';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'cliente_sub_tipo';
	var $primaryKey = 'codigo';
	var $displayField = 'descricao';
	var $actsAs = array('Secure');

	const SUBTIPO_EMBARCADOR = 1;
	const SUBTIPO_TRANSPORTADOR = 4;
	
	function listaPorTipo($codigo_cliente_tipo){
	    return $this->find('list', array('conditions' => array('codigo_cliente_tipo' => $codigo_cliente_tipo), array('order' => 'descricao')));
	}
        
    function listaPorTipoJson($codigo_cliente_tipo, $id = null) {
        if ($codigo_cliente_tipo != 'null' && ( is_null($id) || empty($id) )) {
            $data = $this->find('all', array('conditions' => array('codigo_cliente_tipo' => $codigo_cliente_tipo), 'fields' => array('codigo', 'descricao')));
        } else {
            $data = $this->find('all', array('conditions' => array('codigo' => $id), 'fields' => array('descricao')));
        }
        return $this->retiraModel($data);
    }

    public static function subTipo($codigo_cliente_sub_tipo) {
    	$transportadora = array(1,7,13,19);
		$embarcadora = array(2,8,14,20);
		if(in_array($codigo_cliente_sub_tipo, $transportadora)) {
			return self::SUBTIPO_TRANSPORTADOR;
		} else {
			return self::SUBTIPO_EMBARCADOR;
		}
    }

}
?>