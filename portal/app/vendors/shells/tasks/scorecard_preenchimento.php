<?php
App::import('Component', 'Bcb');
//App::import('Component', 'Serasa');
class ScorecardPreenchimentoTask extends Shell {
	var $bcbComponent = null;	
	public function processar($codigo_ficha) {
		App::import('Model', 'FichaScorecardStatus');
		ClassRegistry::init('Criterio');
	    ClassRegistry::init('StatusCriterio');
	    App::import('Model','MGeradorOcorrencia');
        App::import('Model','MRmaOcorrencia');
        App::import('Model','MRmaEstatistica');
        App::import('Model','ClientEmpresa');	     
	    $ficha 			  = $this->fichaScorecard()->findExtracaoPorCodigo($codigo_ficha);
        $ficha_alterada   = $this->fichaScorecard()->carrega_ficha_robo($codigo_ficha);        
        $ficha_ocorrencia = $this->fichaScorecard()->carrega_ocorrencias_robo($codigo_ficha);
	    //$this->processarUltimaFicha($ficha);
        try{
            //Serasa Proprietario Carreta,Bitrem,Cavalo e Motorista
		    $this->processarBcb($ficha,$ficha_alterada); 
	        $this->processarIdadeProfissional($ficha); 
	        //TeleCheque foi comentado pois esta trazendo informações desnecessárias Pedido Camarinho 04/04/2014
		    //$this->processarSqlsTelecheque($ficha);
		    $this->processarSqlsTeleconsult($ficha);
		    $this->processarSqlsTeleconsultFaturamentoConsultas($ficha);
		    $this->processarSqlsTeleconsultFaturamentoAtualizacoes($ficha);		    
		    $this->processarSqlsViagensBsat($ficha);		    
		    $this->processarExtracaoCNH($ficha);
		    $this->processarExtracaoVeiculo($ficha);
		    $this->processarExtracaoStj($ficha);
	        $this->processarOcorrenciaVeiculo($ficha,$ficha_ocorrencia);  	         
		    //Trazer respostas de outras perguntas conforme ultima ficha preenchida
		    $this->fichaScorecard()->robo_respostas_ultima_ficha($codigo_ficha);		    
            $this->processarSqlsViagensRma($ficha);
		    //Atualiza Resumo Marginado
		    $this->processarSqlTeleconsultMarginado($ficha);
		    //Pesquisador Automatico 
            if(!$this->fichaScorecard()->pesquisador_automatico_scorecard($codigo_ficha)){
            	// Atualiza Status
            	$this->fichaScorecard()->atualizaStatus($codigo_ficha, FichaScorecardStatus::A_PESQUISAR); 
        	}
		    
		}catch(Exception $e){
            echo "Exceção pega RoboPreenchimentoScorecard : Scorecard (Função : processar(alteraStatus)) ->",  $e->getMessage(), "\n";
        }     

	}

	
    public function processarOcorrenciaVeiculo($ficha, $ficha_ocorrencia){
          $msg_ocorrencia_vei = 'Existem '.$ficha_ocorrencia['ocorrencia_veiculo_qtd'].' ocorrências,verificar no histórico de ocorrências.';
          //Veículo
          if($ficha_ocorrencia['ocorrencia_veiculo_qtd']==0){ 
          	//sem ocorrencia
          	ClassRegistry::init('FichaStatusCriterio')->salvarStatus2($ficha['FichaScorecard']['codigo'],6,30,true,'');
          }else{
          	//com ocorrencia
          	ClassRegistry::init('FichaStatusCriterio')->salvarStatus2($ficha['FichaScorecard']['codigo'],6,34,true,$msg_ocorrencia_vei);
          }
          
           $msg_ocorrencia_car = 'Existem '.$ficha_ocorrencia['ocorrencia_carreta_qtd'].' ocorrências,verificar no histórico de ocorrências.';
         
          //Carreta
          if($ficha_ocorrencia['ocorrencia_carreta_qtd'] ==0){
          	//sem ocorrencia
          	ClassRegistry::init('FichaStatusCriterio')->salvarStatus2($ficha['FichaScorecard']['codigo'],28, 152,true,'');
          }else{
          	//com ocorrencia
          	ClassRegistry::init('FichaStatusCriterio')->salvarStatus2($ficha['FichaScorecard']['codigo'],28, 156,true,$msg_ocorrencia_car);
       
          }
          
          $msg_ocorrencia_bi = 'Existem '.$ficha_ocorrencia['ocorrencia_bitrem_qtd'].' ocorrências,verificar no histórico de ocorrências.';
         
          //Bitrem
          if($ficha_ocorrencia['ocorrencia_bitrem_qtd'] ==0){
          	//sem ocorrencia
          	ClassRegistry::init('FichaStatusCriterio')->salvarStatus2($ficha['FichaScorecard']['codigo'], 29, 157,true,'');
          }else{
          	//com ocorrencia
          	ClassRegistry::init('FichaStatusCriterio')->salvarStatus2($ficha['FichaScorecard']['codigo'], 29, 161,true,$msg_ocorrencia_bi);
          } 
          
    } 

