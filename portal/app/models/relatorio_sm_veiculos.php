<?php
class RelatorioSmVeiculos extends AppModel {

	var $name = 'RelatorioSmVeiculos';
	var $useTable = false;
	var $useDbConfig = 'dbtrafegus';
	var $tableSchema = 'public';
	var $databaseTable = 'trafegus';
	

	public function converteFiltrosEmConditions($filtros) {
		App::Import('Component',array('DbbuonnyGuardian'));
		$this->Cliente = ClassRegistry::init('Cliente');

		$conditions = Array();

		$codigo_cliente = $filtros['RelatorioSmVeiculos']['codigo_cliente'];
		
		$dados_cliente = $this->Cliente->carregar($codigo_cliente);
		$tipo_empresa  = $this->Cliente->retornarClienteSubTipo($codigo_cliente);

        $pess_oras_codigo = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($filtros['RelatorioSmVeiculos']['codigo_cliente'], $filtros['RelatorioSmVeiculos']['base_cnpj']);

	    if ($tipo_empresa == Cliente::SUBTIPO_EMBARCADOR) {
	    	$conditions[] = array('TVembVeiculoEmbarcador.vemb_emba_pjur_pess_oras_codigo'=>$pess_oras_codigo);
	    } elseif ($tipo_empresa == Cliente::SUBTIPO_TRANSPORTADOR) {
	    	$conditions[] = array('TVtraVeiculoTransportador.vtra_tran_pess_oras_codigo'=>$pess_oras_codigo);
	    }

        if(!empty($filtros['RelatorioSmVeiculos']['placa'])){
            $conditions[] = array('TVeicVeiculo.veic_placa'=>strtoupper(str_replace('-', '', $filtros['RelatorioSmVeiculos']['placa'])));
        }
    
        if(!empty($filtros['RelatorioSmVeiculos']['codigo_tipo_veiculo'])){
            if (!is_array($filtros['RelatorioSmVeiculos']['codigo_tipo_veiculo'])) {
                $filtros['RelatorioSmVeiculos']['codigo_tipo_veiculo'] = array($filtros['RelatorioSmVeiculos']['codigo_tipo_veiculo']);
            }
            $fitros_aplicar = Array();
            foreach ($filtros['RelatorioSmVeiculos']['codigo_tipo_veiculo'] as $filtro_veiculo) {
                if ($filtro_veiculo!=99) {
                    $fitros_aplicar[] = $filtro_veiculo;
                }/* else {
                    $conditions[] = array('EXISTS(SELECT COUNT(*) FROM vvei_viagem_veiculo WHERE vvei_viag_codigo = viag_codigo GROUP BY vvei_viag_codigo HAVING COUNT(*) > 2 )');
                }*/
            }
            if (count($fitros_aplicar)>0) $conditions[] = array('TVeicVeiculo.veic_tvei_codigo'=>$fitros_aplicar);
        }

        if(!empty($filtros['RelatorioSmVeiculos']['cd_id'])){
		    if ($tipo_empresa == Cliente::SUBTIPO_EMBARCADOR) {
				$conditions[] = array('TVembVeiculoEmbarcador.vemb_refe_codigo_origem'=>$filtros['RelatorioSmVeiculos']['cd_id']);
		    } elseif ($tipo_empresa == Cliente::SUBTIPO_TRANSPORTADOR) {
				$conditions[] = array('TVtraVeiculoTransportador.vtra_refe_codigo_origem'=>$filtros['RelatorioSmVeiculos']['cd_id']);
		    }
        }


        if (isset($filtros['RelatorioSmVeiculos']['posicionando'])) {
            if($filtros['RelatorioSmVeiculos']['posicionando'] == 1)
                $conditions[] = array("TUposUltimaPosicao.upos_data_comp_bordo + interval '120' minute >= NOW()");
            elseif($filtros['RelatorioSmVeiculos']['posicionando'] == 2)
                $conditions[] = array("(TUposUltimaPosicao.upos_data_comp_bordo + interval '120' minute < NOW() OR TUposUltimaPosicao.upos_data_comp_bordo IS NULL)");
        }

        if (!empty($filtros['RelatorioSmVeiculos']['solicitante']) ||
        	!empty($filtros['RelatorioSmVeiculos']['sm']) ||
        	!empty($filtros['RelatorioSmVeiculos']['pedido_cliente']) ||
        	!empty($filtros['RelatorioSmVeiculos']['codigo_status_viagem']) ||
        	!empty($filtros['RelatorioSmVeiculos']['codigo_tipo_transporte']) ||
        	!empty($filtros['RelatorioSmVeiculos']['nf']) ||
        	!empty($filtros['RelatorioSmVeiculos']['loadplan']) ||
        	!empty($filtros['RelatorioSmVeiculos']['cpf']) ||
        	!empty($filtros['RelatorioSmVeiculos']['UFOrigem']) ||
        	!empty($filtros['RelatorioSmVeiculos']['UFDestino']) ||
        	!empty($filtros['RelatorioSmVeiculos']['vrot_codigo'])
        ) {
        	$conditions[] = "UltimaViagem.menor_sm is not null";
        }
        return $conditions;
 
	}

