<?php
class MetaCentroCusto extends AppModel {
    var $name = 'MetaCentroCusto';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'metas_centro_custo';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

	var $validate = array(
		'centro_custo' => array(
			'notEmpty' => array(				
				'rule' => 'notEmpty',
				'message' => 'Informe o Centro de Custo',
			 ),
		),
		'codigo_fluxo' => array(
			'impedeDuplicidadeMeta' => array(
				'rule' => 'impedeDuplicidadeMeta',
				'message' => 'Meta já cadastrada no sistema com esses dados',
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Fluxo',
			 ),			
		),
		'codigo_sub_fluxo' => array(
			'verificaRegraInclusaoFluxoSubFluxo' => array(
				'rule' => 'verificaRegraInclusaoFluxoSubFluxo',
				'message' => 'Meta já cadastrada para esse Fluxo/Subfluxo',
			)
		),
		'ano' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Ano',
		),
		'mes' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Mês',
		),
		'valor_meta' => array(
			'rule' => 'valida_valor_maior_que_zero',
			'message' => 'Valor não informado',
			'required' => true,
			'allowEmpty' => false,
		),	
		'empresa' => array(
			'notEmpty'	 => array(
				'on' => array('create','update'),
				'rule' => 'notEmpty',
				'message' => 'Informe a Empresa',
			)
		),
		'grupo_empresa' => array(
			'notEmpty'	 => array(
				'on' => array('create','update'),
				'rule' => 'notEmpty',
				'message' => 'Informe o Grupo da Empresa',
			)
		)
	);

    function valida_valor_maior_que_zero( $check ){
        return($this->ajustaFormatacao( $check['valor_meta'])>0 );
    }

    function ajustaFormatacao($valor){
        if (strpos($valor, '.')>0 && strpos($valor, ',')>0) {
            $valor = str_replace('.', '', $valor);
        }
        return str_replace(',', '.', $valor);
    }

    public function converteFiltroEmCondition( $filtros ) {
    	$conditions = array();
    	if( !empty($filtros['ano']) && !empty($filtros['mes']) )
    		$conditions['MetaCentroCusto.ano_mes'] = $filtros['ano'].str_pad($filtros['mes'], 2,'0',STR_PAD_LEFT);
		if( !empty($filtros['ano']) && empty($filtros['mes']) )
    		$conditions['MetaCentroCusto.ano_mes like '] = $filtros['ano'].'%';
		if( empty($filtros['ano']) && !empty($filtros['mes']) )
    		$conditions['MetaCentroCusto.ano_mes like '] = '%' .str_pad($filtros['mes'], 2,'0',STR_PAD_LEFT);
		if( !empty($filtros['centro_custo']))
			$conditions['MetaCentroCusto.centro_custo'] = $filtros['centro_custo'];
		if( !empty($filtros['codigo_fluxo']))
			$conditions['MetaCentroCusto.codigo_fluxo'] = $filtros['codigo_fluxo'];
		if( !empty($filtros['codigo_sub_fluxo']))
			$conditions['MetaCentroCusto.codigo_sub_fluxo'] = $filtros['codigo_sub_fluxo'];		
		if( !empty($filtros['empresa']))
			$conditions['MetaCentroCusto.empresa'] = $filtros['empresa'];		
		if( !empty($filtros['grupo_empresa']))
			$conditions['MetaCentroCusto.grupo_empresa'] = $filtros['grupo_empresa'];

    	return $conditions;
    }

    public function incluir( $data ){
    	$data[$this->name]['ano_mes'] = $data[$this->name]['ano'].str_pad($data[$this->name]['mes'], 2,'0',STR_PAD_LEFT);
    	if( isset($data['MetaCentroCusto']['quantidade_repetir_meta']) && $data['MetaCentroCusto']['quantidade_repetir_meta'] >= 1 ){
    		$this->query('begin transaction');
    		$ano_mes = $data[$this->name]['ano_mes'].'01';
    		for( $i=0; $i <= $data['MetaCentroCusto']['quantidade_repetir_meta']; $i++ ){
    			$data[$this->name]['ano_mes'] = date( "Ym", strtotime( "$ano_mes +$i month" ) );
    			$this->data = $data;
    			if( !parent::incluir( $this->data ) ){
    				if( !empty($this->validationErrors['codigo_sub_fluxo']))
    					$this->invalidate('codigo_sub_fluxo', $this->validationErrors['codigo_sub_fluxo']. ' no mês '. substr( $this->data['MetaCentroCusto']['ano_mes'], 4, 2 ).'/'.substr( $this->data['MetaCentroCusto']['ano_mes'], 0, 4 ) );
    				$this->rollback();
					return false;
    			}
    		}
    		$this->commit();
    		return true;
    	} else {
			return parent::incluir( $data );
    	}
    }

    public function atualizar( $data ) {
		$data[$this->name]['ano_mes'] = $data[$this->name]['ano'].str_pad($data[$this->name]['mes'], 2,'0',STR_PAD_LEFT);
		return parent::atualizar( $data );
    }

    public function verificaRegraInclusaoFluxoSubFluxo(  ){
		$codigo_fluxo     = $this->data[$this->name]['codigo_fluxo'];
		$codigo_sub_fluxo = $this->data[$this->name]['codigo_sub_fluxo'];
		$centro_custo     = $this->data[$this->name]['centro_custo'];
		$empresa          = $this->data[$this->name]['empresa'];
		$ano_mes          = $this->data[$this->name]['ano_mes'];
		$codigo 		  = isset($this->data[$this->name]['codigo']) ? $this->data[$this->name]['codigo'] : NULL;
		if( !$codigo_sub_fluxo ){
			$conditions = array(
				'centro_custo'        => $centro_custo, 
				'codigo_fluxo'        => $codigo_fluxo, 
				'codigo_sub_fluxo <>' => '', 
				'ano_mes'             => $ano_mes, 
				'empresa'             => $empresa
			);
			if( $codigo ){
				array_push($conditions, array( 'codigo <>' => $codigo));			
			}
			$meta_nivel_sub_fluxo = $this->find('count', compact('conditions'));
			if( $meta_nivel_sub_fluxo > 0)
				return false;
		} else {
			$meta_nivel_fluxo = $this->find('count', array(
				'conditions'=>array( 
					'centro_custo' 		=> $centro_custo, 
					'codigo_fluxo' 		=> $codigo_fluxo, 
					'codigo_sub_fluxo' 	=> '', 
					'ano_mes' 			=> $ano_mes,
					'empresa'           => $empresa
				)
			));
			if( $meta_nivel_fluxo > 0)
				return false;
		}
		return true;
    }

    public function impedeDuplicidadeMeta(  ){
		$codigo_fluxo     = $this->data[$this->name]['codigo_fluxo'];
		$codigo_sub_fluxo = $this->data[$this->name]['codigo_sub_fluxo'];
		$centro_custo     = $this->data[$this->name]['centro_custo'];
		$ano_mes 		  = $this->data[$this->name]['ano_mes'];
		$empresa 		  = $this->data[$this->name]['empresa'];
		$codigo 		  = isset($this->data[$this->name]['codigo']) ? $this->data[$this->name]['codigo'] : NULL;
		$conditions = array(
							'centro_custo' => $centro_custo, 
							'codigo_fluxo' => $codigo_fluxo, 
							'ano_mes'      => $ano_mes, 
							'empresa'      => $empresa
						);
		if( $codigo )
			array_push($conditions, array( 'codigo <>' => $codigo));		
		if( $codigo_sub_fluxo)
			array_push($conditions, array( 'codigo_sub_fluxo' => $codigo_sub_fluxo));
		else
			array_push($conditions, array( 'codigo_sub_fluxo' => NULL ));
		$meta = $this->find('count', compact('conditions'));
		if( $meta )
			return false;
		return true;
    }
}
?>