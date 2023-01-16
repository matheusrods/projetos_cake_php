<?php
class ExposicaoOcupacional extends AppModel {

    var $name = 'ExposicaoOcupacional';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'exposicao_ocupacional';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    

  const TEMPO_EXPOSICAO = 1;
	const INTENSIDADE = 2;
	const RESULTANTE = 3;
	const DANO = 4;
  const MEIO_EXPOSICAO = 5;
	const GRAU_RISCO = 6;
	
	function retorna_exposicao($codigo_exposicao_ocupacional) {
		$ExposicaoOcupAtributo =& ClassRegistry::Init('ExposicaoOcupAtributo');

		$conditions = array('ExposicaoOcupacional.codigo' => $codigo_exposicao_ocupacional);

		if($codigo_exposicao_ocupacional == self::MEIO_EXPOSICAO){
			$fields = array('ExposicaoOcupAtributo.codigo', 'ExposicaoOcupAtributo.descricao');
		}
		else{
        	$fields = array('ExposicaoOcupAtributo.codigo', 'ExposicaoOcupAtributo.descricao');
        }

        $joins  = array(
            array(
              'table' => $ExposicaoOcupAtributo->databaseTable.'.'.$ExposicaoOcupAtributo->tableSchema.'.'.$ExposicaoOcupAtributo->useTable,
              'alias' => 'ExposicaoOcupAtributo',
              'type' => 'LEFT',
              'conditions' => 'ExposicaoOcupAtributo.codigo_exposicao_ocupacional = ExposicaoOcupacional.codigo',
            )
        );

		$dados = $this->find('list', array('conditions' => $conditions,'fields' => $fields,'joins' => $joins));
		return $dados;
	} 

  function buscar_exposicao($codigo, $codigo_exposicao_ocupacional) {
    $ExposicaoOcupAtributo =& ClassRegistry::Init('ExposicaoOcupAtributo');

    $conditions = array('ExposicaoOcupAtributo.codigo' => $codigo, 'ExposicaoOcupAtributo.codigo_exposicao_ocupacional' => $codigo_exposicao_ocupacional);

    if($codigo_exposicao_ocupacional == self::MEIO_EXPOSICAO){
      $fields = array('ExposicaoOcupAtributo.codigo', 'ExposicaoOcupAtributo.descricao');
    }
    else{
          $fields = array('ExposicaoOcupAtributo.codigo', 'ExposicaoOcupAtributo.abreviacao');
        }

        $joins  = array(
            array(
              'table' => $ExposicaoOcupAtributo->databaseTable.'.'.$ExposicaoOcupAtributo->tableSchema.'.'.$ExposicaoOcupAtributo->useTable,
              'alias' => 'ExposicaoOcupAtributo',
              'type' => 'LEFT',
              'conditions' => 'ExposicaoOcupAtributo.codigo_exposicao_ocupacional = ExposicaoOcupacional.codigo',
            )
        );

    $dados = $this->find('list', array('conditions' => $conditions,'fields' => $fields,'joins' => $joins));
    return $dados;
  }  

}