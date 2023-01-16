<?php
class AlertaAgrupamento extends AppModel {
	var $name = 'AlertaAgrupamento';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'alertas_agrupamento';
    var $displayField = 'descricao';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

    function verifica_existencia_agrupamento(){
        $this->AlertaTipo =& ClassRegistry::init('AlertaTipo');
        $codigo_existentes = $this->AlertaTipo->find('all',array('group' => 'codigo_alerta_agrupamento','fields' => 'codigo_alerta_agrupamento'));
        
        $codigo_agrupamentos = array();
        foreach ($codigo_existentes as $key => $codigo_existente) {
            $codigo_agrupamentos[] = $codigo_existente['AlertaTipo']['codigo_alerta_agrupamento'];
        }
        
        return count($codigo_agrupamentos) ? $this->find('all',array('conditions' => array('codigo' => $codigo_agrupamentos))) : array();
    }

    function listarAgrupamentoAlerta($filtros = array()){
        $conditions = $this->converteFiltrosEmConditions($filtros);
        $order = 'descricao';
        return $this->find('all', compact('conditions','order'));
    }
    
    function converteFiltrosEmConditions($filtros){
        $conditions = array();

        if(isset($filtros['AlertaAgrupamento']['descricao']) && $filtros['AlertaAgrupamento']['descricao']){
            $conditions['descricao LIKE'] = '%'.$filtros['AlertaAgrupamento']['descricao'].'%';        }

        return $conditions;
    }   
}
?>