<?php
class FichaStatusCriterio extends AppModel {
	var $name = 'FichaStatusCriterio';
	var $primaryKey = 'codigo';
	var $displayField = 'pontos';
	var $databaseTable = 'dbTeleconsult';
	var $tableSchema = 'informacoes';
	var $useTable = 'fichas_status_criterios';
	var $actsAs = array('Secure');

	public function atualizarParaGravarPontosCriterio( $codigo_ficha, $em_transaction = FALSE ){
		$this->FichaScorecard 	= ClassRegistry::init('FichaScorecard');
		$this->Pontuacao 		= ClassRegistry::init('PontuacoesStatusCriterio');
		$this->ParametroScore 	= ClassRegistry::init('ParametroScore');		
		$fichaStatus 			= $this->buscarPorFicha( $codigo_ficha );
		$data   = array();
		$pontos = null;
		try {
			if( $em_transaction === FALSE )
				$this->query('begin transaction');
			foreach ($fichaStatus as $value) {
				$pontos = $this->Pontuacao->retornaPonto( $value['FichaScorecard']['codigo_cliente'], $value['FichaStatusCriterio']['codigo_status_criterio'] );
				$data['FichaStatusCriterio']['codigo'] = $value['FichaStatusCriterio']['codigo'];
				$data['FichaStatusCriterio']['pontos'] = $pontos;
				if( !$this->atualizar($data) )
					throw new Exception("");
			}
			$total			  = $this->totalPontosPorFicha( $codigo_ficha );			
			$percentual_total = $this->totalPercentualPorFicha( $codigo_ficha, $total );
			$status_motorista = $this->ParametroScore->classificaMotorista( $percentual_total );
			$ficha = array(
				'FichaScorecard' => array(
					'codigo' => $codigo_ficha,
					'total_pontos' => $total,
					'percentual_pontos' => $percentual_total,
					'codigo_parametro_score' => $status_motorista['ParametroScore']['codigo'],
					'codigo_score' => $status_motorista['ParametroScore']['codigo']
				)
			);
			$this->FichaScorecard->save($ficha, array('validate'=>false));
			if( $em_transaction === FALSE )
				$this->commit();
			if( $total < 0 )
				return false;
			else
				return true;
		} catch(Exception $e) {
			if( $em_transaction === FALSE )
				$this->rollback();
			return false;
		}
	}

	public function buscarPorFicha( $codigo_ficha ){
    	$this->bindModel(array(
    		'belongsTo' => array(
    			'FichaScorecard' => array(
    				'class' 	 => 'FichaScorecard',
    				'foreignKey' => false,
    				'conditions' => 'FichaScorecard.codigo = FichaStatusCriterio.codigo_ficha'
    			),
    		)
    	));
		return $this->find('all',
			array( 
				'conditions' 	=> array('codigo_ficha' => $codigo_ficha ), 
				'fields'		=> array(
					'FichaStatusCriterio.codigo', 
					'FichaStatusCriterio.codigo_criterio', 
					'FichaStatusCriterio.pontos', 
					'FichaStatusCriterio.codigo_status_criterio', 
					'FichaScorecard.codigo_cliente'
				)
			));
	}

	public function totalPontosPorFicha($codigo_ficha){

		$result = $this->find('first',array(
			'fields' => 'SUM(pontos) AS pontos',
			'conditions' => array(
				'codigo_ficha' => $codigo_ficha
			)
		));

		return $result[0]['pontos'];
	}


	public function formatarDados($dados, $codigo_ficha, $codigo_usuario_inclusao = 1) {
		$dados = $dados['FichaStatusCriterio'];
		$retorno = array();
		foreach($dados as $codigo_criterio => $resposta) {
			if(!empty($resposta['codigo_status_criterio'])){
				$retorno[$codigo_criterio] = $dados[$codigo_criterio];
				$retorno[$codigo_criterio]['codigo_ficha'] = $codigo_ficha;
				if(!isset($resposta['observacao']))
					$retorno[$codigo_criterio]['observacao'] = null;
			}
		}

		return $retorno;
	}

	public function salvarFichaStatusCriterioAlt($codigo_ficha, $dados ) {
		try {
            
			$dados  = $this->verificaAlteracaoCriterio( $dados );
			$linhas = $this->find('list', array('conditions'=>array('codigo_ficha'=>$codigo_ficha), 'fields'=>array('codigo')));
			
			$this->query('begin transaction');
			//if(!empty($linhas)){
			//	if (!$this->deleteAll(array('codigo'=>$linhas)))
			//		throw new Exception("");
			//}
			if (!$this->saveAll($dados))
				throw new Exception("");

			$this->commit();
			return true;
		} catch(Exception $e){
			$this->rollback();
			return false;
		}

	}

