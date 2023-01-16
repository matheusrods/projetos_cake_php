<?php
class FuncionarioMatriculasController extends AppController {
	public $name = 'FuncionarioMatriculas';
	var $uses = array('FuncionarioMatricula');

	public function salva_matricula()
	{
		$this->autoRender = false;
		$retorno = -1;
		if($_POST) {
			$dados = $_POST['dados'];
			if(!isset($dados[$this->name]['codigo'])) {
				$dados[$this->FuncionarioMatricula->name]['ativo'] = 1;
			}
			if(!empty($dados[$this->FuncionarioMatricula->name]['data_admissao'])) {
				$this->loadModel('FuncionarioSetorCargo');
				$dados[$this->FuncionarioSetorCargo->name][0]['data_inicio'] = $dados[$this->FuncionarioMatricula->name]['data_admissao'];
			}
			$retorno = ($this->FuncionarioMatricula->incluirTodos($dados));
		}
		echo json_encode($retorno);
	}

	public function edita_matricula()
	{
		$this->autoRender = false;
		$retorno = -1;
		if($_POST) {
			$dados[$this->FuncionarioMatricula->name] = $_POST['dados'];
			if($this->FuncionarioMatricula->atualizar($dados)) {
				if(!empty($dados[$this->FuncionarioMatricula->name]['data_demissao'])) {
					$this->loadModel('FuncionarioSetorCargo');
					$date = DateTime::createFromFormat('d/m/Y', $dados[$this->FuncionarioMatricula->name]['data_demissao']);
					$this->FuncionarioSetorCargo->updateAll(
						array(
							$this->FuncionarioSetorCargo->name.'.data_fim' => '\''.$date->format('Y-m-d').'\''
							), 
						array(
							'AND' => array(
								$this->FuncionarioSetorCargo->name.'.codigo_funcionario_matricula' => $dados[$this->FuncionarioMatricula->name]['codigo'],
								$this->FuncionarioSetorCargo->name.'.data_fim =' => null
								)
							)
						);
				}
			}
		}
		echo json_encode($retorno);
	}

	public function insere_setor_cargo()
	{
		$this->autoRender = false;
		$retorno = -1;
		if($_POST) {
			$this->loadModel('FuncionarioSetorCargo');
			$dados[$this->FuncionarioSetorCargo->name] = $_POST['dados'];
			$retorno = ($this->FuncionarioSetorCargo->incluir($dados));
		}
		echo json_encode($retorno);
	}

	public function edita_setor_cargo()
	{
		$this->autoRender = false;
		$retorno = -1;
		if($_POST) {
			$this->loadModel('FuncionarioSetorCargo');
			$dados[$this->FuncionarioSetorCargo->name] = $_POST['dados'];
			$retorno = ($this->FuncionarioSetorCargo->atualizar($dados));
			if(!$retorno) {
				$retorno['error'] = true;
				$retorno['message'] = $this->FuncionarioSetorCargo->validationErrors['data_fim'];
			} else {
				$retorno['error'] = false;
			}
		}
		echo json_encode($retorno);
	}


	public function listagem_matriculas($codigo_funcionario, $codigo_cliente) {

		$this->layout = 'ajax';
		$this->paginate['FuncionarioMatricula'] = array(
			'limit' => 50,
			);
		$funcionario_matriculas = $this->paginate('FuncionarioMatricula');
		$this->loadModel('Setor');
		$this->Setor->recursive = -1;
		$this->loadModel('Cargo');
		$this->Cargo->recursive = -1;
		foreach ($funcionario_matriculas as $key => $funcionario_matricula) {
			foreach ($funcionario_matricula['FuncionarioSetorCargo'] as $key2 => $funcionario_setor_cargo) {
				$funcionario_matriculas[$key]['FuncionarioSetorCargo'][$key2] = $funcionario_matriculas[$key]['FuncionarioSetorCargo'][$key2] + $this->Setor->findByCodigo($funcionario_setor_cargo['codigo_setor'], array('codigo', 'descricao'));
				$funcionario_matriculas[$key]['FuncionarioSetorCargo'][$key2] = $funcionario_matriculas[$key]['FuncionarioSetorCargo'][$key2] + $this->Cargo->findByCodigo($funcionario_setor_cargo['codigo_cargo'], array('codigo', 'descricao'));
			}
		}
		$this->set(compact('funcionario_matriculas'));

		$this->GrupoEconomicoCliente =& ClassRegistry::init('GrupoEconomicoCliente');
		$bloqueado = $this->GrupoEconomicoCliente->findByCodigoCliente($codigo_cliente, array('bloqueado'));
		$bloqueado = $bloqueado['GrupoEconomicoCliente']['bloqueado'];
		$this->set(compact('bloqueado'));


		$this->Cliente =& ClassRegistry::init('Cliente');
		$unidades = $this->Cliente->lista_por_cliente($codigo_cliente, $bloqueado);

		$this->Setor =& ClassRegistry::init('Setor');
		$setores = $this->Setor->lista_por_cliente($codigo_cliente, $bloqueado);

		if(!$bloqueado) {
			$cargos = $this->Cargo->lista_por_cliente($codigo_cliente, $bloqueado);
		} else {
			$cargos = $this->Cargo->lista_por_cliente_setor($setores);
		}

		$this->set(compact('bloqueado', 'unidades', 'setores', 'cargos', 'codigo_funcionario', 'codigo_cliente'));
	}


}