	private function montaConditionsViagens($filtros) {
        $conditions = Array();

		$codigo_cliente = $filtros['RelatorioSmVeiculos']['codigo_cliente'];
		
		$dados_cliente = $this->Cliente->carregar($codigo_cliente);
        $pess_oras_codigo = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($filtros['RelatorioSmVeiculos']['codigo_cliente'], $filtros['RelatorioSmVeiculos']['base_cnpj']);

        $conditions[] = Array(
        	'OR' => Array(
        		'TViagViagem.viag_emba_pjur_pess_oras_codigo' => $pess_oras_codigo,
        		'TViagViagem.viag_tran_pess_oras_codigo' => $pess_oras_codigo
        	)
        );


        if(isset($filtros['RelatorioSmVeiculos']['solicitante']) && !empty($filtros['RelatorioSmVeiculos']['solicitante'])){
            $conditions[] = array('TViagViagem.viag_usuario_adicionou LIKE' => $filtros['RelatorioSmVeiculos']['solicitante']."%");
        }


        if(!empty($filtros['RelatorioSmVeiculos']['sm'])){
            $conditions[] = array('TViagViagem.viag_codigo_sm'=>$filtros['RelatorioSmVeiculos']['sm']);
        }

        if(!empty($filtros['RelatorioSmVeiculos']['pedido_cliente'])){
            $conditions[] = array('TViagViagem.viag_pedido_cliente LIKE'=>$filtros['RelatorioSmVeiculos']['pedido_cliente'].'%');
        }

        if(empty($filtros['RelatorioSmVeiculos']['codigo_status_viagem'])){            
	        if(!empty($filtros['RelatorioSmVeiculos']['!codigo_status_viagem'])){
	            if($filtros['RelatorioSmVeiculos']['!codigo_status_viagem'] == StatusViagem::ENCERRADA){
	                $filtros['RelatorioSmVeiculos']['codigo_status_viagem'] = array(
	                    StatusViagem::AGENDADO,
	                    StatusViagem::EM_TRANSITO,
	                    StatusViagem::ENTREGANDO,
	                    StatusViagem::LOGISTICO
	                );
	            }
	        }
	    }

        if(!empty($filtros['RelatorioSmVeiculos']['codigo_status_viagem'])){            
            if(!is_array($filtros['RelatorioSmVeiculos']['codigo_status_viagem'])){
                $filtros['RelatorioSmVeiculos']['codigo_status_viagem'] = array($filtros['RelatorioSmVeiculos']['codigo_status_viagem']);
            }
            $conditions_status = array();
            foreach($filtros['RelatorioSmVeiculos']['codigo_status_viagem'] as $codigo_status_viagem){
                if($codigo_status_viagem == StatusViagem::CANCELADO)
                    $conditions_status[] = array('vest_estatus'=>'2');
                if($codigo_status_viagem == StatusViagem::AGENDADO)
                    $conditions_status[] = array('AND' => array('OR'=>array('vest_estatus IS NULL', 'vest_estatus'=>'1'), 'viag_status_viagem' => 'N', 'viag_data_inicio IS NULL', 'viag_data_fim IS NULL'));
                if($codigo_status_viagem == StatusViagem::EM_TRANSITO)
                    $conditions_status[] = array('AND' => array('viag_status_viagem' => array('N', 'V'), 'viag_data_inicio IS NOT NULL', 'viag_data_fim IS NULL'));
                if($codigo_status_viagem == StatusViagem::ENTREGANDO)
                    $conditions_status[] = array('AND' => array('viag_status_viagem'=>'D', 'viag_data_fim IS NULL'));
                if($codigo_status_viagem == StatusViagem::LOGISTICO)
                    $conditions_status[] = array('AND' => array('viag_status_viagem'=>'L', 'viag_data_fim IS NULL'));
                if($codigo_status_viagem == StatusViagem::ENCERRADA)
                    $conditions_status[] = array('viag_data_fim IS NOT NULL', 'OR'=>array('vest_estatus IS NULL', 'vest_estatus'=>'1'));
            }
            $conditions[] = array('OR' => $conditions_status);
        }

        if(!empty($filtros['RelatorioSmVeiculos']['codigo_tipo_transporte'])){
            $conditions[] = array('TViagViagem.viag_ttra_codigo'=>$filtros['RelatorioSmVeiculos']['codigo_tipo_transporte']);
        }

        if ((isset($filtros['RelatorioSmVeiculos']['nf']) && !empty($filtros['RelatorioSmVeiculos']['nf'])) || (isset($filtros['RelatorioSmVeiculos']['loadplan']) && !empty($filtros['RelatorioSmVeiculos']['loadplan']))){
            $TVnfiViagemNotaFiscal =& ClassRegistry::init('TVnfiViagemNotaFiscal');
            $subconditions = $TVnfiViagemNotaFiscal->converteFiltrosEmConditions($filtros['RelatorioSmVeiculos']);
            $subquery = $TVnfiViagemNotaFiscal->findSubQuery($subconditions);
            $conditions[] = "viag_codigo IN({$subquery})";
        }

        if(!empty($filtros['RelatorioSmVeiculos']['cpf'])){
           $conditions[] = array('TPfisPessoaFisica.pfis_cpf'=>$filtros['RelatorioSmVeiculos']['cpf']);
        }

        if(!empty($filtros['RelatorioSmVeiculos']['UFOrigem'])){
            $conditions[] = array('"EstadoOrigem"."esta_codigo"'=>$filtros['RelatorioSmVeiculos']['UFOrigem']);
            $conditions[] = array('"TTparTipoParada"."tpar_codigo"'=>'4');
        }

        //$conditions['uf_destino'] = false;
        if(!empty($filtros['RelatorioSmVeiculos']['UFDestino'])){
            $conditions[] = array('"EstadoDestino"."esta_codigo"'=>$filtros['RelatorioSmVeiculos']['UFDestino']);
            //$conditions['uf_destino'] = true;
        }

        if(isset($filtros['RelatorioSmVeiculos']['vrot_codigo']) && !empty($filtros['RelatorioSmVeiculos']['vrot_codigo'])){
            if($filtros['RelatorioSmVeiculos']['vrot_codigo'] == 1){
                $conditions['vrot_codigo <>'] = NULL;
            }else{
                $conditions['vrot_codigo'] = NULL;
            }    
        }

        return $conditions;
	}