	public function processarBcb($ficha, $ficha_alterada) {
	   $consultas_realizadas_serasa = $ficha_alterada['consultas_realizadas_serasa'];//Array retorna se a consulta for realizado com sucesso ou nao

		if( $ficha_alterada['serasa_motorista_msg'] ==" NADA CONSTA") {
			ClassRegistry::init('FichaStatusCriterio')->salvarStatus2($ficha['FichaScorecard']['codigo'], 3, 120, $consultas_realizadas_serasa['motorista'],''); //retirado a pedido do Nelson Ota  $ficha_alterada['serasa_motorista_msg'] 
		} else {
			ClassRegistry::init('FichaStatusCriterio')->salvarStatus2($ficha['FichaScorecard']['codigo'], 3, 12, $consultas_realizadas_serasa['motorista'], $ficha_alterada['serasa_motorista_msg']);
		}
       
		if (trim($ficha_alterada['serasa_proprietario_vei_msg']) !='NAOPOSSUIVEICULO') {
			if ($ficha_alterada['serasa_proprietario_vei_msg']==" NADA CONSTA") {
				ClassRegistry::init('FichaStatusCriterio')->salvarStatus2($ficha['FichaScorecard']['codigo'], 23, 131,$consultas_realizadas_serasa['proprietario_veiculo'],''); //retirado a pedido do Nelson Ota$ficha_alterada['serasa_proprietario_vei_msg'] 
			} else {
				ClassRegistry::init('FichaStatusCriterio')->salvarStatus2($ficha['FichaScorecard']['codigo'], 23, 97,$consultas_realizadas_serasa['proprietario_veiculo'],$ficha_alterada['serasa_proprietario_vei_msg']); 
			}
		}

		if (trim($ficha_alterada['serasa_proprietario_car_msg']) !='NAOPOSSUICARRETA') {
			if ($ficha_alterada['serasa_proprietario_car_msg']==" NADA CONSTA") {
				ClassRegistry::init('FichaStatusCriterio')->salvarStatus2($ficha['FichaScorecard']['codigo'], 26, 145,$consultas_realizadas_serasa['proprietario_carreta'],'');//retirado a pedido do Nelson Ota $ficha_alterada['serasa_proprietario_car_msg'] 
			} else {
				ClassRegistry::init('FichaStatusCriterio')->salvarStatus2($ficha['FichaScorecard']['codigo'], 26, 140,$consultas_realizadas_serasa['proprietario_carreta'],$ficha_alterada['serasa_proprietario_car_msg']); 
			}
		}

		if ($ficha_alterada['serasa_proprietario_bi_msg'] !="NAOPOSSUIBITREM") {
			if ($ficha_alterada['serasa_proprietario_car_msg']==" NADA CONSTA") {
				ClassRegistry::init('FichaStatusCriterio')->salvarStatus2($ficha['FichaScorecard']['codigo'], 27, 151,$consultas_realizadas_serasa['proprietario_bitrem'],''); //retirado a pedido do Nelson Ota $ficha_alterada['serasa_proprietario_bi_msg']  
			} else {
				ClassRegistry::init('FichaStatusCriterio')->salvarStatus2($ficha['FichaScorecard']['codigo'], 27, 146,$consultas_realizadas_serasa['proprietario_bitrem'],$ficha_alterada['serasa_proprietario_bi_msg']);  
			}
		}
	}
	