	public function salvarFichaStatusCriterio( $codigo_ficha, $dados, $em_transaction = FALSE ) {
		try {
			$dados  = $this->verificaAlteracaoCriterio( $dados );
			$linhas = $this->find('list', array('conditions'=>array('codigo_ficha'=>$codigo_ficha), 'fields'=>array('codigo')));
			if( $em_transaction === FALSE )
				$this->query('begin transaction');			
			if(!empty($linhas)){
				if (!$this->deleteAll(array('codigo'=>$linhas)))
					throw new Exception("");
			}
			if (!$this->saveAll($dados))
				throw new Exception("");
			if( $em_transaction === FALSE )
				$this->commit();
			return true;
		} catch(Exception $e){
			if( $em_transaction === FALSE )
				$this->rollback();
			return false;
		}

	}

	public function listarResultadoporFicha($codigo_ficha){
		$Criterio           = ClassRegistry::init("Criterio"); 
		$StatusCriterio     = ClassRegistry::init("StatusCriterio");

	    $options = array(
           'fields' => array(
                  
                'Criterio.descricao       AS descricao_criterio',
                'StatusCriterio.descricao  AS descricao_status_criterio',
                'FichaStatusCriterio.observacao AS observacao'
              
            ),
            'joins' => array(
                
                array(
                    'table' => "{$Criterio->databaseTable}.{$Criterio->tableSchema}.{$Criterio->useTable}",
                    'alias' => 'Criterio',
                    'type' => 'INNER',
                    'conditions' => 'FichaStatusCriterio.codigo_criterio = Criterio.codigo'
                ),
                array(
                    'table' => "{$StatusCriterio->databaseTable}.{$StatusCriterio->tableSchema}.{$StatusCriterio->useTable}",
                    'alias' => 'StatusCriterio',
                    'type' => 'INNER',
                    'conditions' => 'FichaStatusCriterio.codigo_status_criterio = StatusCriterio.codigo'
                ),
             
            ),
            'conditions'=>  array('FichaStatusCriterio.codigo_ficha' =>$codigo_ficha)
                //'limit' => 50
    	);
    
        $return =  $this->find('all',$options);        
        return $return;
	}
	
	public function listarRespostasFicha($codigo_ficha) {
		$PontuacaoSCProfissional = ClassRegistry::init("PontuacaoSCProfissional");
		$FichaScorecard          = ClassRegistry::init("FichaScorecard");
		$joins = array(
                
                array(
                    'table' => "{$PontuacaoSCProfissional->databaseTable}.{$PontuacaoSCProfissional->tableSchema}.{$PontuacaoSCProfissional->useTable}",
                    'alias' => 'FichasStatusCriteriosProfissional',
                    'type' => 'INNER',
                    'conditions' => 'FichaStatusCriterio.codigo =  FichasStatusCriteriosProfissional.codigo_pontuacao_status_criterio'
                ),
                array(
                    'table' => "{$FichaScorecard->databaseTable}.{$FichaScorecard->tableSchema}.{$FichaScorecard->useTable}",
                    'alias' => 'FichaScorecard',
                    'type' => 'INNER',
                    'conditions' => ' FichasStatusCriteriosProfissional.codigo_tipo_profissional = FichaScorecard.codigo_profissional_tipo 
                                     AND FichaStatusCriterio.codigo_ficha = FichaScorecard.codigo'
                ),
             
            );
		$lista =  $this->find('all', array('conditions'=>array('FichaStatusCriterio.codigo_ficha'=>$codigo_ficha), 'fields'=>array('codigo', 'codigo_criterio', 'codigo_status_criterio', 'observacao', 'automatico')));
		//print_r($lista);
		$return['FichaStatusCriterio'] = array();
		foreach($lista as $item){
			$return['FichaStatusCriterio'][$item['FichaStatusCriterio']['codigo_criterio']] = $item['FichaStatusCriterio'];
		} 
		return $return;
	}

	public function totalPercentualPorFicha($codigo_ficha, $total_pontos, $arredondar = false) {
		$PontuacoesStatusCriterio	= ClassRegistry::init("PontuacoesStatusCriterio"); 
		$FichaScorecard   			= ClassRegistry::init('FichaScorecard');
		
		$dadosFicha   	= $FichaScorecard->carregar($codigo_ficha);
		$codigo_cliente = $dadosFicha['FichaScorecard']['codigo_cliente'];
		
		$maximo_pontos	= $PontuacoesStatusCriterio->maximoPontos($codigo_cliente);
		
		if ($maximo_pontos != 0) {
			$percentual = round((100 * ($total_pontos/$maximo_pontos)));
			return  $percentual < 0 ? 0 : $percentual;
		} else
			return 0;
	}