	private function montaSubqueryViagens($filtros) {

	    $this->TVveiViagemVeiculo = ClassRegistry::init('TVveiViagemVeiculo');
		$this->TViagViagem = ClassRegistry::init('TViagViagem');
	    $this->TVestViagemEstatus = ClassRegistry::init('TVestViagemEstatus');
	    $this->TPfisPessoaFisica = ClassRegistry::init('TPfisPessoaFisica');
		$this->TVrotViagemRota = ClassRegistry::init('TVrotViagemRota');
	    $this->TVlocViagemLocal = ClassRegistry::init('TVlocViagemLocal');
	    $this->TRefeReferencia = ClassRegistry::init('TRefeReferencia');
	    $this->TCidaCidade = ClassRegistry::init('TCidaCidade');
	    $this->TEstaEstado = ClassRegistry::init('TEstaEstado');
	    $this->TTparTipoParada = ClassRegistry::init('TTparTipoParada');

	    $joins = Array(
			array(
				'table' => "{$this->TVeicVeiculo->databaseTable}.{$this->TVeicVeiculo->tableSchema}.{$this->TVeicVeiculo->useTable}",
				'alias' => 'TVeicVeiculo',
				'conditions' => array('vvei_veic_oras_codigo = veic_oras_codigo'),
				'type' => 'INNER'
		    ),
			array(
				'table' => "{$this->TViagViagem->databaseTable}.{$this->TViagViagem->tableSchema}.{$this->TViagViagem->useTable}",
				'alias' => 'TViagViagem',
				'conditions' => array('vvei_viag_codigo = viag_codigo'),
				'type' => 'LEFT'
		    ),
			array(
				'table' => "{$this->TVestViagemEstatus->databaseTable}.{$this->TVestViagemEstatus->tableSchema}.{$this->TVestViagemEstatus->useTable}",
				'alias' => 'TVestViagemEstatus',
				'conditions' => array('vest_viag_codigo = viag_codigo'),
				'type' => 'LEFT'
		    ),
			array(
				'table' => "{$this->TPfisPessoaFisica->databaseTable}.{$this->TPfisPessoaFisica->tableSchema}.{$this->TPfisPessoaFisica->useTable}",
				'alias' => 'TPfisPessoaFisica',
				'conditions' => array('vvei_moto_pfis_pess_oras_codigo = pfis_pess_oras_codigo'),
				'type' => 'LEFT'
		    ),
			array(
				'table' => "{$this->TVlocViagemLocal->databaseTable}.{$this->TVlocViagemLocal->tableSchema}.{$this->TVlocViagemLocal->useTable}",
				'alias' => 'ViagemLocalOrigem',
				'conditions' => array(
					'ViagemLocalOrigem.vloc_viag_codigo = viag_codigo',
					'ViagemLocalOrigem.vloc_tpar_codigo' => 4,
				),
				'type' => 'LEFT',
			),			
			array(
				'table' => "{$this->TRefeReferencia->databaseTable}.{$this->TRefeReferencia->tableSchema}.{$this->TRefeReferencia->useTable}",
				'alias' => 'ReferenciaOrigem',
				'conditions' => 'ReferenciaOrigem.refe_codigo = ViagemLocalOrigem.vloc_refe_codigo',
				'type' => 'LEFT',
			),

			array(
				'table' => "{$this->TTparTipoParada->databaseTable}.{$this->TTparTipoParada->tableSchema}.{$this->TTparTipoParada->useTable}",
				'alias' => 'TTparTipoParada',
				'conditions' => 'TTparTipoParada.tpar_codigo = ViagemLocalOrigem.vloc_tpar_codigo',
				'type' => 'LEFT',
			),

			array(
				'table' => "{$this->TCidaCidade->databaseTable}.{$this->TCidaCidade->tableSchema}.{$this->TCidaCidade->useTable}",
				'alias' => 'CidadeOrigem',
				'conditions' => 'CidadeOrigem.cida_codigo = ReferenciaOrigem.refe_cida_codigo',
				'type' => 'LEFT',
			),

			array(
				'table' => "{$this->TEstaEstado->databaseTable}.{$this->TEstaEstado->tableSchema}.{$this->TEstaEstado->useTable}",
				'alias' => 'EstadoOrigem',
				'conditions' => 'EstadoOrigem.esta_codigo = CidadeOrigem.cida_esta_codigo',
				'type' => 'LEFT',
			),
			array(
				'table' => "{$this->TVlocViagemLocal->databaseTable}.{$this->TVlocViagemLocal->tableSchema}.{$this->TVlocViagemLocal->useTable}",
				'alias' => 'ViagemLocalDestino',
				'conditions' => array(
					'ViagemLocalDestino.vloc_viag_codigo = viag_codigo',
					'ViagemLocalDestino.vloc_tpar_codigo' => 5,
				),
				'type' => 'LEFT',
			),			
			array(
				'table' => "{$this->TRefeReferencia->databaseTable}.{$this->TRefeReferencia->tableSchema}.{$this->TRefeReferencia->useTable}",
				'alias' => 'ReferenciaDestino',
				'conditions' => 'ReferenciaDestino.refe_codigo = ViagemLocalDestino.vloc_refe_codigo',
				'type' => 'LEFT',
			),

			array(
				'table' => "{$this->TCidaCidade->databaseTable}.{$this->TCidaCidade->tableSchema}.{$this->TCidaCidade->useTable}",
				'alias' => 'CidadeDestino',
				'conditions' => 'CidadeDestino.cida_codigo = ReferenciaDestino.refe_cida_codigo',
				'type' => 'LEFT',
			),

			array(
				'table' => "{$this->TEstaEstado->databaseTable}.{$this->TEstaEstado->tableSchema}.{$this->TEstaEstado->useTable}",
				'alias' => 'EstadoDestino',
				'conditions' => 'EstadoDestino.esta_codigo = CidadeDestino.cida_esta_codigo',
				'type' => 'LEFT',
			),			

			array(
				'table' => "{$this->TVrotViagemRota->databaseTable}.{$this->TVrotViagemRota->tableSchema}.{$this->TVrotViagemRota->useTable}",
				'alias' => 'TVrotViagemRota',
				'type' => 'LEFT',
				'conditions' => "TViagViagem.viag_codigo = TVrotViagemRota.vrot_viag_codigo"
			),

		);

		if ($this->useDbConfig == 'test_suite') {
			$fields = Array(
				'TVveiViagemVeiculo.vvei_veic_oras_codigo',
				'MIN(TViagViagem.viag_codigo_sm) as menor_sm'
			);			
		} else {
			$fields = Array(
				'"TVveiViagemVeiculo".vvei_veic_oras_codigo',
				'MIN("TViagViagem".viag_codigo_sm) as menor_sm'
			);			
		}

		$group = Array(
			'TVveiViagemVeiculo.vvei_veic_oras_codigo'
		);
		/*$order = Array(
			'TVveiViagemVeiculo.vvei_veic_oras_codigo'
		);*/

		$conditions = $this->montaConditionsViagens($filtros);

		$dbo = $this->getDataSource();
		$query = $dbo->buildStatement(
		array(
			'table' => "{$this->TVveiViagemVeiculo->databaseTable}.{$this->TVveiViagemVeiculo->tableSchema}.{$this->TVveiViagemVeiculo->useTable}",
			'alias' => 'TVveiViagemVeiculo',
			'joins' => $joins,
			'fields' => $fields,
			'conditions' => $conditions,
			'order' => null,
			'group' => $group,
			'limit' => null,
			'offset' => null
		)
		, $this);

		return $query;
	}

