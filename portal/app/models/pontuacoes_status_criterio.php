<?php
class PontuacoesStatusCriterio extends AppModel {
	var $name = 'PontuacoesStatusCriterio';
	var $primaryKey = 'codigo';
	var $displayField = 'pontos';
	var $databaseTable = 'dbTeleconsult';
	var $tableSchema = 'informacoes';
	var $useTable = 'pontuacoes_status_criterios';
	var $actsAs = array('Secure');
	var $validate = array(
		    
		    'codigo_status_criterio' => array(
			    'rule' => 'valida_pontuacoes_status_criterio',
		        'message' => 'Status já existe ou não Selecionado '		   		
		    ),		    
		   'codigo_criterio'=> array(
			    'rule' => 'notEmpty',
		        'message' => 'Prencher Critério'
		    	
		    ),		    
		    'pontos'=> array(
			    'rule' => 'valida_pontos',
		        'message' => 'Prencher Pontos',
		    	//'on' => 'create'
		    ),
			'insuficiente'=> array(
			    'rule' => 'notEmpty',
		        'message' => 'O campo insuficiente é obrigatório'
		    ),
			'qtd_ate' =>array(	
				'rule' => 'valida_qtd_ate',
                'message' => 'Qtd deve ser maior que zero',	 
			)
		);

	function valida_qtd_ate($check) {
		
		$this->Criterio = ClassRegistry::init('Criterio');
		$conditions = array('codigo'=>$this->data['PontuacoesStatusCriterio']['codigo_criterio']);
		$fields =array('controla_qtd','aceita_texto');
		$lista = $this->Criterio->find('all', compact('conditions','fields'));
		$verifica =NULL;
		foreach ($lista as $verifica) 	
		
		if($verifica['Criterio']['controla_qtd']==1){
			if ($this->data['PontuacoesStatusCriterio']['qtd_ate'] == 0) {
				return false;
			}elseif($this->data['PontuacoesStatusCriterio']['qtd_ate'] != 0 ){ 
				return true;
			} 
		} 
		 
		
		if($verifica['Criterio']['controla_qtd']==0){
			$this->data['PontuacoesStatusCriterio']['qtd_ate'] = 0;
			return true;
		}
	}



	function valida_pontos( $check ) {
		return is_numeric( $this->data[$this->name]['pontos'] );
		/*	
		if (!isset($this->data[$this->name]['pontos']) ||  empty($this->data[$this->name]['pontos'])){//$this->data[$this->name]['codigo_status_criterio'] == 'Selecione um Status' )		
			return false;
		}		
		if(!empty($this->data[$this->name]['pontos'])){
			return true;
		}
		$codigo_cliente = $this->data[$this->name]['codigo_cliente'];
		$codigo_seguradora = $this->data[$this->name]['codigo_seguradora'];
		$codigo_status_criterio =  $this->data[$this->name]['codigo_status_criterio'];
		$pontos =$check['pontos'];
		$conditions = array(
			'codigo_cliente'=>$codigo_cliente,
			'codigo_seguradora'=>$codigo_seguradora,
			'codigo_status_criterio'=>$codigo_status_criterio,
			'pontos'=>$pontos,
			'codigo <>' => isset($this->data[$this->name][$this->primaryKey])?$this->data[$this->name][$this->primaryKey]:NULL
		);
		return !$this->find('all',compact('conditions'));
		*/
	}

	function valida_pontuacoes_status_criterio($check) {
		
			
		if (!isset($this->data[$this->name]['codigo_status_criterio']) ||  empty($this->data[$this->name]['codigo_status_criterio']))//$this->data[$this->name]['codigo_status_criterio'] == 'Selecione um Status' )
		
		return false;
		
		$codigo_cliente = $this->data[$this->name]['codigo_cliente'];
		$codigo_seguradora = $this->data[$this->name]['codigo_seguradora'];
		$codigo_status_criterio = $check['codigo_status_criterio'];
		
		$conditions = array(
			'codigo_cliente'=>$codigo_cliente,
			'codigo_seguradora'=>$codigo_seguradora,
			'codigo_status_criterio'=>$codigo_status_criterio,
			'codigo <>' => isset($this->data[$this->name][$this->primaryKey])?$this->data[$this->name][$this->primaryKey]:NULL
		);
		return !$this->find('all',compact('conditions'));
	}