	public function listarResultados($filtros = array()) { 
		App::import('Model', 'FichaScorecardStatus');
		$this->FichaScorecard     = ClassRegistry::init('FichaScorecard');
		$filtros['codigo_status'] = FichaScorecardStatus::FINALIZADA;
		$conditions = $this->converteFiltroEmConditions($filtros);
		
		$dados  = array();
		$fichas = array();
        $this->FichaScorecard->bindModel(array('belongsTo' => array(
            'Cliente'         => array('foreignKey' => false, 'conditions' => array('FichaScorecard.codigo_cliente = Cliente.codigo')),
            'ProfissionalLog' => array('foreignKey' => false, 'conditions' => array('FichaScorecard.codigo_profissional_log = ProfissionalLog.codigo')),
            'Seguradora'      => array('foreignKey' => false, 'conditions' => array('Cliente.codigo_seguradora = Seguradora.codigo')),
            'ParametroScore'  => array('foreignKey' => false, 'conditions' => array('FichaScorecard.codigo_parametro_score = ParametroScore.codigo')),
        )));
        
        if(count($conditions) > 0){
			$fichas = $this->FichaScorecard->find('all',array('conditions' => $conditions,'order' => array('FichaScorecard.codigo desc')));
		}
		
		foreach($fichas as $key => $ficha) {
			$dados[$key]['codigo_ficha'] 		= $ficha['FichaScorecard']['codigo'];
			$dados[$key]['data_inclusao']	    = AppModel::DbDateToDate($ficha['FichaScorecard']['data_inclusao']);
			$dados[$key]['codigo_profissional'] = $ficha['ProfissionalLog']['codigo'];
			$dados[$key]['profissional'] 	    = $ficha['ProfissionalLog']['nome'];
			$dados[$key]['profissional_cpf']	= $ficha['ProfissionalLog']['codigo_documento'];
			$dados[$key]['codigo_cliente'] 		= $ficha['Cliente']['codigo'];
			$dados[$key]['cliente'] 			= $ficha['Cliente']['razao_social'];
			$dados[$key]['seguradora']			= $ficha['Seguradora']['nome'];
			$dados[$key]['total'] 				= $ficha['FichaScorecard']['total_pontos'];
			$dados[$key]['percentual_total'] 	= $ficha['FichaScorecard']['percentual_pontos'];
			$dados[$key]['classificacao_motorista'] = $ficha['ParametroScore']['nivel'];
			$dados[$key]['qtd_maxima'] 		    = $ficha['ParametroScore']['valor'];
			$dados[$key]['pontos']	 		    = $ficha['ParametroScore']['pontos'];
		}
		return $dados;
	}

	function converteFiltroEmConditions($data) {
        $conditions = array();

        if (isset($data['codigo_status']) && (!empty($data['codigo_status']) || $data['codigo_status'] === 0))  {
            $conditions['FichaScorecard.codigo_status'] = $data['codigo_status'];
        }
        if (isset($data['codigo_ficha']) && (!empty($data['codigo_ficha']) || $data['codigo_ficha'] === 0))  {
            $conditions['FichaScorecard.codigo'] = $data['codigo_ficha'];
        }
        if (isset($data['codigo_cliente']) && !empty($data['codigo_cliente'])) {
            $conditions['Cliente.codigo'] = preg_replace('/\D/', '', $data['codigo_cliente']);
        }
        if (isset($data['codigo_documento']) && !empty($data['codigo_documento'])) {
            $conditions['ProfissionalLog.codigo_documento'] = preg_replace('/\D/', '', $data['codigo_documento']);
        }
        if (isset($data['codigo_seguradora']) && !empty($data['codigo_seguradora'])) {
            $conditions['Seguradora.codigo'] = $data['codigo_seguradora'];
        }
        if (isset($data['classificacao']) && !empty($data['classificacao'])) {
        	if($data['classificacao'] == 'R')
	            $conditions['ParametroScore.codigo'] = null;
        	else
            	$conditions['ParametroScore.codigo'] = $data['classificacao'];
        }

        if (isset($data['data_inicial']) && !empty($data['data_inicial']) && isset($data['data_final']) && !empty($data['data_final'])) {
            $conditions['FichaScorecard.data_inclusao >='] = AppModel::DateToDbDate($data['data_inicial']);
            $conditions['FichaScorecard.data_inclusao <='] = AppModel::DateToDbDate($data['data_final']).' 23:59';

        }
        
        return $conditions;
    }
    
