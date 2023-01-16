<?php
class ItemPedidoExameBaixa extends AppModel {

	public $name		   	= 'ItemPedidoExameBaixa';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'itens_pedidos_exames_baixa';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure','Loggable' => array('foreign_key' => 'codigo_itens_pedidos_exames_baixa'));

	
	var $validate = array(
		'data_realizacao_exame' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a data de realização do exame',
			 ),
		),
		'codigo_itens_pedidos_exames' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Código de Item'
			 ),
			  'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'Este item do pedido já possui baixa registrada',
                'on' => 'create'
            )
		),

		//trecho comentado para que o resultado nao seja mais um campo obrigatorio; Demanda - PC-1115

		// 'resultado' => array(
		// 	'notEmpty' => array(
		// 		'rule' => 'notEmpty',
		// 		'message' => 'Informe o Resultado do Exame'
		// 	)
		// )
	);
	
	/***
	 * Função verifica se exame já foi realizado por um determinado funcionário, e retorna a data de realização
	 * 
	 * @param (int) $codigo_cliente_funcionario
	 * @param (int) $codigo_exame
	 */
	
	public function verificaDataFuncionarioRealizouExame($codigo_funcionario_setor_cargo, $codigo_exame) {

		$options['fields'] = array('ItemPedidoExameBaixa.data_realizacao_exame');
		$options['joins'] = array(
			array(
				'table' => 'itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => array('ItemPedidoExame.codigo = ItemPedidoExameBaixa.codigo_itens_pedidos_exames')
			),
			array(
				'table' => 'pedidos_exames',
				'alias' => 'PedidoExame',
				'type' => 'INNER',
				'conditions' => array('PedidoExame.codigo = ItemPedidoExame.codigo_pedidos_exames')
			),				
		);
		$options['conditions'] = array(
				'PedidoExame.codigo_func_setor_cargo' => $codigo_funcionario_setor_cargo,
				'ItemPedidoExame.codigo_exame' => $codigo_exame
		);
		$options['order'] = array('ItemPedidoExameBaixa.data_realizacao_exame DESC');
			
		return $this->find('first', $options);
	}
	
	/**
	 * 
	 * 
	 */
	
	public function verificaValidadeASO($dados_cliente, $dados_config) {
		
		$funcionarios_com_ASO_vencida = array();
		$qtd_funcionarios = 0;
		
		foreach($dados_cliente as $codigo_cliente => $cliente) {
			foreach($cliente['cliente_funcionario'] as $codigo_cliente_funcionario => $cliente_funcionario) {
					
				$qtd_funcionarios++;
				unset($options);
					
				$options['conditions'][] = array("ItemPedidoExame.codigo_exame = (select valor from configuracao where chave = 'INSERE_EXAME_CLINICO')");
				$options['conditions'][] = array("ClienteFuncionario.codigo = {$codigo_cliente_funcionario}");
					
				$options['fields'] = array('ItemPedidoExameBaixa.data_realizacao_exame');
				$options['order'] = array('ItemPedidoExameBaixa.data_realizacao_exame DESC');
		
				$options['joins'] = array(
					array(
						'table' => 'itens_pedidos_exames',
						'alias' => 'ItemPedidoExame',
						'type' => 'INNER',
						'conditions' => array('ItemPedidoExame.codigo = ItemPedidoExameBaixa.codigo_itens_pedidos_exames')
					),
					array(
						'table' => 'pedidos_exames',
						'alias' => 'PedidoExame',
						'type' => 'INNER',
						'conditions' => array('PedidoExame.codigo = ItemPedidoExame.codigo_pedidos_exames')
					),
					array(
						'table' => 'cliente_funcionario',
						'alias' => 'ClienteFuncionario',
						'type' => 'INNER',
						'conditions' => array('ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario')
					)
				);
		
				$dados_ASO = $this->find('first', $options);
				
					
				if(isset($dados_ASO['ItemPedidoExameBaixa']['data_realizacao_exame'])) {
					$data_exame = implode('-', array_reverse(explode("/", $dados_ASO['ItemPedidoExameBaixa']['data_realizacao_exame'])));
					$data_validade = date('Y-m-d', strtotime("+{$dados_config['Configuracao']['valor']} days", strtotime($data_exame)));
					$hoje = date('Ymd');
					
					if((int) (str_replace('-', '', $data_validade)) > (int) $hoje) {
						$funcionarios_com_ASO_vencida[$codigo_cliente_funcionario] = array(
							'data_validade' => implode('/', array_reverse(explode("-", $data_validade))),
							'data_exame' => $dados_ASO['ItemPedidoExameBaixa']['data_realizacao_exame'],
							'nome' => $cliente_funcionario['Funcionario']['nome']
						);
					}
				} else {
					$funcionarios_com_ASO_vencida[$codigo_cliente_funcionario] = array('data_validade' => null, 'data_exame' => null);
				}
			}
		}
		
		return array(
			'funcionarios_com_ASO_vencida' => $funcionarios_com_ASO_vencida,
			'qtd_funcionarios' => $qtd_funcionarios
		);
	}
	
	public function reverte_baixa ($codigo_pedidos_exames, $itens_pedidos_exames = null) {

		$PedidoExame =& ClassRegistry::init('PedidoExame');
		$ItemPedidoExame =& ClassRegistry::init('ItemPedidoExame');
		$this->ItemPedidoExame = ClassRegistry::init('ItemPedidoExame');

 		$conditions = array('ItemPedidoExame.codigo_pedidos_exames' => $codigo_pedidos_exames );
 		if( $itens_pedidos_exames ){
			if( count( $itens_pedidos_exames ) == 1 ) $itens_pedidos_exames = $itens_pedidos_exames[0];
 			$conditions['AND'] = array("ItemPedidoExameBaixa.codigo_itens_pedidos_exames" => $itens_pedidos_exames );
 		}
                    
        $joins  = array(
            array(
                'table' => 'itens_pedidos_exames_baixa',
                'alias' => 'ItemPedidoExameBaixa',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExameBaixa.codigo_itens_pedidos_exames = ItemPedidoExame.codigo'
            ),
        );

        $fields = array('ItemPedidoExameBaixa.codigo');

        //fields para a query da buscar pelo item pedido de exame
        $fields_baixa_exame = array('ItemPedidoExame.codigo', 'ItemPedidoExame.compareceu', 'ItemPedidoExame.data_realizacao_exame');

        //query que buscar o itens pedidos de exames
        $baixa_exame = $ItemPedidoExame->find('all', array('fields' => $fields_baixa_exame, 'conditions' => $conditions, 'joins' => $joins));

        //variavel que auxiliar o foreach
        $baixas_exames = array();
        foreach($baixa_exame as $dado) {
			$baixas_exames['ItemPedidoExame'][] = $dado['ItemPedidoExame'];
		}
		
		//variavel que auxiliar o foreach
		$dados = array();
		foreach($baixas_exames['ItemPedidoExame'] as $key => $dado_baixa_exame){
			$dados['ItemPedidoExame'] = array( 
	            'codigo' => $dado_baixa_exame['codigo'],
	            'compareceu'     => NULL,
	            'data_realizacao_exame'=> NULL,
	        );
			//atualiza o item pedido de exame e tira o comparecimento e o a data de realizacao do exame
			$this->ItemPedidoExame->atualizar($dados);
		}       

        $itens = $ItemPedidoExame->find('list', array('fields' => $fields,'conditions' => $conditions, 'joins' => $joins));
        
        if(!empty($itens)){

			try {
				$this->query('begin transaction');

				if($this->deleteAll(array('ItemPedidoExameBaixa.codigo'  => $itens
				), false, true)){
					$PedidoExame->read(null,$codigo_pedidos_exames);
					$status = $PedidoExame->statusBaixasExames( $codigo_pedidos_exames );
					$PedidoExame->set('codigo_status_pedidos_exames',$status);
				
				if(!$PedidoExame->save()) throw new Exception();

				} else {
					throw new Exception();
				}
				
				$this->commit();
				return true;
			} catch (Exception $ex) {
				$this->rollback();
				return false;
			}  

        }

        return false;
	}	
}