	private function montaSubqueryUpos($tipo_cliente,$filtros) {
		App::Import('Component',array('DbbuonnyGuardian'));
	    $this->TVembVeiculoEmbarcador = ClassRegistry::init('TVembVeiculoEmbarcador');
	    $this->TVtraVeiculoTransportador = ClassRegistry::init('TVtraVeiculoTransportador');
	    $this->TOrteObjetoRastreadoTermina = ClassRegistry::init('TOrteObjetoRastreadoTermina');
	    $this->TTermTerminal = ClassRegistry::init('TTermTerminal');
	    $this->TUposUltimaPosicao = ClassRegistry::init('TUposUltimaPosicao');
		$this->Cliente = ClassRegistry::init('Cliente');

	    $joins = Array(
			array(
				'table' => "{$this->TTveiTipoVeiculo->databaseTable}.{$this->TTveiTipoVeiculo->tableSchema}.{$this->TTveiTipoVeiculo->useTable}",
				'alias' => 'TTveiTipoVeiculo',
				'conditions' => array('veic_tvei_codigo = tvei_codigo'),
				'type' => 'INNER'
		    ),
			array(
				'table' => "{$this->TOrteObjetoRastreadoTermina->databaseTable}.{$this->TOrteObjetoRastreadoTermina->tableSchema}.{$this->TOrteObjetoRastreadoTermina->useTable}",
				'alias' => 'TOrteObjetoRastreadoTermina',
				'conditions' => array(
					'orte_oras_codigo = veic_oras_codigo',
					'orte_sequencia' => 'P'
				),
				'type' => 'INNER'
		    ),
			array(
				'table' => "{$this->TTermTerminal->databaseTable}.{$this->TTermTerminal->tableSchema}.{$this->TTermTerminal->useTable}",
				'alias' => 'TTermTerminal',
				'conditions' => array('orte_term_codigo = term_codigo'),
				'type' => 'INNER'
		    ),
			array(
				'table' => "{$this->TUposUltimaPosicao->databaseTable}.{$this->TUposUltimaPosicao->tableSchema}.{$this->TUposUltimaPosicao->useTable}",
				'alias' => 'TUposUltimaPosicao',
				'conditions' => array('term_numero_terminal = upos_term_numero_terminal'),
				'type' => 'INNER'
		    ),
		);	
	    if ($tipo_cliente == Cliente::SUBTIPO_EMBARCADOR) {
	    	$joins[] = array(
				'table' => "{$this->TVembVeiculoEmbarcador->databaseTable}.{$this->TVembVeiculoEmbarcador->tableSchema}.{$this->TVembVeiculoEmbarcador->useTable}",
				'alias' => 'TVembVeiculoEmbarcador',
				'conditions' => array('vemb_veic_oras_codigo = veic_oras_codigo'),
				'type' => 'LEFT'
			);
		
	    } elseif ($tipo_cliente == Cliente::SUBTIPO_TRANSPORTADOR) {
	    	$joins[] = array(
				'table' => "{$this->TVtraVeiculoTransportador->databaseTable}.{$this->TVtraVeiculoTransportador->tableSchema}.{$this->TVtraVeiculoTransportador->useTable}",
				'alias' => 'TVtraVeiculoTransportador',
				'conditions' => array('vtra_veic_oras_codigo = veic_oras_codigo'),
				'type' => 'LEFT'
			);
	    }

		$conditions = Array();

		$codigo_cliente = $filtros['RelatorioSmVeiculos']['codigo_cliente'];
		
		$dados_cliente = $this->Cliente->carregar($codigo_cliente);
		$tipo_empresa  = $this->Cliente->retornarClienteSubTipo($codigo_cliente);

        $pess_oras_codigo = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($filtros['RelatorioSmVeiculos']['codigo_cliente'], $filtros['RelatorioSmVeiculos']['base_cnpj']);

	    if ($tipo_empresa == Cliente::SUBTIPO_EMBARCADOR) {
	    	$conditions[] = array('TVembVeiculoEmbarcador.vemb_emba_pjur_pess_oras_codigo'=>$pess_oras_codigo);
	    } elseif ($tipo_empresa == Cliente::SUBTIPO_TRANSPORTADOR) {
	    	$conditions[] = array('TVtraVeiculoTransportador.vtra_tran_pess_oras_codigo'=>$pess_oras_codigo);
	    }

		if ($this->useDbConfig == 'test_suite') {
			$fields = Array(
				'TUposUltimaPosicao.*',
			);			
		} else {
			$fields = Array(
				'"TUposUltimaPosicao".*',
			);			
		}


		$dbo = $this->getDataSource();
		$query = $dbo->buildStatement(
		array(
			'table' => "{$this->TVeicVeiculo->databaseTable}.{$this->TVeicVeiculo->tableSchema}.{$this->TVeicVeiculo->useTable}",
			'alias' => 'TVeicVeiculo',
			'joins' => $joins,
			'fields' => $fields,
			'conditions' => $conditions,
			'order' => null,
			'group' => null,
			'limit' => null,
			'offset' => null
		)
		, $this);

		return $query;



	}