	function bindStatusCriterio() {
		$this->bindModel(array(
		   'belongsTo' => array(
			   'StatusCriterio' => array(
				   'class' => 'StatusCriterio',
				   'foreignKey' => 'codigo_status_criterio'
			   )
		   )
		));
	}

	function bindCliente() {
		$this->bindModel(array(
		   'belongsTo' => array(
			   'Cliente' => array(
				   'class' => 'Cliente',
				   'foreignKey' => 'codigo_cliente'
			   )
		   )
		));
	}

	function bindSeguradora() {
		$this->bindModel(array(
		   'belongsTo' => array(
			   'Seguradora' => array(
				   'class' => 'Seguradora',
				   'foreignKey' => 'codigo_seguradora'
			   )
		   )
		));
	}

	function formatarLista($lista) {
		$lista_final = array();
		foreach($lista as $criterio) {
			$codigo_criterio = $criterio['Criterio']['codigo'];
			$lista_final[$codigo_criterio]['codigo'] 				= $codigo_criterio;
			$lista_final[$codigo_criterio]['descricao']     		= $criterio['Criterio']['descricao'];
			$lista_final[$codigo_criterio]['campo_sistema'] 		= $criterio['Criterio']['campo_sistema'];
			$lista_final[$codigo_criterio]['aceita_texto']  		= $criterio['Criterio']['aceita_texto'];
			$lista_final[$codigo_criterio]['opcional']      		= $criterio['CriterioOpcional']['opcional'];			
			if(isset($criterio[0]['criterio_bloqueado']))
				$lista_final[$codigo_criterio]['criterio_bloqueado']= $criterio[0]['criterio_bloqueado'];
		   	if( isset($criterio['FichaStatusCriterio']['automatico']))
		   		$lista_final[$codigo_criterio]['preenchimento_automatico'] = $criterio['FichaStatusCriterio']['automatico'];
		   	$lista_final[$codigo_criterio]['StatusCriterio'][$criterio['StatusCriterio']['codigo']] = $criterio['StatusCriterio']['descricao'];
			if( isset($criterio['0']['codigo_profissional_tipo']))
				$lista_final[$codigo_criterio]['codigo_profissional_tipo'] = $criterio['0']['codigo_profissional_tipo'];
			if( isset($criterio['0']['codigo_profissional_tipo']))
			    $lista_final[$codigo_criterio]['StatusCodigoProfissionalTipo'][$criterio['StatusCriterio']['codigo']] = $criterio['0']['codigo_profissional_tipo'];
			if( isset($criterio['0']['codigo_profissional_tipo2'])){
			    $lista_final[$codigo_criterio]['StatusCodigoProfissionalTipo2'][$criterio['StatusCriterio']['codigo']] = $criterio['0']['codigo_profissional_tipo2']; 
			    $array = $lista_final[$codigo_criterio]['StatusCodigoProfissionalTipo2'][$criterio['StatusCriterio']['codigo']];
			  	if ($criterio['0']['codigo_profissional_tipo'] == $criterio['0']['codigo_profissional_tipo2']){
			       $lista_final[$codigo_criterio]['codigo_profissional_tipo2'] =$criterio['0']['codigo_profissional_tipo2'];
			    }
			}		    
		}
		return $lista_final;
	}

	function formatarListaPontos($lista) {
		$lista_final = array();

		foreach($lista as $criterio) {
			$nome_criterio   							= $criterio['Criterio']['descricao'];
            
			$pontos 									= $criterio['PontuacoesStatusCriterio']['pontos'];
			$aceita_texto								= $criterio['Criterio']['aceita_texto'];
            $lista_final[$nome_criterio][]['pontos']	= $pontos;
          
		}
		return $lista_final;
	}