	public function processarSqlsTelecheque($ficha) {
	    
	    $sqlTelecheque = "SELECT count(*) FROM dbBCB.telecheque.ccf647 WHERE codigo_documento LIKE '%%%s';";	    
	    $this->defineStatusSeNaoTemResultados(
	        sprintf($sqlTelecheque, $ficha['Proprietario']['cpf']),
	        $ficha['FichaScorecard']['codigo'],
	        Criterio::TELECHEQUE_PROPRIETARIO,
	        StatusCriterio::TELECHEQUE_PROPRIETARIO_ATE_LIMITE
	    );
	    
	    $this->defineStatusSeNaoTemResultados(
	        sprintf($sqlTelecheque, $ficha['Profissional']['cpf']),
	        $ficha['FichaScorecard']['codigo'],
	        Criterio::TELECHEQUE_MOTORISTA,
	        StatusCriterio::TELECHEQUE_MOTORISTA_ATE_LIMITE
	    );

	}
	
	public function processarSqlsTeleconsult($ficha) {
        $sqlTeleconsult = "SELECT TOP 1 datediff(month, f.data_inclusao, GETDATE()) + CASE WHEN DATEPART(day,  f.data_inclusao) < DATEPART(day, GETDATE()) THEN 1 ELSE 0 END 
        		FROM dbTeleconsult.informacoes.ficha f
                INNER JOIN dbBuonny.publico.profissional_log pl ON f.codigo_profissional_log = pl.codigo
                WHERE pl.codigo_profissional = '%s'
                ORDER BY f.data_inclusao";
        $codigo_criterio = Criterio::EXP_BANCO_DADOS_TLC;
        $this->processarSqlsQuantidades($ficha, sprintf($sqlTeleconsult, $ficha['Profissional']['codigo']), $codigo_criterio);
	}
	
	public function processarSqlsTeleconsultFaturamentoConsultas($ficha){
		$sqlTeleconsult = "SELECT count(*) 
			FROM dbTeleconsult.informacoes.log_faturamento 
			WHERE codigo_tipo_operacao = 1 AND codigo_profissional = '%s' AND DATEDIFF(month, data_inclusao, getdate()) <= 24";
		$codigo_criterio = Criterio::CONSULTAS_OK_TLC_24_MESES;
		
		$this->processarSqlsQuantidades($ficha, sprintf($sqlTeleconsult, $ficha['Profissional']['codigo']), $codigo_criterio);
	}
	
	public function processarSqlsTeleconsultFaturamentoAtualizacoes($ficha){
		$sqlTeleconsult = "SELECT count(*) 
			FROM dbTeleconsult.informacoes.log_faturamento 
			WHERE codigo_tipo_operacao IN (21,22,75) AND codigo_profissional = '%s' AND DATEDIFF(month, data_inclusao, getdate()) <= 24";
		$codigo_criterio = Criterio::ATUALIZACOES_RENOVACOES_AUTOMATICAS;
		
		$this->processarSqlsQuantidades($ficha, sprintf($sqlTeleconsult, $ficha['Profissional']['codigo']), $codigo_criterio);
	}
	
