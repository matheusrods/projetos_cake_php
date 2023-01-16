<?php
class FichasStatusCriteriosLogController extends AppController {
	var $name = 'FichasStatusCriteriosLog';
	public $components = array('Session','Fichas');
	public $uses = array(
		'MRmaEstatistica',
		'MRmaOcorrencia',
		'FichaStatusCriterioLog',
		'MGeradorOcorrencia',
		'Sinistro',
		'FichaScorecardLog',
		'ArtigoCriminal',
		'TipoOcorrenciaTeleconsult',
		'FichaStatusCriterio',
		'Ficha',
		'FichaPesquisa',
		'PontuacoesStatusCriterio',
		'Criterio',
		'StatusCriterio',
		'ParametroScore',
		'TipoRetorno', 
		'Usuario', 
		'FichaScorecard', 
		'FichaScorecardRetorno', 
		'ProfissionalTipo', 
		'EnderecoEstado', 
		'QuestaoResposta',
    	'TipoCnh', 
    	'TipoContato', 
    	'VEndereco', 
    	'Profissional', 
    	'ProfissionalEndereco', 
    	'ProfissionalContato', 
    	'ProfissionalTelecheque',
    	'ProfissionalSerasa',
    	'ProfissionalNegativacao',
    	'Proprietario',  
    	'ProprietarioEndereco',
    	'ProprietarioContato',
    	'ProprietarioTelecheque', 
    	'ProprietarioSerasa',
    	'Tecnologia', 
    	'VeiculoCor', 
    	'VeiculoFabricante', 
    	'VeiculoModelo', 
    	'EnderecoCidade', 
    	'CargaTipo',
    	'CargaValor', 
    	'FichaScorecardQuestao', 
    	'Status', 
    	'Veiculo',
    	'VeiculoOcorrencia', 
    	'FichaScorecardQuestaoResp', 
    	'Cliente', 
    	'Seguradora',
    	'FichaScProfContatoLog', 
    	'FichaScorecardVeiculo', 
    	'FichaScorecardStatus', 
    	'VeiculoLog', 
    	'ProprietarioLog',
    	'EmbarcadorTransportador', 
    	'FichaScVeicPropContatoLog', 
    	'ProfissionalContatoLog', 
    	'ProprietarioContatoLog',
		'FichaScorecardLog',
		'Produto',
		'ProfissionalLog',
		'TipoOperacao',
		'LogAtendimento'		
		//'AcessoScorecard'
	);

