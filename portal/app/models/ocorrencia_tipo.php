<?php

class OcorrenciaTipo extends AppModel {
    var $name = 'OcorrenciaTipo';
    var $tableSchema = 'monitora';
    var $databaseTable = 'dbMonitora';
    var $useTable = 'ocorrencias_tipos';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $belongsTo = array(
      'TipoOcorrencia' => array(
          'className' => 'TipoOcorrencia',
          'foreignKey' => 'codigo_tipo_ocorrencia'
      )
    );

    function atualizar($dados, $in_another_transaction = false){
        try {
            if (!$in_another_transaction) $this->query('begin transaction');
            $tipos = $this->find('all' , array('fields' => 'codigo', 'conditions' => array('codigo_ocorrencia' => $dados['Ocorrencia']['codigo'])));

            foreach ($tipos as $tipo) {
                if (!$this->delete($tipo['OcorrenciaTipo']['codigo'])) throw new Exception();
            }
            foreach ($dados['OcorrenciaTipo'] as $ocorrencia_tipo) {
                $tipo_ocorrencia = array(
                    'codigo_ocorrencia' => $dados['Ocorrencia']['codigo'],
                    'codigo_tipo_ocorrencia' => $ocorrencia_tipo['codigo_tipo_ocorrencia'],
                    'observacao' => $ocorrencia_tipo['observacao']
                );
                $this->create();
                if (!$this->save($tipo_ocorrencia)) throw new Exception();
            }
            if (!$in_another_transaction) $this->commit();
            return true;
        } catch (Exception $ex){
            if (!$in_another_transaction) $this->rollback();
            return false;
        }
    }
    
    function listaSLA($periodo, $tempo_sla) {
        $codigo_status_ocorrencias = array(1,3,4,7,11);
        $this->bindModel(array('belongsTo' => array('Ocorrencia' => array('className' => 'Ocorrencia', 'foreignKey' => 'codigo_ocorrencia'))));
        $group = array('OcorrenciaTipo.codigo_tipo_ocorrencia', 'TipoOcorrencia.descricao');
        $fields = array_merge($group, 
            array(
        		"SUM(
    				CASE 
    					WHEN DATEDIFF(n, Ocorrencia.data_inclusao, isnull(Ocorrencia.data_alteracao, getdate())) <= $tempo_sla 
    					THEN 1 
    					ELSE 0 
    				END) AS dentro",
    			"SUM(
    				CASE 
    					WHEN DATEDIFF(n, Ocorrencia.data_inclusao, isnull(Ocorrencia.data_alteracao, getdate())) > $tempo_sla 
    					THEN 1 
    					ELSE 0 
    				END) AS fora",
        	)
        );
    	$conditions = array('Ocorrencia.codigo_status_ocorrencia' => $codigo_status_ocorrencias);
        return $this->find('all', array('fields' => $fields, 'group' => $group, 'conditions' => $conditions));
    }
}

?>