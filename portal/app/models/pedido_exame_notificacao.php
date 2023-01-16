<?php
class PedidoExameNotificacao extends AppModel {

	public $name = 'PedidoExameNotificacao';
	public $databaseTable = 'RHHealth';
	public $tableSchema = 'dbo';
	public $useTable = 'pedidos_exames_notificacao';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_pedido_exame';
	public $actsAs = array('Secure');

	/**
	 * [gravaDados description]
	 * 
	 * pega os dados da tela de notificacao e guarda os emails digitados
	 * 
	 * @return [type] [description]
	 */
	public function gravaDados($dados,$id_pedido=null)
	{

		// debug($dados);exit;

		//variaveis auxilizares
		$codigo_pedido_exame = '';
		$codigo_funcionario = '';
		$funcionario_email = '';
		$codigo_cliente = '';
		$cliente_email = '';
		$codigo_fornecedor = '';
		$clinica_email = '';

		if(!is_null($id_pedido)) {
			$codigo_pedido_exame = $id_pedido;
		}
		else if(isset($dados['PedidosExames']['sugestao'])) {
			$codigo_pedido_exame = array_keys($dados['PedidosExames']['sugestao']);
			$codigo_pedido_exame = $codigo_pedido_exame[0];
		}
		else if(isset($dados['relatorio_especifico'])) {
			//seta o pedidod de exames
			$codigo_pedido_exame = key($dados['relatorio_especifico']);
		}//fim codigo_pedido de exames

		// debug($codigo_pedido_exame);exit;

		//pega os dados
		if(isset($dados['Email'])) {
			//verifica se tem indice de funcionarios
			if(isset($dados['Email']['Funcionario'])) {
				//pega os dados
				$codigo_funcionario = key($dados['Email']['Funcionario']);
				$funcionario_email = $dados['Email']['Funcionario'][$codigo_funcionario]['email'];
			}

			//verifica se tem indice de cliente
			if(isset($dados['Email']['Cliente'])) {
				//seta os dados
				$codigo_cliente = key($dados['Email']['Cliente']);
				$cliente_email = $dados['Email']['Cliente'][$codigo_cliente]['email'];
			}
			//verifica se tem indice de fornecedor
			if(isset($dados['Email']['Fornecedor'])) {

				foreach($dados['Email']['Fornecedor'] as $key_for => $for) {					
					$fornecedor_email[$key_for] = $for['email'];
				}

			}

		}//fim indice email
		else {
			//verifica se tem indice de funcionarios
			if(isset($dados['EmailFuncionario']['email'])) {
				//pega os dados				
				$funcionario_email = $dados['EmailFuncionario']['email'];
			}

			//verifica se tem indice de cliente
			if(isset($dados['EmailCliente']['email'])) {
				//seta os dados
				$cliente_email = $dados['EmailCliente']['email'];
			}
			//verifica se tem indice de fornecedor
			if(isset($dados['EmailFornecedor'])) {

				foreach($dados['EmailFornecedor'] as $key_for => $for) {					
					$fornecedor_email[$key_for] = $for['fornecedor'];
				}
			}
		}

		//pode haver mais de um fornecedor para enviar os emails
		foreach ($fornecedor_email as $codigo_fornecedor => $email) {
			//seta os dados
			$dados_incluir = array(
				'codigo_pedido_exame' => $codigo_pedido_exame,
				'codigo_funcionario' => $codigo_funcionario,
				'funcionario_email' => $funcionario_email,
				'codigo_cliente' => $codigo_cliente,
				'cliente_email' => $cliente_email,
				'codigo_fornecedor' => $codigo_fornecedor,
				'clinica_email' => $email
			);

			//monta o array para gravar os dados
			$pedidos_dados = array('PedidoExameNotificacao' => $dados_incluir);

			//grava os dados na tabela
			$this->incluir($pedidos_dados);
		
		}//fim fornecedor email


		// debug($dados_incluir);exit;
		return true;

	}//fim gravaDados

	/**
	 * [buscarNotificacao metodo para buscar a ultima notificacao sem log para relacionar o codigo da log]
	 * @param  [type] $codigo_pedido_exame     [description]
	 * @param  [type] $codigo_pedido_exame_log [description]
	 * @return [type]                          [description]
	 */
	public function buscarNotificacao($codigo_pedido_exame,$codigo_pedido_exame_log)
	{	
		$this->PedidoExame = & ClassRegistry::init('PedidoExame');
		//pega os dados do pedido de exames
		$pedido_exame = $this->PedidoExame->find('first', array('conditions' => array('codigo' => $codigo_pedido_exame)));
		//buscar a ultima notificacao do pedido sem o codigo pedido_log
		$buscar_notificacao = $this->getNotificacao($codigo_pedido_exame);
		//se obtiver resultados
		if($buscar_notificacao){
			$buscar_notificacao['PedidoExameNotificacao']['codigo'] = $buscar_notificacao['PedidoExameNotificacao']['codigo'];
			$buscar_notificacao['PedidoExameNotificacao']['codigo_funcionario'] = $pedido_exame['PedidoExame']['codigo_funcionario'];
			$buscar_notificacao['PedidoExameNotificacao']['codigo_cliente'] = $pedido_exame['PedidoExame']['codigo_cliente'];
			$buscar_notificacao['PedidoExameNotificacao']['codigo_pedido_exame_log'] = $codigo_pedido_exame_log;

			$this->atualizar($buscar_notificacao);
		}
	}//fim buscarNotificacao($codigo_pedido_exame,$codigo_pedido_exame_log)

	public function getNotificacao($codigo_pedido_exame){
		//conditions
		$conditions = array('PedidoExameNotificacao.codigo_pedido_exame' => $codigo_pedido_exame);
		$conditions[] = array('PedidoExameNotificacao.codigo_pedido_exame_log IS NULL');
		//order
		$order = array('PedidoExameNotificacao.codigo DESC');
		//buscar
		$buscar_notificacao = $this->find('first', array('conditions' => $conditions, 'order' => $order));

		return $buscar_notificacao;
	}

}