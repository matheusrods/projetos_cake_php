<?php
class ClienteContato extends AppModel {
	var $name = 'ClienteContato';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'cliente_contato';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_cliente_contato'));
	var $belongsTo = array(
		'TipoContato' => array(
			'className' => 'TipoContato',
			'foreignKey' => 'codigo_tipo_contato'
			),
		'TipoRetorno' => array(
			'className' => 'TipoRetorno',
			'foreignKey' => 'codigo_tipo_retorno'
			)
		);
	var $validate = array(
		'codigo_cliente' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o cliente',
			'required' => true
			),
		'codigo_tipo_contato' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o tipo',
			'required' => true
			),
		'codigo_tipo_retorno' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o tipo',
			'required' => true
			),
		// 'descricao' => array(
		// 	'rule' => 'trataDescricao',
		// 	'message' => 'Informação inválida',
		// 	'required' => true
		// 	),
		'nome' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o representante',
			'required' => true
			),
		);
	
	function trataDescricao($check) {
		if ($this->data[$this->name]['codigo_tipo_retorno'] == TipoRetorno::TIPO_RETORNO_EMAIL)
			return Validation::email($check['descricao']);
		return !empty($check['descricao']);
	}

	function bindLazy() {
		$this->bindModel(
			array('belongsTo' => 
				array('Cliente' => 
					array(
						'className' => 'Cliente',
						'foreignKey' => 'codigo_cliente'
						)
					)
				)
			);
	}
	
	function contatosDoCliente($codigo_cliente, $codigo_tipo_retorno = NULL ) {
		$conditions = array('codigo_cliente' => $codigo_cliente);
		if( $codigo_tipo_retorno )
			array_push( $conditions, array('codigo_tipo_retorno' => $codigo_tipo_retorno));
		return $this->find('all', compact('conditions') );
	}

	function emailsFinanceirosPorCliente($codigo_cliente, $utilizar_email_buonny = false) {
		$emails = $this->find('all', array('conditions' => array('codigo_cliente' => $codigo_cliente, 'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_FINANCEIRO, 'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL)));
		if ($utilizar_email_buonny && count($emails) < 1)
			$emails = array(array('ClienteContato' => array('descricao' => 'cobranca@rhhealth.com.br')));
		$emails = Set::extract($emails, '/ClienteContato/descricao');
		return $emails;
	}

	function retornaTodosEmailsFinanceirosPorCliente($codigo_cliente = null) {
		if(isset($codigo_cliente)){
			$emails = $this->find('all',
				array(
					'conditions' => array(
						'codigo_cliente' => $codigo_cliente, 
						'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_FINANCEIRO, 
						'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL
						),
					'order' => array(
						'ClienteContato.data_inclusao DESC'
						),
					'fields' => array(
						'ClienteContato.codigo',
						'ClienteContato.codigo_cliente',
						'ClienteContato.nome',
						'ClienteContato.descricao',
						'ClienteContato.data_inclusao'
						)
					)

				);
		}
		return $emails;
	}

	function incluirContato($dados) {
		$contatos = array();
		$codigos_tipo_contato = $dados[0]['ClienteContato']['codigo_tipo_contato'];
		$dados[0]['ClienteContato']['codigo_tipo_contato'] = (isset($codigos_tipo_contato[0]) ? $codigos_tipo_contato[0] : null);
		$codigo_cliente = $dados[0]['ClienteContato']['codigo_cliente'];
		$codigo_tipo_contato = $dados[0]['ClienteContato']['codigo_tipo_contato'];
		$nome = $dados[0]['ClienteContato']['nome'];
		for ($indice = 1; $indice < count($dados); $indice++) {
			$dados[$indice]['ClienteContato']['codigo_cliente'] = $codigo_cliente;
			$dados[$indice]['ClienteContato']['codigo_tipo_contato'] = $codigo_tipo_contato;
			$dados[$indice]['ClienteContato']['nome'] = $nome;
		}
		$contatos = array_merge($contatos, $dados);
		for ($indice = 1; $indice < count($codigos_tipo_contato); $indice++) {
			$novo_contato = $dados;
			foreach ($novo_contato as $key => $contato) {
				$novo_contato[$key]['ClienteContato']['codigo_tipo_contato'] = $codigos_tipo_contato[$indice];
			}
			$contatos = array_merge($contatos, $novo_contato);
		}
		$result = $this->saveAll($contatos);
		return $result;
	}



	function importacao_contato_unidade($data){
		$retorno = '';
		$erro_contato = '';

		foreach ($data as $chave => $dados) {
			$conditions = array(
				'codigo_cliente' => $dados['ClienteContato']['codigo_cliente'],
				'codigo_tipo_retorno' => $dados['ClienteContato']['codigo_tipo_retorno'],
				'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL
				);
			$consulta = $this->find("first", array('conditions' => $conditions));
			$dados['ClienteContato']['codigo_tipo_contato'] = TipoContato::TIPO_CONTATO_COMERCIAL;
			if(empty($consulta)){

			// if (!isset($dados['ClienteContato']['codigo']) && empty($dados['ClienteContato']['codigo'])) {
				if(!parent::incluir($dados)){
					$erro_cliente = '';
					foreach ($this->validationErrors as $key => $value) {
						if($dados['ClienteContato']['codigo_tipo_retorno'] == TipoRetorno::TIPO_RETORNO_TELEFONE){
							$erro_cliente .= 'Telefone de Contato da Unidade inválido|';
						}
						elseif($dados['ClienteContato']['codigo_tipo_retorno'] == TipoRetorno::TIPO_RETORNO_EMAIL){
							$erro_cliente .= 'E-mail de Contato da Unidade inválido|';
						}
						$this->validationErrors[$key] = $erro_cliente;
					}
					$retorno['ClienteContato'] = $this->validationErrors;
				}
			}
			else {
				$dados['ClienteContato']['codigo'] = $consulta['ClienteContato']['codigo'];

				if(!parent::atualizar($dados)){
					$erro_cliente = '';
					foreach ($this->validationErrors as $key => $value) {
						// $erro_cliente .= utf8_decode($value).'|';
						if($dados['ClienteContato']['codigo_tipo_retorno'] == TipoRetorno::TIPO_RETORNO_TELEFONE){
							$erro_cliente .= 'Telefone de Contato da Unidade inválido|';
						}
						elseif($dados['ClienteContato']['codigo_tipo_retorno'] == TipoRetorno::TIPO_RETORNO_EMAIL){
							$erro_cliente .= 'E-mail de Contato da Unidade inválido|';
						}
						$this->validationErrors[$key] = $erro_cliente;
					}
					$retorno['ClienteContato'] = $this->validationErrors;
				}
			}
		}

		return $retorno;
	}

	public function obtemEmailsCliente($codigo_cliente = NULL, $return_query = false)
	{
		if(is_null($codigo_cliente)) return false;
		$query = "SELECT LTRIM(RTRIM(CAST(STUFF((SELECT '; '+descricao FROM cliente_contato cc where cc.codigo_cliente = C.codigo and cc.codigo_tipo_retorno=2 FOR xml PATH('')),1,1,'') AS VARCHAR(1000)))) as emails FROM cliente C WHERE C.codigo = ".$codigo_cliente;
		if($return_query) {
			return $query;
		} 
		$dados = $this->query($query);
		return $dados[0][0]['emails'];
	}

}