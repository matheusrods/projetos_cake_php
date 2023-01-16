<?php
class PrevencaoRiscoAmbiental extends AppModel {

	public $name = 'PrevencaoRiscoAmbiental';
	public $databaseTable = 'RHHealth';
	public $tableSchema = 'dbo';
	public $useTable = 'prevencao_riscos_ambientais';
	public $primaryKey = 'codigo';
	var $actsAs = array('Secure','Containable', 'Loggable' => array('foreign_key' => 'codigo_prevencao_riscos_ambientais'));

    var $validate = array(
        'codigo_setor' => array(
            'rule' => 'notEmpty',
            'message' => 'Selecione um setor!',
        ),
        'codigo_tipo_acao' => array(
            'rule' => 'notEmpty',
            'message' => 'Selecione uma ação!',
        ),
    );

	public $belongsTo = array(
		'Gpra' => array(
			'className' => 'Gpra',
			'foreignKey' => 'codigo_grupo_prevencao_risco_ambiental',
		)
	);


	public function obterDadosDoCliente($codigo_cliente = null)
	{	
		if(is_null($codigo_cliente)) {
			return false;
		}

		$this->Cliente = ClassRegistry::init('Cliente');
		$this->Funcionario = ClassRegistry::init('Funcionario');
		$this->ClienteFuncionario = ClassRegistry::init('ClienteFuncionario');

		$this->Cliente->virtualFields = array(
			 'qnt_func' => '( SELECT count(*) as qtd_funcionario 
								FROM '.$this->Funcionario->databaseTable.'.'.$this->Funcionario->tableSchema.'.'.$this->Funcionario->useTable.' AS Funcionario
								LEFT JOIN '.$this->ClienteFuncionario->databaseTable.'.'.$this->ClienteFuncionario->tableSchema.'.'.$this->ClienteFuncionario->useTable.' AS ClienteFuncionario on ClienteFuncionario.codigo_funcionario = Funcionario.codigo
								WHERE ClienteFuncionario.codigo_cliente = Cliente.codigo )'
								);


		$dados_cliente = $this->Cliente->find('first', array(
			'conditions' => array(
				'Cliente.codigo' => $codigo_cliente
				),
			'joins' => array(
				array(
					'table' => 'cliente_endereco',
					'alias' => 'ClienteEndereco',
					'type' => 'LEFT',
					'conditions' => array(
						'ClienteEndereco.codigo_cliente = Cliente.codigo'
						)
					),
				),
			'fields' => array(
				'Cliente.codigo',
				'Cliente.razao_social',
				'Cliente.qnt_func',
				'ClienteEndereco.bairro',
				'ClienteEndereco.cidade',
				'ClienteEndereco.estado_descricao'
				)
			)
		);
		return $dados_cliente;
	}

	public function incluir($data = null)
	{
		$deletarDados = $this->Gpra->PrevencaoRiscoAmbiental->find('list',
            array(
                'conditions' => array(
                    'PrevencaoRiscoAmbiental.codigo_grupo_prevencao_risco_ambiental' => $data['Gpra']['codigo']
                ),
                'fields' => array(
                    'PrevencaoRiscoAmbiental.codigo',
                    'PrevencaoRiscoAmbiental.codigo'
                )
			)
		);

		if($this->Gpra->saveAll($data)) {
			if(!empty($deletarDados)) {
				$this->Gpra->PrevencaoRiscoAmbiental->deleteAll(array('PrevencaoRiscoAmbiental.codigo' => $deletarDados));
			}
			return true;
		}
		return false;
	}

	public function obterSetoresDaEmpresa($codigo_cliente)
	{
		$this->GrupoEconomicoCliente = ClassRegistry::init('GrupoEconomicoCliente');
		return $this->GrupoEconomicoCliente->find('list', array(
			'conditions' => array(
				'GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente
				),
			'joins' => array(
				array(
					'table' => 'grupos_economicos',
					'alias' => 'GrupoEconomico',
					'type' => 'INNER',
					'conditions' => array(
						'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
						)
					),
				array(
					'table' => 'setores',
					'alias' => 'Setor',
					'type' => 'INNER',
					'conditions' => array(
						'Setor.codigo_cliente = GrupoEconomico.codigo_cliente'
						)
					)
				),
			'fields' => array(
				'Setor.codigo',
				'Setor.descricao'
				),
			'order' => 'Setor.descricao ASC'
			)
		);
	}

	public function afterFind($dados) {

		if(isset($dados[0][$this->name]['data_inicial'])){
			if(!empty($dados[0][$this->name]['data_inicial'])) {

				//verifica se tem barra para nao dar erro na funcao
				if(strpos($dados[0][$this->name]['data_inicial'], "-")) {
					$dados[0][$this->name]['data_inicial'] = comum::formataData($dados[0][$this->name]['data_inicial'], 'ymd', 'dmy');
				}//fim data inicial
			}
		}
		if(isset($dados[0][$this->name]['data_final'])){
		
			if(!empty($dados[0][$this->name]['data_final'])) {
				//verifica se tem barra para nao dar erro na funcao
				if(strpos($dados[0][$this->name]['data_final'], "-")) {
					$dados[0][$this->name]['data_final'] = comum::formataData($dados[0][$this->name]['data_final'], 'ymd', 'dmy');
				}//fim data final
			}
		}
		return $dados;
	}

}