    function salvarStatus2($codigoFicha, $criterio, $statusCriterio, $automatico = true, $obs='') {        
        $data = array(
            'codigo_ficha'            => $codigoFicha,
            'codigo_criterio'         => $criterio,
            'codigo_status_criterio'  => $statusCriterio,
            'codigo_usuario_inclusao' => 1,
            'automatico' 			  => $automatico,
            'observacao'              => $obs
        );     
        $this->create();
        if ($found = $this->find('first', array('conditions'=>array('codigo_ficha'=>$codigoFicha, 'codigo_criterio'=>$criterio))))
            $this->id = $found['FichaStatusCriterio']['codigo'];
        $this->save($data); 
    }
    

    function salvarStatus($codigoFicha, $criterio, $statusCriterio, $automatico = true) {
        $data = array(
            'codigo_ficha'            => $codigoFicha,
            'codigo_criterio'         => $criterio,
            'codigo_status_criterio'  => $statusCriterio,
            'codigo_usuario_inclusao' => 1,
            'automatico' 			  => $automatico
        );
        
        $this->create();
        if ($found = $this->find('first', array('conditions'=>array('codigo_ficha'=>$codigoFicha, 'codigo_criterio'=>$criterio))))
            $this->id = $found['FichaStatusCriterio']['codigo'];
        $this->save($data);
    }
    
    function verificarFichaIncompleta($data){
    	$incompleta = false;
    	foreach($data['FichaStatusCriterio'] as $item){
    		if($item['codigo_status_criterio'] == -1) 
    			$incompleta = true;
    	}
    	return $incompleta;
    }

    function obterCriteriosUltimaFichaProfissional($codigo_cliente, $documento_profissional){
    	App::import('Model', 'FichaScorecardStatus');
    	$conditions = array(
    		'FichaScorecard.codigo_status' => FichaScorecardStatus::FINALIZADA,
    		'FichaScorecard.codigo_cliente' => $codigo_cliente,
    		'ProfissionalLog.codigo_documento' => preg_replace('/[-\.]/', '', $documento_profissional)
    	);
    	
    	$order = array('FichaScorecard.data_inclusao DESC');
    	
    	$this->FichaScorecard = ClassRegistry::init('FichaScorecard');
    	$this->FichaScorecard->bindModel(array(
    		'belongsTo' => array(
    			'ProfissionalLog' => array(
    				'class' => 'ProfissionalLog',
    				'foreignKey' => false,
    				'conditions' => 'ProfissionalLog.codigo = FichaScorecard.codigo_profissional_log'
    			),
    		)
    	));
    	
    	$fields = array('FichaScorecard.codigo', 'FichaScorecard.data_inclusao');
    	
    	$ficha = $this->FichaScorecard->find('first', compact('fields', 'conditions', 'order'));
    	
    	$respostas = $this->find('all', array('conditions'=>array('codigo_ficha'=>$ficha['FichaScorecard']['codigo'])));
    	return array($ficha['FichaScorecard']['data_inclusao'], Set::combine($respostas, '/FichaStatusCriterio/codigo_criterio', '/FichaStatusCriterio/codigo_status_criterio'));
    }

	public function removePontosCriterio( $codigo_ficha, $pontos_score=0 ){
		$this->FichaScorecard = ClassRegistry::init('FichaScorecard');		
		$this->ParametroScore = ClassRegistry::init('ParametroScore');		
		$fichaStatus = $this->buscarPorFicha($codigo_ficha);
		try{
			$this->query('begin transaction');
			$status_motorista = $this->ParametroScore->classificaMotorista( $pontos_score, TRUE );
			$ficha = array(
				'FichaScorecard'	 	=> array(
					'codigo' 			=> $codigo_ficha,
					'total_pontos' 		=> 0,
					'percentual_pontos' => 0,
					'codigo_parametro_score' => $status_motorista['ParametroScore']['codigo'],
					'codigo_score_manual' => $status_motorista['ParametroScore']['codigo']
				)
			);			
			$this->FichaScorecard->save($ficha, array('validate'=>false));
			$this->commit();
			return true;
		} catch(Exception $e) {
			$this->rollback();
			return false;
		}
	} 

    function verificarCamposInsuficientesFicha( $data ){
		$this->PontuacoesStatusCriterio = ClassRegistry::init('PontuacoesStatusCriterio');
		$insuficiente = 0;
		if(isset($data['FichaStatusCriterio'])){
			foreach( $data['FichaStatusCriterio'] as $codigo_criterio => $dados ){
				if( !empty($dados['codigo_status_criterio']) )
					$insuficiente += $this->PontuacoesStatusCriterio->verificaCampoInsuficiente( $dados['codigo_status_criterio'] );
			}
		}
    	return $insuficiente;
    }

