<?php
class Medico extends AppModel {

	public $name 			= 'Medico';
	public $tableSchema 	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable 		= 'medicos';
	public $primaryKey 		= 'codigo';
	public $actsAs          = array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_medico'));
	public $displayField 	= 'nome';
	//public $recursive = -1;

	/**
	 * @see http://book.cakephp.org/2.0/en/core-libraries/components/pagination.html#custom-query-pagination
	 */
	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) 
	{
		/*if (!empty($fields)) {
			foreach ($fields as $field) {
				if (stripos($field, 'DISTINCT') !== false) {
					$this->__paginateDistinct = $field;

					break;
				}
			}
		}*/

		if (isset($extra['contain'])) {
			$contain = $extra['contain'];
		}

		if (isset($extra['joins'])) {
			$joins = $extra['joins'];
		}

		$group = array();
		if(isset($extra['groupBy'])) {
			$group = $extra['groupBy'];
		}

		// debug(array($conditions, $fields, $order, $limit, $page, $recursive, $extra,$group));
		// debug($this->find('sql', compact('conditions', 'fields', 'joins', 'order', 'limit', 'page', 'recursive', 'group', 'contain')));

		return $this->find('all', compact('conditions', 'fields', 'joins', 'order', 'limit', 'page', 'recursive', 'group', 'contain'));
	}

	/**
	 * @see http://book.cakephp.org/2.0/en/core-libraries/components/pagination.html#custom-query-pagination
	 */
	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) 
	{
		$params = compact('conditions', 'recursive');

		if (isset($this->__paginateDistinct)) {
			$params['fields'] = $this->__paginateDistinct;
		}

		return $this->find('count', array_merge($params, $extra));
	}

	public $hasMany = array(
		'FichaClinica' => array(
			'className'    => 'FichaClinica',
			'foreignKey'    => 'codigo_medico'
			),
		'FichaAssistencial' => array(
			'className'    => 'FichaAssistencial',
			'foreignKey'    => 'codigo_medico'
			)
		);

	public $hasAndBelongsToMany = array(
		'Fornecedor' => array(
			'className'    				=> 'Fornecedor',
			'joinTable'    				=> 'fornecedores_medicos',
			'foreignKey'             	=> 'codigo_medico',
			'associationForeignKey'  	=> 'codigo_fornecedor',
		),
	);

	public $belongsTo = array(
		'ConselhoProfissional' => array(
			'className' => 'ConselhoProfissional',
			'foreignKey' => 'codigo_conselho_profissional'
		),
	);

	public $validate = array(
		'nome' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Nome!'
		),
		'numero_conselho' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Número do Conselho!'
			),
			'UnicaPorMedico' => array(
				'rule' => 'validaConselhoMedico',
				'message' => 'Número do Conselho Existente',
			)
		),
		'conselho_uf' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a UF do Conselho!'
		),
		'codigo_conselho_profissional' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Conselho!'
		)

	);

	function validaFormato(){
		if(strlen($this->data['Medico']['numero_conselho']) < 4){
			return false;
		}
		else if(strlen($this->data['Medico']['numero_conselho']) > 10){
			return false;
		}
		else{
			return true;
		}
	}//FINAL FUNCTION validaFormato

	function validaConselhoMedico(){

		$conditions = array(
			'numero_conselho' => $this->data['Medico']['numero_conselho'], 
			'conselho_uf' => $this->data['Medico']['conselho_uf'],
			'codigo_conselho_profissional' => $this->data['Medico']['codigo_conselho_profissional'],
			//conditions ativo colocado para atender o chamado CDCT-192, que mostrou o caso de existe o numero conselho, o conselho_uf e o codigo_conselho_profissional, mas nao verificou se era ativo.
			'ativo' => 1
		);

		$dados = $this->find('first', array('recursive' => -1, 'conditions' =>  $conditions));

		// $this->log($dados,'debug');

		if(empty($dados)){
			return true;
		}
		else{
			if(!empty($this->data['Medico']['codigo'])){
				if($dados['Medico']['codigo'] != $this->data['Medico']['codigo']){
					return false;
				}
				else{
					return true;
				}
			}
			else{
				return false;
			}
		}
	}//FINAL FUNCTION validaConselhoMedico

	function converteFiltroEmCondition($data) {
		$conditions = array();
		if (!empty($data['codigo']))
			$conditions['Medico.codigo'] = $data['codigo'];

		if (! empty ( $data ['nome'] ))
			$conditions ['Medico.nome LIKE'] = '%' . $data ['nome'] . '%';

		if (!empty($data['codigo_conselho_profissional'])){
			if ($data ['codigo_conselho_profissional'] === '1')
				$conditions[] = '(Medico.codigo_conselho_profissional ='.$data['codigo_conselho_profissional'].' OR Medico.codigo_conselho_profissional IS NULL)';
			else
				$conditions['Medico.codigo_conselho_profissional'] = $data['codigo_conselho_profissional'];
		}

		if (!empty($data['numero_conselho']))
			$conditions['Medico.numero_conselho'] = $data['numero_conselho'];

		if (!empty($data['conselho_uf']))
			$conditions['Medico.conselho_uf'] = $data['conselho_uf'];

		if (isset( $data['ativo'] ) && $data['ativo'] != "") 
			$conditions ['Medico.ativo'] = $data['ativo'];
			
		if (isset( $data['assinatura_eletronica'] ) && $data['assinatura_eletronica'] != ""){
			if($data['assinatura_eletronica'] == 1){
				$conditions[] = 'AnexoAssinaturaEletronica.caminho_arquivo IS NOT NULL';
			} else {
				$conditions[] = 'AnexoAssinaturaEletronica.caminho_arquivo IS NULL';
			}
		}

		return $conditions;
	}//FINAL FUNCTION converteFiltroEmCondition  

	function incluir($dados) {
		$model_MedicoEndereco = & ClassRegistry::init('MedicoEndereco');

		try {
			$this->query('begin transaction');

			if(parent::incluir($dados)) {

				if(isset($dados['MedicoEndereco']) && !empty($dados['MedicoEndereco']['cep'])) {
					$dados['MedicoEndereco']['codigo_medico'] = $this->id;
					if(!$model_MedicoEndereco->incluir($dados['MedicoEndereco'])) {
						return false;
					} else {
						$this->commit();
						return true;
					}   					
				} else {
					$this->commit();
					return true;				
				}

			} else {
				return false;
			}
		} catch (Exception $ex) {
			$this->rollback();
			return false;
		}
	}//FINAL FUNCTION incluir

	function atualizar($dados) {

		$model_MedicoEndereco = & ClassRegistry::init('MedicoEndereco');

		if(empty($dados['MedicoEndereco']['cep'])){//ajuste para o cdct-244 por que estava incluindo enderecos vazios e isso estava impactando nos relatorios asos.
			return true;
		}

		try {
			$this->query('begin transaction');
			if(parent::atualizar($dados)) {

				$dados['MedicoEndereco']['codigo_medico'] = $dados['Medico']['codigo'];
				unset($dados['Medico']);

   				// se nao tiver endereco ( INCLUI )
				if(isset($dados['MedicoEndereco']['codigo']) && $dados['MedicoEndereco']['codigo']) {

					if(!$model_MedicoEndereco->atualizar($dados)) {
						return false;
					} else {
						$this->commit();
						return true;
					}
				} else {

   					// retira campo
					unset($dados['MedicoEndereco']['codigo']);

   					// inclui
					if(!$model_MedicoEndereco->incluir($dados)) {
						return false;
					} else {
						$this->commit();
						return true;
					}   					
				}
			} else {
				return false;
			}
		} catch (Exception $ex) {
			$this->rollback();
			return false;
		}
	}//FINAL FUNCTION atualizar

	public function lista_somente_engenhgeiros_por_cliente($codigo_cliente)
	{
		$this->virtualFields = array(
			'nome' => 'CONCAT(Medico.nome, \' - \', ConselhoProfissional.descricao, \': \', Medico.numero_conselho)'
		);

		$profissionais = $this->find('list', array(
			'joins' => array(
				array(
					'table' => 'conselho_profissional',
					'alias' => 'ConselhoProfissional',
					'type' => 'INNER',
					'conditions' => array(
						'ConselhoProfissional.codigo = Medico.codigo_conselho_profissional AND
						(ConselhoProfissional.descricao LIKE \'crea\' OR ConselhoProfissional.descricao LIKE \'mte\')'
					)		
				)
			),
			'conditions' => array('Medico.ativo' => 1)
		)
	);
		return $profissionais;
	}//FINAL FUNCTION Medico

	public function getMedicosFromVersoesPCMSO(){

		$fields = "{$this->name}.codigo, {$this->name}.nome";

		$joins = array(
			array('table'        => 'pcmso_versoes',
				'alias'        => 'PCMSOVersoes',
				'type'         => 'INNER',
				'conditions'   => "PCMSOVersoes.codigo_medico = {$this->name}.codigo"),
		);

		$group = array("{$this->name}.codigo","{$this->name}.nome");

		return $this->find('list', array('fields'    => $fields, 
										'joins'     => $joins, 
										'group'     => $group)
							);
    }//FINAL FUNCTION getMedicosFromVersoesPCMSO    

    public function getMedicosFromVersoesPpra(){

		$fields = "{$this->name}.codigo, {$this->name}.nome";

		$joins = array(
			array('table'      => 'ppra_versoes',
				'alias'        => 'PpraVersoes',
				'type'         => 'INNER',
				'conditions'   => "PpraVersoes.codigo_medico = {$this->name}.codigo"),
		);

		$group = array("{$this->name}.codigo","{$this->name}.nome");

		return $this->find('list', array('fields'    => $fields, 'joins'     => $joins, 'group'     => $group));

    }//FINAL FUNCTION getMedicosFromVersoesPpra
	
	
	// medicos (Profissional representante legal da empresa)
	/**
	 * obter medico (Profissional representante legal da empresa)
	 *
	 * @param integer $codigo_cliente
	 * @return array|null
	 */
	public function obterMedicoRepresentanteLegal($codigo_cliente = null){
		
		$medicos_args = array(
			'joins'=>array(
				array(
					'table' => "cliente",
					'alias' => 'Clientes',
					'conditions' => "{$this->name}.codigo = Clientes.codigo_medico_pcmso",
					'type' => 'INNER',
				)
			),
			'conditions'=>array(
				'Clientes.codigo' => $codigo_cliente,
				"Clientes.codigo_medico_responsavel IS NOT NULL"
			)
		);

		return $this->find('first', $medicos_args);
	}

    public function converte_filtro_em_conditions_listagem_corpo_clinico(array $data = array()){
        $conditions = array();

        if(isset($data['codigo_cliente']) && $data['codigo_cliente'] != '')
            $conditions["Cliente.codigo"] = $data['codigo_cliente'];
        if(isset($data['codigo_cliente_alocacao']) && $data['codigo_cliente_alocacao'] != '')
            $conditions['Cliente.codigo'] = $data['codigo_cliente_alocacao'];
        if(isset($data['codigo_fornecedor']) && $data['codigo_fornecedor'] != '')
            $conditions['Fornecedor.codigo'] = $data['codigo_fornecedor'];

        return $conditions;
    }

    public function get_parametros_para_consulta_corpo_clinico(array $data = array())
    {

    	$this->FornecedorMedico =& ClassRegistry::init('FornecedorMedico');

	    $fields = array(
            'Medico.nome as medico',
            'Medico.conselho_uf as conselho_uf',
            'Medico.numero_conselho as conselho_numero',
            // 'MIN(FornecedorMedico.data_inclusao) as incluido',
            'FornecedorMedico.data_inclusao as incluido',
            // 'Fornecedor.codigo AS codigo_fornecedor',
            // 'Fornecedor.nome AS nome_fornecedor',
            'ConselhoProfissional.descricao as conselho_profissional',
		);
		
	    $group = array(
            'Medico.nome',
            'Medico.conselho_uf',
            'Medico.numero_conselho',
            'FornecedorMedico.data_inclusao',
            'ConselhoProfissional.descricao',
		);
		
        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.medicos',
                'alias' => 'Medico',
                'type' => 'INNER',
                'conditions' => 'FornecedorMedico.codigo_medico = Medico.codigo AND Medico.ativo = 1',
            ),
            array(
                'table' => 'Rhhealth.dbo.fornecedores',
                'alias' => 'Fornecedor',
                'type' => 'INNER',
                'conditions' => 'FornecedorMedico.codigo_fornecedor = Fornecedor.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.clientes_fornecedores',
                'alias' => 'ClienteFornecedor',
                'type' => 'INNER',
                'conditions' => 'ClienteFornecedor.codigo_fornecedor = Fornecedor.codigo AND ClienteFornecedor.ativo = 1',
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'ClienteFornecedor.codigo_cliente = Cliente.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.conselho_profissional',
                'alias' => 'ConselhoProfissional',
                'type' => 'INNER',
                'conditions' => 'Medico.codigo_conselho_profissional = ConselhoProfissional.codigo',
            ),
        );
        $conditions = self::converte_filtro_em_conditions_listagem_corpo_clinico($data);
        $conditions[] = "Medico.codigo_conselho_profissional = 1";
        $conditions[] = "FornecedorMedico.codigo = (SELECT TOP 1 codigo
								FROM fornecedores_medicos
								WHERE codigo_medico = Medico.codigo
								ORDER BY codigo ASC)";
        $limit = 50;
        $order = 'Medico.conselho_uf ASC, Medico.nome ASC';
		$recursive = -1;
		
        return compact('fields','joins', 'conditions', 'limit', 'order', 'group', 'recursive');
    }

}//FINAL CLASS Medico
