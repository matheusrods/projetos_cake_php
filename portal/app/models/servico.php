<?php

class Servico extends AppModel {

	var $name = 'Servico';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'servico';
	var $primaryKey = 'codigo';
	var $displayField = 'descricao';
	// var $actsAs = array('Secure');
	var $actsAs = array('Secure','Containable', 'Loggable' => array('foreign_key' => 'codigo_servico'));
	
	const CADASTRO_DE_FICHA    = 1;
	const ATUALIZACAO_DE_FICHA = 2;
	const CONSULTA_MOTORISTA   = 3;
	const RENOVACAO_AUTOMATICA = 4;
	const PREMIO_MINIMO        = 5;
	const TAXA_BANCARIA        = 6;
	const PLACA_FROTA          = 21;
	const SM                   = 22;
	const SM_TELE              = 23;
	const PRECO_FECHADO        = 26;
	const KM                   = 27;
	const DIA                  = 29;
	const PLACA_AVULSA         = 113;

	const ASSINATURA_BASICA    = 124;
	const MENSAGEM             = 125;
	const CARACTER             = 126;
	const COMANDO_ALERTA       = 127;
	const CARACTER_OBC         = 128;
	const MENSAGEM_PRIORITARIA = 129;
	const MACRO                = 130;
	const DEF_GRUPO            = 131;
	const ALARME_PANICO        = 132;
	const MENSAGEM_GRUPO       = 133;
	const PRIOR_GRUPO          = 134;
	const TRANSF_MCT           = 135;
	const DESATIV_REAT         = 136;
	const QMASS                = 137;
	const MACRO_AC             = 138;    
	const PERM_AC              = 140;
	const INCLUSAO_EXCLUSAO_AC = 141;
	const POSICAO_ADICIONAL    = 142;

	const TIPO_EXAME = 'E';
	const TIPO_ENGENHARIA = 'G';
	const TIPO_SAUDE    = 'S';
	const TIPO_CONSULTORIA    = 'C';