    function verificarCamposDivergentesFicha( $data ){
		$this->PontuacoesStatusCriterio = ClassRegistry::init('PontuacoesStatusCriterio');
		$divergente = 0;
		if(isset($data['FichaStatusCriterio'])){
			foreach( $data['FichaStatusCriterio'] as $codigo_criterio => $dados ){
				if( !empty($dados['codigo_status_criterio']))
					$divergente += $this->PontuacoesStatusCriterio->verificaCampoDivergente( $dados['codigo_status_criterio'] );
			}
		}
    	return $divergente;
    }

    function retornaCamposDivergentesFicha( $data ){
		$this->PontuacoesStatusCriterio = ClassRegistry::init('PontuacoesStatusCriterio');
		$this->Criterio       			= ClassRegistry::init('Criterio');
		$campo_divergente = array();		
		$dados_divergente = array();
		foreach( $data['FichaStatusCriterio'] as $codigo_criterio => $dados ){

			if( !empty($dados['codigo_status_criterio']) ){
				$divergente = $this->PontuacoesStatusCriterio->verificaCampoDivergente($dados['codigo_status_criterio'] );
				if( $divergente ){ 
					$dados = $this->PontuacoesStatusCriterio->retornaCampoDivergente( $dados['codigo_status_criterio'] );
					array_push( $dados_divergente, $dados );
				}
			}
		}
		return $dados_divergente;
    }
    
    function retornaCamposInsuficientesFicha( $data ){
		$this->PontuacoesStatusCriterio = ClassRegistry::init('PontuacoesStatusCriterio');
		$this->Criterio       			= ClassRegistry::init('Criterio');
		$campo_insuficiente = array();
		$dado_insuficiente  = array();
		if( isset($data['FichaStatusCriterio'])){
			foreach( $data['FichaStatusCriterio'] as $codigo_criterio => $dados ){
				if( !empty($dados['codigo_status_criterio']) && is_numeric($dados['codigo_status_criterio']) ){
					$insuficiente = $this->PontuacoesStatusCriterio->verificaCampoInsuficiente( $dados['codigo_status_criterio'] );
					if( $insuficiente ){
						$dados = $this->PontuacoesStatusCriterio->retornaCampoInsuficiente( $dados['codigo_status_criterio'] );
						array_push( $dado_insuficiente, $dados );
					}
				}
			}
		}
		return $dado_insuficiente;
    }

    function verificaAlteracaoCriterio( $dados ){ 
    	$criterios = array();
    	foreach( $dados as $criterio ){    	
    		$automatico = $this->find('first', array(
    			'conditions' => array(
    				'codigo_ficha'			=> $criterio['codigo_ficha'], 
    				'codigo_criterio'		=> $criterio['codigo_criterio'],
    				'codigo_status_criterio'=> $criterio['codigo_status_criterio']
				),
    			'fields' => 'automatico'
    			)
    		);
			$criterio['automatico'] = count($automatico['FichaStatusCriterio']['automatico']);
    		array_push($criterios, $criterio );
    	}
    	return $criterios;
    }

    function preVisualizarClassificacao( $codigo_ficha, $dados ) {
		$this->PontuacoesStatusCriterio = ClassRegistry::init('PontuacoesStatusCriterio');
		$this->ParametroScore 			= ClassRegistry::init('ParametroScore');
		$total_pontos  = 0;
		$insuficiente  = $this->verificarCamposInsuficientesFicha( $dados);
		$divergente    = $this->verificarCamposDivergentesFicha( $dados);
		if( $insuficiente == 0 &&  $divergente == 0 ){
			foreach ($dados['FichaStatusCriterio'] as $codigo_criterio => $values ) {
				$pontos_por_criterio = $this->PontuacoesStatusCriterio->verificaPonto( $values['codigo_status_criterio'] );
				$total_pontos +=$pontos_por_criterio['PontuacoesStatusCriterio']['pontos'];
			}
			$percentual_total = $this->totalPercentualPorFicha($codigo_ficha, $total_pontos);
			$classificacao    = $this->ParametroScore->classificaMotorista($percentual_total);
		} else {
			if( $divergente > 0)
				$total_pontos = -1;
			$percentual_total = $total_pontos;
			$classificacao    = $this->ParametroScore->classificaMotorista($percentual_total, TRUE);
		}
		return compact('classificacao', 'percentual_total');
	}
}