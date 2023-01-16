<?php
class PosConfiguracoes extends AppModel {

	public $name		   	= 'PosConfiguracoes';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'pos_configuracoes';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Loggable' => array('foreign_key' => 'codigo_pos_configuracao'), 'Containable');

	 public $validate = array(
	 	'descricao' => array(
	 		'notEmpty' => array(
	 			'rule' => 'notEmpty',
	 			'message' => 'Informe a descrição',
	 			'required' => true
	 		 )
	 	)
	 );
	

    public function converteFiltrosEmConditions( $filtros = array() ){

        $conditions = array();
        
        if(isset($filtros['codigo_cliente']) && Comum::validarCodigoClean($filtros['codigo_cliente']))
            $conditions['Cliente.codigo'] = Comum::clean($filtros['codigo_cliente']);
        
        if(isset($filtros['codigo_pos_ferramenta']) && Comum::validarCodigoClean($filtros['codigo_pos_ferramenta']))
            $conditions['PosConfiguracoes.codigo_pos_ferramenta'] = Comum::clean($filtros['codigo_pos_ferramenta']);

        if(isset($filtros['descricao']) && Comum::validarTextoClean($filtros['descricao']))
            $conditions['PosConfiguracoes.descricao LIKE'] = '%' . Comum::clean($filtros['descricao']) . '%';

        if(isset($filtros['codigo_documento_cliente']) && Comum::validarCodigoClean($filtros['codigo_documento_cliente']))
            $conditions['Cliente.codigo_documento'] = $filtros['codigo_documento_cliente'];

        if(isset($filtros['razao_social_cliente']) && Comum::validarTextoClean($filtros['razao_social_cliente']))
            $conditions['Cliente.razao_social LIKE'] = '%' . Comum::clean($filtros['razao_social_cliente']) . '%';
        
        if(isset($filtros['nome_fantasia_cliente']) && Comum::validarTextoClean($filtros['nome_fantasia_cliente']))
            $conditions['Cliente.nome_fantasia LIKE'] = '%' . Comum::clean($filtros['nome_fantasia_cliente']) . '%';    
            
		if (isset($filtros['ativo']) && Comum::validarBoolClean($filtros['ativo']))
			$conditions['PosConfiguracoes.ativo'] = Comum::clean($filtros['ativo']);

        // TODO:pendente de definição
        // [codigo_opco]
        // [codigo_business_unit]
        // [codigo_depth_structure]

        return $conditions;
    }

	public function obterListagem( $filtros = array() ){
	
		$conditions = $this->converteFiltrosEmConditions($filtros);

		$fields = array(
			'Cliente.codigo as codigo_cliente',
			'Cliente.razao_social as razao_social',
			'Cliente.nome_fantasia as nome_fantasia' ,
			'Cliente.codigo_documento as documento',
			'PosConfiguracoes.codigo as codigo_pos_configuracao',
			'PosConfiguracoes.codigo_pos_ferramenta as codigo_pos_ferramenta',
			'PosConfiguracoes.descricao as configuracao_descricao',
			'PosConfiguracoes.valor as configuracao_valor',
			'PosConfiguracoes.chave as configuracao_chave',
			'PosConfiguracoes.observacao as configuracao_observacao',
			'PosConfiguracoes.ativo as configuracao_ativo',
			'PosFerramenta.descricao as ferramenta_descricao',
		);

		$joins = array(
			array(
				'table' => 'grupos_economicos',
				'alias' => 'GrupoEconomico',
				'type' => 'INNER',
				'conditions' => array('GrupoEconomico.codigo_cliente = Cliente.codigo')
			),
			array(
				'table' => 'pos_configuracoes',
				'alias' => 'PosConfiguracoes',
				'type' => 'INNER',
				'conditions' => array('PosConfiguracoes.codigo_cliente = Cliente.codigo')   
			),
			array(
				'table' => 'pos_ferramenta',
				'alias' => 'PosFerramenta',
				'type' => 'LEFT',
				'conditions' => array('PosConfiguracoes.codigo_pos_ferramenta = PosFerramenta.codigo')   
			)
		);

		return array(
			'fields' => $fields,
			'joins'=> $joins,
			'conditions' => $conditions,
			'limit' => 50,
			'order' => "Cliente.nome_fantasia"
		);	
	}

}