	private function montaJoinsVeiculoMapaGR($tipo_cliente,$filtros) {
	    $this->TVembVeiculoEmbarcador = ClassRegistry::init('TVembVeiculoEmbarcador');
	    $this->TVtraVeiculoTransportador = ClassRegistry::init('TVtraVeiculoTransportador');
	    $this->TViagViagem = ClassRegistry::init('TViagViagem');
	    $this->TVestViagemEstatus = ClassRegistry::init('TVestViagemEstatus');
	    $this->TVlocViagemLocal = ClassRegistry::init('TVlocViagemLocal');
	    $this->TVlevViagemLocalEvento = ClassRegistry::init('TVlevViagemLocalEvento');
	    $this->TVveiViagemVeiculo = ClassRegistry::init('TVveiViagemVeiculo');


	    $this->TOrteObjetoRastreadoTermina = ClassRegistry::init('TOrteObjetoRastreadoTermina');
	    $this->TTermTerminal = ClassRegistry::init('TTermTerminal');
	    $this->TUposUltimaPosicao = ClassRegistry::init('TUposUltimaPosicao');
		$this->TVtecVersaoTecnologia = ClassRegistry::init('TVtecVersaoTecnologia');
		$this->TTecnTecnologia = ClassRegistry::init('TTecnTecnologia');
		$this->TTveiTipoVeiculo = ClassRegistry::init('TTveiTipoVeiculo');
		$this->TPessPessoa = ClassRegistry::init('TPessPessoa');

		ClassRegistry::init('Cliente');

	    $joins = Array(
			array(
				'table' => "{$this->TTveiTipoVeiculo->databaseTable}.{$this->TTveiTipoVeiculo->tableSchema}.{$this->TTveiTipoVeiculo->useTable}",
				'alias' => 'TTveiTipoVeiculo',
				'conditions' => array('veic_tvei_codigo = tvei_codigo'),
				'type' => 'INNER'
		    ),
			array(
				'table' => "{$this->TOrteObjetoRastreadoTermina->databaseTable}.{$this->TOrteObjetoRastreadoTermina->tableSchema}.{$this->TOrteObjetoRastreadoTermina->useTable}",
				'alias' => 'TOrteObjetoRastreadoTermina',
				'conditions' => array(
					'orte_oras_codigo = veic_oras_codigo',
					'orte_sequencia' => 'P'
				),
				'type' => 'LEFT'
		    ),
			array(
				'table' => "{$this->TTermTerminal->databaseTable}.{$this->TTermTerminal->tableSchema}.{$this->TTermTerminal->useTable}",
				'alias' => 'TTermTerminal',
				'conditions' => array('orte_term_codigo = term_codigo'),
				'type' => 'LEFT'
		    ),
			array(
				'table' => "{$this->TVtecVersaoTecnologia->databaseTable}.{$this->TVtecVersaoTecnologia->tableSchema}.{$this->TVtecVersaoTecnologia->useTable}",
				'alias' => 'TVtecVersaoTecnologia',
				'conditions' => array('term_vtec_codigo = vtec_codigo'),
				'type' => 'LEFT'
		    ),
			array(
				'table' => "{$this->TTecnTecnologia->databaseTable}.{$this->TTecnTecnologia->tableSchema}.{$this->TTecnTecnologia->useTable}",
				'alias' => 'TTecnTecnologia',
				'conditions' => array('vtec_tecn_codigo = tecn_codigo'),
				'type' => 'LEFT'
		    ),
			array(
				'table' => "({$this->montaSubqueryUpos($tipo_cliente,$filtros)})",
				'alias' => 'TUposUltimaPosicao',
				'conditions' => array('term_numero_terminal = upos_term_numero_terminal'),
				'type' => 'LEFT'
		    ),
			array(
				'table' => "({$this->montaSubqueryViagens($filtros)})",
				'alias' => 'UltimaViagem',
				'conditions' => array('UltimaViagem.vvei_veic_oras_codigo = veic_oras_codigo'),
				'type' => 'LEFT'
		    ),		    
			array(
				'table' => "{$this->TViagViagem->databaseTable}.{$this->TViagViagem->tableSchema}.{$this->TViagViagem->useTable}",
				'alias' => 'TViagViagem',
				'conditions' => array('UltimaViagem.menor_sm = TViagViagem.viag_codigo_sm'),
				'type' => 'LEFT'
		    ),
			array(
				'table' => "{$this->TVestViagemEstatus->databaseTable}.{$this->TVestViagemEstatus->tableSchema}.{$this->TVestViagemEstatus->useTable}",
				'alias' => 'TVestViagemEstatus',
				'conditions' => array('TVestViagemEstatus.vest_viag_codigo = TViagViagem.viag_codigo'),
				'type' => 'LEFT'
		    ),
		    array(
				'table' => "{$this->TPjurPessoaJuridica->databaseTable}.{$this->TPjurPessoaJuridica->tableSchema}.{$this->TPjurPessoaJuridica->useTable}",
				'alias' => 'Embarcador',
				'type' => 'LEFT',
				'conditions' => "TViagViagem.viag_emba_pjur_pess_oras_codigo = Embarcador.pjur_pess_oras_codigo"
			),
			array(
				'table' => "{$this->TPjurPessoaJuridica->databaseTable}.{$this->TPjurPessoaJuridica->tableSchema}.{$this->TPjurPessoaJuridica->useTable}",
				'alias' => 'Transportador',
				'type' => 'LEFT',
				'conditions' => "TViagViagem.viag_tran_pess_oras_codigo = Transportador.pjur_pess_oras_codigo"
			),
			array(
				'table' => "{$this->TVlocViagemLocal->databaseTable}.{$this->TVlocViagemLocal->tableSchema}.{$this->TVlocViagemLocal->useTable}",
				'alias' => 'TVlocViagemLocal',
				'conditions' => array(
					'TVlocViagemLocal.vloc_viag_codigo = viag_codigo',
					'TVlocViagemLocal.vloc_status_viagem' => 'D',
				),
				'type' => 'LEFT',
			),	
			array(
				'table' => "{$this->TRefeReferencia->databaseTable}.{$this->TRefeReferencia->tableSchema}.{$this->TRefeReferencia->useTable}",
				'alias' => 'TRefeReferencia',
				'conditions' => array(
					'TVlocViagemLocal.vloc_refe_codigo = TRefeReferencia.refe_codigo',
				),
				'type' => 'LEFT',
			),	
			array(
				'table' => "{$this->TVlevViagemLocalEvento->databaseTable}.{$this->TVlevViagemLocalEvento->tableSchema}.{$this->TVlevViagemLocalEvento->useTable}",
				'alias' => 'TVlevViagemLocalEvento',
				'conditions' => array(
					'TVlevViagemLocalEvento.vlev_vloc_codigo = TVlocViagemLocal.vloc_codigo',
					'TVlevViagemLocalEvento.vlev_tlev_codigo' => 1,
				),
				'type' => 'LEFT',
			),	
			array(
				'table' => "{$this->TVveiViagemVeiculo->databaseTable}.{$this->TVveiViagemVeiculo->tableSchema}.{$this->TVveiViagemVeiculo->useTable}",
				'alias' => 'TVveiViagemVeiculo',
				'conditions' => array(
					'TVveiViagemVeiculo.vvei_viag_codigo = TViagViagem.viag_codigo',
					'TVveiViagemVeiculo.vvei_veic_oras_codigo = TVeicVeiculo.veic_oras_codigo',
				),
				'type' => 'LEFT',
			),				
			array(
				'table' => "{$this->TPessPessoa->databaseTable}.{$this->TPessPessoa->tableSchema}.{$this->TPessPessoa->useTable}",
				'alias' => 'TPessPessoa',
				'conditions' => array(
					'TPessPessoa.pess_oras_codigo = TVveiViagemVeiculo.vvei_moto_pfis_pess_oras_codigo',
				),
				'type' => 'LEFT',
			),						
	    );
	    if ($tipo_cliente == Cliente::SUBTIPO_EMBARCADOR) {
	    	$joins[] = array(
				'table' => "{$this->TVembVeiculoEmbarcador->databaseTable}.{$this->TVembVeiculoEmbarcador->tableSchema}.{$this->TVembVeiculoEmbarcador->useTable}",
				'alias' => 'TVembVeiculoEmbarcador',
				'conditions' => array('vemb_veic_oras_codigo = veic_oras_codigo'),
				'type' => 'LEFT'
			);
		
	    } elseif ($tipo_cliente == Cliente::SUBTIPO_TRANSPORTADOR) {
	    	$joins[] = array(
				'table' => "{$this->TVtraVeiculoTransportador->databaseTable}.{$this->TVtraVeiculoTransportador->tableSchema}.{$this->TVtraVeiculoTransportador->useTable}",
				'alias' => 'TVtraVeiculoTransportador',
				'conditions' => array('vtra_veic_oras_codigo = veic_oras_codigo'),
				'type' => 'LEFT'
			);
	    }
	    return $joins;
	}

