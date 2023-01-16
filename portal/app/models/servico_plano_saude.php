<?php

class ServicoPlanoSaude extends AppModel {

	public $name = 'ServicoPlanoSaude';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'servico_plano_saude';
	public $primaryKey = 'codigo';
	public $recursive = -1;
	public $actsAs = array('Secure');

	public $belongsTo = array(
		'Servico' => array(
			'className' => 'Servico',
			'foreignKey' => 'codigo_servico'
			),
		'TipoUso' => array(
			'className' => 'TipoUso',
			'foreignKey' => 'codigo_tipo_uso'
			),
		'ClassificacaoServico' => array(
			'className' => 'ClassificacaoServico',
			'foreignKey' => 'codigo_classificacao_servico'
			)
		);

	public $validate = array(
		'codigo_servico' => array(
			'rule' => 'notEmpty',
			'message' => 'Este campo é obrigatório',
			'required' => true
			),
		'codigo_classificacao_servico' => array(
			'rule' => 'notEmpty',
			'message' => 'Este campo é obrigatório',
			'required' => true
			),
		'codigo_tipo_uso' => array(
			'rule' => 'notEmpty',
			'message' => 'Este campo é obrigatório',
			'required' => true
			),
		'maximo' => array(
			'rule' => 'notEmpty',
			'message' => 'Este campo é obrigatório',
			'required' => true
			)
		);

	public function obtemTipos()
	{
		return $this->TipoUso->find('list');
	}

	public function incluirServicos($data = null)
	{
		if(is_null($data)) {
			return false;
		}
		$codigo = $data['codigo'];
		unset($data['codigo']);

		if(!empty($data['nao_excluir'])) {
			$nao_excluir = $data['nao_excluir']; 
		}
		$nao_excluir[]=0; $nao_excluir[]=0;
		unset($data['nao_excluir']);
		$excluir = $this->find('list', array(
			'conditions' => array(
				'ServicoPlanoSaude.codigo_servico' => $codigo,
				'ServicoPlanoSaude.codigo_classificacao_servico NOT' => $nao_excluir
				),
			'fields' => array(
				'ServicoPlanoSaude.codigo',
				'ServicoPlanoSaude.codigo'
				)
			)
		);
		if(!empty($data)) {	
			foreach ($data as $key => $value) {
				$data[$key][$this->name]['maximo'] = Comum::ajustaFormatacao($value[$this->name]['maximo']);
			}
			if(!$return = $this->saveAll($data)) {
				return false;
			}
		}
		if(!empty($excluir)) {
			foreach ($excluir as $key => $value) {
				if(!parent::excluir($value)) {
					return false;
				}
			}
		}
		return true;
	}

	public function obtemServicos($codigo = null)
	{
		return $this->find('all', array('recursive' => 1, 'conditions' => array('ServicoPlanoSaude.codigo_servico' => $codigo)));
	}


}

