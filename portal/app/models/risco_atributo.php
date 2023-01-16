<?php
class RiscoAtributo extends AppModel {

    var $name = 'RiscoAtributo';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'riscos_atributos';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

  const MEIO_EXPOSICAO = 1;
	const CLASSIFICACAO_EFEITO_CRITICO = 2;
		
	function retorna_exposicao($codigo_exposicao_ocupacional) {
		$RiscoAtributoDetalhe =& ClassRegistry::Init('RiscoAtributoDetalhe');
    
		$conditions = array('RiscoAtributo.codigo' => $codigo_exposicao_ocupacional);

		$fields = array('RiscoAtributoDetalhe.codigo', 'RiscoAtributoDetalhe.descricao');

        $joins  = array(
            array(
              'table' => $RiscoAtributoDetalhe->databaseTable.'.'.$RiscoAtributoDetalhe->tableSchema.'.'.$RiscoAtributoDetalhe->useTable,
              'alias' => 'RiscoAtributoDetalhe',
              'type' => 'LEFT',
              'conditions' => 'RiscoAtributoDetalhe.codigo_risco_atributo = RiscoAtributo.codigo AND RiscoAtributoDetalhe.ativo = 1',
            )
        );        
        $empresa_user_codigo = $_SESSION['Auth']['Usuario']['codigo_empresa'];
        
        if(isset($empresa_user_codigo)){
          $joins[0]['conditions'] .= ' AND RiscoAtributoDetalhe.codigo_empresa = '.$empresa_user_codigo;
        }

		$dados = $this->find('list', array('conditions' => $conditions,'fields' => $fields,'joins' => $joins, 'order' => 'RiscoAtributoDetalhe.descricao ASC'));
		return $dados;
	}

  function retorna_detalhe_exposicao($codigo_exposicao_ocupacional, $codigo) {
    $RiscoAtributoDetalhe =& ClassRegistry::Init('RiscoAtributoDetalhe');

    $conditions = array('RiscoAtributo.codigo' => $codigo_exposicao_ocupacional, 'RiscoAtributoDetalhe.codigo' => $codigo);

    $fields = array('RiscoAtributoDetalhe.codigo', 'RiscoAtributoDetalhe.descricao');

    $joins  = array(
        array(
          'table' => $RiscoAtributoDetalhe->databaseTable.'.'.$RiscoAtributoDetalhe->tableSchema.'.'.$RiscoAtributoDetalhe->useTable,
          'alias' => 'RiscoAtributoDetalhe',
          'type' => 'LEFT',
          'conditions' => 'RiscoAtributoDetalhe.codigo_risco_atributo = RiscoAtributo.codigo',
        )
    );

    $dados = $this->find('first', array('conditions' => $conditions,'fields' => $fields,'joins' => $joins));
    return $dados;
  }
}