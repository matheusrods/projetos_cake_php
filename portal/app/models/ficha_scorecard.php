<?php
class FichaScorecard extends AppModel {

    var $name = 'FichaScorecard';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'ficha_scorecard';
    var $primaryKey = 'codigo';
    //var $displayField = '';
    var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_ficha_scorecard'));   
    const ENVIA_EMAIL_SCORECARD = FALSE;
    CONST RETORNO_CONSULTA = 35;
    CONST EXCLUSAO_VINCULO = 48;
    var $validate = array(
        'codigo_cliente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o código do cliente'
        ),
        'codigo_profissional_tipo' => array(
            'rule' => 'notEmpty',
            'message' => 'Selecione a categoria'
        ),
        'codigo_usuario' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Usuário'
        )

    );

    function listStatus() {
      return array(
        1 => 'Cadastrada',
        2 => 'A Pesquisar',
        3 => 'Em Pesquisa',
        4 => 'Pendente',
        5 => 'A Aprovar',
        6 => 'Em Aprovação',
        7 => 'Finalizada',
        8 => 'Renovada',
      );
    }


    function save($data = null, $validate = true, $fieldList = array()){
      $produto = ClassRegistry::init('Produto');
      $data['codigo_produto'] = $produto::SCORECARD;      
      return parent::save($data, $validate, $fieldList);
    }
    function incluir($data = null, $validate = true, $fieldList = array()){      
      $produto = ClassRegistry::init('Produto');
      $data['codigo_produto'] = $produto::SCORECARD;      
      return parent::incluir($data, $validate, $fieldList);
    }

    function converteFiltroEmConditionsFina($data) {
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
          if( self::ENVIA_EMAIL_SCORECARD ){
            $conditions['FichaScorecard.codigo_parametro_score'] = $data['classificacao'];            
          } else {//MANUAL
            $conditions['FichaScorecard.codigo_score_manual'] = $data['classificacao'];
          }

        }
        if (isset($data['origem_ficha']) && !empty($data['origem_ficha'])) {
          if($data['origem_ficha'] == 'E') 
            $conditions['FichaScorecard.origem_ficha'] = 'E';
          else
              $conditions['FichaScorecard.origem_ficha'] = 'F';
        } 
        if(!empty($data['cliente_vip'])) {
          $conditions['ClienteProdutoVip.cliente_vip']  = true ;
        } else {
          unset($data['cliente_vip']);
        }

        if (isset($data['data_inicial']) && !empty($data['data_inicial']) && isset($data['data_final']) && !empty($data['data_final'])) {
           array_push($conditions, array('FichaScorecard.data_alteracao BETWEEN ? AND ? '=> array( preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1', $data['data_inicial'])." 00:00:00", preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1', $data['data_final'])." 23:59:59")));
        }
        return $conditions;
    }

    public function busca_ficha_finalizadas( $filtros ){
      $this->Usuario   = ClassRegistry::init('Usuario');
      $this->LogFaturamentoTeleconsult  = ClassRegistry::init('LogFaturamentoTeleconsult');
      $this->ProfissionalLog  = ClassRegistry::init('ProfissionalLog');
      $this->Cliente  = ClassRegistry::init('Cliente');
      $this->Seguradora  = ClassRegistry::init('Seguradora');
      $this->ClienteProdutoVip  = ClassRegistry::init('ClienteProdutoVip');
      $this->ParametroScore  = ClassRegistry::init('ParametroScore');
      $this->ProfissionalSerasa  = ClassRegistry::init('ProfissionalSerasa');

      $conditions = $this->converteFiltroEmConditionsFina( $filtros );
      if(!empty($filtros['apenas_renovadas'])){
               $conditions['FichaScorecard.codigo_usuario_inclusao']  = 159 ;
      }else{
             unset($filtros['apenas_renovadas']);
      }
      if ( !empty($filtros['numero_liberacao'])) {
         $conditions['(SELECT MAX(numero_liberacao) 
                        FROM
                        '.$this->LogFaturamentoTeleconsult->databaseTable.'.'.$this->LogFaturamentoTeleconsult->tableSchema.'.'.$this->LogFaturamentoTeleconsult->tableSchema.' 
                        WHERE FichaScorecard.codigo_cliente=codigo_cliente AND
                        codigo_ficha_scorecard = FichaScorecard.codigo AND FichaScorecard.total_pontos > 0
                       ) LIKE '] = '%'.$filtros['numero_liberacao'].'%';
       }
       if( !empty($filtros['usuario'])) {
          $conditions['(SELECT
                        MAX(b.apelido) 
                         FROM
                        '.$this->LogFaturamentoTeleconsult->databaseTable.'.'.$this->LogFaturamentoTeleconsult->tableSchema.'.'.$this->LogFaturamentoTeleconsult->useTable.'  a
                        inner join  '.$this->Usuario->databaseTable.'.'.$this->Usuario->tableSchema.'.'.$this->Usuario->useTable.'  b  on a.codigo_usuario_inclusao = b.codigo
                      WHERE
                        FichaScorecard.codigo_cliente= a.codigo_cliente AND
                        codigo_ficha_scorecard = FichaScorecard.codigo AND
                        FichaScorecard.total_pontos > 0) LIKE'] =  '%'.$filtros['usuario'].'%';
      }

      if( self::ENVIA_EMAIL_SCORECARD ){
        $query_log_faturamento = 'SELECT MAX(numero_liberacao) FROM '.$this->LogFaturamentoTeleconsult->databaseTable.'.'.$this->LogFaturamentoTeleconsult->tableSchema.'.'.$this->LogFaturamentoTeleconsult->useTable.' 
          WHERE FichaScorecard.codigo_cliente=codigo_cliente 
          AND codigo_ficha_scorecard = FichaScorecard.codigo 
          AND FichaScorecard.total_pontos > 0 ';
      } else {
        $query_log_faturamento = 'SELECT MAX(numero_liberacao) FROM '.$this->LogFaturamentoTeleconsult->databaseTable.'.'.$this->LogFaturamentoTeleconsult->tableSchema.'.'.$this->LogFaturamentoTeleconsult->useTable.' 
          WHERE FichaScorecard.codigo_cliente=codigo_cliente 
          AND codigo_ficha_scorecard = FichaScorecard.codigo 
          AND FichaScorecard.codigo_score_manual < 7 ';
      }


      $fields = array(
        "($query_log_faturamento) AS codigo_liberacao",
        'FichaScorecard.codigo', 
        'FichaScorecard.data_alteracao',
        'Cliente.codigo as codigo_cliente',
        'Cliente.razao_social as cliente',
        'Seguradora.nome as seguradora',
        'ProfissionalLog.codigo_profissional as codigo_profissional',
        'ProfissionalLog.nome as nome',
        'ProfissionalLog.codigo_documento as profissional_cpf',
        'ParametroScore.pontos as pontos',
        'FichaScorecard.percentual_pontos as percentual_total',
        'FichaScorecard.total_pontos as total',
        'ParametroScore.nivel  as classificacao_motorista',
        'ParametroScore.valor  as qtd_maxima',
        'FichaScorecard.codigo_score_manual',
        "CASE WHEN FichaScorecard.codigo_score_manual = 2 THEN 'Adequado' 
              WHEN FichaScorecard.codigo_score_manual = 7 THEN 'Insuficiente' 
              WHEN FichaScorecard.codigo_score_manual = 8 THEN 'Divergente' 
          END AS status_manual",
        'Usuario.apelido as usuario',
        '(select max(codigo) 
          from '.$this->databaseTable.'.'.$this->tableSchema.'.'.$this->useTable.'   as fs 
          where codigo_profissional_log IN (select codigo
            from '.$this->ProfissionalLog->databaseTable.'.'.$this->ProfissionalLog->tableSchema.'.'.$this->ProfissionalLog->useTable.' 
            where codigo_documento = ProfissionalLog.codigo_documento and fs.codigo_status=7)) AS ultima_ficha'); 

        $joins = array(
          array(
              "table"     => $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
              "alias"     => "Cliente",
              "type"      => "LEFT",
              "conditions"=> array("FichaScorecard.codigo_cliente = Cliente.codigo")
          ),
          array(
              "table"     => $this->ProfissionalLog->databaseTable.'.'.$this->ProfissionalLog->tableSchema.'.'.$this->ProfissionalLog->useTable,
              "alias"     => "ProfissionalLog",
              "type"      => "LEFT",
              "conditions"=> array("FichaScorecard.codigo_profissional_log=ProfissionalLog.codigo")
          ),
          
          array(
              "table"     => $this->Seguradora->databaseTable.'.'.$this->Seguradora->tableSchema.'.'.$this->Seguradora->useTable,
              "alias"     => "Seguradora",
              "type"      => "LEFT",
              "conditions"=> array("Cliente.codigo_seguradora=Seguradora.codigo")
          ),
          
          array(
              "table"     => $this->ClienteProdutoVip->databaseTable.'.'.$this->ClienteProdutoVip->tableSchema.'.'.$this->ClienteProdutoVip->useTable,
              "alias"     => "ClienteProdutoVip",
              "type"      => "LEFT",
              "conditions"=> array("FichaScorecard.codigo_cliente=ClienteProdutoVip.codigo_cliente AND ClienteProdutoVip.codigo_produto=1 ")
          ),
          array(
              "table"     => $this->ParametroScore->databaseTable.'.'.$this->ParametroScore->tableSchema.'.'.$this->ParametroScore->useTable,
              "alias"     => "ParametroScore",
              "type"      => "LEFT",
              "conditions"=> array("ParametroScore.codigo=FichaScorecard.codigo_parametro_score")
          ),
          array(
              "table"     => $this->Usuario->databaseTable.'.'.$this->Usuario->tableSchema.'.'.$this->Usuario->useTable,
              "alias"     => "Usuario",
              "type"      => "LEFT",
              "conditions"=> array("Usuario.codigo = FichaScorecard.codigo_usuario_alteracao")
          ),          
        );                   

        $conditions['FichaScorecard.ativo'] = 1;
        $conditions['FichaScorecard.codigo_status'] = 7;
        $retorno = array( 
          'fields'     => $fields,
          'joins'      => $joins, 
          'conditions' => $conditions,
          'limit'      => 50,
          'order'      => 'FichaScorecard.codigo DESC'
        );
        return $retorno;
    }

    public function busca_ficha_consulta_serasa ($codigo_ficha){
    $this->ProfissionalSerasa  = ClassRegistry::init('ProfissionalSerasa');
         $fields =array('cast(ProfissionalSerasa.data_ultima_ocorrencia as date) as data_ultima_ocorrencia',
                      'ProfissionalSerasa.descricao              as descricao',
                      'ProfissionalSerasa.quantidade_ocorrencias as qtd_ocorrencia',
                      'ProfissionalSerasa.valor_ocorrencias      as valor_ocorrencias',
                      'cast(ProfissionalSerasa.data_inclusao as date) as data_inclusao') ;
         $joins   = $joins = array(
                              array(
                                  "table"     => $this->ProfissionalLog->databaseTable.'.'.$this->ProfissionalLog->tableSchema.'.'.$this->ProfissionalLog->useTable,
                                  "alias"     => "ProfissionalLog",
                                  "type"      => "LEFT",
                                  "conditions"=> array("FichaScorecard.codigo_profissional_log = ProfissionalLog.codigo")
                              ),
                              array(
                                  "table"     => $this->ProfissionalSerasa->databaseTable.'.'.$this->ProfissionalSerasa->tableSchema.'.'.$this->ProfissionalSerasa->useTable,
                                  "alias"     => "ProfissionalSerasa",
                                  "type"      => "LEFT",
                                  "conditions"=> array("ProfissionalLog.codigo_profissional = ProfissionalSerasa.codigo_profissional")
                              )
                          );
         $conditions['FichaScorecard.codigo'] = $codigo_ficha;
         $retorno = $this->find('all',array('fields'=>$fields ,'joins' =>$joins,'conditions'=>$conditions));
         return $retorno;

    }

  public function proximoExtrair() {
      APP::import('Model', 'FichaScorecardStatus');
      $conditions = array(
        'FichaScorecard.codigo_status'=>FichaScorecardStatus::CADASTRADA,
        'FichaScoreCard.ativo' => 1
       );      
      return $this->findExtracao($conditions);
  }
  
  public  function busca_ultima_ficha_cliente($codigo_cliente){ 
        $this->FichaScorecard  = ClassRegistry::init('FichaScorecard');
          $sql = '
                   SELECT MAX(codigo) as ultima_ficha
                   FROM '.$this->FichaScorecard->databaseTable.'.'.$this->FichaScorecard->tableSchema.'.'.$this->FichaScorecard->useTable.'
                   WHERE codigo_cliente =\''.$codigo_cliente.'\'';
          
          $retorno = $this->query ($sql);
          return $retorno;
  } 
    
     /* Principal */
    public function resultadoSerasaRobo($stringsRecebidas=null, $cpf=null, $consulta='Resumo')  {

        if (!empty($stringsRecebidas)) {
            //Json para Array
            $dados_profissional = json_decode($stringsRecebidas, true);
            
            //if($consulta=='Resumo'){
                $resultado =$this->formatarDadosResumoSerasa($dados_profissional);
            //}
     

            return $resultado;
        }
        return ' --- Não foi possível identificar o profissional --- ';
    }

    // Formata o Resumo do SERASA
    public function formatarDadosResumoSerasa($arrayRecebido=null)  {
       // $linha = array(); $total_geral=0;
        if(!empty($arrayRecebido['protesto'])) {
          //Protesto
            $total=0; $qtde=0; $descricao=''; 
            foreach ($arrayRecebido['protesto'] as $dados['B361']) {
                
                
                @$descricao .= $dados['B361'][0]['descricao'].'/';
                @$qtde += $dados['B361'][0]['quantidadeTotal'];
            }
            $periodo = $arrayRecebido['protesto']['B361'][0]['dataPrimeiraOcorrencia']. ' a '.$arrayRecebido['protesto']['B361'][0]['dataUltimaOcorrencia'];
            if(!empty($arrayRecebido['protesto'])) {
              foreach ($arrayRecebido['protesto'] as $dados['B361']) { 
                  @$total +=  $dados['B361'][0]['valorUltimoProtesto'];
              }
            }  

        }
        
        if(!empty($arrayRecebido['protesto']) || !empty($arrayRecebido['protesto'])) {
            $linha[]=$qtde.';'.$descricao.';'.$periodo.';'. $total;
            @$total_geral += $total;
        }

        if(!empty($arrayRecebido['pendenciaPagamento']['B357'])) {
            //Pendencias Financeira
            $total=0; $qtde=0; $descricao=''; 
            foreach ($arrayRecebido['pendenciaPagamento']['B357'] as $dados['B357']) {
                $descricao .= $dados['B357']['descricao'].'/';
                $qtde += $dados['B357']['quantidadeTotal'];
            }
            $periodo = $arrayRecebido['pendenciaPagamento']['B357'][0]['dataMenor']. ' a '.$arrayRecebido['pendenciaPagamento']['B357'][0]['dataMaior'];
        }
        if(!empty($arrayRecebido['pendenciaPagamento']['B358'])) {
            foreach ($arrayRecebido['pendenciaPagamento']['B358'] as $dados['B358']) { 
                $total +=  $dados['B358']['valor'];
            }
        }  
        if(!empty($arrayRecebido['pendenciaPagamento']['B357']) || !empty($arrayRecebido['pendenciaPagamento']['B358'])) {
            $linha[]=$qtde.';'.$descricao.';'.$periodo.';'. $total;
            @$total_geral += $total;
        }
        
        
        if(!empty($arrayRecebido['chequeSemFundo']['B359'])) {
            //Cheque Sem Fundo
            $total=0; $qtde=0; $descricao='';
            
            
            $qtde = $arrayRecebido['chequeSemFundo']['B359'][0]['quantidadeTotal'];
            $descricao = $arrayRecebido['chequeSemFundo']['B359'][0]['tipoOcorrencia'];
            $periodo = $arrayRecebido['chequeSemFundo']['B359'][0]['dataMenor']. ' a '.$arrayRecebido['chequeSemFundo']['B359'][0]['dataMaior'];
        }

        if(!empty($arrayRecebido['chequeSemFundo']['B360'])) {
            foreach ($arrayRecebido['chequeSemFundo']['B360'] as $dados['B360']) { 
                
                $total +=  $dados['B360']['valor'];
            }

        }
        if(!empty($arrayRecebido['chequeSemFundo']['B359']) || !empty($arrayRecebido['chequeSemFundo']['B360'])) {
            $linha[]=$qtde.';'.$descricao.';'.$periodo.';'. $total;
            @$total_geral += $total;
        }
        if($total_geral > 0) {
            $linha[]=';'.';Total Geral;'. $total_geral;
        }
        
        return $linha;
    }


 
  public function carrega_ocorrencias_robo($codigo_ficha){
    
    $this->ProprietarioLog   = ClassRegistry::init('ProprietarioLog');
    $this->Profissional      = ClassRegistry::init('Profissional');
    $this->VeiculoOcorrencia = ClassRegistry::init('VeiculoOcorrencia');
    $this->VeiculoLog        = ClassRegistry::init('VeiculoLog'); 
    $this->Veiculo           = ClassRegistry::init('Veiculo'); 
    $this->Ocorrencia        = ClassRegistry::init('Ocorrencia'); 
    $this->Usuario           = ClassRegistry::init('Usuario'); 
    $codigo_profissional     = $this->buscaCodigoProfissional($codigo_ficha);
    $profissional            = $this->Profissional->carregar($codigo_profissional); 
    

    $this->bindModel(array(
        'hasMany' => array(
            'FichaScorecardVeiculo' => array('foreignKey'=>'codigo_ficha_scorecard'),
            'FichaScorecardQuestaoResp' => array('className'=>'FichaScorecardQuestaoResp','foreignKey'=>'codigo_ficha_scorecard')
        )
    ));
    $fichascorecard = $this->find('first', array('conditions'=>array('codigo'=>$codigo_ficha), 'order'=>'FichaScorecard.codigo DESC', 'recursive'=>2));   
    
    $veiculosficha = $this->FichaScorecardVeiculo->find('all',array('conditions' => array('FichaScorecardVeiculo.codigo_ficha_scorecard'=>$codigo_ficha)));        
            
     if(!isset($veiculosficha[0]['FichaScorecardVeiculo']['codigo_veiculo_log'])){
         $veiculosficha[0]['FichaScorecardVeiculo']['codigo_veiculo_log']=''; 
     }
     if(!isset($veiculosficha[1]['FichaScorecardVeiculo']['codigo_veiculo_log'])){
         $veiculosficha[1]['FichaScorecardVeiculo']['codigo_veiculo_log']=''; 
     }
     if(!isset($veiculosficha[2]['FichaScorecardVeiculo']['codigo_veiculo_log'])){
         $veiculosficha[2]['FichaScorecardVeiculo']['codigo_veiculo_log']=''; 
     }

     $placa_veiculo = $this->VeiculoLog->find('all',array('conditions' => array('VeiculoLog.codigo'=>$veiculosficha[0]['FichaScorecardVeiculo']['codigo_veiculo_log'])));
     $placa_carreta = $this->VeiculoLog->find('all',array('conditions' => array('VeiculoLog.codigo'=>$veiculosficha[1]['FichaScorecardVeiculo']['codigo_veiculo_log'])));
     $placa_bitrem  = $this->VeiculoLog->find('all',array('conditions' => array('VeiculoLog.codigo'=>$veiculosficha[2]['FichaScorecardVeiculo']['codigo_veiculo_log'])));
            
           

     $joins = array( 
              array(
                "table"   => $this->Veiculo->databaseTable.'.'.$this->Veiculo->tableSchema.'.'.$this->Veiculo->useTable,
                      "alias"   => "Veiculo",
                      "type"    => "INNER",
                "conditions"=> array("Veiculo.codigo = VeiculoOcorrencia.codigo_veiculo")
              ),
              array(
                "table"   =>   $this->Ocorrencia->databaseTable.'.'.$this->Ocorrencia->tableSchema.'.'.$this->Ocorrencia->useTable,
                      "alias"   => "TipoOcorrencia",
                      "type"    => "INNER",
                "conditions"=> array("TipoOcorrencia.codigo = VeiculoOcorrencia.codigo_ocorrencia")
              ),
              array(
                "table"   => $this->Usuario->databaseTable.'.'.$this->Usuario->tableSchema.'.'.$this->Usuario->useTable,
                      "alias"   => "Usuario",
                      "type"    => "INNER",
                "conditions"=> array("Usuario.codigo = VeiculoOcorrencia.codigo_usuario_inclusao")
              )
     );
     


     if(!isset($placa_veiculo[0]['VeiculoLog']['codigo_veiculo'])){
             $placa_veiculo[0]['VeiculoLog']['codigo_veiculo']='';
     }
     if(!isset($placa_carreta[0]['VeiculoLog']['codigo_veiculo'])){
            $placa_carreta[0]['VeiculoLog']['codigo_veiculo']='';
     }
     if(!isset($placa_bitrem[0]['VeiculoLog']['codigo_veiculo'])){
           $placa_bitrem[0]['VeiculoLog']['codigo_veiculo']='';
     }
    
      
     $ocorrencia_placa   = $this->VeiculoOcorrencia->find('count',array('joins'=>$joins,'conditions' => array('VeiculoOcorrencia.codigo_veiculo'=>$placa_veiculo[0]['VeiculoLog']['codigo_veiculo'])));
     $ocorrencia_carreta = $this->VeiculoOcorrencia->find('count',array('joins'=>$joins,'conditions' => array('VeiculoOcorrencia.codigo_veiculo'=>$placa_carreta[0]['VeiculoLog']['codigo_veiculo'])));
     $ocorrencia_bitrem  = $this->VeiculoOcorrencia->find('count',array('joins'=>$joins,'conditions' => array('VeiculoOcorrencia.codigo_veiculo'=>$placa_bitrem[0]['VeiculoLog']['codigo_veiculo'])));
     
        
    $result['ocorrencia_veiculo_qtd']      = $ocorrencia_placa;
    $result['ocorrencia_carreta_qtd']      = $ocorrencia_carreta;
    $result['ocorrencia_bitrem_qtd']       = $ocorrencia_bitrem;

    return $result;

  }  

  function carrega_ficha_robo($codigo_ficha, $permite_consulta_serasa = FALSE ){
    $this->ProprietarioLog = ClassRegistry::init('ProprietarioLog');
    $this->Profissional    = ClassRegistry::init('Profissional');
    $codigo_profissional   = $this->buscaCodigoProfissional($codigo_ficha);
    $profissional          = $this->Profissional->carregar($codigo_profissional); 
    $consulta_serasa_motorista            = FALSE;
    $consulta_serasa_proprietario_veiculo = FALSE;
    $consulta_serasa_proprietario_carreta = FALSE;
    $consulta_serasa_proprietario_bitrem  = FALSE;
    $this->bindModel(array(
      'hasMany' => array(
        'FichaScorecardVeiculo' => array('foreignKey'=>'codigo_ficha_scorecard'),
        'FichaScorecardQuestaoResp' => array('className'=>'FichaScorecardQuestaoResp','foreignKey'=>'codigo_ficha_scorecard')
    )));
    $fichascorecard = $this->find('first', array(
      'conditions' => array('codigo'=>$codigo_ficha), 
      'order'=>'FichaScorecard.codigo DESC', 'recursive'=>2)
    );
    if (!empty($fichascorecard['FichaScorecardVeiculo'][0]['codigo_proprietario_log'])){
      $proprietario_vei_log = $fichascorecard['FichaScorecardVeiculo'][0]['codigo_proprietario_log'];
    }else{
      $proprietario_vei_log = "NAOPOSSUI";
    }
    if(!empty($fichascorecard['FichaScorecardVeiculo'][1]['codigo_proprietario_log'])){
      $proprietario_car_log = $fichascorecard['FichaScorecardVeiculo'][1]['codigo_proprietario_log'];
    }else{
      $proprietario_car_log = "NAOPOSSUI";
    }
    if(!empty($fichascorecard['FichaScorecardVeiculo'][2]['codigo_proprietario_log'])){
      $proprietario_bi_log  = $fichascorecard['FichaScorecardVeiculo'][2]['codigo_proprietario_log'];
    }else{
      $proprietario_bi_log = "NAOPOSSUI";
    }
    if ($proprietario_vei_log!="NAOPOSSUI"){
      $proprietario_vei = $this->ProprietarioLog->find('first', array('conditions'=>array('ProprietarioLog.codigo'=>$proprietario_vei_log), 'recusive'=>-1)); 
      $cpf_proprietario_vei = $proprietario_vei['ProprietarioLog']['codigo_documento'];
      if (isset($cpf_proprietario_vei)){
        $str_prop_vei = strlen($cpf_proprietario_vei);
      }
      $valor =array();
      if( $permite_consulta_serasa === TRUE ){
        if($str_prop_vei == 11 ){
          die('ERRO: NAO PODE');
          $serasa_proprietario_vei = file_get_contents("http://informacoes.buonny.com.br/bcb/index/consulta-informacoes-rest?codigoDocumento=".$cpf_proprietario_vei);
          if ( json_decode($serasa_proprietario_vei) )
            $consulta_serasa_proprietario_veiculo = TRUE;
          $valor = $this->resultadoSerasaRobo($serasa_proprietario_vei,$cpf_proprietario_vei);
        }
      } 
      $total = count($valor);
      $serasa_proprietario_vei_msg = "Pendências proprietário veículo CPF:".$cpf_proprietario_vei."\n";
      foreach ($valor as $key=>$serasa_proprietario_vei) {
        $proprietario_serasa_array = explode(";",$serasa_proprietario_vei);
        if ( $key==$total-1 ) {
          $serasa_proprietario_vei_msg .= $proprietario_serasa_array[2]." : R$ ". number_format($proprietario_serasa_array[3], 2, ',', '');
          if ($proprietario_serasa_array[1]=='CHEQUES S/FUNDOS-ACHEI CCF'){
            $serasa_proprietario_vei_msg .= "\n".$proprietario_serasa_array[1].'-'.$proprietario_serasa_array[2]." : Quantidade :".$proprietario_serasa_array[0]."\n";
          }
        } else {
          if ($proprietario_serasa_array[1]=='CHEQUES S/FUNDOS-ACHEI CCF'){
            $serasa_proprietario_vei_msg .= $proprietario_serasa_array[1].'-'.$proprietario_serasa_array[2]." : Quantidade :".$proprietario_serasa_array[0]."\n";
          }else{
            $serasa_proprietario_vei_msg .= $proprietario_serasa_array[1].'-'.$proprietario_serasa_array[2]." : R$ ". number_format($proprietario_serasa_array[3], 2, ',', '')."\n";
          }
        }
      }
      if($total==0){
        $serasa_proprietario_vei_msg =" NADA CONSTA";
      }
    }else{
      $serasa_proprietario_vei_msg = "NAOPOSSUIVEICULO";
    }
    if ($proprietario_car_log!="NAOPOSSUI"){
      $proprietario_car = $this->ProprietarioLog->find('first', array('conditions'=>array('ProprietarioLog.codigo'=>$proprietario_car_log), 'recusive'=>-1)); 
      $cpf_proprietario_car = $proprietario_car['ProprietarioLog']['codigo_documento'];
      if (isset( $cpf_proprietario_car)){
        $str_prop_car = strlen( $cpf_proprietario_car);
      } 
      $valor = array();
      if( $permite_consulta_serasa === TRUE ){
        if ($str_prop_car==11){
          die('ERRO: NAO PODE');
          $serasa_proprietario_car = file_get_contents("http://informacoes.buonny.com.br/bcb/index/consulta-informacoes-rest?codigoDocumento=".$cpf_proprietario_car);
          if ( json_decode($serasa_proprietario_car) )
            $consulta_serasa_proprietario_carreta = TRUE;
          $valor = $this->resultadoSerasaRobo($serasa_proprietario_car,$cpf_proprietario_car);        
        }
      }
      $total = count($valor);
      $serasa_proprietario_car_msg = "Pendências proprietário carreta CPF:".$cpf_proprietario_car."\n";
      foreach ($valor as $key=>$serasa_proprietario_car) {
        $proprietario_serasa_array = explode(";",$serasa_proprietario_car);
        if ($key==$total-1){
          $serasa_proprietario_car_msg .= $proprietario_serasa_array[2]." : R$ ". number_format($proprietario_serasa_array[3], 2, ',', '');
          if ($proprietario_serasa_array[1]=='CHEQUES S/FUNDOS-ACHEI CCF'){
            $serasa_proprietario_car_msg .= "\n".$proprietario_serasa_array[1].'-'.$proprietario_serasa_array[2]." : Quantidade :".$proprietario_serasa_array[0]."\n";
          }
        }else{
          if ($proprietario_serasa_array[1]=='CHEQUES S/FUNDOS-ACHEI CCF'){
            $serasa_proprietario_car_msg .= $proprietario_serasa_array[1].'-'.$proprietario_serasa_array[2]." : Quantidade :".$proprietario_serasa_array[0]."\n";
          }else{
            $serasa_proprietario_car_msg .= $proprietario_serasa_array[1].'-'.$proprietario_serasa_array[2]." : R$ ". number_format($proprietario_serasa_array[3], 2, ',', '')."\n";
          }
        }
      }
      if($total==0){
        $serasa_proprietario_car_msg =" NADA CONSTA";
      }
    }else{
      $serasa_proprietario_car_msg = "NAOPOSSUICARRETA";
    }
    if ($proprietario_bi_log!="NAOPOSSUI"){
      $proprietario_bi = $this->ProprietarioLog->find('first', array('conditions'=>array('ProprietarioLog.codigo'=>$proprietario_car_log), 'recusive'=>-1)); 
      $cpf_proprietario_bi = $proprietario_car['ProprietarioLog']['codigo_documento'];
    if (isset( $cpf_proprietario_bi)){
      $str_prop_bi = strlen( $cpf_proprietario_bi);
    }
    $valor = array();
    if( $permite_consulta_serasa === TRUE ){
      if ($str_prop_bi==11){
        die('ERRO: NAO PODE');
        $serasa_proprietario_bi = file_get_contents("http://informacoes.buonny.com.br/bcb/index/consulta-informacoes-rest?codigoDocumento=".$cpf_proprietario_bi);
        if ( json_decode($serasa_proprietario_bi) )
          $consulta_serasa_proprietario_bitrem = TRUE;               
        $valor = $this->resultadoSerasaRobo($serasa_proprietario_bi,$cpf_proprietario_bi);
      }
    }  

    $total = count($valor);
    $serasa_proprietario_bi_msg = "Pendências proprietário bitrem CPF:".$cpf_proprietario_bi."\n";
    foreach ($valor as $key=>$serasa_proprietario_bi) {
      $proprietario_serasa_array = explode(";",$serasa_proprietario_bi);
      if ($key==$total-1){
        $serasa_proprietario_bi_msg .= $proprietario_serasa_array[2]." : R$ ". number_format($proprietario_serasa_array[3], 2, ',', '');
        if ($proprietario_serasa_array[1]=='CHEQUES S/FUNDOS-ACHEI CCF'){
          $serasa_proprietario_bi_msg .= "\n".$proprietario_serasa_array[1].'-'.$proprietario_serasa_array[2]." : Quantidade :".$proprietario_serasa_array[0]."\n";
        }
      } else {
        if ($proprietario_serasa_array[1]=='CHEQUES S/FUNDOS-ACHEI CCF'){
          $serasa_proprietario_bi_msg .= $proprietario_serasa_array[1].'-'.$proprietario_serasa_array[2]." : Quantidade :".$proprietario_serasa_array[0]."\n";
        } else {
          $serasa_proprietario_bi_msg .= $proprietario_serasa_array[1].'-'.$proprietario_serasa_array[2]." : R$ ". number_format($proprietario_serasa_array[3], 2, ',', '')."\n";
        }
      }
    }
    if( $total==0 ){
      $serasa_proprietario_bi_msg =" NADA CONSTA";
    }
  }else{
    $serasa_proprietario_bi_msg = "NAOPOSSUIBITREM";
  }        

  if ($profissional['Profissional']['codigo_documento']){
    if( $permite_consulta_serasa === TRUE ){
      die('ERRO: NAO PODE');
      $serasa_motorista = file_get_contents("http://informacoes.buonny.com.br/bcb/index/consulta-informacoes-rest?codigoDocumento=".$profissional['Profissional']['codigo_documento']);
      if ( json_decode($serasa_motorista) )
        $consulta_serasa_motorista = TRUE;
      $valor = $this->resultadoSerasaRobo($serasa_motorista,$profissional['Profissional']['codigo_documento']);
      $total = count($valor);
      @$serasa_motorista_msg .= "Pendências motorista CPF:".$profissional['Profissional']['codigo_documento']."\n";
      foreach ($valor as $key=>$motorista_serasa) {
        $motorista_serasa_array = explode(";",$motorista_serasa);
        if ($key==$total-1){
          $serasa_motorista_msg .= $motorista_serasa_array[2]." : R$ ". number_format($motorista_serasa_array[3], 2, ',', '');
          if ($motorista_serasa_array[1]=='CHEQUES S/FUNDOS-ACHEI CCF'){
            $serasa_motorista_msg .= "\n".$motorista_serasa_array[1].'-'.$motorista_serasa_array[2]." : Quantidade :".$motorista_serasa_array[0]."\n";
          } 
        }else{
          if ($motorista_serasa_array[1]=='CHEQUES S/FUNDOS-ACHEI CCF'){
            $serasa_motorista_msg .= $motorista_serasa_array[1].'-'.$motorista_serasa_array[2]." : Quantidade :".$motorista_serasa_array[0]."\n";
          }else{
            $serasa_motorista_msg .= $motorista_serasa_array[1].'-'.$motorista_serasa_array[2]." : R$ ". number_format($motorista_serasa_array[3], 2, ',', '')."\n";
          }
        }
      }
      if( $total==0 ) {
        $serasa_motorista_msg =" NADA CONSTA";
      }
    }
  }
  $result['serasa_motorista_msg']         = !empty($serasa_motorista_msg)         ? utf8_decode($serasa_motorista_msg)         : NULL;
  $result['serasa_proprietario_bi_msg']   = !empty($serasa_proprietario_bi_msg)   ? utf8_decode($serasa_proprietario_bi_msg)   : NULL;
  $result['serasa_proprietario_car_msg']  = !empty($serasa_proprietario_car_msg)  ? utf8_decode($serasa_proprietario_car_msg)  : NULL;
  $result['serasa_proprietario_vei_msg']  = !empty($serasa_proprietario_vei_msg)  ? utf8_decode($serasa_proprietario_vei_msg)  : NULL;
  $result['consultas_realizadas_serasa']  = array(
    'motorista'             => $consulta_serasa_motorista, 
    'proprietario_veiculo'  => $consulta_serasa_proprietario_veiculo, 
    'proprietario_carreta'  => $consulta_serasa_proprietario_carreta, 
    'proprietario_bitrem'   => $consulta_serasa_proprietario_bitrem
  );
  return $result;
  }  

  public function findExtracaoPorCodigo($codigo) {    
      $conditions = array('FichaScorecard.codigo'=>$codigo);
      return $this->findExtracao($conditions);
  }
  
  private function findExtracao($conditions) {
    $this->ProfissionalLog = ClassRegistry::init('ProfissionalLog');
    $this->ProprietarioLog = ClassRegistry::init('ProprietarioLog');
    $this->VeiculoLog = ClassRegistry::init('VeiculoLog');
    $this->bindModel(array(
      'hasOne' => array(
        'FichaScorecardVeiculo' => array('foreignKey'=>'codigo_ficha_scorecard')
      )
    ));
    $dados = $this->find('first', array('recursive'=>1, 'conditions'=>$conditions));

    if (!$dados) return false;
    if (isset($dados['FichaScorecardVeiculo']) && !empty($dados['FichaScorecardVeiculo']['codigo_proprietario_log'])) {
      $dados = array_merge($dados, $this->ProprietarioLog->find('first', array(
        'fields' => array('codigo_documento', 'nome_razao_social'),
        'conditions'=>array('codigo' => $dados['FichaScorecardVeiculo']['codigo_proprietario_log']),
        'recursive'=>-1
      )));
    }
    if (!empty($dados['FichaScorecard']['codigo_profissional_log'])) {
      $retorno = $this->ProfissionalLog->find('first', array(
        'fields' => array('codigo_documento', 'cnh', 'codigo_seguranca_cnh', 'nome', 'codigo_profissional', 'data_nascimento'),
        'conditions'=>array('codigo' => $dados['FichaScorecard']['codigo_profissional_log']),
        'recursive'=>-1
      ));
      if(is_array($retorno))
        $dados = array_merge($dados, $retorno);
    }
    if (isset($dados['FichaScorecardVeiculo']) && !empty($dados['FichaScorecardVeiculo']['codigo_veiculo_log'])) {
      $retorno = $this->VeiculoLog->find('first', array(
        'fields' => array('renavam'),
        'conditions'=>array('codigo' => $dados['FichaScorecardVeiculo']['codigo_veiculo_log']),
        'recursive'=>-1
      ));
      if(is_array($retorno))
        $dados['FichaScorecardVeiculo'] = array_merge($dados['FichaScorecardVeiculo'], $retorno);
    }
    $dadosExtracao = array(
      'FichaScorecard' => array(
        'codigo' => $dados['FichaScorecard']['codigo'],
        'extracao' => $dados['FichaScorecard']['extracao'],
        'codigo_cliente' => $dados['FichaScorecard']['codigo_cliente'],
      ),
      'Profissional' => array(
        'codigo' => isset($dados['ProfissionalLog']['codigo_profissional']) ? $dados['ProfissionalLog']['codigo_profissional'] : '',
        'cpf' => isset($dados['ProfissionalLog']['codigo_documento']) ? $dados['ProfissionalLog']['codigo_documento'] : '',
        'cnh' => isset($dados['ProfissionalLog']['cnh']) ? $dados['ProfissionalLog']['cnh'] : '',
        'cnh_seguranca' => isset($dados['ProfissionalLog']['codigo_seguranca_cnh']) ? $dados['ProfissionalLog']['codigo_seguranca_cnh'] : '',
        'nome' => isset($dados['ProfissionalLog']['nome']) ? $dados['ProfissionalLog']['nome'] : '',
        'data_nascimento' => isset($dados['ProfissionalLog']['data_nascimento']) ? $dados['ProfissionalLog']['data_nascimento'] : '',
      ),
      'Proprietario' => array(
        'cpf' => isset($dados['ProprietarioLog']) ? $dados['ProprietarioLog']['codigo_documento'] : '',
        'nome' => isset($dados['ProprietarioLog']) ? $dados['ProprietarioLog']['nome_razao_social'] : '',
      ),
      'Veiculo' => array(
        'renavam' => isset($dados['FichaScorecardVeiculo']['VeiculoLog']['renavam']) ? $dados['FichaScorecardVeiculo']['VeiculoLog']['renavam'] : ''
      )
    );
    return $dadosExtracao;
  }
  
  public function gravaStatus($codigo_score){
      //$dados_ficha['FichaScorecard']['codigo_ficha_scorecard'] = $codigo_ficha;
      $dados_ficha['FichaScorecard']['codigo_parametro_score'] = $codigo_score;
      $this->save(array(
        'extracao' => $dados_extraidos,
        'codigo_status' => FichaScorecardStatus::A_PESQUISAR
     ));
  }


  public function gravaExtracao($id, $dados_extraidos) {
    $this->id = $id;    
    $this->save(array(
        'extracao' => $dados_extraidos,
        'codigo_status' => FichaScorecardStatus::A_PESQUISAR
    ));
  }
  
  public function buscaPorCPF( $codigo_documento, $tipocpf=NULL, $codigo_cliente=NULL ) {
    $codigo_documento       = preg_replace('/\D/', '', $codigo_documento);
    $this->ProfissionalLog  = ClassRegistry::init('ProfissionalLog');
    $this->Profissional     = ClassRegistry::init('Profissional');
    $this->EnderecoCidade   = ClassRegistry::init('EnderecoCidade');    
    $this->QuestaoResposta = ClassRegistry::init('QuestaoResposta');
    $this->ProfissionalContatoLog = ClassRegistry::init('ProfissionalContatoLog');
    $this->VeiculoLog = ClassRegistry::init('VeiculoLog');
    $this->ProprietarioLog = ClassRegistry::init('ProprietarioLog');
    $this->ProprietarioContatoLog = ClassRegistry::init('ProprietarioContatoLog');
    
    $codigo_profissional_log = $this->ProfissionalLog->find('all', array( 
      'conditions'=>array('ProfissionalLog.codigo_documento'=>$codigo_documento ), 
      'fields'    =>array('ProfissionalLog.codigo', 'ProfissionalLog.codigo_profissional'),
      'order' => 'ProfissionalLog.codigo DESC' ));
    $codigo_profissional     = Set::extract('/ProfissionalLog/codigo_profissional', $codigo_profissional_log);
    $codigo_profissional_log = Set::extract('/ProfissionalLog/codigo', $codigo_profissional_log);
    if(empty($codigo_profissional_log)){
      return false;
    }
    $ultima_ficha = $this->carregaFichaAnteriorProfissional( $codigo_profissional[0] );
    $conditions   = array( 'ProfissionalLog.codigo_documento' => $codigo_documento );
    if(!empty($ultima_ficha['FichaScorecard']) )
      array_push($conditions, array( 'FichaScorecard.codigo' => $ultima_ficha['FichaScorecard']['codigo'] ));
    $this->bindModel(
      array(
          'belongsTo'=>array(
              'Cliente'=>array('foreignKey'=>'codigo_cliente'),
              'Usuario'=>array('foreignKey'=>'codigo_usuario_responsavel'),
              'ProfissionalLog'=>array('foreignKey'=>'codigo_profissional_log'),
              'Profissional'=>array('foreignKey'=>FALSE, 'conditions' =>array('Profissional.codigo = ProfissionalLog.codigo_profissional') ),
              'ProfissionalEnderecoLog'=>array('foreignKey'=>'codigo_profissional_endereco_log'),
              'VEndereco'=>array('foreignKey'=>false, 'conditions'=>'ProfissionalEnderecoLog.codigo_endereco = VEndereco.endereco_codigo'),
              'EnderecoCidadeOrigem' => array('className'=>'EnderecoCidade', 'foreignKey' => 'codigo_endereco_cidade_carga_origem'),
              'EnderecoCidadeDestino' => array('className'=>'EnderecoCidade', 'foreignKey' => 'codigo_endereco_cidade_carga_destino'),
          ),
          'hasMany'=>array(
              'FichaScorecardRetorno'=>array('foreignKey'=>'codigo_ficha_scorecard'),
              'FichaScProfContatoLog'=>array('foreignKey'=>'codigo_ficha_scorecard'),
              'FichaScorecardVeiculo'=>array('foreignKey'=>'codigo_ficha_scorecard'),
              'FichaScorecardQuestaoResp' => array('foreignKey'=>'codigo_ficha_scorecard'),
          )
      )
    );         
    $dados_ficha = $this->find('first', compact('conditions') );

    if (!empty($dados_ficha)) {
      if(isset($dados_ficha['VEndereco']['endereco_cep']))
        $dados_ficha['ProfissionalEnderecoLog']['cep'] = $dados_ficha['VEndereco']['endereco_cep'];
        if ( isset($dados_ficha['FichaScorecardVeiculo']) ){
            foreach($dados_ficha['FichaScorecardVeiculo'] as $key=> $ficha_scorecard_veiculo ){
                $this->VeiculoLog->bindModel(array(
                    'belongsTo' => array(
                            'VeiculoModelo'  => array('foreignKey' => 'codigo_veiculo_modelo'),
                            'EnderecoCidade' => array('foreignKey' => 'codigo_cidade_emplacamento'),
                            'EnderecoEstado' => array('foreignKey' => false, 'conditions'=>'EnderecoCidade.codigo_endereco_estado = EnderecoEstado.codigo'),
                    ),
                ));
                $veiculo = $this->VeiculoLog->find('first', array('conditions' =>
                        array('VeiculoLog.codigo'=>$ficha_scorecard_veiculo['codigo_veiculo_log'])
                    )
                );
                $dados_ficha['FichaScorecardVeiculo'][$key]['VeiculoLog'] = $veiculo['VeiculoLog'];
                $dados_ficha['FichaScorecardVeiculo'][$key]['VeiculoLog']['codigo_veiculo_tecnologia']   = $ficha_scorecard_veiculo['codigo_tecnologia'];
                $dados_ficha['FichaScorecardVeiculo'][$key]['VeiculoLog']['codigo_veiculo_fabricante']   = $veiculo['VeiculoModelo']['codigo_veiculo_fabricante'];
                $dados_ficha['FichaScorecardVeiculo'][$key]['VeiculoLog']['codigo_estado_emplacamento']  = $veiculo['EnderecoCidade']['codigo_endereco_estado'];                
                $dados_ficha['FichaScorecardVeiculo'][$key]['VeiculoLog']['cidade_emplacamento']         = $veiculo['EnderecoCidade']['descricao'].' - '.$veiculo['EnderecoEstado']['abreviacao'];
                if(!empty($ficha_scorecard_veiculo['codigo_proprietario_endereco_log'])){
                  $this->ProprietarioLog->bindModel(
                    array(
                        'belongsTo'=>array(
                            'ProprietarioEnderecoLog'=>array('foreignKey'=>false, 'conditions'=>'ProprietarioEnderecoLog.codigo = '.$ficha_scorecard_veiculo['codigo_proprietario_endereco_log']),
                            'VEndereco'=>array('foreignKey'=>false, 'conditions'=>'ProprietarioEnderecoLog.codigo_endereco = VEndereco.endereco_codigo'),
                        ),
                        'hasMany'=>array(
                            'FichaPropContatoLog'=>array('className'=>'FichaScVeicPropContatoLog', 'foreignKey'=>false, 'conditions'=>'FichaPropContatoLog.codigo_ficha_scorecard_veiculo = '.$ficha_scorecard_veiculo['codigo']),
                        )
                    )
                );
                $proprietario = $this->ProprietarioLog->find('first', array('conditions'=>array('ProprietarioLog.codigo'=>$ficha_scorecard_veiculo['codigo_proprietario_log'])));
                  if(!empty($proprietario)){
                    $dados_ficha['FichaScorecardVeiculo'][$key]['Proprietario']         = $proprietario['ProprietarioLog'];
                    $dados_ficha['FichaScorecardVeiculo'][$key]['ProprietarioEndereco'] = $proprietario['ProprietarioLog'];
                    $dados_ficha['FichaScorecardVeiculo'][$key]['ProprietarioEndereco'] = $proprietario['ProprietarioEnderecoLog'];
                    $dados_ficha['FichaScorecardVeiculo'][$key]['ProprietarioEndereco']['endereco_cep'] = $proprietario['VEndereco']['endereco_cep'];                    
                    $dados_ficha['Motorista'][$key]['proprietario'] = ($dados_ficha['Profissional']['codigo_documento']==$dados_ficha['FichaScorecardVeiculo'][$key]['Proprietario']['codigo_documento'] ? 1 : 0);
                  }
                }            
          }
        }
    } else {//Não possui fichas
      $dados_profissional = $this->ProfissionalLog->carregarDadosCadastraisLog( $codigo_profissional_log[0] ) ;
      $dados_ficha = $dados_profissional;
      $dados_ficha['FichaScorecard']['codigo'] = NULL;
      $dados_ficha['FichaScorecard']['data_inclusao'] = '';
      $dados_ficha['ProfissionalEnderecoLog']['cep']  = $dados_ficha['ProfissionalEnderecoLog']['endereco_cep'];
    }
    
    APP::import('Model', 'FichaScorecardStatus');
    $this->bindModel(array('belongsTo'=>array('ProfissionalLog'=>array('foreignKey'=>'codigo_profissional_log'))));         
    $conditions = array( 
      'ProfissionalLog.codigo_documento' => $codigo_documento,
      'FichaScorecard.codigo_status <>' => FichaScorecardStatus::FINALIZADA,
    );
    $fields = array('FichaScorecard.codigo_cliente', 'FichaScorecard.codigo_profissional_tipo');
    $fichas_em_pesquisa    = $this->find('first', compact('conditions', 'fields') );
    $tipo_profissional     = Set::extract('/FichaScorecard/codigo_profissional_tipo', $fichas_em_pesquisa);
    
  
    $this->ProfissionalTipo = ClassRegistry::init('ProfissionalTipo');
    $dados_ficha['Carreteiro']['total'] = ( in_array(ProfissionalTipo::CARRETEIRO, $tipo_profissional) ? 1 : 0 );    
    $dados_ficha['Cliente']['total'] = 0;
    if( $fichas_em_pesquisa ){
      foreach( $fichas_em_pesquisa as $dados ){
        if ( ($dados['codigo_profissional_tipo'] != ProfissionalTipo::CARRETEIRO) && ($dados['codigo_cliente']==$codigo_cliente) ){
          $dados_ficha['Cliente']['total'] = 1;
        }
      }
    }
    return $dados_ficha;
  }

  public function validaPorCPF($codigo_documento, $tipo_profissional) {
      $this->ProfissionalLog = ClassRegistry::init('ProfissionalLog');
      $codigo_documento = preg_replace('/\D/', '', $codigo_documento);        
      $joins  =  array(array( "table"     => $this->ProfissionalLog->databaseTable.'.'.$this->ProfissionalLog->tableSchema.'.'.$this->ProfissionalLog->useTable,
                              "alias"     => "ProfissionalLog",
                              "type"      => "INNER",
                              "conditions"=> array("ProfissionalLog.codigo = FichaScorecard.codigo_profissional_log")
                            ),
                     );
      $conditions['ProfissionalLog.codigo_documento'] = $codigo_documento;
      $conditions['FichaScorecard.codigo_profissional_tipo'] = $tipo_profissional;
      $conditions['FichaScorecard.codigo_status <>']='7' ;

      $count_profissional_carr = $this->find('count', array('joins'=>$joins,'conditions'=>$conditions));        

      return $count_profissional_carr==0 ? true : false;        
  }

    public function buscaPorCPFCarreteiro($codigo_documento) {
      $this->ProfissionalLog = ClassRegistry::init('ProfissionalLog');
      $codigo_documento = preg_replace('/\D/', '', $codigo_documento);
      $fields = array('count(*) as total');
      $joins  =  array(array( "table"     => $this->ProfissionalLog->databaseTable.'.'.$this->ProfissionalLog->tableSchema.'.'.$this->ProfissionalLog->useTable,
                            "alias"     => "ProfissionalLog",
                            "type"      => "INNER",
                            "conditions"=> array("ProfissionalLog.codigo = FichaScorecard.codigo_profissional_log")
                          ),
                   );

        $conditions['codigo_documento'] = $codigo_documento;
        $conditions['codigo_status <>']='7' ;
        $conditions['FichaScorecard.codigo_profissional_tipo']='1' ;

        $count_profissional_carr = $this->find('all', array('fields'=>$fields ,'joins'=>$joins,'conditions'=>$conditions));
        
        $ficha['Carreteiro']['total'] = $count_profissional_carr[0][0]['total'];
        $ficha['Cliente']['total']    = 0;
    
        $ficha['FichaScorecard']['observacao'] ='';
        $ficha['FichaScorecard']['extracao'] ='';
        $ficha['FichaScorecard']['observacao_supervisor'] ='';
        $ficha['FichaScorecard']['justificativa_alteracao'] ='';
        $ficha['FichaScorecard']['resumo'] ='';
        $ficha['FichaScorecard']['codigo_carga_valor'] ='';
        $ficha['FichaScorecard']['codigo_status'] ='';
        $ficha['FichaScorecard']['ativo'] ='';
        $ficha['FichaScorecard']['codigo_profissional_tipo'] ='';
        $ficha['FichaScorecard']['codigo_carga_tipo'] ='';
        $ficha['FichaScorecard']['codigo'] ='';
        $ficha['FichaScorecard']['codigo_cliente'] ='';
        $ficha['FichaScorecard']['codigo_profissional_log'] ='';
        $ficha['FichaScorecard']['codigo_endereco_cidade_carga_origem'] ='';
        $ficha['FichaScorecard']['codigo_endereco_cidade_carga_destino'] ='';
        $ficha['FichaScorecard']['codigo_usuario_inclusao'] ='';
        $ficha['FichaScorecard']['codigo_embarcador'] ='';
        $ficha['FichaScorecard']['codigo_transportador'] ='';
        $ficha['FichaScorecard']['codigo_parametro_score']='';
        $ficha['FichaScorecard']['percentual_pontos'] ='';
        $ficha['FichaScorecard']['total_pontos']='';
        $ficha['FichaScorecard']['codigo_ficha_teleconsult'] ='';
        $ficha['FichaScorecard']['codigo_usuario_alteracao'] ='';
        $ficha['FichaScorecard']['codigo_usuario_responsavel'] ='';
        $ficha['FichaScorecard']['codigo_usuario_em_pesquisa'] ='';
        $ficha['FichaScorecard']['codigo_usuario_em_aprovacao'] =''; 
        $ficha['FichaScorecard']['codigo_cliente_embarcador'] ='';
        $ficha['FichaScorecard']['codigo_cliente_transportador'] ='';
        $ficha['FichaScorecard']['codigo_profissional_endereco_log'] =''; 
        $ficha['FichaScorecard']['codigo_usuario'] ='';
        $ficha['FichaScorecard']['data_validade'] ='';
        $ficha['FichaScorecard']['data_inclusao'] ='';
        $ficha['FichaScorecard']['data_alteracao'] ='';
        $ficha['FichaScorecard']['codigo_endereco_estado_carga_origem'] ='';
        $ficha['FichaScorecard']['codigo_endereco_estado_carga_destino'] ='';
        $ficha['ProfissionalLog']['observacao'] =''; 
        $ficha['ProfissionalLog']['codigo_tipo_cnh'] ='';
        $ficha['ProfissionalLog']['codigo_profissional_tipo'] =''; 
        $ficha['ProfissionalLog']['codigo_estado_rg'] ='';
        $ficha['ProfissionalLog']['codigo_modulo'] ='';
        $ficha['ProfissionalLog']['codigo_endereco_estado_emissao_cnh'] ='';
        $ficha['ProfissionalLog']['codigo'] ='';
        $ficha['ProfissionalLog']['codigo_profissional'] ='';
        $ficha['ProfissionalLog']['codigo_endereco_cidade_naturalidade'] ='';
        $ficha['ProfissionalLog']['codigo_usuario_inclusao'] ='';
        $ficha['ProfissionalLog']['rg_data_emissao'] ='';
        $ficha['ProfissionalLog']['data_nascimento'] ='';
        $ficha['ProfissionalLog']['cnh_vencimento'] ='';
        $ficha['ProfissionalLog']['data_inclusao'] ='';
        $ficha['ProfissionalLog']['data_primeira_cnh'] ='';
        $ficha['ProfissionalLog']['estrangeiro'] ='';
        $ficha['ProfissionalLog']['codigo_seguranca_cnh'] ='';
        $ficha['ProfissionalLog']['codigo_documento'] ='';
        $ficha['ProfissionalLog']['nome'] ='';
        $ficha['ProfissionalLog']['rg'] ='';
        $ficha['ProfissionalLog']['cnh'] ='';
        $ficha['ProfissionalLog']['nome_pai'] ='';
        $ficha['ProfissionalLog']['nome_mae'] ='';
        $ficha['ProfissionalLog']['codigo_endereco_estado_naturalidade'] ='';
        

    return $ficha; 
  }
   

  public  function parametros_fichas_a_pesquisar($filtros){
    $conditions = array();
    $this->FichaScorecardLog = ClassRegistry::init('FichaScorecardLog');
    $this->Usuario = ClassRegistry::init('Usuario');
    if(isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])){
      $conditions['Cliente.codigo'] = $filtros['codigo_cliente'];
    }
    if(isset($filtros['codigo_seguradora']) && !empty($filtros['codigo_seguradora'])){
      $conditions['Seguradora.codigo'] = $filtros['codigo_seguradora'];
    }
    if(isset($filtros['codigo_ficha_scorecard']) && !empty($filtros['codigo_ficha_scorecard'])){
      $conditions['FichaScorecard.codigo'] = $filtros['codigo_ficha_scorecard'];
    }
    if(isset($filtros['codigo_documento']) && !empty($filtros['codigo_documento'])){
      if (strlen($filtros['codigo_documento']) == 11) {
          $conditions['ProfissionalLog.codigo_documento'] = preg_replace('/[^\d]+/', '', $filtros['codigo_documento']);
        }else{
          $conditions['ProfissionalLog.codigo_documento like '] = "%".preg_replace('/[^\d]+/', '', $filtros['codigo_documento'])."%";
      }
    }
    if (!empty($filtros['filtro_status_ficha'])) {
      unset($filtros['codigo_status']);
      $conditions['FichaScorecard.codigo_status'] = $filtros['filtro_status_ficha'];
    }

    if(!empty($filtros['origem_ficha'])) {
      if($filtros['origem_ficha']=='W') {
        $conditions['FichaScorecard.origem_ficha']  = 'W' ;
      }else{
        $conditions['FichaScorecard.origem_ficha']  = 'E' ;
      }
    }

    if(!empty($filtros['cliente_vip'])){
             $conditions['ClienteProdutoVip.cliente_vip']  = true ;
    }else{
           unset($filtros['cliente_vip']);
    }
        
    if(!empty($filtros['dentro_prazo']) and !empty($filtros['fora_prazo'])) {
      unset($filtros['fora_prazo']);
      unset($filtros['dentro_prazo']);
    }else{  

       
      if (isset($filtros['tipos_prazo'])) {
          if($filtros['tipos_prazo']=='0'){
            $conditions['DATEDIFF(mi,DATEADD(MI,-90,getdate()),[FichaScorecard].[data_inclusao]) <']  = '0' ;
          } 
                if($filtros['tipos_prazo']=='1'){
            $conditions['DATEDIFF(mi,DATEADD(MI,-90,getdate()),[FichaScorecard].[data_inclusao]) >=']  = '0' ;
          } 
        }   
    }
        
        
    if(isset($filtros['codigo_tipo_profissional']) && $filtros['codigo_tipo_profissional'] != null ){
      if($filtros['codigo_tipo_profissional'] == ProfissionalTipo::CARRETEIRO)
          $conditions['ProfissionalTipo.codigo'] = ProfissionalTipo::CARRETEIRO;
      if($filtros['codigo_tipo_profissional'] == ProfissionalTipo::OUTROS)
          $conditions['ProfissionalTipo.codigo <>'] = ProfissionalTipo::CARRETEIRO;
      
    }
    
    if(!empty($filtros['apenas_renovadas'])){
             $conditions['FichaScorecard.codigo_usuario_inclusao']  = 159 ;
    }else{
           unset($filtros['apenas_renovadas']);
    }
    if(isset($filtros['codigo_status']) && $filtros['codigo_status'] != null ){
      $conditions['FichaScorecard.codigo_status'] = $filtros['codigo_status'];
    }
        $dbo   = $this->FichaScorecardLog->getDataSource();
        $query = $this->FichaScorecardLog->find('sql', array(
                'fields' => array('top 1 Usuario.nome as nome_responsavel'),
                'joins'  => array(
                    array(
                        "table"     => $this->Usuario->databaseTable.'.'.$this->Usuario->tableSchema.'.'.$this->Usuario->useTable,
                        "alias"     => "Usuario",
                        "type"      => "LEFT",
                        "conditions"=> array("Usuario.codigo = FichaScorecardLog.codigo_usuario_responsavel")
                    ),
                ),
                'conditions' => array(
                  "FichaScorecardLog.codigo_usuario_responsavel IS NOT NULL",
                  "FichaScorecardLog.codigo_ficha_scorecard = FichaScorecard.codigo"
              ),
              'order' => 'FichaScorecardLog.codigo DESC'
            )
        );  
    $fields = array(
        'ClienteProdutoVip.cliente_vip as cliente_vip',
        'Cliente.codigo                   AS codigo_cliente',
        'Cliente.razao_social             AS razao_social',
        'Seguradora.codigo                AS codigo_seguradora' ,
        'Seguradora.nome                  AS nome_seguradora' ,
        'FichaScorecard.tempo_sla         AS tempo_sla',
        'FichaScorecard.codigo            AS codigo_ficha',
        'FichaScorecard.codigo_status     AS codigo_status',
        'FichaScorecard.codigo_usuario_responsavel   AS codigo_usuario_responsavel',
        'FichaScorecard.codigo_usuario_alteracao     AS codigo_usuario_alteracao',
        'FichaScorecard.codigo_usuario_em_pesquisa   AS codigo_usuario_em_pesquisa',
        'FichaScorecard.codigo_usuario_em_aprovacao  AS codigo_usuario_em_aprovacao',
        'FichaScorecard.codigo_usuario_inclusao      AS codigo_usuario_inclusao',
        'CONVERT(VARCHAR(20), FichaScorecard.data_inclusao, 20) AS data_inclusao',
        'ProfissionalTipo.descricao       AS profissional_descricao',
        'ProfissionalTipo.codigo          AS codigo_tipo_profissional',
        'ProfissionalLog.nome             AS profissional_nome',
        'ProfissionalLog.codigo_documento AS codigo_documento',
        'CASE WHEN FichaScorecard.codigo_status != '.FichaScorecardStatus::A_APROVAR.' THEN Usuario.apelido
            WHEN FichaScorecard.codigo_status = ' .FichaScorecardStatus::A_APROVAR.' THEN Usuario.apelido                 
              END  
        AS nome_responsavel'
    );
    //Alterado (Acima) o nome para apelido
    $this->bindModel(
      array('belongsTo' => array(
        'Cliente' => array(
          'foreignKey' => false,
          'conditions' => 'FichaScorecard.codigo_cliente = Cliente.codigo'
        ),
        'ClienteProdutoVip' => array(
          'foreignKey' => false,
          'conditions' => 'FichaScorecard.codigo_cliente = ClienteProdutoVip.codigo_cliente and ClienteProdutoVip.codigo_produto=1'
        ),
        
        'Seguradora' => array(
          'foreignKey' => false,
          'conditions' => 'Cliente.codigo_seguradora = Seguradora.codigo'
        ),
        'ProfissionalLog' => array(
          'foreignKey' => false,
          'conditions' => 'FichaScorecard.codigo_profissional_log = ProfissionalLog.codigo'
        ),
        'ProfissionalTipo' => array(
          'foreignKey' => false,
          'conditions' => 'ProfissionalTipo.codigo = FichaScorecard.codigo_profissional_tipo'
        ),
          'Usuario' => array(
            'foreignKey' => false,
            'conditions' => 'Usuario.codigo = FichaScorecard.codigo_usuario_responsavel'
          ),
      )), false
    );
  
    $limit     = 50;
    $order     = array('data_inclusao ASC', 'codigo_ficha DESC');
  
    return compact('conditions','fields','limit','order');
  }
  
  public function buscaCodigoProfissional($codigo_ficha) {
    $this->bindModel(array('belongsTo' => array('ProfissionalLog' => array('foreignKey' => 'codigo_profissional_log'))));
    $ficha = $this->find('first', array('fields' => array('ProfissionalLog.codigo_profissional'), 'conditions' => array('FichaScorecard.codigo' => $codigo_ficha)));
    return $ficha['ProfissionalLog']['codigo_profissional'];
  }


  public function buscaCodigoProfissionalCodDoc($codigo_documento) {
    $this->bindModel(array('belongsTo' => array('ProfissionalLog' => array('foreignKey' => 'codigo_profissional_log'))));
    $ficha = $this->find('first', array('fields' => array('ProfissionalLog.codigo_profissional'), 'conditions' => array('ProfissionalLog.codigo_documento' => $codigo_documento)));
    return $ficha['ProfissionalLog']['codigo_profissional'];
  }

  public function buscaTipoProfissional($codigo_ficha) {
    $this->bindModel(array('belongsTo' => array('ProfissionalLog' => array('foreignKey' => 'codigo_profissional_log'))));
    $ficha = $this->find('first', array('fields' => array('FichaScorecard.codigo_profissional_tipo'), 'conditions' => array('FichaScorecard.codigo' => $codigo_ficha)));
    return $ficha['FichaScorecard']['codigo_profissional_tipo'];
  }
  
  public function buscaCodigoCliente($codigo_ficha) {
    $codigo_cliente = $this->field('FichaScorecard.codigo_cliente', array('FichaScorecard.codigo' => $codigo_ficha));
    return $codigo_cliente;
  }
  
  public function buscaResumo($codigo_ficha) {
    return $this->field('FichaScorecard.resumo', array('FichaScorecard.codigo' => $codigo_ficha));
  }
  
  public function buscaValidade($codigo_ficha) {
    return $this->field('FichaScorecard.data_validade', array('FichaScorecard.codigo' => $codigo_ficha));
  }

  public function buscaDataFicha($codigo_ficha) {
    return $this->field('FichaScorecard.data_inclusao', array('FichaScorecard.codigo' => $codigo_ficha));
  }
     
  public function buscaPenultimaFicha( $codigo_ficha, $codigo_profissional ) {
    $codigo_profissional_log = $this->field('FichaScorecard.codigo_profissional_log', array('FichaScorecard.codigo' => $codigo_ficha));
    $codigo_profissional     = $this->ProfissionalLog->field('ProfissionalLog.codigo_profissional', array('ProfissionalLog.codigo' => $codigo_profissional_log));
    return  $this->ProfissionalLog->field('ProfissionalLog.data_inclusao', array('ProfissionalLog.codigo' => $codigo_profissional_log,'ProfissionalLog.codigo_profissional' => $codigo_profissional));
  }
 

  public function buscaEmbarcador($codigo_ficha) {
    $this->Cliente = ClassRegistry::init('Cliente');
    return $this->field('(select razao_social from '.$this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable.' where  codigo=codigo_cliente_embarcador)
                              as codigo_cliente_embarcador'
                             , 
    array('FichaScorecard.codigo' => $codigo_ficha));
  }

  public function buscaTransportador($codigo_ficha) {
    $this->Cliente = ClassRegistry::init('Cliente');
    return $this->field('(select razao_social from '.$this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable.' where  codigo=codigo_cliente_transportador) 
                             as codigo_cliente_transportador', 
    array('FichaScorecard.codigo' => $codigo_ficha));
  }

    public function atualizaPendente($codigo_ficha,$codigo_usuario_responsavel = null, $situacao=null) {
    $data = array(
      'codigo' => $codigo_ficha,
      
    );
    //die($situacao);
    if (!empty($situacao)) {
      //die($situacao);
      switch ($situacao) {
        case 'pesquisando': // Em Pesquisa
          $data['codigo_usuario_responsavel'] = $codigo_usuario_responsavel; 
          $data['codigo_usuario_em_pesquisa'] = $codigo_usuario_responsavel; 
          break;
        case 'pendente': // Em Pesquisa
          $data['codigo_usuario_em_pesquisa'] = null; 
          break;
        case 'aprovar': // Em Aprovacao
          $data['codigo_usuario_em_pesquisa'] = null; 
          $data['codigo_usuario_alteracao'] = $codigo_usuario_responsavel;
          $data['codigo_usuario_em_aprovacao'] = $codigo_usuario_responsavel;
          break;
        case 'reprovar': // Reprovando e devolvendo
          $data['codigo_usuario_em_aprovacao'] = null; 
          $data['codigo_usuario_em_pesquisa'] = null;
          break; 
        case 'pesquisador_automatico':
          $data['codigo_usuario_em_pesquisa'] = 27046; 
          $data['codigo_usuario_alteracao'] = 27046;
          $data['codigo_usuario_em_aprovacao'] = 27046;
          break;
        default:
          $data['codigo_usuario_alteracao'] = null;
          $data['codigo_usuario_em_aprovacao'] = null;
          break;
      }
    }
    
    if($situacao=='pesquisando') {
      //checar  se o responsavel esta vazio 
      $conditions['codigo'] = $codigo_ficha;
      $conditions['codigo_usuario_responsavel != '] = '';
      $valor=$this->find('count', array('conditions'=>$conditions ));
      if($valor == 1) { 
        //echo 'NAO GRAVA'; 
      } else { 
        return $this->save($data); 
      }
    } else { //die('atualizaStatus');
      if($this->find('count', array('conditions'=>$data)) == 0) {
        return $this->save($data); 
      } 
    }
  } 
  
  public function alterarStatusScore($codigo_ficha,$ativo){
    $data = array(
      'codigo' => $codigo_ficha,
      'ativo' => $ativo
    );
     
    return $this->saveall($data);
  }

  public function atualizaStatus($codigo_ficha, $status, $codigo_usuario_responsavel = null, $situacao=null) {
    $data  = array( 'codigo' => $codigo_ficha, 'codigo_status' => $status );
    $ficha = $this->carregar( $codigo_ficha );
    if (!empty($situacao)) {
      switch ($situacao) {
        case 'altera_perfil':
        break;
        case 'pesquisando': // Em Pesquisa
          if( empty($ficha['FichaScorecard']['codigo_usuario_responsavel'] ))
            $data['codigo_usuario_responsavel']  = $codigo_usuario_responsavel; 

          $data['codigo_usuario_em_pesquisa']  = $codigo_usuario_responsavel;
          $data['codigo_usuario_em_aprovacao'] = NULL;
          break;
        case 'pendente': // Em Pesquisa
          $data['codigo_usuario_em_pesquisa'] = null; 
          break;
        case 'aprovar': // Em Aprovacao
          // $data['codigo_usuario_em_pesquisa'] = null; 
          // $data['codigo_usuario_alteracao'] = $codigo_usuario_responsavel;
          $data['codigo_usuario_em_aprovacao'] = $codigo_usuario_responsavel;
          break;
        case 'reprovar': // Reprovando e devolvendo
          $data['codigo_usuario_em_aprovacao'] = null;
          $data['codigo_usuario_em_pesquisa'] = null;
          break; 
        case 'pesquisador_automatico':
          //$data['codigo_usuario_em_pesquisa'] = 27046;
          $data['codigo_usuario_alteracao'] = 27046;
          $data['codigo_usuario_responsavel'] = 27046;
          break;
        default:
          $data['codigo_usuario_alteracao'] = null;
          $data['codigo_usuario_em_aprovacao'] = null;
          break;
      }
    }  

    if($situacao=='pesquisando') {
      if( $ficha['FichaScorecard']['codigo_usuario_em_pesquisa'] ) {
        if($ficha['FichaScorecard']['codigo_status'] == FichaScorecardStatus::EM_PESQUISA){
          if($ficha['FichaScorecard']['codigo_usuario_em_pesquisa'] == $codigo_usuario_responsavel)
            return true;
          else
            return false;
        }else{
          unset($data['codigo_usuario_responsavel']);
          return $this->save($data); 
        }
      } else {
        return $this->save($data); 
      }
    } elseif($situacao=='aprovar') {
      if( $ficha['FichaScorecard']['codigo_usuario_em_aprovacao'] ) {
        if($ficha['FichaScorecard']['codigo_status'] == FichaScorecardStatus::EM_APROVACAO){
          return ($ficha['FichaScorecard']['codigo_usuario_em_aprovacao'] == $codigo_usuario_responsavel);
        }
      }
      return $this->save($data);
    } else {
      if($this->find('count', array('conditions'=>$data)) == 0) {
        return $this->save($data); 
      } 
    }
    return true;
  }   
  
  public function reprovarFicha($codigo_ficha, $observacao_supervisor=null,  $codigo_usuario_responsavel){
    APP::import('Model', 'FichaScorecardStatus');
    $data = array(
      'codigo' => $codigo_ficha,
      'observacao_supervisor' => $observacao_supervisor,
      'codigo_status' => FichaScorecardStatus::PENDENTE,
      'codigo_usuario_em_aprovacao' => null,
      'codigo_usuario_em_pesquisa' => null
    );
    return $this->save($data);
  }

  public function pendenteAprovacaoFicha($codigo_ficha, $codigo_usuario_responsavel, $data_ficha){
    APP::import('Model', 'FichaScorecardStatus');
    $FichaStatusCriterio = ClassRegistry::init('FichaStatusCriterio');
    $data = array(
      'codigo' => $codigo_ficha,
      'codigo_status' => FichaScorecardStatus::EM_APROVACAO,
      'observacao_supervisor' => $data_ficha['FichaStatusCriterio']['observacao_supervisor'],
      'resumo' => $data_ficha['FichaStatusCriterio']['resumo']
    );
    unset($data_ficha['FichaStatusCriterio']['observacao_supervisor']);
    unset($data_ficha['FichaStatusCriterio']['BotaoClicado']);
    unset($data_ficha['FichaStatusCriterio']['resumo']);
    $dados           = $FichaStatusCriterio->formatarDados( $data_ficha, $codigo_ficha, $_SESSION['Auth']['Usuario']['codigo'] );
    $salva_criterios = $FichaStatusCriterio->salvarFichaStatusCriterio( $codigo_ficha, $dados, TRUE );
    $gravar_pontos   = $FichaStatusCriterio->atualizarParaGravarPontosCriterio( $codigo_ficha, TRUE );
    return $this->save($data);
  }
  
  public function atualizarResumo($codigo_ficha, $resumo_ficha){
    $data = array(
      'codigo' => $codigo_ficha,
      'resumo' => $resumo_ficha
    );
    return $this->save($data);
  }
  
  public function liberarResponsavelFicha($codigo_ficha, $situacao=null){
    if($situacao=='pesquisa' || $situacao==null) {
      $data = array(
        'codigo' => $codigo_ficha,
        'codigo_status'=>FichaScorecardStatus::PENDENTE, 
        // Apenas grava NULL para Codigo Usuario Em Pesquisa
        'codigo_usuario_em_pesquisa' => null);
    } 
    if ($situacao=='aprovacao') { 
      $data = array(
        'codigo' => $codigo_ficha,
        'codigo_status'=>FichaScorecardStatus::A_APROVAR, 
        // Libera a Ficha para aprovar
        'codigo_usuario_em_aprovacao' => null);
    }
    return $this->save($data);
  }
  
  public function buscarPontuacao($codigo_ficha) {
    $this->bindModel(array('belongsTo'=>array('ParametroScore'=>array('foreignKey'=>'codigo_parametro_score'))));
    $ficha = $this->find('first', array('conditions'=>array('FichaScorecard.codigo'=>$codigo_ficha)));
    
    $i = 0;
    $pontos = 0;
    if(!empty($ficha['ParametroScore']['pontos'])){
      do{
        $i++;
        $pontos++;
      }while($i*20 < $ficha['ParametroScore']['pontos']);
    }
    
    $data = array(
      'total_pontos' => $ficha['FichaScorecard']['total_pontos'],
      'percentual_pontos' => $ficha['FichaScorecard']['percentual_pontos'],
      'nivel' => empty($ficha['ParametroScore']['nivel']) ? 'Motorista NÃO Habilitado!' : $ficha['ParametroScore']['nivel'],
      'valor' => empty($ficha['ParametroScore']['valor']) ? 0 : $ficha['ParametroScore']['valor'],
      'codigo_parametro_score' => empty($ficha['ParametroScore']['codigo']) ? 0 : $ficha['ParametroScore']['codigo'],
      'tipo_profissional' => $ficha['FichaScorecard']['codigo_profissional_tipo'],
      'pontos' => $pontos
    );
    return $data;
  }
  
  public function buscarDadosEmailResultado($codigo_ficha) {
    $this->VeiculoLog = ClassRegistry::init('VeiculoLog');
    $this->bindModel(
      array(
      'belongsTo'=>array(
        'ParametroScore'=>array('foreignKey'=>'codigo_parametro_score'),
        'Cliente'=>array('foreignKey'=>'codigo_cliente'),
        'ProfissionalTipo'=>array('foreignKey'=>'codigo_profissional_tipo'),
        'ProfissionalLog'=>array('foreignKey'=>'codigo_profissional_log'),
        'FichaScorecardRetorno'=>array('foreignKey'=>false, 'conditions'=>'FichaScorecard.codigo = FichaScorecardRetorno.codigo_ficha_scorecard AND FichaScorecardRetorno.codigo_tipo_retorno = 2'),
        'CargaTipo'=>array('foreignKey'=>false,'conditions'=>'CargaTipo.codigo = FichaScorecard.codigo_carga_tipo'),
      ),
      'hasMany'=>array(
        'FichaScorecardVeiculo'=>array('foreignKey'=>'codigo_ficha_scorecard'),
      )
    ));
    $conditions = array('FichaScorecard.codigo'=>$codigo_ficha);
    $fields = array(
      'FichaScorecard.codigo', 
      'FichaScorecard.codigo_profissional_tipo',
      'FichaScorecard.total_pontos',
      'FichaScorecard.percentual_pontos',
      'Cliente.codigo',
      'ProfissionalLog.codigo_documento', 
      'Cliente.razao_social', 
      'ProfissionalLog.nome', 
      'ProfissionalLog.codigo_profissional',
      'ProfissionalLog.rg',
      'CargaTipo.descricao',
      'ProfissionalTipo.descricao', 
      'FichaScorecardRetorno.descricao',
      'ParametroScore.codigo',      
      'ParametroScore.nivel',
      'ParametroScore.pontos',
      'ParametroScore.valor',
      'FichaScorecard.codigo_carga_tipo',
      'FichaScorecard.data_validade',
      'FichaScorecard.codigo_cliente_embarcador',
      'FichaScorecard.codigo_cliente_transportador',
      'FichaScorecard.codigo_endereco_cidade_carga_origem',
      'FichaScorecard.codigo_endereco_cidade_carga_destino'  
    );
    $ficha = $this->find('all', compact('conditions', 'fields'));
    if(isset($ficha[0]['FichaScorecardVeiculo'])){
      
      foreach($ficha[0]['FichaScorecardVeiculo'] as $key=>$ficha_scorecard_veiculo){
        if ($key==0){
            $tipo = 'Veiculo';
        }
        if ($key==1){
            $tipo = 'Carreta';
        }
        if ($key==2){
            $tipo = 'Bitrem';
        }
        $ficha[0][$tipo]['placa'] = $this->VeiculoLog->field('placa', array('VeiculoLog.codigo'=>$ficha_scorecard_veiculo['codigo_veiculo_log']));
        
       
      }
    }  
    unset($ficha['FichaScorecardVeiculo']);
    if(!isset($ficha['ProfissionalLog']['nome'])){
        $ficha['ProfissionalLog']['nome'] = 'NÃO INFORMADO';
    } 
    
    $ficha['Profissional']['nome'] = $ficha['ProfissionalLog']['nome'];
    $ficha['FichaScorecard']['nivel'] = empty($ficha['ParametroScore']['nivel']) ? 'Motorista NÃO Habilitado!' : $ficha['ParametroScore']['nivel'];
    $ficha['FichaScorecard']['valor'] = empty($ficha['ParametroScore']['valor']) ? 0 : $ficha['ParametroScore']['valor'];
    return $ficha;
  }

  
  
  public function alterarScoreCrite($data,$codigo_usuario_inclusao) {
    $this->validate = array(
        'codigo_parametro_score' => array(
            'rule' => 'NotEmpty',
            'message' => 'Selecione uma classificação'
        )
    ); 
    if($this->save($data)){
      return $this->geraBackupFicha($data['FichaScorecard']['codigo'],$data['FichaScorecard']['codigo_parametro_score']);      
    }
  }

  public function alterarScore( $data ) {
    $this->validate = array(
        'codigo_parametro_score' => array(
            'rule' => 'NotEmpty',
            'message' => 'Selecione uma classificação'
        ),
        'justificativa_alteracao' => array(
            'rule' => 'NotEmpty',
            'required' => true,
            'message' => 'Informe uma justificativa para a alteração do score'
        ),
    ); 
    $codigo_ficha  = $this->geraBackupFicha( $data );
    return $codigo_ficha;

  }
  
  public function temPermissaoEditar($codigo_ficha, $codigo_usuario_responsavel){
    return $this->find('count', array('conditions'=>array('codigo'=>$codigo_ficha, array('OR' => array(array('codigo_usuario_responsavel'=>$codigo_usuario_responsavel), array('codigo_usuario_responsavel'=>null))), 'codigo_status'=>array(FichaScorecardStatus::A_PESQUISAR, FichaScorecardStatus::EM_PESQUISA, FichaScorecardStatus::PENDENTE))));
  }
  
  public function temPermissaoAprovar($codigo_ficha, $codigo_usuario_responsavel){
    return $this->find('count', array('conditions'=>array('codigo'=>$codigo_ficha, array('OR' => array(array('codigo_usuario_responsavel'=>$codigo_usuario_responsavel), array('codigo_usuario_responsavel'=>null))), 'codigo_status'=>array(FichaScorecardStatus::A_APROVAR,FichaScorecardStatus::EM_APROVACAO))));
  }
  
  public function temPermissaoAlterarScore($codigo_ficha, $codigo_usuario_responsavel){
    return $this->find('count', 
      array('conditions'=>
        array('codigo'=> $codigo_ficha, 
        array('OR' => array(
            array('codigo_usuario_responsavel'=>$codigo_usuario_responsavel), 
            array('codigo_usuario_responsavel'=>null)
          )
        ), 
        'codigo_status'=>FichaScorecardStatus::FINALIZADA)
      )
    );
  }
  
  public function listarFichasARenovar( $options ) {
    $this->ProfissionalLog        = ClassRegistry::init('ProfissionalLog');
    $this->RenovacaoAutomatica    = ClassRegistry::init('RenovacaoAutomatica');
    $this->ClienteProdutoServico2 = ClassRegistry::init('ClienteProdutoServico2');
    $this->ClienteProduto         = ClassRegistry::init('ClienteProduto');
    $this->ProfissionalTipo       = ClassRegistry::init('ProfissionalTipo');
    $produto                      = ClassRegistry::init('Produto');    

    if(isset($options['condicoes'])){
      $condicoes = $options['condicoes'];
    }else{
      $condicoes= array();
      //$condicoes['conditions']['FichaScorecard.codigo_produto'] = $produto::SCORECARD;
      $condicoes['fields'] = array(
            'FichaScorecard.codigo_cliente',
            'ProfissionalLog.codigo_profissional',
            'ProfissionalLog.nome',
            'ProfissionalLog.codigo_documento',
            'ProfissionalTipo.descricao',
            'FichaScorecard.data_validade'                
      );
      $condicoes['group'] = array(
            'FichaScorecard.codigo_cliente',
            'ProfissionalLog.codigo_profissional',
            'ProfissionalLog.nome',
            'ProfissionalLog.codigo_documento',
            'ProfissionalTipo.descricao',
            'FichaScorecard.data_validade'                
      );

    }

    $codigo_cliente = !empty($options['codigo_cliente']) ? $options['codigo_cliente'] : null;
    if( $options['interno'] == TRUE ){
      $dias_renovacao = (!empty($options['dias_renovacao']) ? $options['dias_renovacao'] : 7);
      $dt_ini = date('Y-m-d 00:00:00');
      $dt_fim = date('Y-m-d 23:59:59', strtotime("+".$dias_renovacao." days",strtotime(date('Y-m-d'))));
    } else {
      $dt_ini = date('Y-m-d 00:00:00', strtotime('first day next month'));
      $dt_fim = date('Y-m-d 23:59:59', strtotime('last day next month'));      
    }     

    $condicoes['joins'] = array(
           array(
              'table'      => $this->ProfissionalLog->databaseTable.'.'.$this->ProfissionalLog->tableSchema.'.'.$this->ProfissionalLog->useTable,
              'alias'      => 'ProfissionalLog',
              'conditions' => 'ProfissionalLog.codigo = FichaScorecard.codigo_profissional_log',
              'type' => 'INNER'
            ),
           array(
              'table'      => $this->ProfissionalTipo->databaseTable.'.'.$this->ProfissionalTipo->tableSchema.'.'.$this->ProfissionalTipo->useTable,
              'alias'      => 'ProfissionalTipo',
              'type'       => 'INNER',
              'conditions' => 'ProfissionalTipo.codigo = FichaScorecard.codigo_profissional_tipo'
            )
        );    

    $condicoes = array_merge_recursive(
        $condicoes, array(
        'conditions' => array(
              'exists
                      (
                        select top 1 * from '.$this->ClienteProdutoServico2->databaseTable.'.'.$this->ClienteProdutoServico2->tableSchema.'.'.$this->ClienteProdutoServico2->useTable.'
                        where codigo_cliente_produto in (select codigo from '.$this->ClienteProduto->databaseTable.'.'.$this->ClienteProduto->tableSchema.'.'.$this->ClienteProduto->useTable.'
                                                         where codigo_cliente = FichaScorecard.codigo_cliente
                                                         and codigo_produto in ('.Produto::SCORECARD.')
                                                         and codigo_motivo_bloqueio = 1 /* OK */
                                                         )
                        and codigo_servico = 4 /* Renovação automática */
                      )',
              '(FichaScorecard.data_validade between ? and ?)' => array($dt_ini, $dt_fim),
              'FichaScorecard.codigo_profissional_tipo >' => 1,
              'FichaScorecard.ativo' => 1,
              'ProfissionalLog.codigo_documento NOT IN
                  (
                      select
                          codigo_documento
                      from
                          '.$this->ProfissionalLog->databaseTable.'.'.$this->ProfissionalLog->tableSchema.'.'.$this->ProfissionalLog->useTable.' as subprofissional
                          inner join '.$this->databaseTable.'.'.$this->tableSchema.'.'.$this->useTable.' as subficha
                          on subficha.codigo_profissional_log = subprofissional.codigo
                      where
                          subficha.data_validade >= \''.$dt_fim.'\' and
                          subficha.ativo = 1 and
                          subprofissional.codigo_documento = ProfissionalLog.codigo_documento and
                          subficha.codigo_cliente = FichaScorecard.codigo_cliente and 
                          subficha.codigo_produto = FichaScorecard.codigo_produto
                  )',
              'not exists (
                      select
                          codigo_profissional
                      from
                          '.$this->RenovacaoAutomatica->databaseTable.'.'.$this->RenovacaoAutomatica->tableSchema.'.'.$this->RenovacaoAutomatica->useTable.' as renovacao
                      where
                          renovacao.codigo_profissional = ProfissionalLog.codigo_profissional and
                          renovacao.data_validade_ficha between \''.$dt_ini.'\' and \''.$dt_fim.'\' and
                          renovacao.codigo_cliente = FichaScorecard.codigo_cliente and 
                          renovacao.codigo_produto = '.Produto::SCORECARD.'
                  )'
            )
        )
      );

    
    if ($codigo_cliente)
      $condicoes['conditions']['FichaScorecard.codigo_cliente'] = $codigo_cliente;    

    $resultados = $this->find('sql', $condicoes);        
    return $resultados;
  }


  public function gravar_renovacao($contato='', $email='',$codigos='0',$dias_renovacao=null,$codigo_cliente=null ,$incremento=1) {
        $this->RenovacaoAutomatica = ClassRegistry::init('RenovacaoAutomatica');
        $mes_atual = date('m');

        $data_pesquisa = date('Y-m',mktime(0, 0, 0, $mes_atual+$incremento, 1, date('Y')));
        list($ano,$mes) = explode('-',$data_pesquisa);
        $ultimo_dia = date('d',mktime(0, 0, 0, $mes+$incremento, 1, $ano)-1);
        
          $dias =  $dias_renovacao +7;
         
          $dt_ini = date('Y-m-d', strtotime("+7 days",strtotime(date('Y-m-d'))));
          $dt_fim = date('Y-m-d', strtotime("+".$dias."days",strtotime(date('Y-m-d')))); 

        if (trim($email) == '' || trim($contato) == '') {
            $email = $this->databaseTable.'.'.$this->tableSchema.'.ufn_obter_contato_cliente(ProfissionalLog.codigo_profissional, FichaScorecard.codigo_cliente, 0)';
            $contato = $this->databaseTable.'.'.$this->tableSchema.'.ufn_obter_contato_cliente(ProfissionalLog.codigo_profissional, FichaScorecard.codigo_cliente, 1)';
        } else {
          $email = "'" . $email . "'";
          $contato = "'" . $contato . "'";
        }
        
        $fields = array ('FichaScorecard.codigo_cliente as codigo_cliente',
                       'ProfissionalLog.codigo_profissional as codigo_profissional',
                       'FichaScorecard.codigo_profissional_tipo as codigo_profissional_tipo',
                       'case when
                         max(FichaScorecard.data_alteracao) is null then
                                convert(varchar, max(FichaScorecard.data_inclusao) , 20)
                            else
                                convert(varchar, max(FichaScorecard.data_alteracao) , 20)
                     end as data_atualizacao_ficha',
                     'convert(varchar, max(FichaScorecard.data_validade), 20) as data_validade_ficha',
                         $email. ' as contato',
                         $contato . ' as representante',
                     'case when 
                            ProfissionalLog.codigo_profissional in ('.$codigos.') then 
                                0 
                            else 
                                1 
                        end as renovar',
                         '0 as processado',
                         '1 as codigo_usuario_inclusao'
                      );
        
        $joins  = array(
                      array(
                            'table' => $this->ProfissionalLog->databaseTable.'.'.$this->ProfissionalLog->tableSchema.'.'.$this->ProfissionalLog->useTable,                           
                            'alias' => 'ProfissionalLog',
                            'type' => 'INNER',
                            'conditions' => array('FichaScorecard.codigo_profissional_log =ProfissionalLog.codigo')
                    )

                );  
        
        $conditions['FichaScorecard.ativo'] = 1; 
        $conditions['FichaScorecard.codigo_cliente']=$codigo_cliente;
        array_push($conditions, array('FichaScorecard.data_validade BETWEEN ? AND ? '=> array( $dt_ini." 00:00:00", $dt_fim." 23:59:59")));
        
        $conditions['FichaScorecard.codigo_profissional_tipo >'] = 1;
        
        $group = array('FichaScorecard.codigo_cliente',
                     'ProfissionalLog.codigo_profissional',
                     'FichaScorecard.codigo_profissional_tipo');

        $linhas = $this->find('all', array('fields' => $fields,'joins' => $joins,'conditions' => $conditions,'group'  => $group)); 
        $reg_save = 0;

        
        foreach($linhas as $linha) {
            $sql = "INSERT INTO ".$this->RenovacaoAutomatica->databaseTable.".".$this->RenovacaoAutomatica->tableSchema.".".$this->RenovacaoAutomatica->useTable."
                ( 
                  codigo_cliente, 
                  codigo_profissional, 
                  codigo_profissional_tipo, 
                  data_atualizacao_ficha, 
                  data_validade_ficha, 
                  contato, 
                  representante, 
                  renovar, 
                  processado, 
                  codigo_usuario_inclusao, 
                  data_inclusao,
                  codigo_produto
                ) values (";

            foreach($linha[0] as $key =>$valor) {
                $sql.= "'" . $valor . "',";
            }
            $produto = ClassRegistry::init('Produto');
            $sql.= "'" . date('Y-m-d H:i:s') . "',".$produto::SCORECARD.",";

            $sql = substr($sql,0,-1) . ");";

            if($this->query($sql) !== false) {
                $reg_save++;
            }
        }
        return $reg_save;
  }
  public function verificarRenovacaoMes($codigo_cliente=null,$mes=null,$ano=null) {

    $mes_atual = $mes == null ? date('m') : $mes;
    $ano_atual = $ano == null ? date('Y') : $ano;
    $conditions = array(
        'MONTH(data_inclusao)'=>$mes_atual,
        'YEAR(data_inclusao)'=>$ano_atual);
    
    if($codigo_cliente != null)
      $conditions['codigo_cliente']=$codigo_cliente;

    $RenovacaoAutomatica = ClassRegistry::init('RenovacaoAutomatica');
    $total = $RenovacaoAutomatica->find('count' ,array('conditions'=>$conditions));
    return $total;
  }
  public function profissionaisARenovar($codigo_cliente=0, $dt_ini=null,$dt_fim=null) {
    $this->ProfissionalTipo    = ClassRegistry::init('ProfissionalTipo');
    $this->ProfissionalLog     = ClassRegistry::init('ProfissionalLog');
    $this->RenovacaoAutomatica = ClassRegistry::init('RenovacaoAutomatica');
    $fields   = array('ProfissionalLog.nome as nome',
              'ProfissionalLog.codigo_profissional as codigo_profissional',
                          'FichaScorecard.codigo_profissional_tipo as codigo_profissional_tipo',
                          'ProfissionalLog.codigo_documento as cpf',
                          'ProfissionalTipo.descricao as tipo',
                          'MAX(FichaScorecard.codigo) as ficha');

    $joins      = array(
                      array(
                             'table' => $this->ProfissionalLog->databaseTable.'.'.$this->ProfissionalLog->tableSchema.'.'.$this->ProfissionalLog->useTable,
                             'alias' => 'ProfissionalLog',
                          'type' => 'INNER',
                          'conditions' => array('ProfissionalLog.codigo =FichaScorecard.codigo_profissional_log')
                      ),
                         array(
                           'table' => $this->ProfissionalTipo->databaseTable.'.'.$this->ProfissionalTipo->tableSchema.'.'.$this->ProfissionalTipo->useTable,
                           'alias' => 'ProfissionalTipo',
                          'type' => 'INNER',
                          'conditions' => array('ProfissionalTipo.codigo =FichaScorecard.codigo_profissional_tipo')
                      )

                );  
        $group = array('ProfissionalLog.nome', 'ProfissionalLog.codigo_profissional',
                        'FichaScorecard.codigo_profissional_tipo', 'ProfissionalLog.codigo_documento', 
                        'ProfissionalTipo.descricao');
        
        if((int)$codigo_cliente > 0) {
      $sqlAux1 = " FichaScorecard.codigo_cliente = " . $codigo_cliente;
        $conditions['FichaScorecard.codigo_cliente'] = $codigo_cliente;
    }else{
      $sqlAux1 = "1=1";
        }

        $conditions['FichaScorecard.ativo'] = 1;
        array_push($conditions, array('FichaScorecard.data_validade BETWEEN ? AND ? '=> array( $dt_ini, $dt_fim)));
        $conditions['FichaScorecard.codigo_profissional_tipo <>'] = 1;
        $conditions["ProfissionalLog.codigo_profissional in (SELECT codigo_profissional FROM ".$this->RenovacaoAutomatica->databaseTable.".".$this->RenovacaoAutomatica->tableSchema.".".$this->RenovacaoAutomatica->useTable." WHERE {$sqlAux1} AND  data_validade_ficha between '".$dt_ini."' and '".$dt_fim."' and (renovar = 1 and processado = 0)) and 1 = "] = "1";

    $results  = $this->find('sql', array(
                    'fields' => $fields,
                      'joins' => $joins,
                      'conditions' => $conditions,
                      'group'  => $group
                      //'order' => ' ProfissionalLog.nome asc'
                        ));
    return $results;
    
    
    
  }
  public function renovarFicha($codigo_cliente,$codigo_profissional, $codigo_profissional_tipo,$ficha) {
        $this->$Cliente               = ClassRegistry::init('Cliente');
        $this->$Produto               = ClassRegistry::init('Produto');
    $this->ProfissionalLog            = ClassRegistry::init('ProfissionalLog');
    $this->ProfissionalContato        = ClassRegistry::init('ProfissionalContato');
    $this->FichaScorecardRetorno      = ClassRegistry::init('FichaScorecardRetorno');
    $this->FichaScorecardVeiculo      = ClassRegistry::init('FichaScorecardVeiculo');
    $this->$LogFaturamentoTeleconsult = ClassRegistry::init('LogFaturamentoTeleconsult');
    $this->FichaScorecardQuestaoResp  = ClassRegistry::init('FichaScorecardQuestaoResp');
    $this->FichaScProfContatoLog      = ClassRegistry::init('FichaScProfContatoLog');
    $this->VeiculoLog                 = ClassRegistry::init('VeiculoLog');

    $logs = $this->ProfissionalLog->find('all',array('fields'=>array('codigo'),'conditions'=>array('ProfissionalLog.codigo_profissional'=>$codigo_profissional)));
    foreach($logs as $log) {
      $dados = $this->find('all',array('conditions'=>array('FichaScorecard.codigo_profissional_log'=>$log['ProfissionalLog']['codigo'],'FichaScorecard.ativo'=>1)));
      if(!empty($dados)) {
        foreach($dados as $dado){
          $dado['FichaScorecard']['ativo'] = 0;
          $this->atualizar($dado);
        }
      }
    }

    $dados = $this->find('first',array('conditions'=>array('FichaScorecard.codigo'=>$ficha)));
    
    $servico = $this->$LogFaturamentoTeleconsult->obterTipoOperacaoLogFaturamento($codigo_cliente, $codigo_profissional, Produto::SCORECARD);
    $result = $Cliente->carregarClientePagadorSemBloqueio($dados['FichaScorecard']['codigo'], $dados['FichaScorecard']['embarcador'], $dados['FichaScorecard']['transportador'], Produto::SCORECARD, $servico);
    if(!$result){
      echo "Não foi possivel renovar a ficha";
      exit;
    }
    
    if(!empty($dados)) {
      if($codigo_cliente == null)
        $codigo_cliente = $dados['FichaScorecard']['codigo_cliente'];
      unset($dados['FichaScorecard']['codigo']);
      $dados['FichaScorecard']['codigo_usuario_inclusao'] = 159; // Usuário de Renovação
      $dados['FichaScorecard']['codigo_status'] = 1;
      $dados['FichaScorecard']['ativo'] = 1;
      $dados['FichaScorecard']['data_inclusao'] = date('Y-m-d H:i:s');
      $dados['FichaScorecard']['data_validade'] = date('Y-m-d H:i:s', strtotime('+ 6 months'));
      $dados['FichaScorecard']['data_alteracao'] = null;
      $dados['FichaScorecard']['codigo_usuario_alteracao'] = null;

      $this->incluir($dados);
      $codigo_ultima_ficha = $this->id;

      $dados = $this->FichaScorecardRetorno->find('all',array('conditions'=>array('FichaScorecardRetorno.codigo_ficha_scorecard'=>$ficha)));
      foreach($dados as $dado) {
        unset($d['FichaScorecardRetorno']['codigo']);
        $dado['FichaScorecardRetorno']['codigo_tipo_contato'] = 2;
        $dado['FichaScorecardRetorno']['codigo_tipo_retorno'] = 2;
        $this->FichaScorecardRetorno->incluir($dado);
      }

      $fields = array('\''.$codigo_ultima_ficha .'\'  as codigo_ficha_scorecard',
                    '( select    max(log.codigo)
                          from      '.$this->VeiculoLog->databaseTable.'.'.$this->VeiculoLog->tableSchema.'.'.$this->VeiculoLog->useTable.'
                                    as log
                          where     codigo_veiculo = ( select top 1
                                                                sublog.codigo_veiculo
                                                       from     '.$this->FichaScorecardVeiculo->databaseTable.'.'.$this->FichaScorecardVeiculo->tableSchema.'.'.$this->FichaScorecardVeiculo->useTable.' as ficha_veiculo
                                                                inner join '.$this->VeiculoLog->databaseTable.'.'.$this->VeiculoLog->tableSchema.'.'.$this->VeiculoLog->useTable.' as sublog on ( sublog.codigo = ficha_veiculo.codigo_veiculo_log )
                                                       where    ficha_veiculo.codigo_ficha_scorecard = '.$ficha.'
                                                     )
                        ) as codigo_veiculo_log',
                            'FichaScorecardVeiculo.tipo',
                            'FichaScorecardVeiculo.codigo_tecnologia',
                            'FichaScorecardVeiculo.codigo_proprietario_log',
                            'FichaScorecardVeiculo.codigo_proprietario_endereco_log' 
      );
            
            $conditions['codigo_ficha_scorecard'] = $ficha;   
            
            $this->FichaScorecardVeiculo =& ClassRegistry::init('FichaScorecardVeiculo');
            
            $linhas  = $this->FichaScorecardVeiculo->find('all', array(
                    'fields' => $fields,
                      'conditions' => $conditions
                        )); 
            
            $reg_save = 0;
          foreach($linhas as $linha) {
            $sql = "
                    insert into
            ".$this->FichaScorecardVeiculo->databaseTable.".".$this->FichaScorecardVeiculo->tableSchema.".".$this->FichaScorecardVeiculo->useTable."
            (
              codigo_ficha_scorecard,
              codigo_veiculo_log,
              tipo,
              codigo_tecnologia,
              codigo_proprietario_log,
              codigo_proprietario_endereco_log
            ) values (";
        foreach($linha[0] as $key => $valor) {
          $sql.= "'" . $valor . "',";
        }
        $sql.= "'" . date('Y-m-d H:i:s') . "',";

        $sql = substr($sql,0,-1) . ");";
        if($this->query($sql) !== false) {
          $reg_save++;
        }
          }
            
          $fields = array($codigo_ultima_ficha . '  as codigo_ficha_scorecard',
                        'ProfissionalContatoLog.codigo as codigo_profissional_contato_log' );

          $joins  = array(
                      array(
                             'table' => $this->ProfissionalContato->databaseTable.'.'.$this->ProfissionalContato->tableSchema.'.'.$this->ProfissionalContato->useTable,
                             'alias' => 'ProfissionalContato',
                             'type' => 'INNER',
                             'conditions' => array('ProfissionalContato.codigo = ProfissionalContatoLog.codigo_profissional_contato')
                      )

                );  
            
            $conditions2['ProfissionalContato.codigo_profissional'] = $codigo_profissional;  
            
            $this->ProfissionalContatoLog =  ClassRegistry::init('ProfissionalContatoLog');
            $linhas  = $this->ProfissionalContatoLog->find('all', array(
                    'fields' => $fields,
                      'conditions' => $conditions2,
                      'joins' =>$joins
                        )); 
         
            $reg_save = 0;
          foreach($linhas as $linha) {
            $sql = "
                   insert into ".$this->FichaScProfContatoLog->databaseTable.'.'.$this->FichaScProfContatoLog->tableSchema.'.'.$this->FichaScProfContatoLog->useTable."
            (
              codigo_ficha_scorecard,
              codigo_profissional_contato_log
            ) values (";
        foreach($linha[0] as $keye => $valor) {
          $sql.= "'" . $valor . "',";
        }

        $sql = substr($sql,0,-1) . ");";
        if($this->query($sql) !== false) {
          $reg_save++;
        }
          }

      $dados = $this->FichaScorecardQuestaoResp->find('all',array('conditions'=>array('FichaScorecardQuestaoResp.codigo_ficha_scorecard'=>$ficha)));
      foreach($dados as $dado) {
        unset($dado['FichaScorecardQuestaoResp']['codigo']);
        $dado['FichaScorecardQuestaoResp']['codigo_ficha_scorecard'] = $codigo_ultima_ficha;
        $this->incluir($dado);
      }

      return $codigo_ultima_ficha;
    }
  }

  public function finalizaRenovacao($codigo_cliente, $dt_ini=null,$dt_fim=null) {
    $this->RenovacaoAutomatica = ClassRegistry::init('RenovacaoAutomatica');
    $renovacoes = $this->RenovacaoAutomatica->find('all',array('conditions'=>array("data_validade_ficha between '".$dt_ini."' and '".$dt_fim."'")));
    if($renovacoes > 0) {
      foreach($renovacoes as $renovacao){
        $renovacao['RenovacaoAutomatica']['processado'] = 1;
        $this->RenovacaoAutomatica->atualizar($renovacao);
      }
    }
    return true;
  }

  public function converteFiltroEmCondition($filtros) {
    $condition = array();
    if(isset($filtros['codigo_documento']) && !empty($filtros['codigo_documento'])){
      $conditions['ProfissionalLog.codigo_documento'] = preg_replace('/[^\d]+/', '', $filtros['codigo_documento']);
    }

    if (isset($filtros['nome']) && !empty($filtros['nome'])) {
      $conditions['ProfissionalLog.nome LIKE'] = $filtros['nome'].'%';
    }

    if (isset($filtros['ativo']) && !empty($filtros['ativo'])) {
      $conditions['FichaScorecard.ativo'] = $filtros['ativo'];
    }
    if(isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])){
      $conditions['Cliente.codigo'] = $filtros['codigo_cliente'];
    }        
    if( $filtros['relatorio_vinculo'] === TRUE ) {//relatorio
      if( isset($filtros['codigo_tipo_profissional']) && $filtros['codigo_tipo_profissional'] && $filtros['codigo_tipo_profissional'] != ProfissionalTipo::CARRETEIRO){
        $conditions['FichaScorecard.codigo_profissional_tipo <>'] = ProfissionalTipo::CARRETEIRO;
      } elseif(isset($filtros['codigo_tipo_profissional']) && $filtros['codigo_tipo_profissional'] == 1) {
        $conditions['FichaScorecard.codigo_profissional_tipo'] = ProfissionalTipo::CARRETEIRO;
      }
    } else {
      $conditions['FichaScorecard.codigo_profissional_tipo <>'] = ProfissionalTipo::CARRETEIRO;
    }
    $conditions['FichaScorecard.codigo_status <>'] = 8;
    if(isset($filtros['data_inclusao_inicio']) && !empty($filtros['data_inclusao_inicio']) && isset($filtros['data_inclusao_fim']) && !empty($filtros['data_inclusao_fim'])){
      $data_inicio  = preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})/", "$3-$2-$1", $filtros['data_inclusao_inicio']);
      $data_fim     = preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})/", "$3-$2-$1", $filtros['data_inclusao_fim']);
      array_push($conditions, array('FichaScorecard.data_inclusao BETWEEN ? AND ? '=> array( $data_inicio.' 00:00:00', $data_fim.' 23:59:59')));
    }

    if ( !empty($filtros['data_inicial']) && !empty($filtros['data_final'])) {
      array_push($conditions, array('FichaScorecard.data_inclusao BETWEEN ? AND ? '=> array( preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1', $filtros['data_inicial'])." 00:00:00", preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1', $filtros['data_final'])." 23:59:59")));
    }
        
    return $conditions; 
  }

  
    public function excluirVinculoProfissional( $dadosExclusao ) {
      App::import('Model', 'Produto');
      $this->LogAtendimento =& ClassRegistry::init('LogAtendimento');
      try{
        $this->query('begin transaction');
        foreach ( $dadosExclusao as $key => $dados ) {
          $codigo_cliente      = $dados['codigo_cliente'];
          $codigo_profissional    = $dados['codigo_profissional'];
          $codigo_profissional_tipo = $dados['codigo_profissional_tipo'];
          $this->bindModel(array('belongsTo' => array('ProfissionalLog' => array('foreignKey' => 'codigo_profissional_log'))));
          $conditions   = array(
            'ProfissionalLog.codigo_profissional' => $codigo_profissional,
            'FichaScorecard.codigo_cliente' => $codigo_cliente,
            'FichaScorecard.codigo_profissional_tipo' => $codigo_profissional_tipo,
          );
          $fields       = array('FichaScorecard.codigo');
          $fichas       = $this->find('all', compact('conditions', 'fields'));
          foreach ($fichas as $key => $ficha) {
            $ficha['FichaScorecard']['ativo'] = 2;
            if( !$this->atualizar($ficha) )
              throw new Exception('Erro ao excluir vinculo');
          }
          $LogAtendimento['LogAtendimento']['codigo_produto']             = Produto::SCORECARD;
          $LogAtendimento['LogAtendimento']['codigo_tipo_operacao']       = 101;//Exclusao de vinculo
          $LogAtendimento['LogAtendimento']['codigo_profissional']        = $codigo_profissional;
          $LogAtendimento['LogAtendimento']['codigo_profissional_tipo']   = $codigo_profissional_tipo;
          $this->LogAtendimento->incluir( $LogAtendimento['LogAtendimento'] );
        }
        $this->commit();
        return true;
      } catch (Exception $ex) {
        $this->rollback();
        return false;
      }
    }

    public function verificaPlaca($placa = '') {
      if(trim($placa) != '') {
        $this->Veiculo =&ClassRegistry::init('Veiculo');
        $this->VeiculoOcorrencia =&ClassRegistry::init('VeiculoOcorrencia');
        $placa = strtoupper( str_replace('-', '', $placa) );
        $retorno = false;
        $achou_placa = $this->Veiculo->find('count',array('conditions'=>array('Veiculo.placa'=>$placa)));
        if($achou_placa > 0) {
            $fields = array ('count(*) as total');
                $conditions['veiculo.placa'] = $placa;
                $conditions['VeiculoOcorrencia.codigo_ocorrencia <>'] = 9;
                $conditions['VeiculoOcorrencia.data_inclusao = ( 
                  select 
                    max(veiculo_ocorrencia.data_inclusao) 
                            from 
                              '.$this->VeiculoOcorrencia->databaseTable.'.'.$this->VeiculoOcorrencia->tableSchema.'.'.$this->VeiculoOcorrencia->useTable.' 
                            inner join '.$this->Veiculo->databaseTable.'.'.$this->Veiculo->tableSchema.'.'.$this->Veiculo->useTable.' 
                               on (veiculo_ocorrencia.codigo_veiculo = veiculo.codigo) 
                            where veiculo.placa = \''.$placa.'\') and 1=']='1';
              
                $joins  = array(
                    array(
                          'table' => $this->Veiculo->databaseTable.'.'.$this->Veiculo->tableSchema.'.'.$this->Veiculo->useTable,
                          'alias' => 'veiculo',
                          'type' => 'INNER',
                          'conditions' => array('veiculo.codigo =VeiculoOcorrencia.codigo_veiculo')
                  )
                );  
              
              $registro = $this->VeiculoOcorrencia->find('all', array(
                    'fields' => $fields,
                      'joins' => $joins,
                      'conditions' => $conditions
                        )); 

        $retorno = $registro[0][0]['total'] > 0 ? 1 : 0;
        
        } else {
          $retorno = true;
        }
        return $retorno;
      } else {
        return false;
      }
    }
    
    public function verificaObrigatoriedadeDaPlaca( $codigo_cliente ) {
      App::import('Model', 'Produto'); 
      $this->Cliente = ClassRegistry::init('Cliente');
      $this->Cliente->bindModel(array('belongsTo' => array(
        'ClienteProduto' => array(
          'foreignKey' => false, 
          'conditions' => 'ClienteProduto.codigo_cliente = Cliente.codigo'
          ),
        'ClienteProdutoServico' => array(
          'foreignKey' => false, 
          'conditions' => 'ClienteProdutoServico.codigo_cliente_produto = ClienteProduto.codigo'
        )
      )));      
      $conditions = array();
      $conditions['Cliente.codigo'] = $codigo_cliente;
      // $conditions['ClienteProduto.codigo_produto'] = Produto::SCORECARD;//NAO EXISTE AINDA o CADASTRO
      $conditions['ClienteProdutoServico.consistencia_veiculo'] = 1;
      $conditions['ClienteProdutoServico.codigo_servico'] = 3; 
      return ( $this->Cliente->find('count', compact('conditions') ) > 0);
    }

    public function  verificaValidadeCnh ($data_vencimento){
      $data_inicio = strtotime($data_vencimento);
      $data_final = strtotime(date('YmdHis',strtotime('-30 days')));
      if ($data_final==$data_inicio) {
        $retorno= 0;
      }
      if ($data_final>$data_inicio) {
        $retorno= 0;
      }
      if ($data_final<$data_inicio) {
        $retorno= 1;
      }
      return $retorno;
    }

  public function verificaFichaScorecardEmAnalise($codigo_profissional,$codigo_cliente){
    App::import('Model', 'Produto');
    //verifica se existe alguma ficha em análise codigo tipo operacao 2 
    $fields = array('count(codigo) as existe_ficha');
    $this->LogFaturamento = ClassRegistry::init('LogFaturamentoTeleconsult');
    $conditions['codigo_cliente']       = $codigo_cliente;
    $conditions['codigo_profissional']  = $codigo_profissional;
    $conditions['codigo_produto']       = Produto::SCORECARD;
    $conditions['codigo_tipo_operacao'] = array(2);
    
    $registro  = $this->LogFaturamento->find('all', array(
                    'fields' => $fields,
                      'conditions' => $conditions,
                      ));

    $existe_ficha_analise = $registro[0][0]['existe_ficha'];

    return $existe_ficha_analise > 0 ? 1 : 0; 
  } 
  
  public function verificaConsulta2horas( $codigo_profissional, $codigo_cliente ){
    $this->LogFaturamento = ClassRegistry::init('LogFaturamentoTeleconsult');
    $this->Produto        = ClassRegistry::init('Produto');    
    $conditions['codigo_cliente <>']   = $codigo_cliente;
    $conditions['codigo_profissional']  = $codigo_profissional;
    $conditions['codigo_produto']       = Produto::SCORECARD;    
    $conditions['codigo_tipo_operacao NOT'] = array(11,21,75, 67);    
    $conditions['data_inclusao BETWEEN ? AND ? '] = array( date('Ymd H:i:s', strtotime("-2 Hours")), date('Ymd H:i:s') );
    return $this->LogFaturamento->find('count', compact('conditions') );
  } 
  
  public function verificaConsulta6horas( $codigo_profissional, $codigo_cliente ){
    App::import('Model', 'Produto');
    $this->LogFaturamento = ClassRegistry::init('LogFaturamentoTeleconsult');
    $conditions['codigo_cliente']       = $codigo_cliente;
    $conditions['codigo_profissional']  = $codigo_profissional;
    $conditions['codigo_produto']       = Produto::SCORECARD;
    $conditions['codigo_tipo_operacao'] = array(1,2,3,4,6,9,10,74,89,100,108,109,110,111,112,113,114,115,116,125);
    $conditions['data_inclusao BETWEEN ? AND ? '] = array( date('Ymd H:i:s', strtotime("-6 Hours")), date('Ymd H:i:s') );
    $registro  = $this->LogFaturamento->find('count', compact('conditions'));
    return $registro > 0;
  }

  public function verificaRegraCobranca ($codigo_profissional,$codigo_cliente){
     if($this->verificaConsulta2horas($codigo_profissional,$codigo_cliente)) {
       $codigo_tipo_operacao = 100;
     } else if($this->verificaConsulta6horas($codigo_profissional,$codigo_cliente)){
       $codigo_tipo_operacao = 8;
     }else {
       $codigo_tipo_operacao = false;  
     }
     return $codigo_tipo_operacao;
  }  

  public function buscaProfissionalPorCliente( $codigo_documento, $codigo_cliente, $fichas_finalizadas = FALSE ) {
    App::import('Model', 'Produto');
    $codigo_documento = preg_replace('/[^\d]+/', '', $codigo_documento);
    $this->bindModel(array('belongsTo' => array(
      'ProfissionalLog' => array(
        'foreignKey' => false, 
        'conditions' => 'FichaScorecard.codigo_profissional_log = ProfissionalLog.codigo'),
    )));
    $conditions['ProfissionalLog.codigo_documento'] = $codigo_documento;    
    $conditions['FichaScorecard.codigo_cliente']    = $codigo_cliente;
    if( $fichas_finalizadas )
      $conditions['FichaScorecard.codigo_status']   = FichaScorecardStatus::FINALIZADA;
    $order = 'FichaScorecard.data_inclusao DESC';
    return $this->find('first', compact('conditions', 'order' ));
  }

  public function buscaProfissionalCarreteiro( $codigo_documento ) {
    App::import('Model', 'Produto');
    $codigo_documento = preg_replace('/[^\d]+/', '', $codigo_documento);
    $this->bindModel(array('belongsTo' => array(
      'ProfissionalLog' => array(
        'foreignKey' => false, 
        'conditions' => 'FichaScorecard.codigo_profissional_log = ProfissionalLog.codigo'),
    )));
    $conditions['ProfissionalLog.codigo_documento'] = $codigo_documento;
    $conditions['FichaScorecard.codigo_profissional_tipo'] = 1;
    $order = 'FichaScorecard.data_inclusao DESC';
    return $this->find('first', compact('conditions', 'order' ));
  }

  public function verificaLiberacaoProvisoria( $codigo_profissional, $codigo_cliente ){      
    App::import('Model', 'Produto');
    $this->LiberacaoProvisoria = ClassRegistry::init('LiberacaoProvisoria');      
    $fields = array("count(*) as carreteiro"); 
    $conditions['LiberacaoProvisoria.codigo_profissional']  = $codigo_profissional;
    $conditions['LiberacaoProvisoria.codigo_produto']  =  Produto::SCORECARD;
    $conditions['LiberacaoProvisoria.codigo_profissional_tipo'] = 1;  
    $conditions['LiberacaoProvisoria.ativo'] = '1';
    $conditions['LiberacaoProvisoria.data_liberacao >'] = date('Y-m-d H:i:s');
    $carreteiro = $this->LiberacaoProvisoria->find('all', array(
      'fields' => $fields,
      'conditions' => $conditions,
    ));
    $fields_outros = array("count(*) as outros");        
    $conditions_outros['LiberacaoProvisoria.codigo_profissional']  =0;
    $conditions_outros['LiberacaoProvisoria.codigo_produto']  =  Produto::SCORECARD;
    $conditions_outros['LiberacaoProvisoria.codigo_profissional_tipo'] = 1;  
    $conditions_outros['LiberacaoProvisoria.ativo'] = '1'.
    $conditions_outros[' LiberacaoProvisoria.data_liberacao >'] = date('Y-m-d H:i:s');
    $conditions_outros['LiberacaoProvisoria.codigo_cliente'] = $codigo_cliente;
    $outros = $this->LiberacaoProvisoria->find('all', array('fields' => $fields_outros,'conditions' => $conditions_outros));
    return $carreteiro[0][0]['carreteiro'] != 0 || $outros[0][0]['outros'] != 0 ? 1 : 0;
  }
  
  function listaFichasComTempo($codigo_cliente, $mes, $ano, $conditions = array()) {  
    $this->LogFaturamentoTeleconsult = ClassRegistry::init('LogFaturamentoTeleconsult');
    $this->Cliente = ClassRegistry::init('Cliente');
    $this->ProfissionalLog = ClassRegistry::init('ProfissionalLog');
    $this->ParametroScore = ClassRegistry::init('ParametroScore');
    $data_ini = $ano . '-' . $mes . '-' . '01 00:00:00.000';
    $data_fim = date('Y-m-d H:i:s.997', mktime(23, 59, 59, $mes+1, 0, $ano));
    $fields   = array('FichaScorecard.codigo','FichaScorecard.codigo_status','FichaScorecard.data_inclusao',
      'FichaScorecard.data_alteracao','FichaScorecard.tempo_sla', 'ProfissionalStatus.codigo','ProfissionalStatus.nivel',
      'ProfissionalLog.codigo_documento','ProfissionalLog.nome','ProfissionalLog.codigo_profissional_tipo',
      'convert(int, datediff(MINUTE, FichaScorecard.data_inclusao, FichaScorecard.data_alteracao)) as tempo_pesquisa_ficha'
    );
    $joins = array(
      array(
          'table' => $this->LogFaturamentoTeleconsult->databaseTable.'.'.$this->LogFaturamentoTeleconsult->tableSchema.'.'.$this->LogFaturamentoTeleconsult->useTable,
          'alias' => 'LogFaturamento',
          'type' => 'LEFT',
          'conditions' => array('FichaScorecard.codigo = LogFaturamento.codigo_ficha_scorecard')
      ),
      array(
          'table' => $this->ProfissionalLog->databaseTable.'.'.$this->ProfissionalLog->tableSchema.'.'.$this->ProfissionalLog->useTable,
          'alias' => 'ProfissionalLog',
          'type' => 'LEFT',
          'conditions' => array('FichaScorecard.codigo_profissional_log = ProfissionalLog.codigo')
      )
    );
    if( self::ENVIA_EMAIL_SCORECARD === FALSE ){
      array_push($joins,
        array(
        'table' => $this->ParametroScore->databaseTable.'.'.$this->ParametroScore->tableSchema.'.'.$this->ParametroScore->useTable,
        'alias' => 'ProfissionalStatus',
        'type' => 'LEFT',
        'conditions' => array('ProfissionalStatus.codigo = FichaScorecard.codigo_score_manual')
      ));
    } else {
      array_push($joins,array(
        'table' => $this->ParametroScore->databaseTable.'.'.$this->ParametroScore->tableSchema.'.'.$this->ParametroScore->useTable,
        'alias' => 'ProfissionalStatus',
        'type' => 'LEFT',
        'conditions' => array('ProfissionalStatus.codigo = FichaScorecard.codigo_score')
      ));
    }
    $filtros_obrigatorios = array( 
      'FichaScorecard.codigo_cliente' => $codigo_cliente,
      'FichaScorecard.data_inclusao BETWEEN ? AND ?' => array($data_ini, $data_fim)
    );
    $conditions = array_merge($conditions, $filtros_obrigatorios);
    return $this->find('all', array('fields' => $fields,'joins' => $joins,'conditions' => $conditions));
  }
  
  public function calcularPorcentagem($dados) {
    $retorno     = array();
    $tempo_total = array();
    $totais      = array();
    $totais['quantidade'] = 0;
    $totais['Estatistica'] = array('no_prazo' => 0,'fora_do_prazo' => 0);
    $this->FichaScorecardStatus = ClassRegistry::init('FichaScorecardStatus');
    foreach ($dados as $key => $value ) {
      $this->ParametroScore = ClassRegistry::init('ParametroScore');
      if( $value['FichaScorecard']['codigo_status'] == FichaScorecardStatus::FINALIZADA ){
        if( !self::ENVIA_EMAIL_SCORECARD ){//MODO TLC
          $parametro_score = $this->ParametroScore->carregar( $value['ProfissionalStatus']['codigo'] );
          if( in_array($parametro_score['ParametroScore']['codigo'], array(ParametroScore::INSUFICIENTE,ParametroScore::DIVERGENTE ))) {
            $indice = $parametro_score['ParametroScore']['nivel'];
          } else{
            $indice = 'ADEQUADO AO RISCO';
          }
        } else {
          $parametro_score  = $this->ParametroScore->carregar( $value['ProfissionalStatus']['codigo'] );
          $indice = $parametro_score['ParametroScore']['nivel'];          
        }
      } else {
        $indice = FichaScorecardStatus::descricao( FichaScorecardStatus::EM_PESQUISA );
      }      
      $tempo_gasto = $value[0]['tempo_pesquisa_ficha'];
      $tempo_restante = ( $value['FichaScorecard']['tempo_sla'] - $tempo_gasto );
      if (!isset($tempo_total[$indice])) {
        $tempo_total[$indice] = 0;
      }
      $tempo_total[$indice] += $tempo_gasto;
      if (!isset($retorno[$indice]['Estatistica'])) {
        $retorno[$indice]['Estatistica'] = array('no_prazo' => 0,'fora_do_prazo' => 0);
      }
      if ( $tempo_restante <= 0 ) {
        $retorno[$indice]['Estatistica']['fora_do_prazo']++;
        $totais['Estatistica']['fora_do_prazo']++;
      } else {
        $retorno[$indice]['Estatistica']['no_prazo']++;
        $totais['Estatistica']['no_prazo']++;
      }
      if (!isset($retorno[$indice]['quantidade'])) {
        $retorno[$indice]['quantidade'] = 0;
      }
      $retorno[$indice]['quantidade']++;
      $totais['quantidade']++;
    }
    if (count($retorno) > 0) {
      $tempo_total['QUANTIDADE TOTAL'] = array_sum($tempo_total);
      $retorno['QUANTIDADE TOTAL']     = $totais;
    }
    foreach (array_keys($retorno) as $key) {
      $quantidade = $retorno[$key]['quantidade'];
      $porcentagem_fora_do_prazo = $retorno[$key]['Estatistica']['fora_do_prazo'] * 100 / $quantidade;
      $tempo_medio = $tempo_total[$key] / $quantidade;
      $retorno[$key]['Estatistica']['porcentagem_fora_do_prazo'] = $porcentagem_fora_do_prazo;
      $retorno[$key]['Estatistica']['tempo_medio'] = $tempo_medio;
    }
    return $retorno;
  }


  public function converteFiltroEmConditionParaFichas($filtros){
    $conditions = array();
    if(isset($filtros['codigo_ficha']) && !empty($filtros['codigo_ficha']))
      $conditions['FichaScorecard.codigo'] = $filtros['codigo_ficha'];

    if(isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente']))
      $conditions['Cliente.codigo'] = $filtros['codigo_cliente'];

    if(isset($filtros['data_inicial']) && !empty($filtros['data_inicial']) && isset($filtros['data_final']) && !empty($filtros['data_final']))
      $conditions['FichaScorecard.data_inclusao BETWEEN ? AND ?'] = array(AppModel::dateToDbDate($filtros['data_inicial'].' 00:00'), AppModel::dateToDbDate($filtros['data_final'].' 23:59'));

    if(isset($filtros['data_alteracao_inicial']) && !empty($filtros['data_alteracao_inicial']) && isset($filtros['data_alteracao_final']) && !empty($filtros['data_alteracao_final']))
      $conditions['FichaScorecard.data_alteracao BETWEEN ? AND ?'] = array(AppModel::dateToDbDate($filtros['data_alteracao_inicial'].' 00:00'), AppModel::dateToDbDate($filtros['data_alteracao_final'].' 23:59'));

    if(isset($filtros['cpf_profissional']) && !empty($filtros['cpf_profissional']))
      $conditions['ProfissionalLog.codigo_documento'] = $filtros['cpf_profissional'];

    if(isset($filtros['nome_profissional']) && !empty($filtros['nome_profissional']))
      $conditions['ProfissionalLog.nome LIKE'] = '%'.$filtros['nome_profissional'].'%';

    if(isset($filtros['cpf_proprietario']) && !empty($filtros['cpf_proprietario']))
      $conditions['ProprietarioLog.codigo_documento'] = Comum::soNumero( $filtros['cpf_proprietario']);

    if(isset($filtros['nome_proprietario']) && !empty($filtros['nome_proprietario']))
      $conditions['ProprietarioLog.nome_razao_social LIKE'] = '%'.$filtros['nome_proprietario'].'%';

    if(isset($filtros['tipo_profissional']) && !empty($filtros['tipo_profissional']))
      $conditions['ProfissionalTipo.codigo'] = $filtros['tipo_profissional'];

    if(isset($filtros['placa']) && !empty($filtros['placa'])){
      $placa = str_replace('-', '', $filtros['placa']);
      array_push($conditions,  array('OR' => array(
        array('VeiculoLog.placa' => $placa), 
        array('CarretaLog.placa' => $placa), 
        array('BitremLog.placa'  => $placa)
      )));
      
    }
    
    if(isset($filtros['status']) && !empty($filtros['status']))
      $conditions['FichaScorecard.codigo_status'] = $filtros['status'];

    if(isset($filtros['ProfissionalLog.codigo_documento']) && !empty($filtros['ProfissionalLog.codigo_documento']))
      $conditions['ProfissionalLog.codigo_documento'] = Comum::soNumero( $filtros['ProfissionalLog.codigo_documento'] );
    
    if(isset($filtros['ativo']) && !empty($filtros['ativo']))
      $conditions['FichaScorecard.ativo'] = $filtros['ativo'];

    if(isset($filtros['codigo_tipo_profissional']) && !empty($filtros['codigo_tipo_profissional'])){
      if( $filtros['codigo_tipo_profissional'] == 1 ){
        $conditions['ProfissionalTipo.codigo'] = $filtros['codigo_tipo_profissional'];
      } else {
        $conditions['ProfissionalTipo.codigo <>'] = 1;
      }
    }   

    if(isset($filtros['codigo_documento']) && !empty($filtros['codigo_documento']))
      $conditions['ProfissionalLog.codigo_documento'] = Comum::soNumero($filtros['codigo_documento']);
    return $conditions;
  }

  public function listarFichas($conditions){ 
    $this->FichaScorecardLog = ClassRegistry::init('FichaScorecardLog');
    $ultimaFichaScorecardLog = $this->FichaScorecardLog->listarUltimaFicha(true);

    $joins = array(
      array(
        'table' => "({$ultimaFichaScorecardLog})",
        'alias' => 'FichaScorecardLog',
        'type' => 'INNER',
        'conditions' => 'FichaScorecardLog.codigo_ficha_scorecard = FichaScorecard.codigo',
      ),
    );
    $this->bindModel(array('belongsTo' => array(
      'Usuario' => array('foreignKey' => false, 'conditions' => 'Usuario.codigo = FichaScorecardLog.codigo_usuario'),
      'ProfissionalLog' => array('foreignKey' => false, 'conditions' => 'FichaScorecard.codigo_profissional_log = ProfissionalLog.codigo'),
      'FichaScorecardVeiculo' => array('foreignKey' => false, 'conditions' => 'FichaScorecardVeiculo.codigo_ficha_scorecard = FichaScorecard.codigo'),
      'ProprietarioLog' => array('foreignKey' => false, 'conditions' => 'ProprietarioLog.codigo = FichaScorecardVeiculo.codigo_proprietario_log'),
      'VeiculoLog' => array('foreignKey' => false, 'conditions' => 'VeiculoLog.codigo = FichaScorecardVeiculo.codigo_veiculo_log'),
      'ProfissionalTipo' => array('foreignKey' => false, 'conditions' => 'ProfissionalTipo.codigo = FichaScorecard.codigo_profissional_tipo'),
      'Cliente' => array('foreignKey' => false, 'conditions' => 'Cliente.codigo = FichaScorecard.codigo_cliente'),
    )));
    $fields = array(
      'FichaScorecard.codigo',
      'Cliente.razao_social',
      'ProfissionalLog.nome',
      'ProfissionalLog.codigo_documento',
      'ProfissionalTipo.descricao',
      'ProprietarioLog.nome_razao_social',
      'ProprietarioLog.codigo_documento',
      'FichaScorecard.data_inclusao',
      'FichaScorecardLog.data_inclusao',
      'VeiculoLog.placa',
      'Usuario.nome',
      'FichaScorecard.codigo_status',
      'convert(int, datediff(MINUTE, FichaScorecard.data_inclusao, FichaScorecardLog.data_inclusao)) as tempo_atendimento'
    );
    $lista_ficha['listar'] = $this->find('all', compact('conditions','joins','fields'));
    return $lista_ficha['listar']; 
  }

  function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = 1, $extra = array()) {
    if (isset($extra['consulta_fichas'])) {
      $this->bindConsultaFichas();
      $fields = $this->consultaFichasFields();
      $order = array('FichaScorecard.data_inclusao DESC');      
    }
    if (isset($extra['joins']))
      $joins = $extra['joins'];

      if (isset($extra['group']))
        $group = $extra['group'];
    
    return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
  }

  function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
    if (isset($extra['consulta_fichas'])) {
      $this->bindConsultaFichas();
    }
    $joins = null;
    if (isset($extra['joins']))
      $joins = $extra['joins'];
    return $this->find('count', compact('conditions', 'recursive', 'joins'));
  }

  function consultaFichasFields() {
    return array(
            'FichaScorecard.codigo',
            'FichaScorecard.ativo',
            'FichaScorecard.codigo_cliente',
            'FichaScorecard.data_validade',
            'FichaScorecard.codigo_profissional_tipo',
            'FichaScorecard.data_inclusao',
            'FichaScorecard.codigo_status',
            'FichaScorecard.codigo_parametro_score',
            'FichaScorecard.codigo_profissional_log',
            "CASE 
              WHEN FichaScorecard.codigo_score_manual = 2 THEN 'Adequado' 
              WHEN FichaScorecard.codigo_score_manual = 7 THEN 'Insuficiente' 
              WHEN FichaScorecard.codigo_score_manual = 8 THEN 'Divergente' 
            END AS status_manual",            
            'Cliente.razao_social',
            'ProfissionalLog.codigo_documento',
            'ProfissionalLog.rg',
            'ProfissionalLog.nome',
            'ProfissionalTipo.descricao',
            'ProprietarioLog.codigo_documento',
            'ProprietarioLog.nome_razao_social',
            "({$this->consultaFichasFieldDataPesquisa()}) AS data_pesquisa",
            "({$this->consultaFichasFieldUsuarioPesquisa()}) AS apelido",
            "({$this->consultaFichasFieldUsuarioAlteracao()}) AS usuario_alteracao",
            'VeiculoLog.placa',
            'CarretaLog.placa',
            'BitremLog.placa',
            'ParametroScore.pontos',
            'ParametroScore.nivel',
            'ParametroScore.valor',
            'Servico.descricao',
            'Usuario.apelido'
        );
  }

  function consultaFichasFieldDataPesquisa() {
    $this->FichaScorecardLog = ClassRegistry::init('FichaScorecardLog');
        $dbo = $this->FichaScorecardLog->getDataSource();
        return $dbo->buildStatement(array(
            'table' => "{$this->FichaScorecardLog->databaseTable}.{$this->FichaScorecardLog->tableSchema}.{$this->FichaScorecardLog->useTable}",
            'alias' => 'FichaScorecardLog',
            'fields' => array('CONVERT(VARCHAR, FichaScorecardLog.data_inclusao, 120)'),
            'offset' => null,
            'joins' => array(),
            'conditions' => array('FichaScorecardLog.codigo_ficha_scorecard = FichaScorecard.codigo'),
            'limit' => 1,
            'order' => 'FichaScorecardLog.data_inclusao DESC',
            'group' => null,

        ), $this->FichaScorecardLog);
  }

  function consultaFichasFieldUsuarioPesquisa() {
    $this->FichaScorecardLog = ClassRegistry::init('FichaScorecardLog');
    $this->Usuario = ClassRegistry::init('Usuario');
        $dbo = $this->FichaScorecardLog->getDataSource();
        return $dbo->buildStatement(array(
            'table' => "{$this->FichaScorecardLog->databaseTable}.{$this->FichaScorecardLog->tableSchema}.{$this->FichaScorecardLog->useTable}",
            'alias' => 'FichaScorecardLog',
            'fields' => array('Usuario.apelido'),
            'offset' => null,
            'joins' => array(
              array(
                'table' => "{$this->Usuario->databaseTable}.{$this->Usuario->tableSchema}.{$this->Usuario->useTable}",
                'alias' => 'Usuario',
                'type' => 'LEFT',
                'conditions' => 'Usuario.codigo = FichaScorecardLog.codigo_usuario',
              ),
            ),
            'conditions' => array('FichaScorecardLog.codigo_ficha_scorecard = FichaScorecard.codigo'),
            'limit' => 1,
            'order' => 'FichaScorecardLog.data_inclusao DESC',
            'group' => null,

        ), $this->FichaScorecardLog);
  }
  function consultaFichasFieldUsuarioAlteracao() {
    $this->Usuario = ClassRegistry::init('Usuario');
        $dbo = $this->getDataSource();
        return $dbo->buildStatement(array(
            'table' => "{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}",
            'alias' => 'FichaScorecardAlteracao',
            'fields' => array('Usuario.apelido'),
            'offset' => null,
            'joins' => array(
              array(
                'table' => "{$this->Usuario->databaseTable}.{$this->Usuario->tableSchema}.{$this->Usuario->useTable}",
                'alias' => 'Usuario',
                'type' => 'LEFT',
                'conditions' => 'Usuario.codigo = FichaScorecardAlteracao.codigo_usuario_alteracao',
              ),
            ),
            'conditions' => array('FichaScorecardAlteracao.codigo = FichaScorecard.codigo'),
            'limit' => 1,
            'order' => 'FichaScorecardAlteracao.data_inclusao DESC',
            'group' => null,

        ), $this);
  }

  function bindConsultaFichas() {
    $this->bindModel(array(
      'belongsTo' => array(
        'Cliente' => array('foreignKey' => 'codigo_cliente'),
        'ProfissionalLog'   => array('foreignKey' => 'codigo_profissional_log'),
        'Usuario'           => array('foreignKey' => 'codigo_usuario_inclusao'),
        'ProfissionalTipo'  => array('foreignKey' => 'codigo_profissional_tipo'),
        'ParametroScore'    => array('foreignKey' => 'codigo_parametro_score'),
      ),
      'hasOne' => array(
        'FichaScorecardVeiculo' => array('foreignKey' => 'codigo_ficha_scorecard', 'conditions' => array('FichaScorecardVeiculo.tipo' => 0)),
        'FichaScorecardCarreta' => array('className'=>'FichaScorecardVeiculo','foreignKey' => 'codigo_ficha_scorecard', 'conditions' => array('FichaScorecardCarreta.tipo' => 1)),
        'FichaScorecardBitrem'  => array('className'=>'FichaScorecardVeiculo','foreignKey' => 'codigo_ficha_scorecard', 'conditions' => array('FichaScorecardBitrem.tipo' => 2)),
        'VeiculoLog'            => array('foreignKey' => false, 'conditions' => array('VeiculoLog.codigo = FichaScorecardVeiculo.codigo_veiculo_log')),
        'CarretaLog'            => array('className'=>'VeiculoLog', 'foreignKey' => false, 'conditions' => array('CarretaLog.codigo = FichaScorecardCarreta.codigo_veiculo_log')),
        'BitremLog'             => array('className'=>'VeiculoLog', 'foreignKey' => false, 'conditions' => array('BitremLog.codigo = FichaScorecardBitrem.codigo_veiculo_log')),
        'ProprietarioLog'       => array('foreignKey' => false, 'conditions' => array('ProprietarioLog.codigo = FichaScorecardVeiculo.codigo_proprietario_log')),
        'LogFaturamentoTeleconsult' => array('foreignKey' => 'codigo_ficha_scorecard'),
        'TipoOperacao'          => array('foreignKey' => false, 'conditions' => array('LogFaturamentoTeleconsult.codigo_tipo_operacao = TipoOperacao.codigo')),
        'Servico'               => array('foreignKey' => false, 'conditions' => array('Servico.codigo = TipoOperacao.codigo_servico')),
      ),
    ));
  }

  public function relatorio_listagem_ct(){
    $this->ParametroScore  = ClassRegistry::init('ParametroScore');
    $this->LogFaturamento  = ClassRegistry::init('LogFaturamentoTeleconsult');
    $this->Cliente         = ClassRegistry::init('Cliente');
    $this->ProfissionalLog = ClassRegistry::init('ProfissionalLog');
    $this->Profissional = ClassRegistry::init('Profissional');
    $this->Status = ClassRegistry::init('Status');
    $this->Corporacao = ClassRegistry::init('Corporacao');
    $this->FichaPesquisa = ClassRegistry::init('FichaPesquisa');
    $this->FichaIn = ClassRegistry::init('FichaPesquisa');
    $this->FichaInformacaoInsuficiente = ClassRegistry::init('FichaInformacaoInsuficiente');


      $fields = array("DISTINCT  FichaScorecard.codigo                      AS 'codigo'",
                  "'SCORECARD'                                              AS 'descricao'",
                  "Fat.codigo                                               AS 'numero'",
                  "FichaScorecard.data_inclusao                             AS 'data_alteracao'",
                  "YEAR(FichaScorecard.data_inclusao)                       AS 'ano'",
                  "CONVERT(VARCHAR(20),FichaScorecard.data_inclusao,103)    AS 'validade_de'",
                  "CONVERT(VARCHAR(20),FichaScorecard.data_validade,103)    AS 'validade_ate'",
                  "cliente.razao_social                                     AS 'razao_social'",
                  "FichaScorecard.codigo_status                             AS 'codigo_status'",
                  "CASE 
                     WHEN (FichaScorecard.codigo_parametro_score = 8) 
                         THEN 'PERFIL DIVERGENTE.' + ': Favor contactar-nos pelos fones (11)5079-2580/2581 ou 2381 ' 
                     WHEN (FichaScorecard.codigo_parametro_score = 7) 
                         THEN 'PERFIL INSUFICIENTE.' + ': Favor contactar-nos pelos fones (11)5079-2580/2581 ou 2381 ' 
                     WHEN (FichaScorecard.codigo_parametro_score <> 8 OR FichaScorecard.codigo_parametro_score <> 9) 
                         THEN UPPER(PS.nivel) + ' - ' + '(Carga máxima permitida: R$' + cast(PS.valor as varchar) + ')' 
                          
                  END                                                      AS 'FichaStatus.descricao'",
                  "Profissional.nome AS 'Profissional.nome'",
                  "Fat.numero_liberacao AS 'FichaCt.numero_liberacao'", 
                  "CONVERT(VARCHAR(20),ProfissionalLog.cnh_vencimento,103) AS 'cnh_vencimento'",
                  
                );
      $joins = array(
                    array(
                      'table' => $this->ParametroScore->databaseTable.'.'.$this->ParametroScore->tableSchema.'.'.$this->ParametroScore->useTable,
                      'alias' => 'PS',
                      'type' => 'INNER',
                      'conditions' => 'PS.codigo= FichaScorecard.codigo_parametro_score',
                    ),
                    array(
                      'table' => $this->LogFaturamento->databaseTable.'.'.$this->LogFaturamento->tableSchema.'.'.$this->LogFaturamento->useTable,
                      'alias' => 'Fat',
                      'type' => 'INNER',
                      'conditions' => 'Fat.codigo_ficha_scorecard = FichaScorecard.codigo',
                    ),
                     array(
                      'table' => $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
                      'alias' => 'Cliente',
                      'type' => 'INNER',
                      'conditions' => 'Cliente.codigo = FichaScorecard.codigo_cliente',
                    ),
                        array(
                      'table' => $this->ProfissionalLog->databaseTable.'.'.$this->ProfissionalLog->tableSchema.'.'.$this->ProfissionalLog->useTable,
                      'alias' => 'ProfissionalLog',
                      'type' => 'INNER',
                      'conditions' => 'ProfissionalLog.codigo = FichaScorecard.codigo_profissional_log',
                    ),
                    array(
                      'table' => $this->Profissional->databaseTable.'.'.$this->Profissional->tableSchema.'.'.$this->Profissional->useTable,
                      'alias' => 'Profissional',
                      'type' => 'INNER',
                      'conditions' => 'Profissional.codigo = ProfissionalLog.codigo_profissional',
                    ),
                          array(
                      'table' => $this->Status->databaseTable.'.'.$this->Status->tableSchema.'.'.$this->Status->useTable,
                      'alias' => 'FichaStatus',
                      'type' => 'INNER',
                      'conditions' => 'FichaStatus.codigo = FichaScorecard.codigo_status',
                    ),
                          array(
                      'table' => $this->Corporacao->databaseTable.'.'.$this->Corporacao->tableSchema.'.'.$this->Corporacao->useTable,
                      'alias' => 'Corporacao',
                      'type' => 'INNER',
                      'conditions' => 'Corporacao.codigo = Cliente.codigo_corporacao',
                    ),
                    array(
                      'table' => $this->FichaPesquisa->databaseTable.'.'.$this->FichaPesquisa->tableSchema.'.'.$this->FichaPesquisa->useTable,
                      'alias' => 'FichaPesquisa',
                      'type' => 'LEFT',
                      'conditions' => 'FichaPesquisa.codigo_ficha = FichaScorecard.codigo',
                    ),
                    array(
                      'table' => $this->FichaInformacaoInsuficiente->databaseTable.'.'.$this->FichaInformacaoInsuficiente->tableSchema.'.'.$this->FichaInformacaoInsuficiente->useTable,
                      'alias' => 'FichaIns',
                      'type' => 'LEFT',
                      'conditions' => 'FichaIns.codigo_ficha_pesquisa = FichaPesquisa.codigo',
                    ),    
            );    
    
    
    return array('fields'=>$fields,'joins'=>$joins);
    
  } 
    
  public function relatorio_demonstrativo_ct(){
    $this->ParametroScore              = ClassRegistry::init('ParametroScore');
    $this->FichaStatusCriterio         = ClassRegistry::init('FichaStatusCriterio');
    $this->PontuacoesStatusCriterio    = ClassRegistry::init('PontuacoesStatusCriterio');
    $this->StatusCriterio              = ClassRegistry::init('StatusCriterio');
    $this->Criterio                    = ClassRegistry::init('Criterio');
    $this->LogFaturamento              = ClassRegistry::init('LogFaturamentoTeleconsult');
    $this->Cliente                     = ClassRegistry::init('Cliente');
    $this->ProfissionalLog             = ClassRegistry::init('ProfissionalLog');
    $this->Profissional                = ClassRegistry::init('Profissional');
    $this->Status                      = ClassRegistry::init('Status');
    $this->Corporacao                  = ClassRegistry::init('Corporacao');
    $this->FichaPesquisa               = ClassRegistry::init('FichaPesquisa');
    $this->FichaInformacaoInsuficiente = ClassRegistry::init('FichaInformacaoInsuficiente');
    
    $fields = array(
      "FichaScorecard.codigo                                    AS 'Ficha.codigo'",
      "'SCORECARD'                                              AS 'Produto.descricao'",
      "Fat.codigo                                               AS 'FichaCt.numero'",
      "FichaScorecard.data_inclusao                             AS 'FichaCt.data_alteracao'",
      "YEAR(FichaScorecard.data_inclusao)                                AS 'FichaCt.ano'",
        "CONVERT(VARCHAR(20),FichaScorecard.data_inclusao,103)    AS 'Ficha.validade_de'",
        "CONVERT(VARCHAR(20),FichaScorecard.data_validade,103)    AS 'Ficha.validade_ate'",
        "cliente.razao_social                                     AS 'Cliente.razao_social'",
        "FichaScorecard.codigo_status                             AS 'Ficha.codigo_status'",
        "CASE 
                     WHEN (FichaScorecard.codigo_parametro_score = 8) 
                         THEN 'PERFIL DIVERGENTE.' + ': Favor contactar-nos pelos fones (11)5079-2580/2581 ou 2381 ' 
                     WHEN (FichaScorecard.codigo_parametro_score = 7) 
                         THEN 'PERFIL INSUFICIENTE.' + ': Favor contactar-nos pelos fones (11)5079-2580/2581 ou 2381 ' 
                     WHEN (FichaScorecard.codigo_parametro_score <> 8 OR FichaScorecard.codigo_parametro_score <> 9) 
                         THEN UPPER(PS.nivel) + ' - ' + '(Carga máxima permitida: R$' + cast(PS.valor as varchar) + ')' 
                          
                  END                                                      AS 'FichaStatus.descricao'",
         "Profissional.nome                                       AS 'Profissional.nome'",
             "Fat.numero_liberacao                                    AS 'FichaCt.numero_liberacao'", 
         "CONVERT(VARCHAR(20),ProfissionalLog.cnh_vencimento,103) AS 'ProfissionalLog.cnh_vencimento'",
         "CASE 
              WHEN (ProfissionalLog.cnh_vencimento IS NOT NULL AND
                    ProfissionalLog.cnh_vencimento < FichaScorecard.data_validade) 
                  THEN CAST(1 AS BIT) 
                  ELSE CAST(0 AS BIT) 
              END                                                      AS 'cnh_vencida'",   
         "CASE   
       WHEN PSC.insuficiente = 'TRUE' THEN
             ISC.descricao 
       ELSE  
             '' 
       END  AS 'descricao_insuficiencia'",
       "CASE   
       WHEN PSC.insuficiente = 'TRUE' THEN
           C.descricao  
       ELSE '' END
                AS 'descricao_insuficiencia2'" 
          );
       
        $joins = array(
           array(
                      'table' => $this->ParametroScore->databaseTable.'.'.$this->ParametroScore->tableSchema.'.'.$this->ParametroScore->useTable,
                      'alias' => 'PS',
                      'type' => 'INNER',
                      'conditions' => 'PS.codigo= FichaScorecard.codigo_parametro_score',
                    ),
          
          array(
            'table' => $this->FichaStatusCriterio->databaseTable.'.'.$this->FichaStatusCriterio->tableSchema.'.'.$this->FichaStatusCriterio->useTable,
            'alias' => 'FSC',
            'type' => 'LEFT',
            'conditions' => 'FichaScorecard.codigo = FSC.codigo_ficha',
          ),
          array(
                      'table' => $this->PontuacoesStatusCriterio->databaseTable.'.'.$this->PontuacoesStatusCriterio->tableSchema.'.'.$this->PontuacoesStatusCriterio->useTable,
                      'alias' => 'PSC',
                      'type' => 'LEFT', 
                      'conditions' => 'PSC.codigo = FSC.codigo_status_criterio',
                    ),
            array(
        'table' => $this->StatusCriterio->databaseTable.'.'.$this->StatusCriterio->tableSchema.'.'.$this->StatusCriterio->useTable,
        'alias' => 'ISC',
        'type' => 'LEFT',
        'conditions' => '    FSC.codigo_criterio = [ISC].[codigo_criterio] 
                         AND FSC.codigo_status_criterio = [ISC].codigo',
      ),
      array(
        'table' => $this->Criterio->databaseTable.'.'.$this->Criterio->tableSchema.'.'.$this->Criterio->useTable,
        'alias' => 'C',
        'type' => 'LEFT',
        'conditions' => 'FSC.codigo_criterio = C.codigo',
      ),
      
      array(
        'table' => $this->LogFaturamento->databaseTable.'.'.$this->LogFaturamento->tableSchema.'.'.$this->LogFaturamento->useTable,
        'alias' => 'Fat',
        'type' => 'INNER',
        'conditions' => 'Fat.codigo_ficha_scorecard = FichaScorecard.codigo',
      ),
       array(
        'table' => $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
        'alias' => 'Cliente',
        'type' => 'INNER',
        'conditions' => 'Cliente.codigo = FichaScorecard.codigo_cliente',
      ),
          array(
        'table' => $this->ProfissionalLog->databaseTable.'.'.$this->ProfissionalLog->tableSchema.'.'.$this->ProfissionalLog->useTable,
        'alias' => 'ProfissionalLog',
        'type' => 'INNER',
        'conditions' => 'ProfissionalLog.codigo = FichaScorecard.codigo_profissional_log',
      ),
      array(
        'table' => $this->Profissional->databaseTable.'.'.$this->Profissional->tableSchema.'.'.$this->Profissional->useTable,
        'alias' => 'Profissional',
        'type' => 'INNER',
        'conditions' => 'Profissional.codigo = ProfissionalLog.codigo_profissional',
      ),
            array(
        'table' => $this->Status->databaseTable.'.'.$this->Status->tableSchema.'.'.$this->Status->useTable,
        'alias' => 'FichaStatus',
        'type' => 'INNER',
        'conditions' => 'FichaStatus.codigo = FichaScorecard.codigo_status',
      ),
            array(
        'table' => $this->Corporacao->databaseTable.'.'.$this->Corporacao->tableSchema.'.'.$this->Corporacao->useTable,
        'alias' => 'Corporacao',
        'type' => 'INNER',
        'conditions' => 'Corporacao.codigo = Cliente.codigo_corporacao',
      ),
      array(
        'table' => $this->FichaPesquisa->databaseTable.'.'.$this->FichaPesquisa->tableSchema.'.'.$this->FichaPesquisa->useTable,
        'alias' => 'FichaPesquisa',
        'type' => 'LEFT',
        'conditions' => 'FichaPesquisa.codigo_ficha = FichaScorecard.codigo',
      ),
            array(
        'table' => $this->FichaInformacaoInsuficiente->databaseTable.'.'.$this->FichaInformacaoInsuficiente->tableSchema.'.'.$this->FichaInformacaoInsuficiente->useTable,
        'alias' => 'FichaIns',
        'type' => 'LEFT',
        'conditions' => 'FichaIns.codigo_ficha_pesquisa = FichaPesquisa.codigo',
      ),    
    );
        return $this->find('sql', compact('conditions','joins','fields'));

  }

    function validaCamposOutros($dados){
      $valido = 1; 
        $this->Profissional          = ClassRegistry::init('Profissional');
        if ($dados > 0){
           $this->Profissional->invalidate('codigo_documento','CPF já em pesquisa para cliente');
       $valido = 0; 
        }
        return $valido;
    }
    
    function validaOrigemDestino($data){
        $this->FichaScorecard        = ClassRegistry::init('FichaScorecard');
        $valido = 1; 
        if ($this->data['FichaScorecard']['cidade_origem']==''){
           $this->FichaScorecard->invalidate('cidade_origem','Cidade Origem Obrigatório.');
           $valido = 0; 
        }

        if ($this->data['FichaScorecard']['cidade_destino']==''){
           $this->FichaScorecard->invalidate('cidade_destino','Cidade Destino Obrigatório.');
           $valido = 0; 
        }

        return $valido;
    } 

    function validaCamposCarreteiro($data){
      $valido = 1; 
      $this->FichaScorecard        = ClassRegistry::init('FichaScorecard');
      $this->Profissional          = ClassRegistry::init('Profissional');
      $this->Veiculo               = ClassRegistry::init('Veiculo');
      $this->FichaScorecardVeiculo = ClassRegistry::init('Veiculo');
      $this->FichaScorecardVeiculo0Veiculo = ClassRegistry::init('Veiculo');
      if ($data['FichaScorecard']['codigo_carga_tipo'] == ''){
        $this->invalidate('codigo_carga_tipo','Tipo de Carga Obrigatório para Carreteiro');
        $valido = 0;
      }
      
      if( empty($data['FichaScorecard']['codigo']) ){
        $existe_carreteiro_cadastrado = $this->buscaPorCPFCarreteiro($data['Profissional']['codigo_documento']);
        if($existe_carreteiro_cadastrado['Carreteiro']['total'] > 0 ) {
          $this->Profissional->invalidate('codigo_documento','CPF em pesquisa para carreteiro');
          $valido = 0;
        }
      }
      
      if ($this->data['FichaScorecard']['cidade_origem']==''){
        $this->FichaScorecard->invalidate('cidade_origem','Cidade Origem Obrigatório para Carreteiro');
        $valido = 0; 
      }
      
      if ($this->data['FichaScorecard']['cidade_destino']==''){
        $this->FichaScorecard->invalidate('cidade_destino','Cidade Origem Obrigatório para Carreteiro');
        $valido = 0; 
      }      
      return $valido;
    }

    // Uso do this->query pois a query usa PIVOT  
    function relatorio_pesquisa_cadastro_tipo_horas( $data,$hora_inicio,$hora_fim, $origem ){
      $this->LogFaturamento = ClassRegistry::init('LogFaturamentoTeleconsult');
      $this->Usuario = ClassRegistry::init('Usuario');
      $sql = "SELECT hora as Hora, isnull([111],0) as [01], isnull([112],0) as [02], isnull([211],0) as [03], 
                isnull([212],0) as [04], isnull([751],0) as [05] 
            FROM ( select CONVERT(CHAR(2), log_faturamento.data_inclusao,114) as hora, 
                  case 
                    when (codigo_profissional_tipo = 1)  and (codigo_tipo_operacao  = 11)  then '111' 
                    when (codigo_profissional_tipo <> 1) and (codigo_tipo_operacao  = 11)  then '112' 
                    when (codigo_profissional_tipo = 1)  and (codigo_tipo_operacao  = 21)  then '211' 
                    when (codigo_profissional_tipo <> 1) and (codigo_tipo_operacao  = 21)  then '212'
                    when (codigo_profissional_tipo = 1)  and (codigo_tipo_operacao  = 67)  then '211' 
                    when (codigo_profissional_tipo <> 1) and (codigo_tipo_operacao  = 67)  then '212'
                    when (codigo_profissional_tipo = 1)  and (codigo_tipo_operacao  = 75)  then '751'
                    when (codigo_profissional_tipo <> 1) and (codigo_tipo_operacao  = 75)  then '751'
                  end as codigo_profissional_tipo, 
                  count(*) as qtde 
              from ".$this->LogFaturamento->databaseTable.".".$this->LogFaturamento->tableSchema.".".$this->LogFaturamento->useTable." log_faturamento 
              inner join  ".$this->Usuario->databaseTable.".".$this->Usuario->tableSchema.".".$this->Usuario->useTable." usuario 
              on (log_faturamento.codigo_usuario_inclusao = usuario.codigo) 
              where log_faturamento.codigo_tipo_operacao in (11,21,67,75)
              and log_faturamento.codigo_produto = 134
              and log_faturamento.data_inclusao between '".$data." ".$hora_inicio.":00' and '".$data." ".$hora_fim.":59' 
              and log_faturamento.codigo_profissional_tipo IS NOT NULL ";
      //Web
      if ($origem==1){
        $sql .= " and usuario.codigo_cliente is not null ";
      }
      //interno 
      if ($origem==2){            
        $sql .= " and usuario.codigo_cliente is  null ";            
      }
      $sql .= " group by CONVERT(CHAR(2), log_faturamento.data_inclusao,114), log_faturamento.codigo_tipo_operacao, log_faturamento.codigo_profissional_tipo ) AS tabelaorigem 
        PIVOT (SUM(QTDE) FOR codigo_profissional_tipo IN ([111], [112], [211], [212],[751])) AS pivothora order by hora asc";            
      $retorno = $this->query ($sql);
      return $retorno;
    }
     
    // Uso do this->query pois a query usa PIVOT  
    function relatorio_pesquisa_atualizacoes($mes, $ano, $usuario, $tipo_prof=NULL ){
      $this->LogFaturamentoTeleconsult = ClassRegistry::init('LogFaturamentoTeleconsult');
      $this->Usuario = ClassRegistry::init('Usuario');
      $this->TipoOperacao = ClassRegistry::init('TipoOperacao');
      $periodo = comum::periodo($ano.$mes);
      $sql ="SELECT apelido as usuario, codigo as codigo_usuario, 
        [01],[02],[03],[04],[05],[06],[07],[08],[09],[10],[11],[12],[13],[14],[15],[16],[17],[18],[19],[20],  
        [21],[22],[23],[24],[25],[26],[27],[28],[29],[30],[31]  
        from ( select usuario.apelido, usuario.codigo, day(FichaScorecard.data_inclusao) as dia, 
        count(*) as qtde  
        from ".$this->databaseTable.".".$this->tableSchema.".".$this->useTable." as FichaScorecard
        inner join ".$this->Usuario->databaseTable.".".$this->Usuario->tableSchema.".".$this->Usuario->useTable." usuario 
        on (FichaScorecard.codigo_usuario_responsavel = usuario.codigo)               
        where FichaScorecard.codigo_status = 7              
        and FichaScorecard.data_inclusao BETWEEN '".$periodo[0]."' AND '".$periodo[1]."'";
        if (!empty($usuario)){
          $sql .= " and usuario.apelido like '%".$usuario."%' ";                 
        }
        if( $tipo_prof ){
          if ($tipo_prof == 1){
            $sql .= " and FichaScorecard.codigo_profissional_tipo = 1 ";
          }else{
            $sql .= " and FichaScorecard.codigo_profissional_tipo <> 1 ";
          }
        }
        $sql .=" group by usuario.apelido, usuario.codigo, day(FichaScorecard.data_inclusao)) as tabelaorigem  
         pivot (sum(qtde) for dia in ([01],[02],[03],[04],[05],[06],[07],[08],[09],[10],  
             [11],[12],[13],[14],[15],[16],[17],[18],[19],[20],  
             [21],[22],[23],[24],[25],[26],[27],[28],[29],[30], [31])) as pivotcadastrocarreteiro  
             order by apelido";
       $retorno = $this->query($sql);
       return $retorno;
    }

    // Uso do this->query pois a query usa PIVOT  
  function relatorio_pesquisa_cadastro( $mes, $ano, $usuario, $tipo_prof=NULL, $origem =NULL){
    $this->Usuario = ClassRegistry::init('Usuario');
    $sql = 'SELECT apelido as usuario, codigo_usuario,
      [01],[02],[03],[04],[05],[06],[07],[08],[09],[10],  
      [11],[12],[13],[14],[15],[16],[17],[18],[19],[20],  
      [21],[22],[23],[24],[25],[26],[27],[28],[29],[30], [31] 
      from ( select usuario.apelido, usuario.codigo as codigo_usuario, day(FichaScorecard.data_inclusao) as dia, count(*) as qtde  
      from '.$this->databaseTable.'.'.$this->tableSchema.'.'.$this->useTable.' as FichaScorecard
      inner join '.$this->Usuario->databaseTable.'.'.$this->Usuario->tableSchema.'.'.$this->Usuario->useTable.' usuario 
      on (FichaScorecard.codigo_usuario_inclusao = usuario.codigo)
      where month(FichaScorecard.data_inclusao)='.$mes.' and year(FichaScorecard.data_inclusao)='.$ano.' ';
      if (!empty($usuario)){
        $sql .= " and usuario.apelido like '%".$usuario."%' ";              
      } 
      //Apenas Carreteiro
      if ( $tipo_prof ){
        if ($tipo_prof==1){
          $sql .= " and FichaScorecard.codigo_profissional_tipo = 1 ";
        } else {
          $sql .= " and FichaScorecard.codigo_profissional_tipo <> 1 ";
        }
      }
      //Web
      if ($origem==1){
        $sql .= " and usuario.codigo_cliente is not null ";
      }
      //interno 
      if ($origem==2){
        $sql .= " and usuario.codigo_cliente is null ";
      }

      $sql .= 'group by usuario.apelido, usuario.codigo, day(FichaScorecard.data_Inclusao)) as tabela_origem  
        pivot (sum(qtde) for dia in ([01],[02],[03],[04],[05],[06],[07],[08],[09],[10],  
                   [11],[12],[13],[14],[15],[16],[17],[18],[19],[20],  
                   [21],[22],[23],[24],[25],[26],[27],[28],[29],[30], [31])) as tabela_pivot  
                   order by apelido';
          $retorno = $this->query($sql);
          return $retorno;
  }
  
    function valida_possui_veiculo (){
      $this->invalidate('possui_veiculo','Campo hora início tem que ser número inteiro e menor que 23.');
      $validate = 'false';
    }     

  public function detalhamento_relatorio_gerencial( $filtros ){
    $this->ParametroScore = ClassRegistry::init('ParametroScore');
    $periodo = comum::periodo($filtros['ano'].$filtros['tipo_mes']);
    $tipo_profissional = !empty($filtros['tipo_profissional']) ? $filtros['tipo_profissional'] : NULL;
    if( !empty($filtros['dia']) ) {
      $mes = str_pad($filtros['tipo_mes'], 2, '0', STR_PAD_LEFT);
      $dia = str_pad($filtros['dia'], 2, '0', STR_PAD_LEFT);
      $data_inicio  = $filtros['ano'] . $mes . $dia . ' 00:00:00';
      $data_final   = $filtros['ano'] . $mes . $dia . ' 23:59:59';
      $periodo      = array($data_inicio, $data_final );
    } else {
      $mes = str_pad($filtros['tipo_mes'], 2, '0', STR_PAD_LEFT);
      $anoMes = $filtros['ano'].$mes;      
      $periodo = comum::periodo( $anoMes );
    }
    $tipo_origem = (isset($filtros['tipo_origem']) ? $filtros['tipo_origem'] : NULL);

    $query = "select score.codigo, usuario.apelido, usuario.codigo as codigo_usuario, count(score.codigo) as quantidade ";
    if( self::ENVIA_EMAIL_SCORECARD == FALSE ){
      $query = "select CASE WHEN score.codigo NOT IN (7, 8) THEN 2 ELSE score.codigo END as codigo, usuario.apelido, usuario.codigo as codigo_usuario, count(score.codigo) as quantidade ";
    }
    $query .= " from dbteleconsult.informacoes.ficha_scorecard ficha ";
    if( !empty($filtros['tipo_busca']) && $filtros['tipo_busca'] == 2 ){
      $query .= " inner join dbbuonny.portal.usuario usuario on (usuario.codigo = ficha.codigo_usuario_inclusao ) ";
    }else{
      $query .= " inner join dbbuonny.portal.usuario usuario on (usuario.codigo = ficha.codigo_usuario_responsavel ) ";      
    }
    if( self::ENVIA_EMAIL_SCORECARD == FALSE ){
      $query .= " left join dbteleconsult.informacoes.parametros_score score on (score.codigo=ficha.codigo_score_manual) ";      
    } else{
      $query .= " left join dbteleconsult.informacoes.parametros_score score on (score.codigo=ficha.codigo_score) ";
    }
    $query .= " where 1=1 ";
    if( !empty($filtros['codigo_usuario']) && $filtros['codigo_usuario'] > 1 )
      $query .= "and usuario.codigo = ".$filtros['codigo_usuario'];

    $query .= " and ficha.codigo_status = 7 ";
    $query .= " and ficha.data_inclusao between '".$periodo[0]."' AND '".$periodo[1]."' ";

    if( $tipo_profissional ){
      if ($tipo_profissional ==1){
        $query .= " and ficha.codigo_profissional_tipo = 1 ";
      } else {
        $query .= " and ficha.codigo_profissional_tipo <> 1 ";
      }      
    }

    //Web
    if ($tipo_origem==1){
      $query .= " and usuario.codigo_cliente is not null ";
    }
    //interno 
    if ($tipo_origem==2){
      $query .= " and usuario.codigo_cliente is null ";
    }

    if( self::ENVIA_EMAIL_SCORECARD == FALSE ){
      $query .= " group by CASE WHEN score.codigo NOT IN (7, 8) THEN 2 ELSE score.codigo END, usuario.apelido, usuario.codigo";
    }else{
      $query .= " group by score.codigo, score.nivel, usuario.apelido, usuario.codigo ";
    }
    $query .= " order by usuario.apelido asc";
    $dados = $this->query( $query );
    $dados = Set::extract('{n}.0', $dados );
    $atendimento_por_usuario = array();
    if ($dados) {
      $dados = array('resumo' => $dados);
      $total = 0;
      $score = $this->ParametroScore->find('list');
      $classificacao_tlc = array( 
          ParametroScore::OURO         => 'PERFIL ADEQUADO AO RISCO', 
          ParametroScore::INSUFICIENTE => 'PERFIL INSUFICIENTE', 
          ParametroScore::DIVERGENTE   => 'PERFIL DIVERGENTE'
      );
      foreach ($dados['resumo'] as $key => $dado) {
        if( self::ENVIA_EMAIL_SCORECARD == FALSE ){
          $dados['resumo'][$key]['nivel'] = $classificacao_tlc[$dado['codigo']];
        } else {
          $dados['resumo'][$key]['nivel'] = $score[$dado['codigo']];
        }        
        $total += (int)$dado['quantidade'];
      }
      foreach ($dados['resumo'] as $key => &$dado ) {
        $dado['quantidade'] = number_format(((int)$dado['quantidade']/($total / 100)), 2, ',', '.') . '% - ' . $dado['quantidade'] . ' registros';
      }      
      foreach ($dados['resumo'] as $key => &$dado ) {
        $atendimento_por_usuario['resumo'][$dado['codigo_usuario']][] = $dado;
      }
    }
    return $atendimento_por_usuario;
  }

    function robo_respostas_ultima_ficha($codigo_ficha){
       //Busca Ficha atual
       $this->ProfissionalLog = ClassRegistry::init('ProfissionalLog');
       $this->Profissional = ClassRegistry::init('Profissional');
       $this->FichaStatusCriterio = ClassRegistry::init('FichaStatusCriterio');
       $this->PontuacoesStatusCriterio = ClassRegistry::init('PontuacoesStatusCriterio');
       $this->PontuacaoSCProfissional = ClassRegistry::init('PontuacaoSCProfissional');

       $fields_busca_ficha = 'FichaScorecard.codigo,Profissional.codigo_documento,FichaScorecard.codigo_profissional_tipo'; 
       $joins_busca_ficha  = array(
                                  array(
                                      "table"     => $this->ProfissionalLog->databaseTable.'.'.$this->ProfissionalLog->tableSchema.'.'.$this->ProfissionalLog->useTable,
                                      "alias"     => "ProfissionalLog",
                                      "type"      => "INNER",
                                      "conditions"=> array("ProfissionalLog.codigo = FichaScorecard.codigo_profissional_log")
                                  ),
                                  array(
                                      "table"     => $this->Profissional->databaseTable.'.'.$this->Profissional->tableSchema.'.'.$this->Profissional->useTable,
                                      "alias"     => "Profissional",
                                      "type"      => "INNER",
                                      "conditions"=> array("Profissional.codigo = ProfissionalLog.codigo_profissional")
                                  ),
                                  );
      
       $busca_ficha = $this->find('all',array('joins'=>$joins_busca_ficha,'fields'=>$fields_busca_ficha,'conditions'=>array('FichaScorecard.codigo'=>$codigo_ficha)));
       
       
      $fields_busca_ficha_anterior = 'FichaScorecard.codigo,Profissional.codigo_documento,FichaScorecard.codigo_profissional_tipo'; 
       
      $busca_ficha_anterior  = $this->find('first',array('order'=>'FichaScorecard.codigo DESC','joins'=>$joins_busca_ficha,'fields'=>$fields_busca_ficha_anterior,'conditions'=>array('FichaScorecard.codigo !='=>$codigo_ficha,'Profissional.codigo_documento'=>$busca_ficha[0]['Profissional']['codigo_documento'],'FichaScorecard.codigo_status >'=>'4')));
      
      $fields_busca_criterios_ficha ="FichasStatusCriterios.codigo_criterio,FichasStatusCriterios.codigo_status_criterio,FichasStatusCriterios.pontos,FichasStatusCriterios.observacao,FichasStatusCriterios.automatico";
      
      $joins_busca_criterios_ficha  = array(
                                            array(
                                                "table"     => $this->FichaStatusCriterio->databaseTable.'.'.$this->FichaStatusCriterio->tableSchema.'.'.$this->FichaStatusCriterio->useTable,
                                                "alias"     => "FichasStatusCriterios",
                                                "type"      => "INNER",
                                                "conditions"=> array("FichaScorecard.codigo = FichasStatusCriterios.codigo_ficha")
                                            ),
                                            array(
                                                "table"     => $this->PontuacoesStatusCriterio->databaseTable.'.'.$this->PontuacoesStatusCriterio->tableSchema.'.'.$this->PontuacoesStatusCriterio->useTable,
                                                "alias"     => "PontuacoesStatusCriterios",
                                                "type"      => "INNER",
                                                "conditions"=> array("FichasStatusCriterios.codigo_status_criterio = PontuacoesStatusCriterios.codigo_status_criterio")
                                            ),
                                            array(
                                                "table"     => $this->PontuacaoSCProfissional->databaseTable.'.'.$this->PontuacaoSCProfissional->tableSchema.'.'.$this->PontuacaoSCProfissional->useTable,
                                                "alias"     => "PontuacoesStatusCriteriosProfissional",
                                                "type"      => "INNER",
                                                "conditions"=> array("PontuacoesStatusCriterios.codigo = PontuacoesStatusCriteriosProfissional.codigo_pontuacao_status_criterio")
                                            ),  

                                            );
      

      $busca_criterios_ficha = $this->find('all',array('joins'=>$joins_busca_criterios_ficha,'fields'=>$fields_busca_criterios_ficha,'conditions'=>array('FichasStatusCriterios.codigo_ficha ='=>$busca_ficha_anterior['FichaScorecard']['codigo'],'PontuacoesStatusCriteriosProfissional.codigo_tipo_profissional'=>$busca_ficha_anterior['FichaScorecard']['codigo_profissional_tipo'],'automatico ='=>'0')));
      
      foreach ($busca_criterios_ficha as $gravar_dados){
         ClassRegistry::init('FichaStatusCriterio')->salvarStatus2($codigo_ficha, $gravar_dados['FichasStatusCriterios']['codigo_criterio'], $gravar_dados['FichasStatusCriterios']['codigo_status_criterio'],true,$gravar_dados['FichasStatusCriterios']['observacao']);

      }
      
    }

 
  public function geraBackupFicha( $data ) {
    $this->FichaScorecardRetorno      = ClassRegistry::init('FichaScorecardRetorno');
    $this->FichaScorecardVeiculo      = ClassRegistry::init('FichaScorecardVeiculo');
    $this->VeiculoLog                 = ClassRegistry::init('VeiculoLog');
    $this->FichaScProfContatoLog      = ClassRegistry::init('FichaScProfContatoLog');
    $this->ProfissionalContatoLog     = ClassRegistry::init('ProfissionalContatoLog');
    $this->FichaScorecardQuestaoResp  = ClassRegistry::init('FichaScorecardQuestaoResp');
    $this->LogFaturamentoTeleconsult  = ClassRegistry::init('LogFaturamentoTeleconsult');
    $this->FichaStatusCriterio        = ClassRegistry::init('FichaStatusCriterio');    
    $this->ParametroScore             = ClassRegistry::init('ParametroScore');
    //Pago os dados da Ficha a ser copiada
    $codigo_ficha = $data['FichaScorecard']['codigo'];
    $dados_ficha  = $this->carregar( $codigo_ficha );
    try {
      $this->query('begin transaction');
      if( !$dados_ficha )
        throw new Exception("Erro ao gerar cópia da ficha atual");
      //Atualizo a ficha para (EDITADA) ATIVO = 3
      $dados_ficha_atualizacao = $dados_ficha;
      $dados_ficha_atualizacao['FichaScorecard']['ativo'] = 3;
      $this->atualizar( $dados_ficha_atualizacao );
      //Faz a inclusao da copia da FICHA            
      $dados_ficha['FichaScorecard']['codigo_ficha_scorecard']  = $codigo_ficha;
      $dados_ficha['FichaScorecard']['justificativa_alteracao'] = $data['FichaScorecard']['justificativa_alteracao'];
      $this->incluir( $dados_ficha );
      $codigo_nova_ficha = $this->id;
      $this->alteraScoreManualmente( $codigo_nova_ficha, $data['FichaScorecard']['codigo_parametro_score'] );

      //Recupera os dados de retorno da ficha a ser copiada
      $dados_retorno = $this->FichaScorecardRetorno->find('all',array('conditions'=>array('FichaScorecardRetorno.codigo_ficha_scorecard' => $codigo_ficha )));
      foreach( $dados_retorno as $retorno ) {
        $retorno['FichaScorecardRetorno']['codigo_ficha_scorecard'] = $codigo_nova_ficha;
        //Gravo os dados de retorno para o BKP
        $this->FichaScorecardRetorno->incluir( $retorno );
      }      
      //Recuperar os dados do veiculo      
      $this->FichaScorecardVeiculo->bindModel(array('belongsTo' => array(
          'VeiculoLog' => array('foreignKey' => 'codigo_veiculo_log')))
      );
      $dados_veiculo_log = $this->FichaScorecardVeiculo->find('all', array('conditions' => array('codigo_ficha_scorecard' => $codigo_ficha) ));
      foreach(  $dados_veiculo_log as $dados_veiculo ){
        //Gerei BKP do Veiculo LOG
        $codigo_veiculo_log = $this->VeiculoLog->duplicar( $dados_veiculo['VeiculoLog']['codigo'] );
        //Vou gravar a Ficha Scorecard Veiculo fazendo referencia ao BKP do veiculo LOG
        $dados_veiculo['FichaScorecardVeiculo']['codigo_veiculo_log'] = $codigo_veiculo_log;
        $dados_veiculo['FichaScorecardVeiculo']['codigo_ficha_scorecard'] = $codigo_nova_ficha;
        $this->FichaScorecardVeiculo->incluir( $dados_veiculo  );
      }
      //Recuperar os dados de contato do Profissional
      $this->FichaScProfContatoLog->bindModel(array('belongsTo' => array(
          'ProfissionalContatoLog' => array('foreignKey' => 'codigo_profissional_contato_log')))
      );      
      $sc_profissional_contato_log = $this->FichaScProfContatoLog->find('all', array('conditions' => array('codigo_ficha_scorecard' => $codigo_ficha) ));
      foreach( $sc_profissional_contato_log as $key => $dados_contato ){
        //Gravo copia do Profissional contato LOG        
        $this->ProfissionalContatoLog->incluir( $dados_contato );        
        $codigo_contato_log = $this->ProfissionalContatoLog->id;
        //Gravo nova scorecard profissional contato LOG
        $data_scorecard_profissional_contato_log = array(
          'codigo_ficha_scorecard'          => $codigo_nova_ficha,
          'codigo_profissional_contato_log' => $codigo_contato_log
        );
        $this->FichaScProfContatoLog->incluir( $data_scorecard_profissional_contato_log );
      }      
      
      $this->bindModel(array('belongsTo' => array(
        'FichaScorecardQuestaoResp' => array( 
          'className'  =>'FichaScorecardQuestaoResp', 
          'foreignKey' => false, 
          'conditions' => array( 'FichaScorecardQuestaoResp.codigo_ficha_scorecard = FichaScorecard.codigo')))
      ));
      $dados_questao_resposta = $this->find('all', array('conditions' => array( 'FichaScorecardQuestaoResp.codigo_ficha_scorecard' => $codigo_ficha) ));
      foreach( $dados_questao_resposta as $questao_resposta ) {        
        $questao_resp['FichaScorecardQuestaoResp'] = $questao_resposta['FichaScorecardQuestaoResp'];
        $questao_resp['FichaScorecardQuestaoResp']['codigo_ficha_scorecard'] = $codigo_nova_ficha;
        $this->FichaScorecardQuestaoResp->incluir( $questao_resp );
      }
      $dados_log_faturamento = $this->LogFaturamentoTeleconsult->find('all', array('conditions' => array('codigo_ficha_scorecard' => $codigo_ficha) ));
      //Atualizo o log de faturamento para nao cobrar 
      $dados_log_faturamento[0]['LogFaturamentoTeleconsult']['codigo_tipo_operacao'] = TipoOperacao::ATUALIZACAO_SEM_COBRANCA;
      $this->LogFaturamentoTeleconsult->atualizar( $dados_log_faturamento[0] );
      //Insiro novo log de faturamento
      $dados_log_faturamento[0]['LogFaturamentoTeleconsult']['codigo_ficha_scorecard'] = $codigo_nova_ficha;
      $dados_log_faturamento[0]['LogFaturamentoTeleconsult']['codigo_ficha']           = NULL;      
      $dados_log_faturamento[0]['LogFaturamentoTeleconsult']['codigo_tipo_operacao']   = TipoOperacao::TIPO_OPERACAO_ATUALIZACAO;
      $this->LogFaturamentoTeleconsult->incluir( $dados_log_faturamento[0] );      
      //Pesquisar Pesquisa de criterios      
      $dados           = $this->FichaStatusCriterio->formatarDados( $data, $codigo_nova_ficha, $_SESSION['Auth']['Usuario']['codigo'] );      
      $salva_criterios = $this->FichaStatusCriterio->salvarFichaStatusCriterio( $codigo_nova_ficha, $dados, TRUE );
      $gravar_pontos   = $this->FichaStatusCriterio->atualizarParaGravarPontosCriterio( $codigo_nova_ficha, TRUE );

      
      $this->commit();
      return $codigo_nova_ficha;
    } catch(Exception $e) {
      $this->rollback();
      return false;
    }
  }


  function relatorioVinculoPaginate (){
          $result['fields'] =array(   
                                   'case when [FichaScorecard].[codigo_ficha_scorecard] is null 
                                         then [FichaScorecard].[codigo]     
                                         else [FichaScorecard].[codigo_ficha_scorecard]
                                    end as codigo_ficha_scorecard',
                                   '[FichaScorecard].[codigo] as codigo', 
                                   'FichaScorecard.codigo_cliente',
                                   'FichaScorecard.codigo_profissional_tipo',
                                   'FichaScorecard.data_inclusao',
                                   'CONVERT(VARCHAR, FichaScorecard.data_validade,103) as data_validade',
                                   'FichaScorecard.codigo_status',
                                   'FichaScorecard.total_pontos',
                                   'FichaScorecard.data_alteracao',
                                   'Score.nivel',
                                   'Score.valor',
                                   'Cliente.razao_social',
                                   'FichaScorecard.ativo',
                                   'FichaScorecard.codigo_parametro_score',
                                   'ProfissionalLog.nome',
                                   'ProfissionalLog.codigo_documento',
                                   'ProfissionalLog.RG',
                                   'ProfissionalLog.codigo_profissional',
                                   'ProfissionalTipo.descricao',
                                   'Usuario.apelido'
                             );
           
                  

          return $result;

    }

    public function carregaFichaAnteriorProfissional( $codigo_profissional, $codigo_cliente=FALSE, $codigo_ficha = FALSE ) {
        $this->bindModel(array('belongsTo' => array(
            'ProfissionalLog' => array('foreignKey' => 'codigo_profissional_log'),
            'ParametroScore'  => array('foreignKey' => 'codigo_parametro_score')
            )
          )
        );        
        $conditions = array(
            'ProfissionalLog.codigo_profissional' => $codigo_profissional,
            'NOT' => array('FichaScorecard.ativo' => array(2,3)),
        );
        if ($codigo_ficha)
            $conditions['FichaScorecard.codigo <'] = $codigo_ficha;        

        if($codigo_cliente)
          $conditions['FichaScorecard.codigo_cliente'] = $codigo_cliente;
        $order  = array('FichaScorecard.codigo DESC');
        $ultimaFicha = $this->find('first', compact('conditions', 'order'));        
        return $ultimaFicha;
    }

  
  function pesquisador_automatico_scorecard($codigo_ficha){
    
    $this->PesquisaConfiguracao    = ClassRegistry::init('PesquisaConfiguracao');
    $this->FichaStatusCriterio     = ClassRegistry::init('FichaStatusCriterio');
    $this->FichaScorecardVeiculo   = ClassRegistry::init('FichaScorecardVeiculo');
    $this->ProprietarioLog         = ClassRegistry::init('ProprietarioLog');
    $this->ProfissionalNegativacao = ClassRegistry::init('ProfissionalNegativacao');
    $this->VeiculoLog              = ClassRegistry::init('ProfissionalNegativacao');
    $this->Proprietario            = ClassRegistry::init('Proprietario');
    $this->Profissional            = ClassRegistry::init('Profissional');

    App::import('Model', 'Produto');
    App::import('Model', 'ParametroScore');
    App::import('Model', 'Criterio');

    //$dados_ficha         = $this->carregar($codigo_ficha);
    $codigo_profissional = $this->buscaCodigoProfissional($codigo_ficha);
    //$profissional        = $this->Profissional->carregar($codigo_profissional); 

    $conditions_pesq['PesquisaConfiguracao.codigo_produto'] = Produto::SCORECARD;// Scorecard
    $criterios_pesquisador = $this->PesquisaConfiguracao->find('first',array('conditions'=>$conditions_pesq));   

    $pesquisador_automatico = 1;

    //Procuro Critérios já gravados na Ficha 
    //$criterios_categoria = $this->FichaStatusCriterio->buscarPorFicha($codigo_ficha);

    $quantidade_cheque = $criterios_pesquisador['PesquisaConfiguracao']['quantidade_cheque'];

    //Regra - Limites de Cheque 
    // if(!$this->PesquisaConfiguracao->validaQuantidadeChequesSerasaProfissionalScorecard($codigo_ficha,$codigo_profissional,$quantidade_cheque)){
    //   echo "erro cheque\n";
    //   return false;
    // }

    $valor_serasa = $criterios_pesquisador['PesquisaConfiguracao']['valor_serasa'];

    //Regra - Valor Serasa
    // if(!$this->PesquisaConfiguracao->validaMontanteSerasaProfissionalScorecard($codigo_ficha,$codigo_profissional,$valor_serasa)){
    //   echo "erro serasa\n";
    //   return false;
    // }

    //Quantidade de viagens (Histórico) //Quantidade de meses (Histórico) 
    if(!$this->PesquisaConfiguracao->validaHistoricoProfissional($codigo_profissional,Produto::SCORECARD)){
      echo "erro histórico: Profissional não Consultado\n";
      return false;
    }

    //Status Última Pesquisa (Profissional) - tem que ser adequado ao risco
    if ($criterios_pesquisador['PesquisaConfiguracao']['codigo_status_anterior']==1){

        if(!$this->PesquisaConfiguracao->validaStatusUltimaPesquisaProfissional($codigo_profissional, $codigo_ficha)){
          echo "erro ultimo status\n";
          return false;
        }
    }     

    //Status Última Pesquisa (Proprietário) -> tem que ter sido proprietario
    if ($criterios_pesquisador['PesquisaConfiguracao']['codigo_status_anterior_proprietario']==1) {
        $proprietario_log                 = $this->buscaProprietarioLog($codigo_ficha);
        $documento_proprietario           = $proprietario_log['ProprietarioLog']['codigo_documento'];
        $profissional_proprietario        = $this->Profissional->listaMotoristaPorCPF($documento_proprietario);
        $codigo_profissional_proprietario = !empty($profissional_proprietario[0]['Profissional']['codigo']) ? $profissional_proprietario[0]['Profissional']['codigo'] : NULL;
        if(!$this->PesquisaConfiguracao->validaStatusUltimaPesquisaProfissional($codigo_ficha, $codigo_profissional_proprietario)){
            echo "erro ultimo status proprietário\n";
            return false;
        }
    }
 
    //Ver Validade CNH
    if ($criterios_pesquisador['PesquisaConfiguracao']['verificar_validade_cnh']==1){
        if(!$this->PesquisaConfiguracao->validaCNHVencida($codigo_profissional)){  
          echo  "erro cnh vencida\n";
          return false;
        }
    }

    //Quantidade de viagens (Ren. Automática) 
    if(!$this->PesquisaConfiguracao->validaHistoricoProfissionalRenovacaoAuto($codigo_profissional, Produto::SCORECARD, $codigo_ficha)){
      echo "erro valida renovação auto\n";
      return false;
    }

    //Quantidade de meses (agregado)
    //$quantidadeMesesOutrosAg = $criterios_pesquisador['PesquisaConfiguracao']['historico_quantidade_meses_agregado'];

    //Ver Profissional Negativado
    if ($criterios_pesquisador['PesquisaConfiguracao']['verificar_profissional_negativado']==1){
        if(!$this->PesquisaConfiguracao->validaProfissionalNegativado($codigo_profissional)){
            echo "erro profissional negativado\n";
            return false;
         }
    }

    //Ver Veículo Ocorrência 
    if ($criterios_pesquisador['PesquisaConfiguracao']['verificar_veiculo_ocorrencia']==1){
       if(!$this->PesquisaConfiguracao->validaVeiculoComOcorrenciasScorecard($codigo_ficha)){
          echo  "erro veiculo com ocorrencia\n";
          return false;
       }
    }
  // Grava Pesquisador Automatico ok
    return $this->concluirPesquisa($codigo_ficha, null, TRUE );
  }  

  public function consultaStatusMotorista( $options ){
    $data_inclusao = date('Ymd H:i:s');
    $this->TipoOperacao = ClassRegistry::init('TipoOperacao');
    $this->ClienteProdutoServico2 = ClassRegistry::init('ClienteProdutoServico2');
    $this->LogFaturamentoTeleconsult = ClassRegistry::init('LogFaturamentoTeleconsult');
    $this->Veiculo = ClassRegistry::init('Veiculo');
    $codigo_tipo_operacao = $this->verificaTipoOperacaoConsulta( $options );    
    $consulta6Horas       = $this->LogFaturamentoTeleconsult->verificaConsulta6horas( $options['codigo_profissional'], $options['codigo_cliente'], Produto::SCORECARD );
    $ultima_ficha         = $this->carregaFichaAnteriorProfissional( $options['codigo_profissional'] );
    if( !empty($consulta6Horas) && AppModel::dateToDbDate($consulta6Horas['LogFaturamentoTeleconsult']['data_inclusao']) > $data_inclusao ) {
      if( $codigo_tipo_operacao == 1 )
        $codigo_tipo_operacao = 7;
      elseif( $codigo_tipo_operacao == 125 )
        $codigo_tipo_operacao = 130;
      else
        $codigo_tipo_operacao = 8;
    }
    $mensagem_retorno = $this->TipoOperacao->carregar( $codigo_tipo_operacao);
    $valores_cobranca = $this->verificaValoresCobrancaConsultaMotorista(  $options['codigo_cliente'], $codigo_tipo_operacao );
    $codigo_veiculo   = isset( $options['placa_veiculo'] ) ? $this->Veiculo->buscaPorPlaca( $options['placa_veiculo'], 'codigo' ) : NULL;
    $codigo_carreta   = isset( $options['placa_carreta'] ) ? $this->Veiculo->buscaPorPlaca( $options['placa_carreta'], 'codigo' ) : NULL;
    $codigo_bitrem    = isset( $options['placa_bitrem'] )  ? $this->Veiculo->buscaPorPlaca( $options['placa_bitrem'], 'codigo' )  : NULL;
    $dados_faturamento = array(
      'LogFaturamentoTeleconsult' => array(
      'codigo_produto'                    => Produto::SCORECARD,
      'codigo_cliente'                    => $options['codigo_cliente'],
      'codigo_cliente_pagador'            => $valores_cobranca['codigo_cliente_pagador'],
      'codigo_profissional'               => $options['codigo_profissional'],
      'codigo_profissional_tipo'          => 1,
      'codigo_veiculo'                    => $codigo_veiculo['Veiculo']['codigo'],
      'codigo_veiculo_carreta'            => $codigo_carreta['Veiculo']['codigo'],
      'codigo_veiculo_bitrem'             => $codigo_carreta['Veiculo']['codigo'],
      'codigo_tipo_operacao'              => $codigo_tipo_operacao, 
      'valor'                             => $valores_cobranca['valor_consulta'],
      'placa'                             => strtoupper( str_replace('-', '', $options['placa_veiculo'] )),
      'valor_premio_minimo'               => $valores_cobranca['valor_premio_minimo'],
      'valor_taxa_bancaria'               => $valores_cobranca['valor_taxa_bancaria'], 
      'codigo_carga_tipo'                 => $options['codigo_carga_tipo'],
      'codigo_endereco_cidade_origem'     => $options['codigo_endereco_cidade_carga_origem'],
      'codigo_endereco_cidade_destino'    => $options['codigo_endereco_cidade_carga_destino'],
      'placa_carreta'                     => strtoupper( str_replace('-', '', $options['placa_carreta'] )),
      'placa_veiculo_bitrem'              => strtoupper( str_replace('-', '', $options['placa_bitrem'] )),
      'codigo_carga_valor'                => NULL,
      'observacao'                        => $mensagem_retorno['TipoOperacao']['descricao'].'. '.$mensagem_retorno['TipoOperacao']['observacao'],
      'codigo_corporacao'                 => !empty($valores_cobranca['codigo_corporacao']) ? $valores_cobranca['codigo_corporacao'] : 1,
      )
    );
    if( !in_array($codigo_tipo_operacao, array( 7,8 ) ) ) {
      $this->LogFaturamentoTeleconsult->incluirLogFaturamento( $dados_faturamento );
      $codigo_log_faturamento = $this->LogFaturamentoTeleconsult->id;
    } else {    
      $codigo_log_faturamento = $this->LogFaturamentoTeleconsult->obterUltimoCodigoLogFaturamentoPorCliente( $options['codigo_cliente'], $options['codigo_profissional'] );
    }
    return compact('codigo_log_faturamento', 'mensagem_retorno', 'ultima_ficha' );
  }


  public function verificaTipoOperacaoConsulta( $options ){
    $this->Cliente             = ClassRegistry::init('Cliente');
    $this->VeiculoOcorrencia   = ClassRegistry::init('VeiculoOcorrencia');
    $this->LiberacaoProvisoria = ClassRegistry::init('LiberacaoProvisoria');
    $this->Profissional        = ClassRegistry::init('Profissional');
    $this->ProfNegativacaoCliente = ClassRegistry::init('ProfNegativacaoCliente');    
    $this->ProfissionalNegativacao = ClassRegistry::init('ProfissionalNegativacao');    
    $this->Produto             = ClassRegistry::init('Produto');
    $this->ParametroScore      = ClassRegistry::init('ParametroScore');
    
    $placa_veiculo = isset( $options['placa_veiculo'] ) ? strtoupper( str_replace('-', '', $options['placa_veiculo'] )) : NULL;
    $placa_carreta = isset( $options['placa_carreta'] ) ? strtoupper( str_replace('-', '', $options['placa_carreta'] )) : NULL;
    $placa_bitrem  = isset( $options['placa_bitrem'] )  ? strtoupper( str_replace('-', '', $options['placa_bitrem'] ))  : NULL;
    //Verifica se o profissional esta cadastrado
    $dados_profissional = $this->Profissional->buscaPorCPF( $options['codigo_documento'] );
    if( !$dados_profissional )
      return 109;//Nao cadastrado
    //Verifica se o motorista foi pesquisa no periodo de 2 horas
    if( $this->verificaConsulta2horas( $dados_profissional['Profissional']['codigo'], $options['codigo_cliente'] ))    
      return 115;//perfil divergente
    
    //Verificar se o profissional possui negativacao
    if( !$this->ProfNegativacaoCliente->verificaProfissional($dados_profissional['Profissional']['codigo'], $options['codigo_cliente'] ))
      return 115;//perfil divergente

    if( $this->ProfissionalNegativacao->existenegativacao ($dados_profissional['Profissional']['codigo']) )
      return 115;//perfil divergente

    //Verifica se o cliente ja enviou alguma ficha do profissional
    $ficha_profissional = $this->buscaProfissionalPorCliente( $options['codigo_documento'], $options['codigo_cliente'], TRUE );
    if( !$ficha_profissional ){
      if( !$this->verificaProfissionalEmPesquisa( $options['codigo_documento'] ))
        return 116;//Nao pesquisado
    }
    
    if( $this->verificaObrigatoriedadeDaPlaca( $options['codigo_cliente'] ) ){
    //Verifica vencimento CNH
      if( $this->dateToDbDate( $dados_profissional['Profissional']['cnh_vencimento'] ) < date('Ymd', strtotime('-30 days')) ){
        return 113;//CNH Vencida
      }
      //Verifica Obrigatoriedade da PLACA
      //Verifica se o profissional possui ficha com a placa enviada      
      if(!$this->verificaFichaPorPlacaProfissional(array('placa'=>$placa_veiculo,'codigo_profissional'=>$dados_profissional['Profissional']['codigo'])))
        return 111;//Veiculo nao cadastrado
      //Verificar veiculo ocorrencia 
      $placas = array();
      array_push($placas, $placa_veiculo );
      if($placa_carreta)
        array_push($placas, $placa_carreta );
      if($placa_bitrem)
        array_push($placas, $placa_bitrem );
      if( $this->VeiculoOcorrencia->ocorrenciaPorPlaca( array('placa'=> $placas), 'count'))
        return 112;      
    }
    
    //Verifica se a carreta já foi informada em alguma ficha desse motorista
    if( $placa_carreta ){
      if(!$this->verificaFichaPorPlacaProfissional(array('placa'=>$placa_veiculo,'codigo_profissional'=>$dados_profissional['Profissional']['codigo'])))
        return 111;//Veiculo nao cadastrado
    }
    //Verifica Liberacao Provisoria
    $ultima_ficha = $this->carregaFichaAnteriorProfissional( $dados_profissional['Profissional']['codigo'] );
    if( !$this->LiberacaoProvisoria->verificaLiberacaoProvisoria( $dados_profissional['Profissional']['codigo'], Produto::SCORECARD ) ){
      if( $ultima_ficha['FichaScorecard']['codigo_status'] != FichaScorecardStatus::FINALIZADA ){//Esta em pesquisa
        $ultima_ficha_carreteiro = $this->buscaProfissionalCarreteiro( $options['codigo_documento'] );
        if( isset($ultima_ficha_carreteiro['FichaScorecard'])) {
          if( ($ultima_ficha_carreteiro['FichaScorecard']['codigo_status'] == FichaScorecardStatus::FINALIZADA ) && $ultima_ficha_carreteiro['FichaScorecard']['total_pontos'] > 0 ){//Se for outra ficha
            return 1;//Adequado
          } else {
            return 102;//Em analise
          }
        }
        return 102;//Em analise
      }
      if( self::ENVIA_EMAIL_SCORECARD ){
        if( $ultima_ficha['FichaScorecard']['total_pontos'] > 0 ){
          return 1;
        }
      } else {
        if( $ultima_ficha['FichaScorecard']['codigo_score_manual'] == ParametroScore::OURO ) {
          return 1; 
        }
      }
      return 2;
    } else {
      return 125;//Adequado ao risco
    }    
  }

  public function verificaFichaPorPlacaProfissional( $options, $type='first' ){
    if( empty($options['placa'] ) )
      return false;
    $conditions = array();    
    $conditions['VeiculoLog.placa'] = strtoupper( str_replace('-', '', $options['placa']) );
    if( !empty($options['codigo_profissional'] ) )
      $conditions['ProfissionalLog.codigo_profissional'] = $options['codigo_profissional'];
    $this->bindModel(array('belongsTo' => array(
      'ProfissionalLog' => array('foreignKey'=>'codigo_profissional_log'),
      'FichaScorecardVeiculo' => array('foreignKey'=>false,  'conditions' => array('FichaScorecardVeiculo.codigo_ficha_scorecard = FichaScorecard.codigo')),
      'VeiculoLog'            => array(
        'foreignKey' =>FALSE, 
        'conditions' => array(
          'FichaScorecardVeiculo.codigo_veiculo_log = VeiculoLog.codigo'
        )
      )
    )));
    return $this->find( $type, compact('conditions'));
  }

  public function verificaProfissionalEmPesquisa( $codigo_documento ) {
    $this->bindModel(array('belongsTo' => array('ProfissionalLog' => array('foreignKey' => 'codigo_profissional_log'))));
    $conditions = array(
      'ProfissionalLog.codigo_documento' => preg_replace('/\D/', '', $codigo_documento),
      'codigo_status NOT' => array( FichaScorecardStatus::FINALIZADA, FichaScorecardStatus::RENOVADA )
    );    
    $ficha = $this->find('count', compact('conditions') );
    return ($ficha > 0);
  }


  public function verificaValoresCobrancaConsultaMotorista( $codigo_cliente, $codigo_tipo_operacao ){
    $this->LogFaturamentoTeleconsult = ClassRegistry::init('LogFaturamentoTeleconsult');
    $codigo_produto = Produto::SCORECARD;
    $codigo_servico = 3;//consulta motorista
    $valor_consulta = $this->ClienteProdutoServico2->verificaValorCobrancaConsultaMotorista( $codigo_cliente, $codigo_tipo_operacao, $codigo_produto, $codigo_servico );
    $codigo_corporacao      = $valor_consulta['Cliente']['codigo_corporacao'];//@codigo_corporacao, 
    $codigo_cliente_pagador = $valor_consulta['ClienteProdutoServico2']['codigo_cliente_pagador'];//@codigo_cliente_pagador,
    $codigo_servico         = $valor_consulta['ClienteProdutoServico2']['codigo_servico'];//@codigo_servico;
    $valor_consulta         = (!empty($valor_consulta[0]['valor']) ? $valor_consulta[0]['valor'] : 0);//@valor_consulta,

    $codigo_servico = 5;//premio minimo
    $valor_consulta_premio_minimo = $this->ClienteProdutoServico2->verificaValorCobrancaConsultaMotorista( $codigo_cliente, $codigo_tipo_operacao, $codigo_produto, $codigo_servico );
    $valor_premio_minimo = 0;
    if ( $valor_consulta_premio_minimo ){
      $codigo_corporacao      = $valor_consulta_premio_minimo['Cliente']['codigo_corporacao'];//@codigo_corporacao, 
      $codigo_cliente_pagador = $valor_consulta_premio_minimo['ClienteProdutoServico2']['codigo_cliente_pagador'];//@codigo_cliente_pagador_premio_minimo,
      $codigo_servico         = $valor_consulta_premio_minimo['ClienteProdutoServico2']['codigo_servico'];//@codigo_servico;
      $valor_premio_minimo    = (!empty($valor_consulta_premio_minimo[0]['valor']) ? $valor_consulta_premio_minimo[0]['valor'] : 0);//@valor_premio_minimo
    }
    
    $codigo_servico = 6;//taxa bancaria
    $valor_consulta_taxa_bancaria = $this->ClienteProdutoServico2->verificaValorCobrancaConsultaMotorista( $codigo_cliente, $codigo_tipo_operacao, $codigo_produto, $codigo_servico );
    $valor_taxa_bancaria = 0;
    if ( $valor_consulta_taxa_bancaria ){
      $codigo_corporacao      = $valor_consulta_taxa_bancaria['Cliente']['codigo_corporacao'];//@codigo_corporacao, 
      $codigo_cliente_pagador = $valor_consulta_taxa_bancaria['ClienteProdutoServico2']['codigo_cliente_pagador'];//@codigo_cliente_pagador_premio_minimo,
      $codigo_servico         = $valor_consulta_taxa_bancaria['ClienteProdutoServico2']['codigo_servico'];//@codigo_servico;
      $valor_taxa_bancaria    = (!empty($valor_consulta_taxa_bancaria[0]['valor']) ? $valor_consulta_taxa_bancaria[0]['valor'] : 0);//@valor_premio_minimo
    }

    $valores_cobranca = array(
      'codigo_cliente_pagador' => (!empty($codigo_cliente_pagador) ? $codigo_cliente_pagador : $codigo_cliente), 
      'valor_consulta'         => $valor_consulta, 
      'valor_premio_minimo'    => $valor_premio_minimo, 
      'valor_taxa_bancaria'    => $valor_taxa_bancaria,
      'codigo_corporacao'      => $codigo_corporacao
    );
    return $valores_cobranca;
  } 



  public function buscaCodigoFichaProfissionalPraRenovacaoAutomatica( $codigo_cliente,  $codigo_profissional, $codigo_profissional_tipo ) {
    APP::import('Model', 'ProfissionalTipo');
    APP::import('Model', 'Produto');
    $this->bindModel(array('belongsTo' => array('ProfissionalLog' => array('foreignKey' => 'codigo_profissional_log'))));
    $conditions = array(
      'FichaScorecard.codigo_cliente'             => $codigo_cliente,      
      'FichaScorecard.codigo_profissional_tipo'   => $codigo_profissional_tipo,
      'FichaScorecard.ativo'                      => 1,
      'ProfissionalLog.codigo_profissional'       => $codigo_profissional,
    );
    $fields = array('FichaScorecard.codigo');
    $order  = 'FichaScorecard.codigo DESC';
    $ficha  = $this->find('first', compact('fields', 'conditions', 'order'));    
    return $ficha;
  }

  public function inativaFichasAnteriores( $codigo_cliente, $codigo_profissional, $codigo_profissional_tipo ){    
    $this->bindModel(array('belongsTo' => array('ProfissionalLog' => array('foreignKey' => 'codigo_profissional_log'))));
    $fichas = $this->find('all', array(
      'conditions' => array( 
          'FichaScorecard.codigo_cliente'           => $codigo_cliente,
          'ProfissionalLog.codigo_profissional'     => $codigo_profissional,
          'FichaScorecard.codigo_profissional_tipo' => $codigo_profissional_tipo,
      ),
      'fields' => array('FichaScorecard.codigo')
    ));
    foreach( $fichas as $dados ){
      $dados['FichaScorecard']['ativo'] = 0;
      if( !$this->atualizar( $dados ) ){
        return FALSE;
      }
    }    
    return TRUE;
  }

  public function buscaProfissionalLog( $codigo_ficha ) {
    $this->bindModel(array('belongsTo' => array('ProfissionalLog' => array('foreignKey' => 'codigo_profissional_log'))));    
    return $this->find('first', array('conditions' => array('FichaScorecard.codigo' => $codigo_ficha), 'order'=>'ProfissionalLog.codigo DESC'));
  }

  public function buscaProprietarioLog( $codigo_ficha ) {
    $this->bindModel(array('belongsTo' => array(
      'FichaScorecardVeiculo' => array( 'foreignKey' => false, 'conditions' => 'FichaScorecardVeiculo.codigo_ficha_scorecard = FichaScorecard.codigo'),
      'ProprietarioLog'       => array( 'foreignKey' => false, 'conditions' => 'ProprietarioLog.codigo = FichaScorecardVeiculo.codigo_proprietario_log')
    )));
    return $this->find('first', array('conditions' => array('FichaScorecard.codigo' => $codigo_ficha), 'order'=>'ProprietarioLog.codigo DESC'));
  }

  public function realizaRenovacaoAutomatica( $dados_renovacao ) {

    $this->LogFaturamentoTeleconsult  = ClassRegistry::init('LogFaturamentoTeleconsult');
    $this->RenovacaoAutomatica        = ClassRegistry::init('RenovacaoAutomatica');
    $this->ClienteProduto             = ClassRegistry::init('ClienteProduto');
    $this->ClienteProdutoServico      = ClassRegistry::init('ClienteProdutoServico');
    APP::import('Model', 'Servico');
    APP::import('Model', 'Produto');
    APP::import('Model', 'TipoOperacao');
    APP::import('Model', 'FichaScorecardStatus');
    APP::import('Model', 'Usuario');

    $this->query('begin transaction');
    $codigo_cliente           = $dados_renovacao['codigo_cliente'];
    $codigo_profissional      = $dados_renovacao['codigo_profissional'];
    $codigo_produto           = $dados_renovacao['codigo_produto'];
    $codigo_profissional_tipo = $dados_renovacao['codigo_profissional_tipo'];
    $codigo_ficha = $this->buscaCodigoFichaProfissionalPraRenovacaoAutomatica( $codigo_cliente,  $codigo_profissional, $codigo_profissional_tipo  );
    $codigo_ficha = $codigo_ficha['FichaScorecard']['codigo'];

    if( $codigo_ficha ){

      if( $this->ClienteProduto->produtoClienteAtivo( $codigo_cliente, Produto::SCORECARD ) ) {

        $renova_ficha = TRUE;   
        $dados_servico = $this->ClienteProdutoServico->obterParametrosDoServico( 
              $codigo_cliente, 
              Produto::SCORECARD, 
              Servico::RENOVACAO_AUTOMATICA, 
              $codigo_profissional_tipo 
            );
        if(empty($dados_servico)){
            $renova_ficha = FALSE;
        }
        $codigo_cliente_pagador = $dados_servico['ClienteProdutoServico']['codigo_cliente_pagador'];

        if( $codigo_cliente != $codigo_cliente_pagador ){
          if( !$this->ClienteProduto->produtoClienteAtivo( $codigo_cliente_pagador, Produto::SCORECARD ) ) {
            $renova_ficha = FALSE;
          }
        }
        if( $renova_ficha === TRUE ){
          if(!$this->inativaFichasAnteriores( $codigo_cliente, $codigo_profissional, $codigo_profissional_tipo )){
            $this->rollback();
            return FALSE;
          }

          $dados_ficha = $this->carregar( $codigo_ficha );          
          unset($dados_ficha['FichaScorecard']['codigo']);
          $dados_ficha['FichaScorecard']['ativo']                       = 1;
          $dados_ficha['FichaScorecard']['codigo_status']               = FichaScorecardStatus::CADASTRADA;
          $dados_ficha['FichaScorecard']['codigo_usuario_inclusao']     = Usuario::RENOVACAO_AUTOMATICA;//Renovacao automatica
          $dados_ficha['FichaScorecard']['data_alteracao']              = NULL;
          $dados_ficha['FichaScorecard']['codigo_usuario_responsavel']  = NULL;
          $dados_ficha['FichaScorecard']['codigo_usuario_em_pesquisa']  = NULL;
          $dados_ficha['FichaScorecard']['codigo_usuario_em_aprovacao'] = NULL;          
          $dados_ficha['FichaScorecard']['codigo_usuario_alteracao']    = NULL;          
          if(!$this->incluir( $dados_ficha )){
            $this->rollback();
            return FALSE;
          }

          if(!$this->relacionarFichaRetorno( $codigo_ficha, $this->id )){
            $this->rollback();
            return FALSE;
          }

          if(!$this->relacionarFichaVeiculo( $codigo_ficha, $this->id )){            
            $this->rollback();
            return FALSE;
          }

          if(!$this->relacionarFichaContatoProfissional( $codigo_ficha, $this->id )){
            $this->rollback();
            return FALSE;
          }
        
          if(!$this->relacionarFichaContatoProprietario( $codigo_ficha, $this->id )){
            $this->rollback();
            return FALSE;
          }

          if(!$this->relacionarFichaQuestaoResposta( $codigo_ficha, $this->id )){
            $this->rollback();
            return FALSE;
          }
          if(!$this->relacionarFichaStatusCriterios( $codigo_ficha, $this->id )){
            $this->rollback();
            return FALSE;
          }
          if(!$this->incluirLogFaturamentoRenovacaoAutomatica( $this->id, $dados_servico ) ){
            $this->rollback();
            return FALSE;            
          }
          $dados_renovacao_atualizar = array(
              'codigo'                   => $dados_renovacao['codigo'],
              'processado'               => TRUE,
              'data_alteracao'           => date("Ymd H:i:s"),
              'codigo_usuario_alteracao' => Usuario::RENOVACAO_AUTOMATICA
            );
          if($this->RenovacaoAutomatica->atualizar( array('RenovacaoAutomatica' => $dados_renovacao_atualizar ) )){
            $this->commit();
            return TRUE; 
          }else{
            $this->rollback();
            return FALSE; 
          }                          
        }
      } 
    }
  }
    
  

  public function relacionarFichaRetorno( $codigo_ficha_anterior, $codigo_ficha_atual ){
    $this->FichaScorecardRetorno = ClassRegistry::init('FichaScorecardRetorno');

    $dados_retorno = $this->FichaScorecardRetorno->find('all',array('conditions'=>array('FichaScorecardRetorno.codigo_ficha_scorecard' => $codigo_ficha_anterior )));
    foreach( $dados_retorno as $retorno ) {
      unset($retorno['FichaScorecardRetorno']['codigo']);
      $retorno['FichaScorecardRetorno']['codigo_ficha_scorecard'] = $codigo_ficha_atual;
      if(!$this->FichaScorecardRetorno->incluir( $retorno )){
        return FALSE;
      }
    }
    return TRUE;
  }

  public function relacionarFichaVeiculo( $codigo_ficha_anterior, $codigo_ficha_atual ){
    $this->FichaScorecardVeiculo =& ClassRegistry::init('FichaScorecardVeiculo');
    $dados_retorno = $this->FichaScorecardVeiculo->find('all',array('conditions'=>array('FichaScorecardVeiculo.codigo_ficha_scorecard' => $codigo_ficha_anterior )));
    foreach( $dados_retorno as $retorno ) {
      unset($retorno['FichaScorecardVeiculo']['codigo']);
      $retorno['FichaScorecardVeiculo']['codigo_ficha_scorecard'] = $codigo_ficha_atual;
      if( !$this->FichaScorecardVeiculo->incluir( $retorno ))
        return false;
    }
    return true;
  }

  public function relacionarFichaContatoProfissional( $codigo_ficha_anterior, $codigo_ficha_atual ){    
    $this->FichaScProfContatoLog = ClassRegistry::init('FichaScProfContatoLog');
    $dados_retorno = $this->FichaScProfContatoLog->find('all',
          array(
            'conditions' => array(
              'FichaScProfContatoLog.codigo_ficha_scorecard' => $codigo_ficha_anterior
            ) 
          )
        );    
    foreach( $dados_retorno as $retorno ) {
      unset($retorno['FichaScProfContatoLog']['codigo']);
      $retorno['FichaScProfContatoLog']['codigo_ficha_scorecard'] = $codigo_ficha_atual;
      if( !$this->FichaScProfContatoLog->incluir( $retorno ))
        return FALSE;
    }
    return TRUE;
  }

  public function relacionarFichaContatoProprietario( $codigo_ficha_anterior, $codigo_ficha_atual ){
    $this->FichaScPropContatoLog = ClassRegistry::init('FichaScPropContatoLog');
    $dados_retorno = $this->FichaScPropContatoLog->find('all',
      array(
        'conditions' => array(
          'FichaScPropContatoLog.codigo_ficha_scorecard' => $codigo_ficha_anterior
        ) 
    ));
    foreach( $dados_retorno as $retorno ) {
      unset($retorno['FichaScPropContatoLog']['codigo']);
      $retorno['FichaScPropContatoLog']['codigo_ficha_scorecard'] = $codigo_ficha_atual;
      if( !$this->FichaScPropContatoLog->incluir( $retorno ))
        return FALSE;
    }
    return TRUE;
  }


  public function relacionarFichaQuestaoResposta( $codigo_ficha_anterior, $codigo_ficha_atual ){
    $this->FichaScorecardQuestaoResp =& ClassRegistry::init('FichaScorecardQuestaoResp');    
    $dados_questao_resposta = $this->FichaScorecardQuestaoResp->find('all',array('conditions'=>array('FichaScorecardQuestaoResp.codigo_ficha_scorecard'=>$codigo_ficha_anterior)));
    foreach( $dados_questao_resposta as $questao_resposta ) {
      unset($questao_resposta['FichaScorecardQuestaoResp']['codigo']);
      $questao_resposta['FichaScorecardQuestaoResp']['codigo_ficha_scorecard'] = $codigo_ficha_atual;
      if( !$this->FichaScorecardQuestaoResp->incluir($questao_resposta))
        return false;
    }
    return true;
  }

  public function relacionarFichaStatusCriterios( $codigo_ficha_anterior, $codigo_ficha_atual ){
    APP::import('Model', 'Usuario');
    $this->FichaStatusCriterio = ClassRegistry::init('FichaStatusCriterio');    
    $conditions = array('FichaStatusCriterio.codigo_ficha'=>$codigo_ficha_anterior);
    $fields     = array( 
      "$codigo_ficha_atual AS codigo_ficha", "codigo_criterio AS codigo_criterio", "codigo_status_criterio AS codigo_status_criterio", 
      "pontos AS pontos", Usuario::RENOVACAO_AUTOMATICA." AS codigo_usuario_inclusao" , "getdate() AS data_inclusao", "observacao", "automatico"
    );
    $query_ficha_status_criterios = $this->FichaStatusCriterio->find('sql', compact('conditions', 'fields') );    
    $query = "INSERT INTO ".$this->FichaStatusCriterio->databaseTable.".".$this->FichaStatusCriterio->tableSchema.".".$this->FichaStatusCriterio->useTable;
    $query.= "( codigo_ficha, codigo_criterio, codigo_status_criterio, pontos, codigo_usuario_inclusao, data_inclusao, observacao, automatico ) ";
    $query.=  $query_ficha_status_criterios;
    $this->FichaStatusCriterio->query( $query );
    return true;
  }


  public function carregarFichaCompleta( $conditions ){    
      $this->bindModel(
      array(
        'belongsTo'=>array(
          'Cliente'=>array('foreignKey'=>'codigo_cliente'),
          'Usuario'=>array('foreignKey'=>'codigo_usuario_responsavel'),
          'ProfissionalLog'=>array('foreignKey'=>'codigo_profissional_log'),
          'Profissional'=>array('foreignKey'=>FALSE, 'conditions' =>array('Profissional.codigo = ProfissionalLog.codigo_profissional') ),
          'ProfissionalEnderecoLog'=>array('foreignKey'=>'codigo_profissional_endereco_log'),
          'VEndereco'=>array('foreignKey'=>false, 'conditions'=>'ProfissionalEnderecoLog.codigo_endereco = VEndereco.endereco_codigo'),
          'EnderecoCidadeOrigem' => array('className'=>'EnderecoCidade', 'foreignKey' => 'codigo_endereco_cidade_carga_origem'),
          'EnderecoCidadeDestino' => array('className'=>'EnderecoCidade', 'foreignKey' => 'codigo_endereco_cidade_carga_destino'),
        ),
        'hasMany'=>array(
          'FichaScorecardRetorno'=>array('foreignKey'=>'codigo_ficha_scorecard'),
          'FichaScProfContatoLog'=>array('foreignKey'=>'codigo_ficha_scorecard'),
          'FichaScorecardVeiculo'=>array('foreignKey'=>'codigo_ficha_scorecard'),
          'FichaScorecardQuestaoResp' => array('foreignKey'=>'codigo_ficha_scorecard'),
        )
      )
    );
    $dados_ficha = $this->find('first', compact('conditions'));
    $this->VeiculoLog = ClassRegistry::init('VeiculoLog');
    foreach( $dados_ficha['FichaScorecardVeiculo'] as $key=> $ficha_scorecard_veiculo ){      
      $this->VeiculoLog->bindModel(array(
          'belongsTo' => array(
            'VeiculoModelo'  => array('foreignKey' => 'codigo_veiculo_modelo'),
            'EnderecoCidade' => array('foreignKey' => 'codigo_cidade_emplacamento'),
            'EnderecoEstado' => array('foreignKey' => false, 'conditions'=>'EnderecoCidade.codigo_endereco_estado = EnderecoEstado.codigo'),
          ),
      ));
      $veiculo = $this->VeiculoLog->find('first', array('conditions' =>
        array('VeiculoLog.codigo'=>$ficha_scorecard_veiculo['codigo_veiculo_log'])
      ));
      $dados_ficha['FichaScorecardVeiculo'][$key] = $veiculo;
    }
    return $dados_ficha;
  }

  public function incluirLogFaturamentoRenovacaoAutomatica( $codigo_ficha, $dados_servico = NULL ){
    $this->LogFaturamentoTeleconsult = ClassRegistry::init('LogFaturamentoTeleconsult');    
    $this->ClienteProdutoServico     = ClassRegistry::init('ClienteProdutoServico');
    APP::import('Model', 'Servico');
    APP::import('Model', 'Produto');    
    $conditions  = array( 'FichaScorecard.codigo' =>  $codigo_ficha );
    $dados_ficha = $this->carregarFichaCompleta( $conditions );
    if( $dados_servico  == NULL)
      $dados_servico        = $this->ClienteProdutoServico->obterParametrosDoServico( 
        $dados_ficha['FichaScorecard']['codigo_cliente'], 
        Produto::SCORECARD, 
        Servico::RENOVACAO_AUTOMATICA, 
        $dados_ficha['FichaScorecard']['codigo_profissional_tipo'] 
      );
    $codigo_cliente_pagador = $dados_servico['ClienteProdutoServico']['codigo_cliente_pagador'];
    $tempo_pesquisa         = $dados_servico['ClienteProdutoServico']['tempo_pesquisa'];
    $valor_renovacao        = $dados_servico['ClienteProdutoServico']['valor'];
    if( empty($valor_renovacao) )
      return false;
    $servicos               = array(Servico::PREMIO_MINIMO, Servico::TAXA_BANCARIA);
    $valores_servico        = $this->ClienteProdutoServico->getValorServico( $codigo_cliente_pagador, Produto::SCORECARD, $servicos );
    $valor_premio_minimo    = isset($valores_servico[Servico::PREMIO_MINIMO]) ? $valores_servico[Servico::PREMIO_MINIMO] : 0;
    $valor_taxa_bancaria    = isset($valores_servico[Servico::TAXA_BANCARIA]) ? $valores_servico[Servico::TAXA_BANCARIA] : 0;
    $codigo_corporacao      = $dados_ficha['Cliente']['codigo_corporacao'];
    $codigo_veiculo         = !empty($dados_ficha['FichaScorecardVeiculo'][0]['VeiculoLog']['codigo_veiculo']) ? $dados_ficha['FichaScorecardVeiculo'][0]['VeiculoLog']['codigo_veiculo'] : NULL;
    $codigo_carreta         = !empty($dados_ficha['FichaScorecardVeiculo'][1]['VeiculoLog']['codigo_veiculo']) ? $dados_ficha['FichaScorecardVeiculo'][1]['VeiculoLog']['codigo_veiculo'] : NULL;
    $data_log_faturamento = array(
      'codigo_produto'                  => Produto::SCORECARD,
      'codigo_cliente'                  => $dados_ficha['FichaScorecard']['codigo_cliente'],
      'codigo_cliente_pagador'          => $codigo_cliente_pagador,
      'codigo_profissional'             => $dados_ficha['ProfissionalLog']['codigo_profissional'],
      'codigo_profissional_tipo'        => $dados_ficha['FichaScorecard']['codigo_profissional_tipo'],
      'codigo_veiculo'                  => $codigo_veiculo,
      'codigo_veiculo_carreta'          => $codigo_carreta,
      'codigo_tipo_operacao'            => TipoOperacao::TIPO_OPERACAO_RENOVACAO_AUTOMATICA,
      'codigo_operacao'                 => NULL,
      'valor'                           => $valor_renovacao,
      'valor_premio_minimo'             => $valor_premio_minimo,
      'valor_taxa_bancaria'             => $valor_taxa_bancaria,
      'codigo_carga_tipo'               => $dados_ficha['FichaScorecard']['codigo_carga_tipo'],
      'codigo_endereco_cidade_origem'   => $dados_ficha['FichaScorecard']['codigo_endereco_cidade_carga_origem'],
      'codigo_endereco_cidade_destino'  => $dados_ficha['FichaScorecard']['codigo_endereco_cidade_carga_destino'],
      'codigo_carga_valor'              => $dados_ficha['FichaScorecard']['codigo_carga_valor'],
      'observacao'                      => '',
      'cobertura_acidentes'             => 0,
      'codigo_ficha'                    => NULL,
      'codigo_ficha_scorecard'          => $codigo_ficha,
      'codigo_corporacao'               => $codigo_corporacao,
      'data_inclusao'                   => date("Ymd H:i:s"),
      'codigo_usuario_inclusao'         => Usuario::RENOVACAO_AUTOMATICA
    );
    return $this->LogFaturamentoTeleconsult->incluir( $data_log_faturamento );
  }

  public function concluirPesquisa($codigo_ficha, $dados, $pesquisador_automatico = FALSE){
    $this->LogFaturamentoTeleconsult = ClassRegistry::init('LogFaturamentoTeleconsult');
    $this->FichaStatusCriterio       = ClassRegistry::init('FichaStatusCriterio');
    $this->ClienteProdutoServico     = ClassRegistry::init('ClienteProdutoServico');
    App::import('Model', 'TipoOperacao');
    App::import('Model', 'Servico');
    App::import('Model', 'FichaScorecardStatus');
    if(is_null($dados)){
      $dados = $this->FichaStatusCriterio->find(
        'all', array('conditions' => array('FichaStatusCriterio.codigo_ficha' => $codigo_ficha))
      );
    }
    $insuficiente = $this->FichaStatusCriterio->verificarCamposInsuficientesFicha( $dados );
    $divergente   = $this->FichaStatusCriterio->verificarCamposDivergentesFicha( $dados );
    //Só grava o score se a pesquisa estiver sem insuficiencias e sem divergencias
    if( $insuficiente === 0 && $divergente === 0 ){
      if(!$this->FichaStatusCriterio->atualizarParaGravarPontosCriterio($codigo_ficha))
        return FALSE;
    } else {//Grava com valores zerados 
      if( $divergente )
        $pontos_score = -1;
      else
        $pontos_score =  0;
      $this->FichaStatusCriterio->removePontosCriterio( $codigo_ficha, $pontos_score );
    }

    $veio_renovacao = $this->LogFaturamentoTeleconsult->find('all',
        array('conditions' => array(
          'LogFaturamentoTeleconsult.codigo_ficha_scorecard' => $codigo_ficha,
          'LogFaturamentoTeleconsult.codigo_tipo_operacao' => TipoOperacao::TIPO_OPERACAO_RENOVACAO_AUTOMATICA
    )));    
    $ficha          = $this->carregar($codigo_ficha);
    $ficha          = $ficha['FichaScorecard'];
    $validade_ficha = comum::dateToTimestamp($ficha['data_validade']);
    $data           = date('Y-m-d',$validade_ficha);
    $status         = FichaScorecardStatus::A_APROVAR;
    if( $pesquisador_automatico )
      $status = FichaScorecardStatus::A_PESQUISAR;
    if(!empty($veio_renovacao)){
      $hoje = strtotime(date('Y-m-d'));      
      $meses_validade = $this->ClienteProdutoServico->obterParametrosDoServico(
        $ficha['codigo_cliente'],
        $ficha['codigo_produto'],
        Servico::RENOVACAO_AUTOMATICA, 
        $ficha['codigo_profissional_tipo']
      );      
      $meses_validade = $meses_validade['ClienteProdutoServico']['validade'];
      if($meses_validade){
        if($validade_ficha > $hoje){
          $data = date('Y-m-d 23:59:59', strtotime('+ '.$meses_validade.' months', $validade_ficha));
        }else{
          $data = date('Y-m-d 23:59:59', strtotime('+ '.$meses_validade.' months', $hoje));
        }
      }
      $status = FichaScorecardStatus::FINALIZADA;
    }
    $dados_atualizar = array('FichaScorecard' => array('codigo' => $codigo_ficha, 'codigo_status' => $status, 'data_validade' => $data));
    if(!is_null($dados)){
      $dados_atualizar['FichaScorecard']['resumo'] = !empty($dados['FichaScorecard']['resumo']) ? $dados['FichaScorecard']['resumo'] : null;
    }
    if($this->atualizar($dados_atualizar)){
      return TRUE;
    }
    return FALSE;
  }

  function buscaCodigoVeiculo($codigo_ficha) {
    $this->bindModel(
      array(
        'belongsTo'   => array(
          'FichaScorecardVeiculo' => array('foreignKey' => false, 'conditions' => 'FichaScorecardVeiculo.codigo_ficha_scorecard = FichaScorecard.codigo'),
          'VeiculoLog' => array('foreignKey' => false, 'conditions' => 'VeiculoLog.codigo = FichaScorecardVeiculo.codigo_veiculo_log')
        )
      )
    );
    $conditions = array(
      'FichaScorecard.codigo' => $codigo_ficha
    );
    $fields = array(
        'FichaScorecardVeiculo.codigo_veiculo_log'
      );
    $veiculos_ficha = $this->find('all', compact('fields','conditions'));    
    $codigos_veiculo = array();    
    foreach ($veiculos_ficha as $veiculoLog) {
        $codigos_veiculo[] = $veiculoLog['FichaScorecardVeiculo']['codigo_veiculo_log'];
    }

    return $codigos_veiculo;
  }

  public function alteraScoreManualmente( $codigo_ficha, $codigo_parametros_score ){
    if( self::ENVIA_EMAIL_SCORECARD === FALSE ){      
      $this->ParametroScore = ClassRegistry::init('ParametroScore');
      $parametros_score = $this->ParametroScore->carregar( $codigo_parametros_score );
      if ( $parametros_score ) {
        $ficha = array( 
          'codigo' => $codigo_ficha, 
          'codigo_score_manual' => $codigo_parametros_score );
        $this->save( $ficha, array( 'validate' => false )); 
      }
    }
  }

  public function detalhamento_relatorio_gerencial_total( $filtros ){
    $this->ParametroScore = ClassRegistry::init('ParametroScore');
    $periodo = comum::periodo($filtros['ano'].$filtros['tipo_mes']);
    $tipo_profissional = !empty($filtros['tipo_profissional']) ? $filtros['tipo_profissional'] : NULL;
    if( !empty($filtros['dia']) ) {
      $mes = str_pad($filtros['tipo_mes'], 2, '0', STR_PAD_LEFT);
      $dia = str_pad($filtros['dia'], 2, '0', STR_PAD_LEFT);
      $data_inicio  = $filtros['ano'] . $mes . $dia . ' 00:00:00';
      $data_final   = $filtros['ano'] . $mes . $dia . ' 23:59:59';
      $periodo      = array($data_inicio, $data_final );
    } else {
      $mes = str_pad($filtros['tipo_mes'], 2, '0', STR_PAD_LEFT);
      $anoMes = $filtros['ano'].$mes;      
      $periodo = comum::periodo( $anoMes );
    }
    $tipo_origem = (isset($filtros['tipo_origem']) ? $filtros['tipo_origem'] : NULL);

    $query = "select score.codigo, count(score.codigo) as quantidade ";
    if( self::ENVIA_EMAIL_SCORECARD == FALSE ){
      $query = "select CASE WHEN score.codigo NOT IN (7, 8) THEN 2 ELSE score.codigo END as codigo, count(score.codigo) as quantidade ";
    }
    $query .= " from dbteleconsult.informacoes.ficha_scorecard ficha ";
    if( !empty($filtros['tipo_busca']) && $filtros['tipo_busca'] == 2 ){
      $query .= " inner join dbbuonny.portal.usuario usuario on (usuario.codigo = ficha.codigo_usuario_inclusao ) ";
    }else{
      $query .= " inner join dbbuonny.portal.usuario usuario on (usuario.codigo = ficha.codigo_usuario_responsavel ) ";      
    }
    if( self::ENVIA_EMAIL_SCORECARD == FALSE ){
      $query .= " left join dbteleconsult.informacoes.parametros_score score on (score.codigo=ficha.codigo_score_manual) ";      
    } else{
      $query .= " left join dbteleconsult.informacoes.parametros_score score on (score.codigo=ficha.codigo_score) ";
    }
    $query .= " where 1=1 ";
    // if( !empty($filtros['codigo_usuario']) && $filtros['codigo_usuario'] > 1 )
    //   $query .= "and usuario.codigo = ".$filtros['codigo_usuario'];

    $query .= " and ficha.codigo_status = 7 ";
    $query .= " and ficha.data_inclusao between '".$periodo[0]."' AND '".$periodo[1]."' ";

    if( $tipo_profissional ){
      if ($tipo_profissional ==1){
        $query .= " and ficha.codigo_profissional_tipo = 1 ";
      } else {
        $query .= " and ficha.codigo_profissional_tipo <> 1 ";
      }      
    }

    //Web
    if ($tipo_origem==1){
      $query .= " and usuario.codigo_cliente is not null ";
    }
    //interno 
    if ($tipo_origem==2){
      $query .= " and usuario.codigo_cliente is null ";
    }

    if( self::ENVIA_EMAIL_SCORECARD == FALSE ){
      $query .= " group by CASE WHEN score.codigo NOT IN (7, 8) THEN 2 ELSE score.codigo END ";
    }else{
      $query .= " group by score.codigo, score.nivel ";
    }
    // $query .= " order by usuario.apelido asc";
    $dados = $this->query( $query );
    $dados = Set::extract('{n}.0', $dados );
    if ($dados) {
      $dados = array('resumo' => $dados);
      $total = 0;
      $score = $this->ParametroScore->find('list');
      $classificacao_tlc = array( 
          ParametroScore::OURO         => 'PERFIL ADEQUADO AO RISCO', 
          ParametroScore::INSUFICIENTE => 'PERFIL INSUFICIENTE', 
          ParametroScore::DIVERGENTE   => 'PERFIL DIVERGENTE'
      );
      foreach ($dados['resumo'] as $key => $dado) {
        if( self::ENVIA_EMAIL_SCORECARD == FALSE ){
          $dados['resumo'][$key]['nivel'] = $classificacao_tlc[$dado['codigo']];
        } else {
          $dados['resumo'][$key]['nivel'] = $score[$dado['codigo']];
        }        
        $total += (int)$dado['quantidade'];
      }
      foreach ($dados['resumo'] as $key => &$dado ) {
        $dado['quantidade'] = number_format(((int)$dado['quantidade']/($total / 100)), 2, ',', '.') . '% - ' . $dado['quantidade'] . ' registros';
      }      
    }
    return $dados;
  }







}
?>