	function listarPorCliente($codigo_cliente, $pontos = false, $aceita_texto=false) {        
		$Cliente = ClassRegistry::init('Cliente');

		$lista = array();
		
		if($codigo_cliente != null) {

			$codigo_seguradora = $Cliente->carregarSeguradora($codigo_cliente);
			($codigo_seguradora == null) ? $filtros['codigo_seguradora'] = 0 : $filtros['codigo_seguradora'] = $codigo_seguradora;
			$filtros['codigo_cliente'] = $codigo_cliente;

			$lista_cliente_seguradora = $this->listar_criterios($filtros);
			
			if (!empty($lista_cliente_seguradora)) {
				$lista = $lista_cliente_seguradora;
			} else {

				$filtros['codigo_seguradora'] = 0;

				$lista_cliente_sem_seguradora = $this->listar_criterios($filtros);

				if (!empty($lista_cliente_sem_seguradora)) {
					$lista = $lista_cliente_sem_seguradora;
				}

				if ($codigo_seguradora != null) {
					$lista_seguradora_sem_cliente = $this->listar_criterios(array('codigo_seguradora' => $codigo_seguradora, 'codigo_cliente' => 0));

					if(!empty($lista_seguradora_sem_cliente)) {
						$lista = $lista_seguradora_sem_cliente;
					} else {
						$lista = $this->listar_criterios(array('codigo_seguradora' => 0, 'codigo_cliente' => 0));
					}

				} else {
					$lista = $this->listar_criterios(array('codigo_seguradora' => 0, 'codigo_cliente' => 0));
				}
			}
		}

		// debug( $lista );


        if($pontos){
			return ($this->formatarListaPontos($lista));
		}
		return ($this->formatarLista($lista));
		return array();
	}
    
    function listarPorClienteTipoProf($codigo_cliente, $pontos = false, $aceita_texto=false,$codigo_ficha) {
        
		$Cliente = ClassRegistry::init('Cliente');

		$lista = array();
		
        $filtros['codigo_ficha'] = $codigo_ficha;
		if($codigo_cliente != null) {
		   	
           $codigo_seguradora = $Cliente->carregarSeguradora($codigo_cliente);
			($codigo_seguradora == null) ? $filtros['codigo_seguradora'] = 0 : $filtros['codigo_seguradora'] = $codigo_seguradora;
			$filtros['codigo_cliente'] = $codigo_cliente;

			$lista_cliente_seguradora = $this->listar_criterios($filtros);

			
			if (!empty($lista_cliente_seguradora)) {
				$lista = $lista_cliente_seguradora;
			    //debug('0');
			} else {
                //debug('1');
				$filtros['codigo_seguradora'] = 0;

				$lista_cliente_sem_seguradora = $this->listar_criterios($filtros);
                
                

				if (!empty($lista_cliente_sem_seguradora)) {
					//debug('2');
					$lista = $lista_cliente_sem_seguradora;
				}

				if ($codigo_seguradora != null) {
					//debug('3');
					$lista_seguradora_sem_cliente = $this->listar_criterios(array('codigo_ficha'=>$codigo_ficha,'codigo_seguradora' => $codigo_seguradora, 'codigo_cliente' => 0));
                    
					if(!empty($lista_seguradora_sem_cliente)) {
						//debug('4');
						//$lista = $lista_seguradora_sem_cliente;
						$lista = $this->listar_criterios(array('codigo_ficha'=>$codigo_ficha,'codigo_seguradora' => 0, 'codigo_cliente' => 0));

					} else {
						//debug('5');
						$lista = $this->listar_criterios(array('codigo_ficha'=>$codigo_ficha,'codigo_seguradora' => 0, 'codigo_cliente' => 0));
					       
					}

				} else {
					//debug('6');
					$lista = $this->listar_criterios(array('codigo_ficha'=>$codigo_ficha,'codigo_seguradora' => 0, 'codigo_cliente' => 0));
				}
			}
		}

		
         
		if($pontos){
			return ($this->formatarListaPontos($lista));
		}

		return ($this->formatarLista($lista));
		return array();
	}

