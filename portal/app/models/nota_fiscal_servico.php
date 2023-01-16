<?php

App::import('Core', 'Validation');
App::import('Helper', 'Form');

class NotaFiscalServico extends AppModel {

	public $name		   	= 'NotaFiscalServico';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'nota_fiscal_servico';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Loggable' => array('foreign_key' => 'codigo_nota_fiscal_servico'), 'Containable');


	var $validate = array(
		'codigo_fornecedor' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Credenciado',
			),
		),
		'codigo_nota_fiscal_status' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Status da Nota fiscal',
			),
		),
		'numero_nota_fiscal' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Numero da Nfs',
			),
			'notDups' => array(
					'rule' => array('notDups', 1),
					'message' => 'Nota fiscal já foi cadastrada para este Prestador.',
					'on'		=> 'create',
			)
		),
		'data_emissao' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a Data de Emissão',
			),
			// 'notValid' => array(
			// 	'rule' => array('validaDate'),
			// 	'message' => 'Insira uma data válida no formato DD-MM-YYYY.',
			// ),

			'regexp' => array(
				'rule' => '/^\d{1,2}\/\d{1,2}\/\d{4}$/',
				'message' => 'Insira uma data válida no formato DD-MM-YYYY.',
				'allowEmpty' => true			
			)
		),

		// 'data_recebimento' => array(
		// 	'notEmpty' => array(
		// 		'rule' => 'notEmpty',
		// 		'message' => 'Informe a Data de Recebimento',
		// 	),
		// 	'date' => array(
		// 		'rule' => 'dmy',
		// 		'message' => 'Insira uma data válida no formato DD-MM-YYYY.',
		// 		'allowEmpty' => true			
		// 	)
		// ),

		// 'data_vencimento' => array(
		// 	'notEmpty' => array(
		// 		'rule' => 'notEmpty',
		// 		'message' => 'Informe a Data de Vencimento',
		// 		),
		// 	),
		
		// 'valor' => array(
		// 	'notEmpty' => array(
		// 		'rule' => 'notEmpty',
		// 		'message' => 'Informe o Valor',
		// 	),
		// 	'regExp' => array(
		// 		'rule' => 'numeric',
		// 		'message' => 'Por favor, informe o valor da nota fiscal.'
		// 	),
		// ),

		// 'ativo' => array(
		// 	'notEmpty' => array(
		// 		'rule' => 'notEmpty',
		// 		'message' => 'Informe o Status',
		// 	),
		// 	'regExp' => array(
		// 		'rule' => '/^(([0-1])$/',
		// 		'message' => 'Status incorreto',
		// 	)
		// )

	);


	const PENDENTE = 'Pendente';
	const EM_ANALISE = 'Em Analise';
	const CANCELADA = 'Cancelada';
	const PROCESSAMENTO_PARCIAL = 'Processamento';
	const PROCESSADO = 'Processado';

	function __construct($id = false, $table = null, $ds = null) {
		
		$this->Validation =& Validation::getInstance();
		$this->FormHelper = new FormHelper();

        parent::__construct($id, $table, $ds); 
	} 
	
	function setUp() {
		$this->Validation =& Validation::getInstance();
		$this->_appEncoding = Configure::read('App.encoding');
	}

	public function listaStatusDeNFS(){
		return array(
			NotaFiscalServico::PENDENTE,
			NotaFiscalServico::EM_ANALISE,
			NotaFiscalServico::CANCELADA,
			NotaFiscalServico::PROCESSAMENTO_PARCIAL,
			NotaFiscalServico::PROCESSADO
		);
	}

	function converteFiltroEmConditiON($data) {
		$conditions = array();
		
		
        
		if (!empty($data['codigo_fornecedor'])) {
			$conditions['NotaFiscalServico.codigo_fornecedor'] = $data['codigo_fornecedor'];
		}

		if (!empty($data['numero_nota_fiscal'])) {
			$conditions['NotaFiscalServico.numero_nota_fiscal'] = $data['numero_nota_fiscal'];
		}

		if (!empty($data['codigo_documento'])) {
			$conditions['Fornecedor.codigo_documento'] = $data['codigo_documento'];
		}

		if (! empty ( $data ['nome'] )) {
			$conditions ['Fornecedor.nome LIKE'] = '%' . $data ['nome'] . '%';
		}

		if (!empty($data['codigo_nota_fiscal_status'])) {
			$conditions['NotaFiscalServico.codigo_nota_fiscal_status'] = $data['codigo_nota_fiscal_status'];
		}
		
		if (!empty($data['codigo_nota_fiscal_status'])) {
			$conditions['NotaFiscalServico.codigo_nota_fiscal_status'] = $data['codigo_nota_fiscal_status'];
		}

		if (!empty($data['status_pagamento'])) {
			//pago
			if($data['status_pagamento'] == 1) {
				$conditions[] = 'Tranpag.dtpagto IS NOT NULL' ;
			}
			else if($data['status_pagamento'] == 2) { //nao pago
				$conditions[] = 'Tranpag.dtpagto IS NULL' ;	
			}
		}

		if (!empty($data['codigo_tipo_servicos_nfs'])) {
			$conditions['NotaFiscalServico.codigo_tipo_servicos_nfs'] = $data['codigo_tipo_servicos_nfs'];
		}

		if(!empty($data["data_inicio"])) {
			$data_inicio = AppModel::dateToDbDate($data["data_inicio"].' 00:00:00');
			$data_fim = AppModel::dateToDbDate($data["data_fim"].' 23:59:59');
			switch ($data["tipo_data"]) {
				case 'I'://data da inclusão
					$conditions["NotaFiscalServico.data_inclusao BETWEEN ? and ? "] = array($data_inicio, $data_fim);
					break;
				case 'V'://data de vencimento
					$conditions["NotaFiscalServico.data_vencimento BETWEEN ? and ?"] = array($data_inicio, $data_fim);
					break;
			}//switch
		}//fim if
				
		return $conditions;
	}

	/**
	 * Cadastro de uma nota fiscal de serviço
	 *
	 * @param array $nota
	 * @return void
	 */
	public function cadastrar(array $nota = array()){

	}


	public function listar( array $options = array() ){

	}


	public function atualizarStatusDaNFS( $codigo_nota = null, $status = null ){

		$statusDisponiveis = $this->listaStatusDeNFS();
		
		if(!empty($codigo_nota) && !empty($status) && !in_array($status, $statusDisponiveis)){
			
		}
	}

	/**
	 * [getDadosNfs description]
	 * 
	 * metodo para pegar os dados do demonstrativo de notas fiscais
	 * 
	 * @param  [type] $filtros [description]
	 * @return [type]          [description]
	 */
	public function get_demonstrativo_nfs($filtros)
	{

		$this->Tranpag = ClassRegistry::init('Tranpag');
		$this->FornecNaveg = ClassRegistry::init('FornecNaveg');

		//monta as conditions
		$conditions = $this->converteFiltroEmCondition($filtros);

		//seta para pegar as notas fiscais com o status processado
		$conditions['codigo_nota_fiscal_status'] = 5; //processado

		//monta o que irá retornar do select
		$fields = array(
			'NotaFiscalServico.codigo as codigo_nfs',
			'Fornecedor.codigo as codigo_credenciado',
			'Fornecedor.nome as nome_credenciado',
			'Fornecedor.codigo_documento as cnpj_credenciado',
			'NotaFiscalServico.numero_nota_fiscal as numero_nfs',
			'NotaFiscalServico.data_emissao as data_emissao_nfs',
			'NotaFiscalServico.data_vencimento as data_vencimento_nfs',
			'NotaFiscalServico.data_recebimento as data_recebimento_nfs',
			'NotaFiscalServico.data_pagamento as data_pagamento_nfs',
			'NotaFiscalServico.valor as valor_nfs',
			'Tranpag.dtpagto as data_pago_tranpag',
			'Tranpag.valor as valor_tranpag',
		);

		//monta o join da query
		$joins = array(			
			array(
				'table' => 'RHHealth.dbo.fornecedores',
				'alias' => 'Fornecedor',
				'type' => 'INNER',
				'conditions' => 'Fornecedor.codigo = NotaFiscalServico.codigo_fornecedor',
			),
			array(
				'table' => $this->FornecNaveg->databaseTable . '.' . $this->FornecNaveg->tableSchema . '.' . $this->FornecNaveg->useTable,
				'alias' => 'FornecNaveg',
				'type' => 'LEFT',
				'conditions' => "FornecNaveg.cgc = RIGHT(replicate('0',15) + Fornecedor.codigo_documento,15)",
			),

			array(
				'table' => $this->Tranpag->databaseTable . '.' . $this->Tranpag->tableSchema . '.' . $this->Tranpag->useTable,
				'alias' => 'Tranpag',
				'type' => 'LEFT',
				'conditions' => "Tranpag.emitente = FornecNaveg.codigo AND Tranpag.numero = RIGHT(replicate('0',8) + NotaFiscalServico.numero_nota_fiscal,8) AND Tranpag.seq = '02'",
			),
		);

		//retorna o array para executar
		return array('fields' => $fields,'conditions' => $conditions,'joins' => $joins);

	}//fim getDadosNfs

	/**
	 * [get_detalhes_nfs description]
	 * 
	 * metodo para pegar os detalhes da nfs
	 * 
	 * @return [type] [description]
	 */
	public function get_detalhes_nfs($codigo_nfs)
	{

		//monta a conditions
		$conditions['NotaFiscalServico.codigo'] = $codigo_nfs;

		//campos da consulta
		$fields = array(
			'Cliente.codigo AS codigo_cliente',
			'Cliente.nome_fantasia AS nome_cliente',
			'Fornecedor.codigo AS codigo_fornecedor',
			'Fornecedor.nome AS nome_fornecedor',
			'Funcionario.nome AS nome_funcionario',
			'Funcionario.cpf AS cpf',
			'Exame.descricao AS exame',
			'ItemPedidoExameBaixa.data_realizacao_exame AS data_realizacao',
			'CAST(ItemPedidoExameBaixa.data_inclusao AS DATE) AS data_baixa',
			'ItemPedidoExame.valor_custo AS valor_custo',
			'Glosa.codigo AS codigo_glosa'
		);

		//monta o join da query
		$joins = array(			
			array(
				'table' => 'RHHealth.dbo.consolidado_nfs_exame',
				'alias' => 'ConsolidadoNfsExame',
				'type' => 'INNER',
				'conditions' => 'ConsolidadoNfsExame.codigo_nota_fiscal_servico = NotaFiscalServico.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo = ConsolidadoNfsExame.codigo_item_pedido_exame',
			),
			array(
				'table' => 'RHHealth.dbo.itens_pedidos_exames_baixa',
				'alias' => 'ItemPedidoExameBaixa',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo = ItemPedidoExameBaixa.codigo_itens_pedidos_exames',
			),
			array(
				'table' => 'RHHealth.dbo.pedidos_exames',
				'alias' => 'PedidoExame',
				'type' => 'INNER',
				'conditions' => 'PedidoExame.codigo = ItemPedidoExame.codigo_pedidos_exames',
			),
			array(
				'table' => 'RHHealth.dbo.exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'PedidoExame.codigo_cliente = Cliente.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.fornecedores',
				'alias' => 'Fornecedor',
				'type' => 'INNER',
				'conditions' => 'Fornecedor.codigo = NotaFiscalServico.codigo_fornecedor',
			),
			array(
				'table' => 'RHHealth.dbo.funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'PedidoExame.codigo_funcionario = Funcionario.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.glosas',
				'alias' => 'Glosa',
				'type' => 'INNER',
				'conditions' => 'Glosa.codigo_nota_fiscal_servico = NotaFiscalServico.codigo',
			),
		);

		//retorna os dados
		return $this->find('all', array('fields' => $fields,'conditions' => $conditions,'joins' => $joins));

	}//fim get_detalhes_nfs

	/**
	 * Validator para verificar duplicados 
	 *
	 * @param array $check
	 * @param integer $limite
	 * @return bool
	 */
	function notDups($check, $limite = 1)
	{		
		$conditions = array();
		$campo = key($check);
		$temValor = !empty($check[$campo]);
		
		$conditions[] = $check;

		if(isset($this->data['NotaFiscalServico']['codigo_fornecedor']) || !empty($this->data['NotaFiscalServico']['codigo_fornecedor']) || $this->data['NotaFiscalServico']['codigo_fornecedor'] != ' ') {
			$codigo_prestador['codigo_fornecedor'] = $this->data['NotaFiscalServico']['codigo_fornecedor'];
			$conditions[] = $codigo_prestador;
		}

		if($temValor){
			$conditions[] = array('ativo'=> 1); // se registro ativo considera duplicidade
		}
		
		$quantidade_existente = $this->find( 'count', array('conditions' => $conditions, 'recursive' => -1) );
		return $quantidade_existente  < $limite;
	}



	/**
	 * 
	 * Converte filtros usado para cadastro e atualização
	 * 
	*/
	function converteFiltroEmConditions($data, $model = 'NotaFiscalServico') {

		
	}
	
	/**
	 * Contrato com payload, a entidade é baseada na query usando a model como referencia
	 * 
	 * inicializa os campos que serão manipulados em crud
	 * 
	 */
	function newEmptyEntity($data = array())
	{
		$entinty = array();

		$entity['NotaFiscalServico'] = array(
			'codigo' => null,
			'codigo_fornecedor' => null,
			'numero_nota_fiscal' => null,
			'data_emissao' => null,
			'data_recebimento' => null,
			'valor' => null,
			'codigo_nota_fiscal_status' => 1, // inicializa pendente
			'numero_nota_fiscal' => null,
			'data_pagamento' => null,
			'codigo_empresa' => null,
			'ativo' => 1,
			'data_inclusao' => null,
			'data_alteracao' => null,
			'codigo_usuario_inclusao' => null,
			'codigo_usuario_alteracao' => null,
			'codigo_tipo_recebimento' => null,
			'codigo_formas_pagto' => null,
			'chave_rastreamento' => null,
			'quantos_dias' => null,

			'codigo_motivo_acrescimo' => null,
			'descricao_acrescimo' => null,
			'flag_acrescimo' => 0,

			'codigo_motivo_desconto'=> null,
			'descricao_desconto' => null,
			'flag_desconto' => 0,

			'baixa_boleto_descricao' => null,
			'baixa_boleto_data' => null,
			'codigo_tipo_servicos_nfs' => null,
			'auditoria_codigo_usuario_responsavel' => null,
			'liberacao_data' => null,
			'observacao' => null
		);

		$entity['Fornecedor'] = array(
			'razao_social' => null,
			'codigo_documento' => null,
			'nome' => null,
		);

		$entity['AnexoNotaFiscalServico'] = array(
			'codigo' => null,
			'codigo_nota_fiscal_servico' => null,
			'caminho_arquivo' => null, // deve ser tratado quando for fazer patch, pode ser string URL quando retornado do banco, ou binario caso venha de um upload
			'codigo_usuario_inclusao' => null,
			'data_inclusao' => null,
			'codigo_usuario_alteracao' => null,
			'data_alteracao' => null,
			'descricao' => null,
			'ativo' => null,
			'codigo_tipo_anexo_nota_fiscal_servico' => 1,
		);

		$entity['AnexoNFsBoleto'] = array(
			'codigo' => null,
			'codigo_nota_fiscal_servico' => null,
			'caminho_arquivo' => null, // deve ser tratado quando for fazer patch, pode ser string URL quando retornado do banco, ou binario caso venha de um upload
			'codigo_usuario_inclusao' => null,
			'data_inclusao' => null,
			'codigo_usuario_alteracao' => null,
			'data_alteracao' => null,
			'descricao' => null,
			'ativo' => null,
			'codigo_tipo_anexo_nota_fiscal_servico' => 2,
		);

		$entity['AnexoNFSEspelhoFaturamento'] = array(
			'codigo' => null,
			'codigo_nota_fiscal_servico' => null,
			'caminho_arquivo' => null, // deve ser tratado quando for fazer patch, pode ser string URL quando retornado do banco, ou binario caso venha de um upload
			'codigo_usuario_inclusao' => null,
			'data_inclusao' => null,
			'codigo_usuario_alteracao' => null,
			'data_alteracao' => null,
			'descricao' => null,
			'ativo' => null,
			'codigo_tipo_anexo_nota_fiscal_servico' => 3,
		);

		//ajuste para o chamado CDCT-183
		$entity['UsuarioAudResponsavel'] = array(
			'apelido' => null,
			'codigo' => null,
		);

		//ajuste PC-2644
		$entity['UsuarioConclusao'] = array(
			'apelido' => null,
			'codigo' => null,
		);

		return $entity;

	}




	/**
	 * Validação do contrato payload
	 *
	 * @param array|null $entity
	 * @param array|null $data
	 * @return array
	 */
	function patchEntity( $entity = null, $data = null )
	{
		// verifica se esta criando ou alterando um registro
		$edit_mode = (isset($data['NotaFiscalServico']['codigo']) && !empty($data['NotaFiscalServico']['codigo']));
		
		$entity['NotaFiscalServico'] = Comum::array_merge_preserve_keys($entity['NotaFiscalServico'], $data['NotaFiscalServico']);

		if(isset($data['Fornecedor'])){
			$entity['Fornecedor'] = Comum::array_merge_preserve_keys($entity['Fornecedor'], $data['Fornecedor']);
		}

		//ajuste para o chamado CDCT-183
		if(isset($data['UsuarioAudResponsavel'])){
			$entity['UsuarioAudResponsavel'] = Comum::array_merge_preserve_keys($entity['UsuarioAudResponsavel'], $data['UsuarioAudResponsavel']);
		}

		//ajuste PC-2644
		if(isset($data['UsuarioConclusao'])){
			$entity['UsuarioConclusao'] = Comum::array_merge_preserve_keys($entity['UsuarioConclusao'], $data['UsuarioConclusao']);
		}

		if(isset($data['AnexoNotaFiscalServico'])){
			
			// se for binario, pode ser um novo upload
			// if(isset($data['AnexoNotaFiscalServico']['caminho_arquivo_binario']['name']) 
			// 	&& isset($data['AnexoNotaFiscalServico']['caminho_arquivo_binario']['error'])
			// 	&& $data['AnexoNotaFiscalServico']['caminho_arquivo_binario']['error'] == 0)
			// {

			// }

			$entity['AnexoNotaFiscalServico'] = Comum::array_merge_preserve_keys($entity['AnexoNotaFiscalServico'], $data['AnexoNotaFiscalServico']);
		}

		if(isset($data['AnexoNFsBoleto'])){
			
			// se for binario, pode ser um novo upload
			// if(isset($data['AnexoNFsBoleto']['caminho_arquivo_binario']['name']) 
			// 	&& isset($data['AnexoNFsBoleto']['caminho_arquivo_binario']['error'])
			// 	&& $data['AnexoNFsBoleto']['caminho_arquivo_binario']['error'] == 0)
			// {

			// }

			$entity['AnexoNFsBoleto'] = Comum::array_merge_preserve_keys($entity['AnexoNFsBoleto'], $data['AnexoNFsBoleto']);
		}

		if(isset($data['AnexoNFSEspelhoFaturamento'])){
			
			// se for binario, pode ser um novo upload
			// if(isset($data['AnexoNFSEspelhoFaturamento']['caminho_arquivo_binario']['name']) 
			// 	&& isset($data['AnexoNFSEspelhoFaturamento']['caminho_arquivo_binario']['error'])
			// 	&& $data['AnexoNFSEspelhoFaturamento']['caminho_arquivo_binario']['error'] == 0)
			// {

			// }

			$entity['AnexoNFSEspelhoFaturamento'] = Comum::array_merge_preserve_keys($entity['AnexoNFSEspelhoFaturamento'], $data['AnexoNFSEspelhoFaturamento']);
		}

		// INCLUINDO :: apenas se incluindo novo registro
		if(!$edit_mode){
			$entity['NotaFiscalServico']['data_inclusao'] = Comum::now();
			$entity['NotaFiscalServico']['codigo_usuario_inclusao'] = Comum::codigoUsuarioAutenticado();
			//ajuste para o chamado CDCT-183
			$entity['NotaFiscalServico']['auditoria_codigo_usuario_responsavel'] = Comum::codigoUsuarioAutenticado();
		}
		
		// configura status sempre pendente quando for criar uma nota
		if (!$edit_mode && isset($data['NotaFiscalServico']['codigo_nota_fiscal_status']) && empty($data['NotaFiscalServico']['codigo_nota_fiscal_status'])) {
		 	$entity['NotaFiscalServico']['codigo_nota_fiscal_status'] = 1;
		}


		// ALTERANDO :: apenas se alterando registro
		if($edit_mode){
			$entity['NotaFiscalServico']['data_alteracao'] = Comum::now();
			$entity['NotaFiscalServico']['codigo_usuario_alteracao'] = Comum::codigoUsuarioAutenticado();
			//ajuste para o chamado CDCT-183
			$entity['NotaFiscalServico']['auditoria_codigo_usuario_responsavel'] = Comum::codigoUsuarioAutenticado();
		}

		// formata
		if (isset($data['Fornecedor']['codigo_documento']) && !empty($data['Fornecedor']['codigo_documento'])) {
		
			$codigo_documento = Comum::soNumero($data['Fornecedor']['codigo_documento']);
			$codigo_documento = Comum::formatarDocumento($codigo_documento);
			$entity['Fornecedor']['codigo_documento'] = $codigo_documento;
		}

		if (isset($data['NotaFiscalServico']['valor']) && !empty($data['NotaFiscalServico']['valor'])) {		
			
			$entity['NotaFiscalServico']['valor'] = ($data['NotaFiscalServico']['valor'] == null) ? 0 : (str_replace(',', '.',$data['NotaFiscalServico']['valor']));;
		}

		// if (isset($data['NotaFiscalServico']['anexo_nota_fiscal_servico']) && !empty($data['NotaFiscalServico']['anexo_nota_fiscal_servico'])) {

		// 	$arquivo = $data['NotaFiscalServico']['anexo_nota_fiscal_servico'];
		// 	$data['NotaFiscalServico']['anexo_nota_fiscal_servico'] = null;
		// 	unset($data['NotaFiscalServico']['anexo_nota_fiscal_servico']);
			
		// 	$entity['AnexoNotaFiscalServico']['caminho_arquivo'] = $arquivo;
		if(isset($entity['AnexoNotaFiscalServico']['caminho_arquivo']))
		{
			// se for binario, pode ser um novo upload
			if(isset($entity['AnexoNotaFiscalServico']['caminho_arquivo']['name']))
			{
				
			}

			// INCLUINDO :: apenas se incluindo novo registro
			if(!$edit_mode){
				$entity['AnexoNotaFiscalServico']['data_inclusao'] = Comum::now();
				$entity['AnexoNotaFiscalServico']['codigo_usuario_inclusao'] = Comum::codigoUsuarioAutenticado();
			}
			// ALTERANDO :: apenas se alterando registro
			if($edit_mode){
				$entity['AnexoNotaFiscalServico']['data_alteracao'] = Comum::now();
				$entity['AnexoNotaFiscalServico']['codigo_usuario_alteracao'] = Comum::codigoUsuarioAutenticado();
			}
		}

		return $entity;
	}


	function obterEntityPorCodigo($codigo)
	{
		
		$modelData = $this->obterPorCodigo($codigo);
		$entity = $this->newEmptyEntity();
		$entity = $this->patchEntity($entity, $modelData);
		
		return $entity;
	}


	function obterPorCodigo($codigo_nfs = null, $codigo_fornecedor = null)
	{
        //pega os dados da nota fiscal para edição
        $options = array();
        $options['joins'] = array(
            array('table' => 'fornecedores',
                'alias' => 'Fornecedor',
                'type' => 'LEFT',
                'conditions' => array(
                    'Fornecedor.codigo = NotaFiscalServico.codigo_fornecedor',
                )
			),
			array('table' => 'anexo_nota_fiscal_servico',
                'alias' => 'AnexoNotaFiscalServico',
                'type' => 'LEFT',
                'conditions' => array(
                    'AnexoNotaFiscalServico.codigo_nota_fiscal_servico = NotaFiscalServico.codigo 
					AND (AnexoNotaFiscalServico.codigo_tipo_anexo_nota_fiscal_servico is null
					OR AnexoNotaFiscalServico.codigo_tipo_anexo_nota_fiscal_servico = 1)',
                )
			),
			array('table' => 'anexo_nota_fiscal_servico',
				'alias' => 'AnexoNFsBoleto',
				'type' => 'LEFT',
				'conditions' => array(
					'AnexoNFsBoleto.codigo_nota_fiscal_servico = NotaFiscalServico.codigo
					AND AnexoNFsBoleto.codigo_tipo_anexo_nota_fiscal_servico = 2',
				)
			),
			array('table' => 'anexo_nota_fiscal_servico',
				'alias' => 'AnexoNFSEspelhoFaturamento',
				'type' => 'LEFT',
				'conditions' => array(
					'AnexoNFSEspelhoFaturamento.codigo_nota_fiscal_servico = NotaFiscalServico.codigo
					AND AnexoNFSEspelhoFaturamento.codigo_tipo_anexo_nota_fiscal_servico = 3',
				)
			),
			array('table' => 'usuario',
                'alias' => 'UsuarioAudResponsavel',
                'type' => 'LEFT',
                'conditions' => array(
                    'UsuarioAudResponsavel.codigo = NotaFiscalServico.auditoria_codigo_usuario_responsavel',
                )
			),
			array('table' => 'usuario',
			'alias' => 'UsuarioConclusao',
			'type' => 'LEFT',
			'conditions' => array(
				'UsuarioConclusao.codigo = NotaFiscalServico.codigo_usuario_conclusao',
			)
		)
        );
        $options['fields'] = array(
            'NotaFiscalServico.codigo',
            'NotaFiscalServico.codigo_fornecedor',
            'Fornecedor.razao_social',
            'Fornecedor.codigo_documento',
            'Fornecedor.nome',
            'NotaFiscalServico.data_emissao',
            'NotaFiscalServico.data_vencimento',
            'NotaFiscalServico.data_vencimento_prorrogado',
            'NotaFiscalServico.data_recebimento',
            'NotaFiscalServico.data_pagamento',
            'NotaFiscalServico.valor',
            'NotaFiscalServico.codigo_nota_fiscal_status',
            'NotaFiscalServico.numero_nota_fiscal',
            'NotaFiscalServico.codigo_empresa',
            'NotaFiscalServico.codigo_usuario_inclusao',
            'NotaFiscalServico.codigo_usuario_alteracao',
            'NotaFiscalServico.codigo_usuario_alteracao',
            'NotaFiscalServico.data_inclusao',
            'NotaFiscalServico.data_alteracao',
            'NotaFiscalServico.chave_rastreamento',
            'NotaFiscalServico.codigo_tipo_recebimento',
            'NotaFiscalServico.codigo_formas_pagto',
            'NotaFiscalServico.codigo_motivo_acrescimo',
            'NotaFiscalServico.quantos_dias',
            'NotaFiscalServico.descricao_acrescimo',
			'NotaFiscalServico.flag_acrescimo',
			'NotaFiscalServico.codigo_motivo_desconto',
			'NotaFiscalServico.descricao_desconto',
			'NotaFiscalServico.flag_desconto',
			'NotaFiscalServico.baixa_boleto_descricao',
			'NotaFiscalServico.baixa_boleto_data',
			'NotaFiscalServico.valor_acrescimo',
			'NotaFiscalServico.valor_desconto',
			'NotaFiscalServico.codigo_tipo_servicos_nfs',
			'NotaFiscalServico.auditoria_codigo_usuario_responsavel',
			'NotaFiscalServico.codigo_usuario_conclusao',
			'NotaFiscalServico.liberacao_data',
			'NotaFiscalServico.observacao',
			'NotaFiscalServico.data_conclusao',
			'AnexoNotaFiscalServico.caminho_arquivo',
			'AnexoNotaFiscalServico.descricao',
			'AnexoNotaFiscalServico.ativo',
			'AnexoNFsBoleto.caminho_arquivo',
			'AnexoNFsBoleto.descricao',
			'AnexoNFsBoleto.ativo',
			'AnexoNFSEspelhoFaturamento.caminho_arquivo',
			'AnexoNFSEspelhoFaturamento.descricao',
			'AnexoNFSEspelhoFaturamento.ativo',
			'UsuarioAudResponsavel.apelido',
			'UsuarioAudResponsavel.codigo',
			'UsuarioConclusao.apelido',
			'UsuarioConclusao.codigo'
		);
		
		if(!empty($codigo_nfs)){
			$options['conditions'] = array('NotaFiscalServico.codigo' => $codigo_nfs);
		}
			


		if(!empty($codigo_fornecedor)){
			$options['conditions'] = array('NotaFiscalServico.codigo_fornecedor' => $codigo_fornecedor);
		}
		
		$dados = $this->find('first', $options);
		return $dados;
	}

	// TODO: por alguma razao isso parou de funcionar na controller e model
	// return $this->Usuario->find('first', array('fields' => array('apelido'),'conditions' => array('Usuario.codigo' => $codigo_usuario)));
	// return $this->Usuario->findByCodigo($codigo_usuario);
	public function obterNomeUsuario($codigo_usuario = null){
		
		$usuarioData = $this->query("select nome, apelido from usuario where codigo = '$codigo_usuario';");
		
		return isset($usuarioData[0][0]['nome']) && !empty($usuarioData[0][0]['nome']) ? $usuarioData[0][0]['nome'] : $usuarioData[0][0]['apelido'];
		
	}

	public function lista_glosas($codigo_fornecedor = null, $codigo_nota = null){
		$fields = array('Glosas.codigo',
            'Glosas.codigo_pedidos_exames',
            'Exame.descricao',
            'Glosas.valor',
            'Glosas.data_glosa',
            'Glosas.data_pagamento',
            'Glosas.codigo_status_glosa',
            'GlosasStatus.descricao',
            'Glosas.data_vencimento',
            'Glosas.motivo_glosa',
            'Glosas.ativo'
        );

        $joins = array(
            array(
                "table"      => "RHHealth.dbo.glosas_status",
                "alias"      => "GlosasStatus",
                "type"       => "LEFT",
                "conditions" => "Glosas.codigo_status_glosa = GlosasStatus.codigo and GlosasStatus.ativo = 1"
            ),
            array(
                "table"      => "RHHealth.dbo.itens_pedidos_exames",
                "alias"      => "ItemPedidoExame",
                "type"       => "LEFT",
                "conditions" => "ItemPedidoExame.codigo = Glosas.codigo_itens_pedidos_exames"
            ),
            array(
                "table"      => "RHHealth.dbo.exames",
                "alias"      => "Exame",
                "type"       => "LEFT",
                "conditions" => "Exame.codigo = ItemPedidoExame.codigo_exame"
            ),
        );

        $conditions = array();        
		if (!empty($codigo_fornecedor)) {
			$conditions['Glosas.codigo_fornecedor'] = $codigo_fornecedor;
		}

		if (!empty($codigo_nota)) {
			$conditions['Glosas.codigo_nota_fiscal_servico'] = $codigo_nota;
		}

        $order = 'Glosas.codigo';

        $dados = array(
            'conditions' => $conditions,
            'joins' => $joins,
            'fields' => $fields,
            'order' => $order       
        );

		return $dados;
	}

	public function notas_por_fornecedor($codigo_fornecedor)
	{
		$fields = array(
			'NotaFiscalServico.codigo',
			'NotaFiscalServico.numero_nota_fiscal',

        );	
		$conditions = array();
		$conditions['NotaFiscalServico.codigo_fornecedor'] = $codigo_fornecedor;
		$conditions['NotaFiscalServico.codigo_nota_fiscal_status'] = array(1,2);
		$conditions['NotaFiscalServico.ativo'] = 1;


		$dados = array(
            'conditions' => $conditions,
            'fields' => $fields,
        );

		return $dados;

	}

	//função criada para não quebrar a listagem do relatório de glosas
	public function lista_glosas_export($codigo_fornecedor = null, $codigo_nota = null){
		$fields = array(
			'Glosas.codigo',
            'Glosas.codigo_pedidos_exames',
            'Exame.descricao',
            'Glosas.valor',
            'Glosas.data_glosa',
            'Glosas.data_pagamento',
            'Glosas.codigo_status_glosa',
            'GlosasStatus.descricao',
            'Glosas.data_vencimento',
            'Glosas.motivo_glosa',
            'Glosas.ativo',
			'PedidoExame.codigo',
			'Funcionario.nome',
			'Cliente.codigo',
			'Cliente.razao_social',
			'TipoGlosa.descricao',
			'ClassificacaoGlosa.descricao',
			'ConsolidadoNfsExame.valor',
			'ConsolidadoNfsExame.valor_corrigido'
        );

        $joins = array(
            array(
                "table"      => "RHHealth.dbo.glosas_status",
                "alias"      => "GlosasStatus",
                "type"       => "LEFT",
                "conditions" => "Glosas.codigo_status_glosa = GlosasStatus.codigo and GlosasStatus.ativo = 1"
            ),
			array(
                "table"      => "RHHealth.dbo.itens_pedidos_exames",
                "alias"      => "ItemPedidoExame",
                "type"       => "LEFT",
                "conditions" => "ItemPedidoExame.codigo = Glosas.codigo_itens_pedidos_exames"
            ),
            array(
                "table"      => "RHHealth.dbo.pedidos_exames",
                "alias"      => "PedidoExame",
                "type"       => "LEFT",
                "conditions" => "ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo"
            ),			
            array(
                "table"      => "RHHealth.dbo.exames",
                "alias"      => "Exame",
                "type"       => "LEFT",
                "conditions" => "Exame.codigo = ItemPedidoExame.codigo_exame"
            ),
			array(
				'table' 	 => 'RHHealth.dbo.funcionarios',
				'alias' 	 => 'Funcionario',
				'type' 		 => 'LEFT',
				'conditions' => 'PedidoExame.codigo_funcionario = Funcionario.codigo',
			),
			array(
				'table' 	 => 'RHHealth.dbo.cliente',
				'alias' 	 => 'Cliente',
				'type' 		 => 'LEFT',
				'conditions' => 'Cliente.codigo = PedidoExame.codigo_cliente',
			),
			array(
                "table"      => "RHHealth.dbo.tipo_glosas",
                "alias"      => "TipoGlosa",
                "type"       => "LEFT",
                "conditions" => "Glosas.codigo_tipo_glosa = TipoGlosa.codigo"
            ),
			array(
				"table"      => "RHHealth.dbo.classificacao_glosa",
                "alias"      => "ClassificacaoGlosa",
                "type"       => "LEFT",
                "conditions" => "Glosas.codigo_classificacao_glosa = ClassificacaoGlosa.codigo"
			),
			array(
				"table"      => "RHHealth.dbo.consolidado_nfs_exame",
                "alias"      => "ConsolidadoNfsExame",
                "type"       => "LEFT",
                "conditions" => "ItemPedidoExame.codigo = ConsolidadoNfsExame.codigo_item_pedido_exame"
			)
        );

		$conditions = array();    

		$conditions['Glosas.ativo'] = 1;    
		
		if (!empty($codigo_fornecedor)) {
			$conditions['Glosas.codigo_fornecedor'] = $codigo_fornecedor;
		}

		if (!empty($codigo_nota)) {
			$conditions['Glosas.codigo_nota_fiscal_servico'] = $codigo_nota;
		}

        $order = 'Glosas.codigo';

        $dados = array(
            'conditions' => $conditions,
            'joins' => $joins,
            'fields' => $fields,
            'order' => $order       
        );

		return $dados;
	}
}