<?php
class Audiometria extends AppModel {

	public $primaryKey      = 'codigo';
	public $actsAs          = array('Secure', 'Containable'); 
	public $recursive       = -1;

	public $validate = array(
		'data_exame' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Data',
			'required' => true
			),
		'resultado' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o resultado',
			'required' => true
			),
		'aparelho' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o aparelho',
			'required' => true
			),
		'ref_seq' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Ref / Seq',
			'required' => true
			),
		'fabricante' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o fabricante',
			'required' => true
			),
		'calibracao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe data da calibração',
			'required' => true
			),
		'repouso_auditivo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o repouso auditivo',
			'required' => true
			),
		'diagnostico' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o diagnóstico',
			'required' => true
		),
		'diagnostico_descricao' => array(
			'rule' => array('diagnostico_descricao'),
			'message' => 'Informe a Descrição diagnóstico',
		),
	);

	public function diagnostico_descricao() {
		if($this->data[$this->alias]['diagnostico'] == 2){
			if(empty($this->data[$this->alias]['diagnostico_descricao'])){
                return false;
			}
		}

        return true;
	}

	public function converteFiltroEmCondition($data) {
		$conditions = array();

		if (!empty($data['codigo_cliente'])) {
			$conditions['OR']['GrupoEconomico.codigo_cliente'] = $data['codigo_cliente'];
			$conditions['OR']['Cliente.codigo'] = $data['codigo_cliente'];
		}

		if (!empty($data['nome_funcionario']))
			$conditions['Funcionario.nome LIKE'] = '%'.$data['nome_funcionario'].'%';

		if (!empty($data['cpf']))
			$conditions['Funcionario.cpf'] = $data['cpf'];

		if (!empty($data['data_exame'])) {
			$dt = explode('/', $data['data_exame']);
			if($dt[0] > 0 && $dt[1] > 0 && $dt[2] > 0) {	
				$date = DateTime::createFromFormat('d/m/Y', $data['data_exame']);
				$conditions['Audiometria.data_exame'] = $date->format('Y-m-d');
			}
		}

		if (!empty($data['data_solicitacao'])) {	
			$dt = explode('/', $data['data_solicitacao']);
			if($dt[0] > 0 && $dt[1] > 0 && $dt[2] > 0) {
				$date = DateTime::createFromFormat('d/m/Y', $data['data_solicitacao']);
				$conditions['PedidoExame.data_solicitacao'] = $date->format('Y-m-d');
			}
		}

		if (!empty($data['tipo_exame'])) {
			switch ($data['tipo_exame']) {
				case '1':
				$conditions['PedidoExame.exame_admissional'] = 1;
				break;

				case '2':
				$conditions['PedidoExame.exame_periodico'] = 1;
				break;

				case '3':
				$conditions['PedidoExame.exame_demissional'] = 1;
				break;

				case '4':
				$conditions['PedidoExame.exame_retorno'] = 1;
				break;

				case '5':
				$conditions['PedidoExame.exame_mudanca'] = 1;
				break;

				case '6':
				$conditions['PedidoExame.exame_monitoracao'] = 1;
				break;

				case '7':
				$conditions['PedidoExame.pontual'] = 1;
				break;
			}
		}

		return $conditions;
	}

	public function incluir($data) {
		return parent::incluir($data);
	}

	public function atualizar($data) {
		return parent::atualizar($data);

	}

	public function carrega_infos_user_incluir($codigo_cliente,$codigo_funcionario){
		$Funcionario = & ClassRegistry::init('Funcionario');
		$conditions['ClienteFuncionario.codigo_funcionario'] = $codigo_funcionario;
		if(!empty($codigo_cliente)) {
			$conditions['OR']['GrupoEconomico.codigo_cliente'] = $codigo_cliente;
			$conditions['OR']['Cliente.codigo'] = $codigo_cliente;
		}
		$funcionario = NULL;
		$funcionario = $Funcionario->find('first', array(
			'joins' => array(
				array(
					'table' => 'cliente_funcionario',
					'alias' => 'ClienteFuncionario',
					'type' => 'INNER',
					'conditions' => array(
						'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'
						) 		
					),
				array(
					'table' => 'cliente',
					'alias' => 'Cliente',
					'type' => 'INNER',
					'conditions' => array(
						'Cliente.codigo = ClienteFuncionario.codigo_cliente'
						) 		
					),
				array(
					'table' => 'grupos_economicos_clientes',
					'alias' => 'GrupoEconomicoCliente',
					'type' => 'INNER',
					'conditions' => array(
						'GrupoEconomicoCliente.codigo_cliente = Cliente.codigo'
						) 		
					),
				array(
					'table' => 'grupos_economicos',
					'alias' => 'GrupoEconomico',
					'type' => 'INNER',
					'conditions' => array(
						'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
						) 		
					),
				),
			'conditions' => $conditions,
			'fields' => array(
				'Funcionario.codigo',
				'Funcionario.nome',
				'Cliente.razao_social'
				)
			)
		);

		return $funcionario;
	}

	public function carrega_infos_user_editar($codigo,$codigo_cliente){
		$Funcionario = & ClassRegistry::init('Funcionario');
		//condicoes
		$conditions['Audiometria.codigo'] = $codigo;
		//tratamento do codigo cliente
		if(!empty($codigo_cliente)) {
			$conditions['OR']['GrupoEconomico.codigo_cliente'] = $codigo_cliente;
			$conditions['OR']['Cliente.codigo'] = $codigo_cliente;
		}
		$funcionario = NULL;
		$funcionario = $Funcionario->find('first', array(
			'joins' => array(
				array(
					'table' => 'cliente_funcionario',
					'alias' => 'ClienteFuncionario',
					'type' => 'INNER',
					'conditions' => array(
						'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'
						) 		
					),
				array(
					'table' => 'cliente',
					'alias' => 'Cliente',
					'type' => 'INNER',
					'conditions' => array(
						'Cliente.codigo = ClienteFuncionario.codigo_cliente'
						) 		
					),
				array(
					'table' => 'grupos_economicos_clientes',
					'alias' => 'GrupoEconomicoCliente',
					'type' => 'INNER',
					'conditions' => array(
						'GrupoEconomicoCliente.codigo_cliente = Cliente.codigo'
						) 		
					),
				array(
					'table' => 'grupos_economicos',
					'alias' => 'GrupoEconomico',
					'type' => 'INNER',
					'conditions' => array(
						'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
						) 		
					),
				array(
					'table' => 'audiometrias',
					'alias' => 'Audiometria',
					'type' => 'INNER',
					'conditions' => array(
						'Audiometria.codigo_funcionario = ClienteFuncionario.codigo_funcionario'
						) 		
					),
				),
			'conditions' => $conditions,
			'fields' => array(
				'Funcionario.codigo',
				'Funcionario.nome',
				'Cliente.razao_social'
				)
			)
		);

		return $funcionario;
	}

}
