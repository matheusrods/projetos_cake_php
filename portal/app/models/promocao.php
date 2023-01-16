<?php
class Promocao extends AppModel {
	var $name = 'Promocao';
	var $databaseTable = 'dbBuonny';
	var $tableSchema = 'vendas';
	var $useTable = 'promocoes';
	var $primaryKey = 'codigo';
	var $displayField = 'nome';
	var $actsAs = array('Secure',);

	var $validate = array(
        'codigo_regra' => array(
        	'notEmpty' =>array(
	            'rule' => 'notEmpty',
	            'message' => 'Regra não informada',
	            'required' => true,
	            'allowEmpty' => false,
	        ),
	        'regra_ativo' => array(
	        	'rule' => 'regra_ativo',
	            'message' => 'Existe outra promoção com esta regra ativa',
	       	),
        ),
        'nome' => array(
            'rule' => 'notEmpty',
            'message' => 'Nome da Promoção não informado',
            'required' => true,
            'allowEmpty' => false,
        ),
        'validade' => array(
            'rule' => 'regra_validade',
            'message' => 'Validade incorreta',
            'required' => true,
            'allowEmpty' => false,
            'on' => 'create',
        ),
        'quantidade' => array(
            'rule' => 'numeric',
            'message' => 'Valor incorreto',
            'required' => true,
            'allowEmpty' => false,
        ),
        'quantidade_por_beneficiado' => array(
            'rule' => 'numeric',
            'message' => 'Valor incorreto',
            'required' => true,
            'allowEmpty' => false,
        ),
	);

	const REGRA_BUONNYCREDIT = 1;

	function regra_ativo($data) {
		$conditions = array($this->name.'.codigo_regra' => $this->data[$this->name]['codigo_regra'], $this->name.'.ativo' => true);
		if (isset($this->data[$this->name][$this->primaryKey])) 
			$conditions = array_merge($conditions, array($this->name.'.'.$this->primaryKey.' !=' => $this->data[$this->name][$this->primaryKey]));
		return ($this->find('count', array('conditions' => $conditions)) == 0);
	}

	function regra_validade($data) {
		$data = Comum::dateToTimestamp($data['validade']);
		return date('Ymd', $data) >= date('Ymd');
	}

	function concederBeneficio($codigo_promocao) {
		$promocao = $this->read(null, $codigo_promocao);
		$promocao[$this->name]['valor_utilizado'] += $promocao[$this->name]['valor_por_beneficiado'];
		$promocao[$this->name]['quantidade_utilizada'] += $promocao[$this->name]['quantidade_por_beneficiado'];
		if ($promocao[$this->name]['ativo'] != true || 
			$promocao[$this->name]['valor_utilizado'] > $promocao[$this->name]['valor'] || 
			$promocao[$this->name]['quantidade_utilizada'] > $promocao[$this->name]['quantidade'])
			return false;
		return $this->save($promocao);
	}

	function codigoPromocaoAtiva($codigo_regra) {
		$result = $this->find('first', array('fields' => array('codigo'), 'conditions' => array('codigo_regra' => $codigo_regra, 'ativo' => true)));
		return $result['Promocao']['codigo'];
	}
}