	public function listaVeiculosMapaGR($filtros) {
		App::import('Component','DbbuonnyGuardian');
		ClassRegistry::init('StatusViagem');

		$this->TVeicVeiculo = ClassRegistry::init('TVeicVeiculo');
		$this->TViagViagem = ClassRegistry::init('TViagViagem');
		$this->Cliente = ClassRegistry::init('Cliente');
		$this->TUposUltimaPosicao = ClassRegistry::init('TUposUltimaPosicao');
		$this->DbbuonnyGuardian = new DbbuonnyGuardianComponent();

		$filtros['RelatorioSmVeiculos']['!codigo_status_viagem'] = StatusViagem::ENCERRADA;

		$codigo_cliente = $filtros['RelatorioSmVeiculos']['codigo_cliente'];
		
		$dados_cliente = $this->Cliente->carregar($codigo_cliente);
		$tipo_empresa  = $this->Cliente->retornarClienteSubTipo($codigo_cliente);
		list($pess_oras_codigo_centralizador, $pess_oras_codigo) = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardianComCentralizador($filtros['RelatorioSmVeiculos']['codigo_cliente'], $filtros['RelatorioSmVeiculos']['base_cnpj']);

		$conditions = $this->converteFiltrosEmConditions($filtros);
		$joins = $this->montaJoinsVeiculoMapaGR($tipo_empresa,$filtros);




		if ($this->useDbConfig == 'test_suite') {
			$sql_refe_descricao = "(
				select top 1 %s from {$this->TRefeReferencia->databaseTable}.{$this->TRefeReferencia->tableSchema}.{$this->TRefeReferencia->useTable} TRefeReferenciaIn
				where TRefeReferenciaIn.refe_pess_oras_codigo_local in (".implode(',', $pess_oras_codigo).")
					and TUposUltimaPosicao.upos_latitude between TRefeReferenciaIn.refe_latitude_min and TRefeReferenciaIn.refe_latitude_max
					and TUposUltimaPosicao.upos_longitude between TRefeReferenciaIn.refe_longitude_min and TRefeReferenciaIn.refe_longitude_max
					and TRefeReferenciaIn.refe_cref_codigo in (47,33,39,50)
				
			)";
			$fields = Array(
				'TVeicVeiculo.veic_placa',
				'TVeicVeiculo.veic_chassi',
				'Embarcador.pjur_razao_social as embarcador',
				'Transportador.pjur_razao_social as transportador',
				'TTecnTecnologia.tecn_descricao',
				'TTermTerminal.term_numero_terminal',
				'TTveiTipoVeiculo.tvei_descricao',
				'TUposUltimaPosicao.upos_codigo', 
	            'TUposUltimaPosicao.upos_descricao_integracao', 
				'TUposUltimaPosicao.upos_descricao_sistema', 
				'TUposUltimaPosicao.upos_data_comp_bordo', 
				'TUposUltimaPosicao.upos_latitude', 
				'TUposUltimaPosicao.upos_longitude',
				"{$this->TViagViagem->getQueryStatus('Sem Viagem')}",
				'CASE WHEN TRefeReferencia.refe_descricao IS NULL THEN '.(sprintf($sql_refe_descricao,'refe_descricao')).' ELSE TRefeReferencia.refe_descricao END AS refe_descricao',
				'CASE WHEN TRefeReferencia.refe_latitude IS NULL THEN '.(sprintf($sql_refe_descricao,'refe_latitude')).' ELSE TRefeReferencia.refe_latitude END AS refe_latitude',
				'CASE WHEN TRefeReferencia.refe_longitude IS NULL THEN '.(sprintf($sql_refe_descricao,'refe_longitude')).' ELSE TRefeReferencia.refe_longitude END AS refe_longitude',
				'TVlevViagemLocalEvento.vlev_data',
				'TViagViagem.viag_codigo',
				'TViagViagem.viag_codigo_sm',
				'CONVERT(varchar,TViagViagem.viag_data_inicio,120) as viag_data_inicio',
				'CONVERT(varchar,TViagViagem.viag_data_fim,120) as viag_data_fim',
				'CONVERT(varchar,TViagViagem.viag_previsao_inicio,120) as viag_previsao_inicio',
				'CONVERT(varchar,TViagViagem.viag_previsao_fim,120) as viag_previsao_fim',
				'TPessPessoa.pess_nome'
			);
		} else {
			$sql_refe_descricao = "(
				select %s from {$this->TRefeReferencia->databaseTable}.{$this->TRefeReferencia->tableSchema}.{$this->TRefeReferencia->useTable} TRefeReferenciaIn
				where TRefeReferenciaIn.refe_pess_oras_codigo_local in (".implode(',', $pess_oras_codigo).")
					and \"TUposUltimaPosicao\".upos_latitude between TRefeReferenciaIn.refe_latitude_min and TRefeReferenciaIn.refe_latitude_max
					and \"TUposUltimaPosicao\".upos_longitude between TRefeReferenciaIn.refe_longitude_min and TRefeReferenciaIn.refe_longitude_max
					and TRefeReferenciaIn.refe_cref_codigo in (47,33,39,50)
				limit 1
			)";			
			$fields = Array(
				'"TVeicVeiculo".veic_placa',
				'"TVeicVeiculo".veic_chassi',
				'"Embarcador".pjur_razao_social as embarcador',
				'"Transportador".pjur_razao_social as transportador',
				'"TTecnTecnologia".tecn_descricao',
				'"TTermTerminal".term_numero_terminal',
				'"TTveiTipoVeiculo".tvei_descricao',
				'"TUposUltimaPosicao".upos_codigo', 
	            '"TUposUltimaPosicao".upos_descricao_integracao', 
				'"TUposUltimaPosicao".upos_descricao_sistema', 
				'"TUposUltimaPosicao".upos_data_comp_bordo', 
				'"TUposUltimaPosicao".upos_latitude', 
				'"TUposUltimaPosicao".upos_longitude',
				"{$this->TViagViagem->getQueryStatus('Sem Viagem')}",
				'CASE WHEN "TRefeReferencia"."refe_descricao" IS NULL THEN '.(sprintf($sql_refe_descricao,'refe_descricao')).' ELSE "TRefeReferencia"."refe_descricao" END AS refe_descricao',
				'CASE WHEN "TRefeReferencia"."refe_latitude" IS NULL THEN '.(sprintf($sql_refe_descricao,'refe_latitude')).' ELSE "TRefeReferencia"."refe_latitude" END AS refe_latitude',
				'CASE WHEN "TRefeReferencia"."refe_longitude" IS NULL THEN '.(sprintf($sql_refe_descricao,'refe_longitude')).' ELSE "TRefeReferencia"."refe_longitude" END AS refe_longitude',
				'"TVlevViagemLocalEvento".vlev_data',
				'"TViagViagem".viag_codigo',
				'"TViagViagem".viag_codigo_sm',
				'"TViagViagem".viag_data_inicio',
				'"TViagViagem".viag_data_fim',
				'"TViagViagem".viag_previsao_inicio',
				'"TViagViagem".viag_previsao_fim',
				'"TPessPessoa".pess_nome'
			);
		}
		$dbo = $this->getDataSource();
		$query = $dbo->buildStatement(
		array(
			'table' => "{$this->TVeicVeiculo->databaseTable}.{$this->TVeicVeiculo->tableSchema}.{$this->TVeicVeiculo->useTable}",
			'alias' => 'TVeicVeiculo',
			'joins' => $joins,
			'fields' => $fields,
			'conditions' => $conditions,
			'order' => Array('veic_placa'),
			'group' => null,
			'limit' => null,
			'offset' => null
		)
		, $this);

		$veiculos = $this->query($query);
		//debug($veiculos);
		//die($veiculos);

		return $veiculos;
	}
}