	public function processarSqlTeleconsultMarginado($ficha){
		$sqlTeleconsult = "select top 1 profissional_log.codigo_documento, ficha_pesquisa_questao_resposta.observacao from dbTeleconsult.informacoes.ficha
            inner join dbTeleconsult.informacoes.ficha_pesquisa on ficha_pesquisa.codigo_ficha = ficha.codigo
            inner join dbBuonny.publico.profissional_log on profissional_log.codigo = ficha.codigo_profissional_log
            inner join dbTeleconsult.informacoes.ficha_pesquisa_questao_resposta on ficha_pesquisa_questao_resposta.codigo_ficha_pesquisa = ficha_pesquisa.codigo
            inner join dbTeleconsult.informacoes.questao_resposta on questao_resposta.codigo = ficha_pesquisa_questao_resposta.codigo_questao_resposta
            where questao_resposta.codigo_questao = 16
            and profissional_log.codigo_documento = '{$ficha['Profissional']['cpf']}'
            order by ficha.codigo desc";
		$data = ClassRegistry::init('FichaStatusCriterio')->query($sqlTeleconsult);
		if (!empty($data)) {
		    $fichaData = current(current($data));
		    $obs = $fichaData['observacao'];
		    $this->fichaScorecard()->atualizarResumo($ficha['FichaScorecard']['codigo'], $obs);
		}

	}
	
	public function processarSqlsViagensBsat($ficha) {
		$sql_viagem = "select count(*) as qtde from viag_viagem viag 
		inner join vvei_viagem_veiculo vvei on vvei.vvei_viag_codigo = viag.viag_codigo
		inner join veic_veiculo veic on veic.veic_oras_codigo=vvei.vvei_veic_oras_codigo
		inner join pfis_pessoa_fisica pfis on pfis.pfis_pess_oras_codigo=vvei.vvei_moto_pfis_pess_oras_codigo
		where pfis.pfis_cpf = '". preg_replace('/\D/', '', $ficha['Profissional']['cpf'] ) ."'";
        $codigo_criterio = Criterio::VIAGENS_BSAT_24_MESES;
        $this->processarSqlsQuantidades( $ficha, $sql_viagem, $codigo_criterio, 'pg' );
	}
	
	public function processarSqlsViagensRma($ficha) {
		$sqlRma = "select count(*) as qtde
		from viag_viagem viag 
		inner join vvei_viagem_veiculo vvei on vvei.vvei_viag_codigo = viag.viag_codigo
		inner join veic_veiculo veic on veic.veic_oras_codigo=vvei.vvei_veic_oras_codigo
		inner join pfis_pessoa_fisica pfis on pfis.pfis_pess_oras_codigo=vvei.vvei_moto_pfis_pess_oras_codigo
		inner join orma_ocorrencia_rma orma on (orma.orma_viag_codigo = viag.viag_codigo)
		inner join trma_tipo_rma trma on (trma.trma_codigo = orma.orma_trma_codigo)
		where pfis.pfis_cpf = '". preg_replace('/\D/', '', $ficha['Profissional']['cpf'] ) ."' 
		and trma.trma_grma_codigo = 1
		and orma.orma_data_cadastro between date_trunc('month', current_date ) - INTERVAL'12 month' and now()";
        $codigo_criterio = 14;//Criterio::RMA_ULTIMOS_12_MESES;
        $this->processarSqlsQuantidades($ficha, $sqlRma, $codigo_criterio, 'pg');   
	}
	
	public function processarExtracaoCNH($ficha) {
	    $cnh = json_decode($ficha['FichaScorecard']['extracao'], true);
	    $cnh = $cnh['denatran_cnh'];
	    if (isset($cnh['txtDataValCNH'])) {
            $vencimento = AppModel::dateToDbDate($cnh['txtDataValCNH']);
            $hojeMenos1Mes = date('Ymd', strtotime('-1 month'));
            if ( $vencimento < $hojeMenos1Mes ) 
                ClassRegistry::init('FichaStatusCriterio')->salvarStatus2( $ficha['FichaScorecard']['codigo'], 4, 107, true, 'CNH SUSPENSA.Validade :'.$cnh['txtDataValCNH']);
	    }
	}
	
