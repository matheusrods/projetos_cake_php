<?php
class BeneficiadoPromocao extends AppModel {
	var $name = 'BeneficiadoPromocao';
	var $databaseTable = 'dbBuonny';
	var $tableSchema = 'vendas';
	var $useTable = 'beneficiados_promocoes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure',);
	var $belongsTo = array(
		'Promocao' => array(
			'className' => 'Promocao',
			'foreignKey' => 'codigo_promocao',
		),
	);

	function incluir($dados) {
		$promocao = $this->Promocao->read(null, $dados[$this->name]['codigo_promocao']);
		if ($promocao['Promocao']['codigo_regra'] == Promocao::REGRA_BUONNYCREDIT) 
			$dados = $this->verificaRegrasBuonnyCredit($dados, $promocao);
		if ($dados === false) 
			return false;
		try {
			$this->query('begin transaction');
			if (!$this->Promocao->concederBeneficio($dados[$this->name]['codigo_promocao'])) throw new Exception();
			if (!parent::incluir($dados)) throw new Exception();
			$this->commit();
			return true;
		} catch (Exception $ex) {
			$this->rollback();
			return false;
		}
	}

	function verificaRegrasBuonnyCredit($dados, $promocao) {
		$this->bindModel(array('belongsTo' => 
			array(
				'Cliente' => array('className' => 'Cliente', 'foreignKey' => 'foreign_key'),
			),
		));
		$ja_cadastrado = $this->find('count', array('conditions' => array($this->name.'.codigo_promocao' => $dados[$this->name]['codigo_promocao'], $this->name.'.foreign_key' => $dados[$this->name]['codigo_cliente'])));
		if ($ja_cadastrado) {
			$this->invalidate('apelido', 'Cliente já cadastrado na promoção');
			return false;
		}
		if (($promocao['Promocao']['valor_utilizado'] + $promocao['Promocao']['valor_por_beneficiado']) > $promocao['Promocao']['valor'])
			return false;
		$cliente = $this->Cliente->carregar($dados[$this->name]['codigo_cliente']);
		if (!$cliente)
			return false;
		$dados[$this->name]['model'] = 'Cliente';
		$dados[$this->name]['foreign_key'] = $cliente['Cliente']['codigo'];
		$dados[$this->name]['valor'] = $promocao['Promocao']['valor_por_beneficiado'];
		$dados[$this->name]['quantidade'] = $promocao['Promocao']['quantidade_por_beneficiado'];
		return $dados;
	}
}
