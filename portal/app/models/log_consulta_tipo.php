<?php
class LogConsultaTipo extends AppModel {

	var $name = 'LogConsultaTipo';
	var $tableSchema = 'portal';
	var $databaseTable = 'dbBuonny';
	var $useTable = 'log_consulta_tipo';
	var $primaryKey = 'codigo';
    var $displayField = 'descricao';
	var $actsAs = array('Secure');

    CONST TIPO_CONSULTA_SM = 1;
    CONST TIPO_CONSULTA_VEICULOS = 2;

	function listarTipoConsulta($tipo = 'all'){
		//$conditions = $this->converteFiltrosEmConditions($filtros);
        $conditions = Array('ativo'=>'S');
        $order = 'descricao';
		return $this->find($tipo, compact('conditions','order'));
	}

	function obtemDescricao($codigo) {
        $dados = $this->findByCodigo($codigo);
        if (!$dados) {
            return null;
        }
        return $dados[$this->name]['descricao'];		
	}

}
?>