	public function processarExtracaoVeiculo($ficha) {
	    $veiculo = json_decode($ficha['FichaScorecard']['extracao'], true);
	    $veiculo = $veiculo['denatran_veiculo'];
	    $keys_restricao = array('Restrição-1', 'Restrição-2', 'Restrição-3', 'Restrição-4','Existe Ocorrência de Roubo Furto Ativa ?', 'Existe Comunicação de Venda ?', 'Existe Restrição Judicial RENAJUD ?', 'Existe Recall ?');
	    $vals_ok = array('NAO HA', 'Não', 'ALIENACAO FIDUCIARIA');
	    if ( count($veiculo) > 0 ) {
	        $ha_restricao = false;//fichaScorecard
	        foreach ($keys_restricao as $key_restricao)
	            $ha_restricao = $ha_restricao || !in_array(utf8_encode(trim(utf8_decode($veiculo[$key_restricao]), chr(160))), $vals_ok);
	            
			$possui_restricao = $ha_restricao ? StatusCriterio::VEICULO_RESTRICAO_DETRANS_ESCLARECER : StatusCriterio::VEICULO_SEM_IMPEDIMENTOS;
	        ClassRegistry::init('FichaStatusCriterio')->salvarStatus2( $ficha['FichaScorecard']['codigo'], Criterio::VEICULO, $possui_restricao, true, $ha_restricao);
        }    
	}
	
	public function processarExtracaoStj($ficha) {
	    $stj = json_decode($ficha['FichaScorecard']['extracao'], true);
	    $stj = $stj['stj'];
        if ( isset($stj['partes_encontradas']) && $stj['partes_encontradas'] > 0 ) {
            ClassRegistry::init('FichaStatusCriterio')->salvarStatus2(
                $ficha['FichaScorecard']['codigo'],
                Criterio::DISTRIBUIDOR_FORENSE,
                StatusCriterio::DISTRIBUIDOR_FORENSE_CONSTA_NAO_ESCLARECIDO,true,$stj
            );	        
	    }
	}
	
	public function processarIdadeProfissional($ficha) {
	    $nascimento       = AppModel::dateToDbDate($ficha['Profissional']['data_nascimento']);
	    $nascimentoAno    = substr($nascimento, 0, 4);
	    $nascimentoMesDia = substr($nascimento, 4, 4);
        $ano    = date('Y');
        $mesDia = date('md');
        $idade  = ($ano - $nascimentoAno) - ($nascimentoMesDia > $mesDia ? 1 : 0);
        $criterio_idade = 13;
	    $this->processarQuantidades($ficha, $idade, $criterio_idade);
	}
	
	public function processarUltimaFicha($ficha) {
		
		list($data_ficha_scorecarrd, $respostas_scorecarrd) = ClassRegistry::init('FichaStatusCriterio')->obterCriteriosUltimaFichaProfissional($ficha['FichaScorecard']['codigo_cliente'], $ficha['Profissional']['cpf']);
		$data_ficha_scorecarrd = ClassRegistry::init('FichaStatusCriterio')->dateTimeToDbDateTime($data_ficha_scorecarrd);
		
		list($data_ficha_teleconsult, $respostas_teleconsult) = ClassRegistry::init('FichaPesquisaQR')->obterCriteriosUltimaFichaProfissional($ficha['FichaScorecard']['codigo_cliente'], $ficha['Profissional']['cpf']);
		$data_ficha_teleconsult = ClassRegistry::init('FichaPesquisaQR')->dateTimeToDbDateTime($data_ficha_teleconsult);

		$respostas = $respostas_scorecarrd;
		if(empty($data_ficha_scorecarrd) && !empty($data_ficha_teleconsult) || (!empty($data_ficha_scorecarrd) && !empty($data_ficha_teleconsult) && $data_ficha_teleconsult > $data_ficha_scorecarrd)){
			$respostas = $respostas_teleconsult;
		}
		
		foreach($respostas as $criterio=>$status_criterio){
			ClassRegistry::init('FichaStatusCriterio')->salvarStatus2(
			$ficha['FichaScorecard']['codigo'],
			$criterio,
			$status_criterio,
			false,$respostas 
			);
		} 
	}
	