	function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('visualizar'));
	}
	var $helpers = array('Paginator','Html','Form');

	function retornaScoreProfissional( $codigo_parametro_score, $codigo_profissional_tipo ){
		$status_anterior_profissional = NULL;
		if( $codigo_parametro_score && $codigo_profissional_tipo ){
			$score_ficha = $this->ParametroScore->carregar( $codigo_parametro_score );
			$pontos      = (isset($score_ficha['ParametroScore']['pontos']) ? $score_ficha['ParametroScore']['pontos'] :  NULL );
			if(  $codigo_profissional_tipo < 5 ){//Profissional que possui veiculo 
				$status_anterior_profissional = isset($score_ficha['ParametroScore']['nivel']) ? $score_ficha['ParametroScore']['nivel'] : NULL;
				if( $pontos && $pontos > 0 )
					$status_anterior_profissional .= " ( R$: ". number_format( $score_ficha['ParametroScore']['valor'], 2, ',', '.') ." )";
			} else {
				$status_anterior_profissional = (isset($score_ficha['ParametroScore']['pontos']) ? ( $score_ficha['ParametroScore']['pontos'] > 0 ? 'Adequado' : 'Divergente' ) : NULL );
			}
		}
		return $status_anterior_profissional;
	}	

	private function carrega_cabecalho_ficha( $codigo ){
		$FichaScorecardVeiculo 	= ClassRegistry::init('FichaScorecardVeiculo');
		$dados_ficha 			= $this->FichaScorecardLog->carregar( $codigo );
		$codigo_profissional    = $this->FichaScorecard->buscaCodigoProfissional( $dados_ficha['FichaScorecardLog']['codigo_ficha_scorecard'] );
		$profissional 		    = $this->Profissional->carregar($codigo_profissional);		
		$codigo_cliente 	    = $dados_ficha['FichaScorecardLog']['codigo_cliente'];
		$cliente 			    = $this->Cliente->carregar($codigo_cliente);
		$criterios_categoria    = $this->PontuacoesStatusCriterio->listarCriteriosCategoriaLog( $dados_ficha );
		$resumo_ficha		    = $dados_ficha['FichaScorecardLog']['resumo'];
		$validade_ult_ficha     = $this->FichaScorecard->buscaValidade( $dados_ficha['FichaScorecardLog']['codigo_ficha_scorecard'] );
		$embarcador          	= $this->FichaScorecard->buscaEmbarcador( $dados_ficha['FichaScorecardLog']['codigo_ficha_scorecard'] );
		$transportador       	= $this->FichaScorecard->buscaTransportador( $dados_ficha['FichaScorecardLog']['codigo_ficha_scorecard'] );
		//Ultima ficha Independente do CLIENTE
		$ultima_ficha		    = $this->FichaScorecard->carregaFichaAnteriorProfissional( $codigo_profissional, FALSE, $dados_ficha['FichaScorecardLog']['codigo_ficha_scorecard'] );
		$ultima_consulta     	= isset($ultima_ficha['FichaScorecard']['data_inclusao']) ? $ultima_ficha['FichaScorecard']['data_inclusao'] : NULL;
		$codigo_ultima_ficha 	= isset($ultima_ficha['FichaScorecard']['codigo']) ? $ultima_ficha['FichaScorecard']['codigo'] : NULL;
		$penultima_ficha     = NULL;
		$criterios           = array();
		$status_anterior_profissional_proprietario = NULL;
		//Ultima ficha Independente do CLIENTE
		if( $codigo_ultima_ficha )
			$penultima_ficha = $this->FichaScorecard->carregaFichaAnteriorProfissional( $codigo_profissional, FALSE, $codigo_ultima_ficha );
		$status_anterior_profissional = $this->retornaScoreProfissional( $ultima_ficha['FichaScorecard']['codigo_parametro_score'], $ultima_ficha['FichaScorecard']['codigo_profissional_tipo'] );

		if( $ultima_ficha ){
			$dados_scorecard_veiculo = $FichaScorecardVeiculo->find('first', array('conditions' => array('FichaScorecardVeiculo.codigo_ficha_scorecard'=> $ultima_ficha['FichaScorecard']['codigo'] )) );
			$codigo_proprietario_log = $dados_scorecard_veiculo['FichaScorecardVeiculo']['codigo_proprietario_log'];
			$dados_proprietario = $this->ProprietarioLog->find('first', array('conditions' => array( 'ProprietarioLog.codigo' => $codigo_proprietario_log) ));
			if( isset($dados_proprietario['Proprietario']['codigo_documento']) ){
				$profissional_proprietario = $this->Profissional->buscaPorCPF( $dados_proprietario['Proprietario']['codigo_documento'] );		
				$codigo_profissional_proprietario = $profissional_proprietario['Profissional']['codigo'];
				$ultima_ficha_proprietario = $this->FichaScorecard->carregaFichaAnteriorProfissional( $codigo_profissional_proprietario, NULL, $dados_ficha['FichaScorecardLog']['codigo_ficha_scorecard'] );
				$status_anterior_profissional_proprietario = $this->retornaScoreProfissional( $ultima_ficha_proprietario['FichaScorecard']['codigo_parametro_score'], $ultima_ficha_proprietario['FichaScorecard']['codigo_profissional_tipo'] );
			}
		}
		if( !empty($criterios_categoria) ){
			foreach( $criterios_categoria as $key=> $dados ) {
				$criterios[$dados['codigo']] = $dados;
			}
		}
		$criterios = $this->organiza_criterios_score( $dados_ficha['FichaScorecardLog']['codigo_ficha_scorecard'], $criterios );

		$this->set(compact('penultima_ficha','ultima_consulta','transportador','embarcador','validade_ult_ficha','codigo_cliente','codigo_ficha','dados_ficha',
			'profissional','cliente','criterios','resumo_ficha', 'status_anterior_profissional', 'ultima_ficha', 'status_anterior_profissional_proprietario'));
	}

	function organiza_criterios_score( $codigo_ficha, $criterios ){
		$this->FichaScorecardVeiculo =& ClassRegistry::init('FichaScorecardVeiculo');
		$veiculos = $this->FichaScorecardVeiculo->find('list', array('fields'=>'tipo','conditions'=>array('codigo_ficha_scorecard'=>$codigo_ficha), 'order'=>array('tipo ASC')));
		$tipo_veiculos = array( 0 =>FALSE, 1=>FALSE, 2=>FALSE );
		if( isset($veiculos) && count($veiculos) > 0 ){
			foreach( $veiculos as $codigo_criterio ){
				if( isset($codigo_criterio) ){
					$tipo_veiculos[$codigo_criterio] = TRUE;
				}
			}
			foreach( $tipo_veiculos as $tipo_veiculo => $value ){
				if( $value === FALSE )
					$criterios = $this->remove_criterio_veiculo( $criterios, $tipo_veiculo );
			}
		} else {//Nao tem veiculo
			foreach ( array(5,6,23,25,27,29,21,26,28) as $codigo_criterio ){
				unset($criterios[$codigo_criterio]);
			}
		}
		return $criterios;
	}
	
	function remove_criterio_veiculo( $criterios, $tipo_veiculo ){
		switch ( $tipo_veiculo ) {
			case 1:
				$criterios_carreta = array(21,26,28);
				foreach ( $criterios_carreta as $codigo_criterio ){
					unset($criterios[$codigo_criterio]);
				}
				break;			
			case 2:
				$bitrem  = array(25,27,29);
				foreach ( $bitrem as $codigo_criterio ){
					unset($criterios[$codigo_criterio]);
				}
				break;			
		}
		return $criterios;
	}

    function visualizar($codigo){
    	$this->pageTitle = 'Dados da Ficha Log - Scorecard';
		$conditions  = array( 'FichaScorecardLog.codigo' => $codigo );
		$dados_ficha = $this->FichaScorecardLog->carregarFichaCompleta( $conditions );
		$this->carrega_cabecalho_ficha( $codigo );
		$codigo_ficha = $dados_ficha['FichaScorecardLog']['codigo_ficha_scorecard'];
		$pontuacao  = $this->FichaScorecardLog->buscarPontuacao( $codigo );
		$this->data	= $this->FichaStatusCriterioLog->listarRespostasFicha( $codigo );
		$this->Fichas->carregarDadosFicha( $dados_ficha['FichaScorecardLog']['codigo_ficha_scorecard'] );
		$this->Fichas->carregarCombos();
        //Retorna um array contendo o criterio que esta insuficiente		
		$campos_insuficientes = $this->FichaStatusCriterio->retornaCamposInsuficientesFicha($this->data);
        $campos_divergentes   = $this->FichaStatusCriterio->retornaCamposDivergentesFicha($this->data);
        if ( count($campos_insuficientes) == 0 && $pontuacao['codigo_parametro_score'] == ParametroScore::INSUFICIENTE )
           $campos_insuficientes[0] = 'O Status da ficha foi alterada para insuficiente manualmente.';
        if (count($campos_divergentes) == 0 && $pontuacao['codigo_parametro_score'] == ParametroScore::DIVERGENTE)
        	$campos_divergentes[0] = 'O Status da ficha foi alterada para divergente manualmente.';
        $this->set(compact('campos_divergentes','campos_insuficientes','dados_parametros','referer','codigo_ficha','pontuacao'));
    }

}
?>