	function listar_criterios($filtros){
		$this->FichaScorecard = ClassRegistry::init('FichaScorecard');
		$this->PontuacaoSCProfissional = ClassRegistry::init('PontuacaoSCProfissional');
       
        if (!isset($filtros['codigo_ficha'])){
        	$filtros['codigo_ficha'] ='';
        } 

        $ficha = $filtros['codigo_ficha'];
          
		$fields = array(
            'PontuacoesStatusCriterio.codigo_cliente',
            'PontuacoesStatusCriterio.codigo_seguradora',
            'PontuacoesStatusCriterio.codigo_status_criterio', 
            'PontuacoesStatusCriterio.codigo',
            'PontuacoesStatusCriterio.pontos',
            'PontuacoesStatusCriterio.qtd_ate',
            'StatusCriterio.codigo',
            'StatusCriterio.descricao',
            'StatusCriterio.data_inclusao',
            'Cliente.codigo',
            'Cliente.razao_social',
            'Criterio.codigo',
            'Criterio.descricao',
            'Criterio.aceita_texto',
            'Criterio.campo_sistema',
            'Seguradora.codigo',
            'Seguradora.nome',
			'CriterioOpcional.opcional',
			"(select codigo_profissional_tipo from {$this->FichaScorecard->databaseTable}.{$this->FichaScorecard->tableSchema}.{$this->FichaScorecard->useTable} a
			  where codigo='{$ficha}') as codigo_profissional_tipo",
		    "(select codigo_tipo_profissional from {$this->PontuacaoSCProfissional->databaseTable}.{$this->PontuacaoSCProfissional->tableSchema}.{$this->PontuacaoSCProfissional->useTable} 
              where codigo_pontuacao_status_criterio = PontuacoesStatusCriterio.codigo  
              and codigo_tipo_profissional in (select codigo_profissional_tipo from {$this->FichaScorecard->databaseTable}.{$this->FichaScorecard->tableSchema}.{$this->FichaScorecard->useTable} a
			  where codigo='{$ficha}' )) as codigo_profissional_tipo2 "
	
        );

		$this->bindModel(array('belongsTo' => array(
			'Cliente' => array('foreignKey' => 'codigo_cliente'),
			'Seguradora' => array('foreignKey' => 'codigo_seguradora'),
			'StatusCriterio' => array('foreignKey' => 'codigo_status_criterio'),
			'Criterio' => array('foreignKey' => false, 'conditions' => 'Criterio.codigo = StatusCriterio.codigo_criterio'),
			'CriterioOpcional' => array('foreignKey' => false, 'conditions' => array(
				'Criterio.codigo = CriterioOpcional.codigo_criterio',
				'(PontuacoesStatusCriterio.codigo_cliente = CriterioOpcional.codigo_cliente OR (PontuacoesStatusCriterio.codigo_cliente is NULL AND CriterioOpcional.codigo_cliente is NULL))',
				'(PontuacoesStatusCriterio.codigo_seguradora = CriterioOpcional.codigo_seguradora OR (PontuacoesStatusCriterio.codigo_seguradora is NULL AND CriterioOpcional.codigo_seguradora is NULL))'
			))
		)));

		$conditions = array();

		
		if(isset($filtros['codigo_cliente']) ){
			if ($filtros['codigo_cliente'] == 0 )
				$conditions[] = array('PontuacoesStatusCriterio.codigo_cliente is null');
			else
				$conditions['PontuacoesStatusCriterio.codigo_cliente'] = $filtros['codigo_cliente'];
		}

		if(isset($filtros['codigo_seguradora']) ) {
			if ($filtros['codigo_seguradora'] == 0)
				$conditions[] = array('PontuacoesStatusCriterio.codigo_seguradora is null');
			else
				$conditions['PontuacoesStatusCriterio.codigo_seguradora'] = $filtros['codigo_seguradora'];
		}	

		if(isset($filtros['codigo_criterio']) && !empty($filtros['codigo_criterio'])){
			$conditions['StatusCriterio.codigo_criterio'] = $filtros['codigo_criterio'];
		}
		$conditions['StatusCriterio.descricao <>'] = 'CONSTA OUTROS';
		/*
		if(isset($filtros['codigo_seguradora']) && $filtros['codigo_seguradora']&& $filtros['codigo_criterio']==NULL && $filtros['codigo_cliente']== NULL){
			$conditions['PontuacoesStatusCriterio.codigo_seguradora'] = $filtros['codigo_seguradora'];
		}else 			
			$conditions['PontuacoesStatusCriterio.codigo_seguradora'] = NULL;
		if(isset($filtros['codigo_criterio']) && $filtros['codigo_criterio'] )
			$conditions['codigo_criterio'] = $filtros['codigo_criterio'];*/
		
		$order = array('PontuacoesStatusCriterio.codigo_cliente','PontuacoesStatusCriterio.codigo_seguradora','Criterio.codigo');

		$return = array();
        
     
			if(!isset($filtros['codigo_cliente']) ||
				$filtros['codigo_cliente'] === null && 
				$filtros['codigo_seguradora'] === NULL && 
				$filtros['codigo_criterio'] === NULL){
				$return = $this->find('all' ,compact('joins','fields','order'));
			    //debug($return);
			   return $return;
		    }
	    
		if($conditions){
			$return = $this->find('all' ,compact('conditions','joins','fields','order'));
            //debug($return);
		    return $return;
		}

	}

	function lista_codigo_status_criterio(){
		$order 	= array('PontuacoeStatusCriterio.codigo_status_criterio');
		$return = $this->find('all',compact('order'));
		return $return;
	}

	public function retornaPonto($codigo_cliente, $codigo_status_criterio){
		$Cliente = ClassRegistry::init('Cliente');
		$dadosCliente = $Cliente->carregar($codigo_cliente);
		$pontos = null;

		for( $i=0; $i<4; $i++ ){ 
			switch (true) {
				case $pontos = $this->verificaPonto($codigo_status_criterio, $dadosCliente['Cliente']['codigo'], $dadosCliente['Cliente']['codigo_seguradora']):
					return $pontos['PontuacoesStatusCriterio']['pontos'];
					break;

				case $pontos = $this->verificaPonto($codigo_status_criterio, $dadosCliente['Cliente']['codigo']):
					return $pontos['PontuacoesStatusCriterio']['pontos'];
					break;

				case $pontos = $this->verificaPonto($codigo_status_criterio, null, $dadosCliente['Cliente']['codigo_seguradora']):
					return $pontos['PontuacoesStatusCriterio']['pontos'];
					break;

				case $pontos = $this->verificaPonto($codigo_status_criterio):
					return $pontos['PontuacoesStatusCriterio']['pontos'];
					break;
			}
		}
	}

	public function verificaPonto($codigo_status_criterio, $codigo_cliente=null, $codigo_seguradora=null){
		$result = $this->find('first',array(
			'fields' => 'pontos',
			'conditions' => array(
				'codigo_status_criterio' => $codigo_status_criterio,
				'codigo_cliente' => $codigo_cliente,
				'codigo_seguradora' => $codigo_seguradora,
			)
		));		
		return $result;
	}

	public function maximoPontos($codigo_cliente) {
		$pontos = $this->listarPorCliente($codigo_cliente, true);
		$max = 0;

		foreach($pontos as $key => $ponto) {

			$maior_por_criterio = 0;
			foreach ($ponto as $ponto) {
				$ponto['pontos'] > $maior_por_criterio ? $maior_por_criterio = $ponto['pontos'] : $maior_por_criterio = $maior_por_criterio ;
			}
			$max += $maior_por_criterio;
		}
		return $max;

	}
    
   

	public function verificaCampoInsuficiente( $codigo_status_criterio ){
		$this->Criterio       = ClassRegistry::init('Criterio');
		$this->StatusCriterio = ClassRegistry::init('StatusCriterio');
		$result = $this->find('first',array(
			'fields' => 'insuficiente',
			'joins'  => array( 
				array(
                    'table' => $this->StatusCriterio->databaseTable.'.'. $this->StatusCriterio->tableSchema.'.'.$this->StatusCriterio->useTable,
                    'alias' => 'StatusCriterio',
                    'type'  => 'INNER',
                    'conditions' => 'StatusCriterio.codigo = PontuacoesStatusCriterio.codigo_status_criterio'
                ),
				array(
                    'table' => $this->Criterio->databaseTable.'.'. $this->Criterio->tableSchema.'.'.$this->Criterio->useTable,
                    'alias' => 'Criterio',
                    'type'  => 'INNER',
                    'conditions' => 'Criterio.codigo = StatusCriterio.codigo_criterio'
                ),
			),
			'conditions' => array(
				'PontuacoesStatusCriterio.codigo_status_criterio' => $codigo_status_criterio
			)
		));		
		return $result[$this->name]['insuficiente'];
	}
 
	public function verificaCampoDivergente( $codigo_status_criterio ){
		$this->Criterio       = ClassRegistry::init('Criterio');
		$this->StatusCriterio = ClassRegistry::init('StatusCriterio');
		$result = $this->find('first',array(
			'fields' => 'divergente',
			'joins'  => array( 
				array(
                    'table' => $this->StatusCriterio->databaseTable.'.'. $this->StatusCriterio->tableSchema.'.'.$this->StatusCriterio->useTable,
                    'alias' => 'StatusCriterio',
                    'type'  => 'INNER',
                    'conditions' => 'StatusCriterio.codigo = PontuacoesStatusCriterio.codigo_status_criterio'
                ),
				array(
                    'table' => $this->Criterio->databaseTable.'.'. $this->Criterio->tableSchema.'.'.$this->Criterio->useTable,
                    'alias' => 'Criterio',
                    'type'  => 'INNER',
                    'conditions' => 'Criterio.codigo = StatusCriterio.codigo_criterio'
                ),
			),
			'conditions' => array(
				'PontuacoesStatusCriterio.codigo_status_criterio' => $codigo_status_criterio
			)
		));		
		return $result[$this->name]['divergente'];
	}

	public function retornaCampoInsuficiente( $codigo_status_criterio ){
        $this->bindModel(array('belongsTo' => array(
            'StatusCriterio' => array('foreignKey' => 'codigo_status_criterio')))
        );
    	$this->bindModel(array('belongsTo' => array(
    		'Criterio' => array(
    				'foreignKey' => false,
    				'conditions' => 'Criterio.codigo = StatusCriterio.codigo_criterio'
    			),
    		)
    	));
    	$conditions = array( 'PontuacoesStatusCriterio.codigo_status_criterio' => $codigo_status_criterio );
    	return $this->find('first', compact('conditions'));
	}

    public function retornaCampoDivergente( $codigo_status_criterio ){
        $this->bindModel(array('belongsTo' => array(
            'StatusCriterio' => array('foreignKey' => 'codigo_status_criterio')))
        );
    	$this->bindModel(array('belongsTo' => array(
    		'Criterio' => array(
    				'foreignKey' => false,
    				'conditions' => 'Criterio.codigo = StatusCriterio.codigo_criterio'
    			),
    		)
    	));
    	$conditions = array( 'PontuacoesStatusCriterio.codigo_status_criterio' => $codigo_status_criterio );
    	return $this->find('first', compact('conditions'));
	}

	function listarCriteriosCategoria( $dados_ficha ){
		$codigo_ficha  = isset($dados_ficha['FichaScorecard']['codigo']) 					? $dados_ficha['FichaScorecard']['codigo'] : NULL;
		$categoria     = isset($dados_ficha['FichaScorecard']['codigo_profissional_tipo']) 	? $dados_ficha['FichaScorecard']['codigo_profissional_tipo'] : NULL;

		if( !$codigo_ficha || !$categoria )		
			return FALSE;

		$this->bindModel(array('belongsTo' => array(
			'StatusCriterio' 	=> array('foreignKey' => 'codigo_status_criterio'),
			'Criterio' 			 => array('foreignKey' => false, 'conditions' => 'Criterio.codigo = StatusCriterio.codigo_criterio'),
			'FichaStatusCriterio'  => array('foreignKey' => false, 
				'conditions' => array(
					'FichaStatusCriterio.codigo_criterio = Criterio.codigo',
					'FichaStatusCriterio.codigo_ficha'  => $codigo_ficha,
					)
				),
			'PontuacaoSCProfissional' => array(
				'foreignKey'  	=> false, 
				'conditions'  	=> 'PontuacaoSCProfissional.codigo_pontuacao_status_criterio = PontuacoesStatusCriterio.codigo'),
			'CriterioOpcional' 	=> array(
				'foreignKey' 	=> false, 
				'conditions' 	=> 'Criterio.codigo = CriterioOpcional.codigo_criterio'

			)
		)));

	   $fields = array(
            'PontuacoesStatusCriterio.codigo_cliente',
            'PontuacoesStatusCriterio.codigo_seguradora',
            'PontuacoesStatusCriterio.codigo_status_criterio', 
            'PontuacoesStatusCriterio.codigo',
            'PontuacoesStatusCriterio.pontos',
            'PontuacoesStatusCriterio.qtd_ate',
            'StatusCriterio.codigo',
            'StatusCriterio.descricao',
            'StatusCriterio.data_inclusao',
            'Criterio.codigo',
            'Criterio.descricao',
            'Criterio.aceita_texto',
            'Criterio.campo_sistema',
			'CriterioOpcional.opcional',
			'FichaStatusCriterio.automatico',
			"(SELECT CASE WHEN count(*)>0 THEN 'N' ELSE 'S' END FROM {$this->databaseTable}.{$this->tableSchema}.pontuacoes_status_criterios_profissional AS PSCP 
			    WHERE PSCP.codigo_tipo_profissional = ".$categoria." AND PSCP.codigo_pontuacao_status_criterio = PontuacoesStatusCriterio.codigo
			) AS criterio_bloqueado"
        );
		$conditions = array(
			// 'PontuacaoSCProfissional.codigo_tipo_profissional' => $categoria,
			'StatusCriterio.descricao <>' => 'CONSTA OUTROS'
		);		
		$criterios = $this->find('all', compact('conditions', 'fields'));
		return $this->formatarLista($criterios);
	}

	function listarCriteriosCategoriaLog( $dados_ficha ){
		$codigo_ficha  = isset($dados_ficha['FichaScorecardLog']['codigo']) 					? $dados_ficha['FichaScorecardLog']['codigo'] : NULL;
		$categoria     = isset($dados_ficha['FichaScorecardLog']['codigo_profissional_tipo']) 	? $dados_ficha['FichaScorecardLog']['codigo_profissional_tipo'] : NULL;
		if( !$codigo_ficha || !$categoria )		
			return FALSE;
		$this->bindModel(array('belongsTo' => array(
			'StatusCriterio' 	=> array('foreignKey' => 'codigo_status_criterio'),
			'Criterio' 			 => array('foreignKey' => false, 'conditions' => 'Criterio.codigo = StatusCriterio.codigo_criterio'),
			'FichaStatusCriterioLog'  => array('foreignKey' => false, 
				'conditions' => array(
					'FichaStatusCriterioLog.codigo_criterio = Criterio.codigo',
					'FichaStatusCriterioLog.codigo_ficha_log'  => $codigo_ficha,
					)
				),
			'PontuacaoSCProfissional' => array(
				'foreignKey'  	=> false, 
				'conditions'  	=> 'PontuacaoSCProfissional.codigo_pontuacao_status_criterio = PontuacoesStatusCriterio.codigo'),
			'CriterioOpcional' 	=> array(
				'foreignKey' 	=> false, 
				'conditions' 	=> 'Criterio.codigo = CriterioOpcional.codigo_criterio'

			)
		)));

	   $fields = array(
            'PontuacoesStatusCriterio.codigo_cliente',
            'PontuacoesStatusCriterio.codigo_seguradora',
            'PontuacoesStatusCriterio.codigo_status_criterio', 
            'PontuacoesStatusCriterio.codigo',
            'PontuacoesStatusCriterio.pontos',
            'PontuacoesStatusCriterio.qtd_ate',
            'StatusCriterio.codigo',
            'StatusCriterio.descricao',
            'StatusCriterio.data_inclusao',
            'Criterio.codigo',
            'Criterio.descricao',
            'Criterio.aceita_texto',
            'Criterio.campo_sistema',
			'CriterioOpcional.opcional',
			'FichaStatusCriterioLog.automatico',
			"(SELECT CASE WHEN count(*)>0 THEN 'N' ELSE 'S' END FROM {$this->databaseTable}.{$this->tableSchema}.pontuacoes_status_criterios_profissional AS PSCP 
			    WHERE PSCP.codigo_tipo_profissional = ".$categoria." AND PSCP.codigo_pontuacao_status_criterio = PontuacoesStatusCriterio.codigo
			) AS criterio_bloqueado"
        );
		$conditions = array('StatusCriterio.descricao <>' => 'CONSTA OUTROS');		
		$criterios = $this->find('all', compact('conditions', 'fields'));
		return $this->formatarLista($criterios);
	}


}