	public function processarSqlsQuantidades($ficha, $sql, $codigo_criterio, $db='mssql' ) {
		$quantidade = $this->sqlRetornaQuantidade($sql, $db );
		if ($quantidade ==''){
			$quantidade = 0;
		}
		$this->processarQuantidades($ficha, $quantidade, $codigo_criterio);
	}
	
	private function processarQuantidades($ficha, $quantidade, $codigo_criterio) {
	    $codigo_ficha = $ficha['FichaScorecard']['codigo'];		
		$status_criterios = ClassRegistry::init('StatusCriterio')->find('all', array(
			'conditions'=>array('codigo_criterio'=>$codigo_criterio),
			'order'=>'intervalo_minimo DESC'
		));
		
		foreach($status_criterios as $status_criterio){
			$status_criterio = $status_criterio['StatusCriterio'];			
			if ($codigo_criterio == 9){ // 9 :  Quantas Cadastros : 10 
               $resp_msg = 'Cadastros Scorecard: '.$quantidade." mes(es).";
               $meses = $quantidade;
               if ($meses>24){
               	  $status_criterio['codigo'] = 40; //40 mais de 24 meses
               }               
               if($meses>12 and $meses <=24 ) {
               	  $status_criterio['codigo'] = 41; //14 a 24 meses
               }
               if($meses>7 and $meses <=12 ) {
               	  $status_criterio['codigo'] = 42; //de 07 a 12 meses
               }
               if($meses<6 and $meses!=0) {
               	  $status_criterio['codigo'] = 43; //até 6 meses
               }
               if($meses==0) {
               	  $status_criterio['codigo'] = 44; //menos de um mês
               }  
			}
            if ($codigo_criterio == 11){  //11 : Quantas consultas ok : 10 	
               $resp_msg = 'Consultas Scorecard :'.$quantidade." vezes .";
			   $consultas = $quantidade ;
			   if ($consultas > 10){
			   	  $status_criterio['codigo'] = 49; //49 acima de 10 consultas
			   }
			   if ($consultas >5 and $consultas<=10){
			   	  $status_criterio['codigo'] = 50; //50 de 6 a 10 consultas
			   }
			   if ($consultas <6){
			   	  $status_criterio['codigo'] = 51; //51 até 5 consultas
			   }
               if ($consultas ==0){
               	  $status_criterio['codigo'] = 52; // 52 sem viagens
               }
			}            
            if ($codigo_criterio == 24){ //24 : Quantras Renovações
               $resp_msg = 'Renovações Scorecard: '.$quantidade." vezes em 24 meses.";
			   if ($quantidade > 4 ){
                   $status_criterio['codigo']= 102;//102 - acima de 04 em 24 meses
			   }
			   if ($quantidade == 4 ){
                   $status_criterio['codigo']= 103;//103 - 04 em 24 meses 
			   }
			   if ($quantidade < 4 and $quantidade !=0){
                   $status_criterio['codigo']= 104;//104 - até 3 em 24 meses
			   }
               if ($quantidade == 0 ){
                   $status_criterio['codigo']= 105;//104 - até 3 em 24 meses
			   }
			}
			if ($codigo_criterio == 12){  //VIAGENS BSAT 24 MESES
               $resp_msg = 'Viagens BSAT 24 meses Scorecard: '.$quantidade." vezes .";
               if($quantidade <=5 ){
               	  $status_criterio['codigo']= 55;//55 até 05 viagens
               }
               if($quantidade >5 and  $quantidade <= 10){
               	  $status_criterio['codigo']= 54; //54 de 06 a 10 viagens
               }
               if($quantidade >10){
               	  $status_criterio['codigo']= 53; //53 acima de 10 viagens
               }
               if($quantidade ==0){
               	  $status_criterio['codigo']= 56;//56 sem viagens
               }
		    }

		    
		    if ($codigo_criterio == 14){  //RMA ÚLTIMOS 12 MESES
               $resp_msg = 'Historico RMA últimos 12 Meses Scorecard: '.$quantidade." eventos .";
               
               if($quantidade <=10 ){
               	  $status_criterio['codigo']= 61;
               }
               if($quantidade >10 and  $quantidade <= 20 ){
               	  $status_criterio['codigo']= 62;
               }
               if($quantidade >20 ){
               	  $status_criterio['codigo']= 63;
               }
               if($quantidade ==0){
               	  $status_criterio['codigo']= 60;
               }
               
		    }

            if ($codigo_criterio == 13){  //Profissional Idade
               $resp_msg = 'Profissional com idade de: '.$quantidade." anos .";
               $idade = $quantidade; 
               if ($idade >40 ){
		        	$status_criterio['codigo'] = 57; // critério automatico >40 anos
		        }
		        if ($idade <26 ){
		        	$status_criterio['codigo'] = 59; // critério automatico até 25 anos
		        }
		        if ($idade >25 and $idade<=40){
		        	$status_criterio['codigo'] = 58; // critério automatico maior que 25 anos e < 40 anos
		        } 
			}


		    if(empty($resp_msg)){
                $resp_msg = $quantidade;
		    }
            
            $resp_msg = utf8_decode($resp_msg);
            if ($resp_msg=='Array'){
            	$resp_msg ='';
            }
			//if($quantidade >= $status_criterio['intervalo_minimo']){
				ClassRegistry::init('FichaStatusCriterio')->salvarStatus2($codigo_ficha, $codigo_criterio, $status_criterio['codigo'], true, $resp_msg );
				//break;
			//}
		}
	}
	
