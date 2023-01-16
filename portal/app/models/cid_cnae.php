<?php

class CidCnae extends AppModel {

	var $name = 'CidCnae';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'cid_cnae';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $belongsTo = array(
		'Cid' => array(
			'className' => 'Cid',
			'foreignKey' => 'codigo_cid'
		),
		'Cnae' => array(
			'className' => 'Cnae',
			'foreignKey' => 'codigo_cnae'
		)
	);


	var $validate = array(
		'codigo_cid' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o CID',
			'required' => true
		),
		'codigo_cnae' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Cnae',
				'required' => true
			)

		)
		
	);

	function converteFiltroEmCondition($data) {
        $conditions = array();

        if (!empty($data['codigo']))
            $conditions['CidCnae.codigo'] = $data['codigo'];

		if (!empty($data['codigo_cid10']))
			$conditions['Cid.codigo_cid10'] = $data['codigo_cid10'];	

		if (!empty($data ['descricao_cid']))
			$conditions ['Cid.descricao LIKE'] = '%' . $data ['descricao_cid'] . '%';

       	if (!empty ( $data['cnae']))
			$conditions['Cnae.cnae'] = $data['cnae'];

		if (!empty($data['descricao_cnae']))
			$conditions ['Cnae.descricao LIKE'] = '%' . $data ['descricao_cnae'] . '%';

		if (isset( $data['ativo'] ) && $data['ativo'] != "") 
			$conditions ['CidCnae.ativo'] = $data['ativo'];
       
        
        return $conditions;
    }

	function carregar($codigo) {
		$dados = $this->find ( 'first', array (
				'conditions' => array (
						$this->name . '.codigo' => $codigo 
				) 
		) );
		return $dados;
	}

	function retorna_codigo_cid($codigo_cid10){
		$Cid =& ClassRegistry::Init('Cid');
		$codigo = "";

		$codigo_cid10 = strtoupper(str_replace(array('.','-','/'), '',  $codigo_cid10)); 

		$dados = $this->Cid->find('first',array('fields' => array('codigo', 'codigo_cid10'), 'conditions' => array('codigo_cid10' => $codigo_cid10, 'ativo' => 1)) );
		

		if(!empty($dados)){
			$codigo = $dados['Cid']['codigo'];
		}
		
		return $codigo;
	}

	function retorna_codigo_cnae($cnae){
		$Cnae =& ClassRegistry::Init('Cnae');
		$codigo = "";

		$cnae = str_replace(array('.','-','/'), '',  $cnae); 

		$dados = $this->Cnae->find('first',array('fields' => array('codigo','cnae'), 'conditions' => array('cnae' => $cnae), 'recursive' => '-1') );
		
		if(!empty($dados)){
			$codigo = $dados['Cnae']['codigo'];
		}
		return $codigo;
	}


	function cnae_cid_unico($cid, $cnae, $codigo = null) {

		$conditions = array(
						'codigo_cid' => $cid, 
						'codigo_cnae' => $cnae  
				);
		
		if(!empty($codigo)){
			$conditions['codigo <>'] = $codigo;
		}

		$dados = $this->find('count', array (
				'conditions' => $conditions,
				'recursive' => '-1'	
		) );

		if($dados > 0){
			return false;
		}

		return true;
	}

}

?>