	var $validate = array(
		'descricao' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a descrição.',
				'required' => true
				),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Descrição já existe.',
				),
			),
		'tipo_servico' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Tipo de Serviço',
			'required' => true
		),
		'ativo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Status',
			'required' => true
			)

		);

	
	public function servicosDoProfissionalPorPeriodo($filtros, $join_type = FALSE ) {

		$this->TipoOperacao = ClassRegistry::init('TipoOperacao');
		$this->LogFaturamentoTeleconsult = ClassRegistry::init('LogFaturamentoTeleconsult');
		$this->Profissional = ClassRegistry::init('Profissional');
		
		$filtros['data_inicio']	= date("Y-m-d 00:00:00",Comum::dateToTimestamp($filtros['data_inicio']));
		$filtros['data_fim'] = date("Y-m-d 23:59:59",Comum::dateToTimestamp($filtros['data_fim']));

		$fields = array(
			'Servico.descricao',
			'TipoOperacao.codigo_servico',
			'COUNT(Profissional.codigo) AS qtd'
			);
		$conditions = array(
			'Servico.codigo' => array(1,2,3,4),
			'LogFaturamentoTeleconsult.data_inclusao BETWEEN ? AND ?' => array($filtros['data_inicio'], $filtros['data_fim']),
			);
		$joins = array(
			array(
				'table' => $this->TipoOperacao->databaseTable.'.'.$this->TipoOperacao->tableSchema.'.'.$this->TipoOperacao->useTable,
				'alias' => 'TipoOperacao',
				'type'  => 'INNER',
				'conditions' => 'Servico.codigo = TipoOperacao.codigo_servico'
				),
			array(
				'table' => $this->LogFaturamentoTeleconsult->databaseTable.'.'.$this->LogFaturamentoTeleconsult->tableSchema.'.'.$this->LogFaturamentoTeleconsult->useTable,
				'alias' => 'LogFaturamentoTeleconsult',
				'type'  => ($join_type === TRUE ? 'INNER' : 'LEFT'),
				'conditions' => 'TipoOperacao.codigo = LogFaturamentoTeleconsult.codigo_tipo_operacao'
				),
			array(
				'table' => $this->Profissional->databaseTable.'.'.$this->Profissional->tableSchema.'.'.$this->Profissional->useTable,
				'alias' => 'Profissional',
				'type'  => ($join_type === TRUE ? 'INNER' : 'LEFT'),
				'conditions' => array(
					'LogFaturamentoTeleconsult.codigo_profissional = Profissional.codigo',
					'Profissional.codigo_documento' => $filtros['pfis_cpf'])
				),
			);
		$group = array(
			'Servico.descricao',
			'TipoOperacao.codigo_servico'
			);
		$order = array('TipoOperacao.codigo_servico');
		return $this->find('all', compact('fields', 'conditions', 'joins', 'group'));
	}

	/**
	 * Retorna um serviço pelo código
	 * 
	 * @param int $codigo_servico
	 * @return array|false
	 */
	public function getServicoByCodigo($codigo_servico) {
		try {
			if(empty($codigo_servico))
				throw new Exception('Codigo servico é obrigatório!');

			$servico = $this->find('first', array(
				'conditions' => array(
					'codigo' => $codigo_servico
					)
				));

			return $servico;
		} catch(Exception $e) {
			return false;
		}
	}

	public function getServicoByProduto($codigo_produto) {
		try {
			if(empty($codigo_produto))
				throw new Exception('Codigo servico é obrigatório!');

			$this->bindModel(
				array('belongsTo' => 
					array(
						'ProdutoServico' => array(
							'className' => 'ProdutoServico',
							'foreignKey' => false ,
							'type' => 'INNER', 
							'conditions' => array('ProdutoServico.codigo_servico =  Servico.codigo')
							)
						)
					),false
				);
			$servicos = $this->find('all', array(
				'conditions' => array(
					'codigo_produto' => $codigo_produto
					)
				));

			return $servicos;
		} catch(Exception $e) {
			return false;
		}
	}

	/**
	* Lista Servicos
	* 
	* @return array
	*/
	function listar() {
		$servicos = $this->find('list');
		return $servicos;
	}

	function converteFiltroEmCondition($data) {
		$conditions = array ();
		if (! empty ( $data ['codigo'] ))
			$conditions ['Servico.codigo'] = $data ['codigo'];
		
		if (! empty ( $data ['descricao'] ))
			$conditions ['Servico.descricao LIKE'] = '%' . $data ['descricao'] . '%';

		if (! empty ( $data ['codigo_externo'] ))
			$conditions ['Servico.codigo_externo'] = $data ['codigo_externo'];

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(Servico.ativo = ' . $data ['ativo'] . ' OR Servico.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['Servico.ativo'] = $data ['ativo'];
		}

		if (! empty ( $data ['tipo_servico'] ))
			$conditions ['Servico.tipo_servico'] = $data ['tipo_servico'];
		
		return $conditions;
	}

	function carregar($codigo) {
		$servicos = $this->find ( 'first', array (
			'conditions' => array (
				$this->name . '.codigo' => $codigo 
				) 
			) );
		return $servicos;
	}

	function atualizar_status($codigo_servico = null, $codigo_exame = null, $status){
		$this->Exame = ClassRegistry::init('Exame');

		if(isset($codigo_servico) && !empty($codigo_servico)){
			$conditions = array('Servico.codigo' => $codigo_servico);			
		}

		if(isset($codigo_exame) && !empty($codigo_exame)){
			$conditions = array('Exame.codigo' => $codigo_exame);			
		}

		if(empty($conditions)) {
			$conditions['Exame.codigo'] = 0;
			$conditions['Servico.codigo'] = 0;
		}

		$fields = array(
			'Servico.codigo',
			'Servico.ativo',
			'Exame.codigo',
			'Exame.ativo'
			);

		$joins = array(
			array(
				'table' => $this->Exame->databaseTable.'.'.$this->Exame->tableSchema.'.'.$this->Exame->useTable,
				'alias' => 'Exame',
				'type'  => 'INNER',
				'conditions' => 'Servico.codigo = Exame.codigo_servico'
				)
			);
		
		$consulta = $this->find('first', compact('conditions','joins','fields'));

		if(!empty($consulta)){
			$dados = array(
				'Servico' => array(
					'codigo' => $consulta['Servico']['codigo'],
					'ativo' => $status
					),
				'Exame' => array(
					'codigo' => $consulta['Exame']['codigo'],
					'ativo' => $status
					)
				);

			if($this->Exame->atualizar($dados, false)){
				if($this->atualizar($dados, false)){
					return true;
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}					
		}
		else{
			return false;
		}	
	}
}

?>