	public function fichaScorecard() {
		return ClassRegistry::init('FichaScorecard');
	}


	
	private function bcbComponent() {
		if ($this->bcbComponent == null)
			$this->bcbComponent = new BcbComponent();
		return $this->bcbComponent;
	}
	
	private function defineStatusSeTemResultados($sql, $codigo_ficha, $codigo_criterio, $codigo_status) {
	    return $this->defineStatus($sql, true, $codigo_ficha, $codigo_criterio, $codigo_status);
	}
	
	private function defineStatusSeNaoTemResultados($sql, $codigo_ficha, $codigo_criterio, $codigo_status) {
	    return $this->defineStatus($sql, false, $codigo_ficha, $codigo_criterio, $codigo_status);
	}
	
	private function defineStatus($sql, $comResultados, $codigo_ficha, $codigo_criterio, $codigo_status) {

	    $found = $this->sqlTemResultados($sql);
	    
	    if (!($found xor $comResultados))
	        ClassRegistry::init('FichaStatusCriterio')->salvarStatus2($codigo_ficha, $codigo_criterio, $codigo_status,true,'teste automatico define status'); 
	    
	    return $found;
	}
	
	protected function sqlTemResultados($sql) {
	    $data = ClassRegistry::init('FichaStatusCriterio')->query($sql);
	    return isset($data) && current(current(current($data)));
	}
	
	protected function sqlRetornaQuantidade($sql, $db='mssql') {
	    if( $db == 'mssql' ){
	    	$data = ClassRegistry::init('FichaStatusCriterio')->query($sql);
	    } else {
			$data = ClassRegistry::init('TViagViagem')->query($sql);
	    }
	    if (!empty($data))
	    	return current(current(current($data)));
	    return 0;
	}	
}
?>