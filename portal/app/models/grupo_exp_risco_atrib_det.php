<?php
class GrupoExpRiscoAtribDet extends AppModel {

	var $name = 'GrupoExpRiscoAtribDet';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'grupo_exposicao_riscos_atributos_detalhes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure','Containable', 'Loggable' => array('foreign_key' => 'codigo_grupo_exposicao_riscos_atributos_detalhes'));

	var $belongsTo = array(
		'GrupoExposicaoRisco' => array(
			'className' => 'GrupoExposicaoRisco',
			'foreignKey' => 'codigo_grupos_exposicao_risco'
		),
		'RiscoAtributoDetalhe' => array(
			'className' => 'RiscoAtributoDetalhe',
			'foreignKey' => 'codigo_riscos_atributos_detalhes'
		),
		'Usuario' => array(
			'className' => 'Usuario',
			'foreignKey' => 'codigo_usuario_inclusao'
		)
	);


	var $validate = array(
		'codigo_grupos_exposicao_risco' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Grupo de Exposição',
			'required' => true
		),
		'codigo_riscos_atributos_detalhes' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Efeito Crítico',
				'required' => true
			)
		),
		'codigo_usuario_inclusao' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Usuário',
				'required' => true
			)
		)
	);


	function carregar($codigo) {
		$dados = $this->find ( 'first', array (
				'conditions' => array (
						$this->name . '.codigo' => $codigo 
				) 
		) );
		return $dados;
	}

	function retorna_grupo_exposicao_risco($codigo_grupos_exposicao_risco) {
		//instancia e registra a class
		$EfeitoCritico =& ClassRegistry::Init('RiscoAtributoDetalhe');
		//condições do grupo
		$conditions = array('GrupoExpRiscoAtribDet.codigo_grupos_exposicao_risco' => $codigo_grupos_exposicao_risco);
		//joins
	 	$joins  = array(
	        array(
	          'table' => $EfeitoCritico->databaseTable.'.'.$EfeitoCritico->tableSchema.'.'.$EfeitoCritico->useTable,
	          'alias' => 'EfeitoCritico',
	          'type' => 'LEFT',
	          'conditions' => 'EfeitoCritico.codigo = GrupoExpRiscoAtribDet.codigo_riscos_atributos_detalhes',
	        )
	    );

	 	$fields = array(
	 		'EfeitoCritico.codigo',
	 		'EfeitoCritico.descricao',
	 		'GrupoExpRiscoAtribDet.codigo',
	 		'GrupoExpRiscoAtribDet.codigo_riscos_atributos_detalhes',
	 		'GrupoExpRiscoAtribDet.codigo_grupos_exposicao_risco'
	 	);

	 	//executa a query montada
		$efeito_critico = $this->find("all", array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields));

		//retorna o efeito critico
		return $efeito_critico;
	
	} //fim retorna_grupo_exposicao_risco

}

?>