<?php

class LogAtendimento extends AppModel {

    var $name = 'LogAtendimento';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'log_atendimento';
    var $primaryKey = 'codigo';
    var $displayField = '';
    var $actsAs = array('Secure');


    function start() {
        $this->LogAtendimento = & ClassRegistry::init('LogAtendimento');
    }
    
     

    function gravaLogExclusaoAtendimentoVeiculoOcorrencia($data){
        
         $queryOcorrenciaV = "select 
                                top 1 *
                            from
                                dbbuonny.publico.veiculo_ocorrencia as ocorrencia
                            where 
                                ocorrencia.codigo =".$data;
         $ocorrenciaV      = $this->query($queryOcorrenciaV);
         
          
        $queryOcorrencia = "select
                                top 1 ocorrencia.descricao
                            from
                                dbbuonny.publico.ocorrencia as ocorrencia
                            where 
                                ocorrencia.codigo =".$ocorrenciaV[0][0]['codigo_ocorrencia'];
         $ocorrencia      = $this->query($queryOcorrencia); 

         
         
         $queryPlacaVeiculo = "select
                                top 1 veiculo.placa
                            from
                                dbbuonny.publico.veiculo as veiculo
                            where
                                veiculo.codigo  = ".$ocorrenciaV[0][0]['codigo_veiculo'];
         $PlacaVeiculo = $this->query($queryPlacaVeiculo); 
 


         $observacao = "Placa: ".$PlacaVeiculo[0][0]['placa'].", ocorrência: ".$ocorrencia[0][0]['descricao'];
         
         //debug($observacao);die();
         App::import('Model', 'TipoOperacao');
         App::import('Model', 'Produto');

         $codigo_tipo_operacao = TipoOperacao::EXCLUSAO_OCORRENCIA_VEICULO;
         
         $codigo_usuario_inclusao = $_SESSION['Auth']['Usuario']['codigo_usuario_inclusao'];
               
         //debug($ocorrencia[0][0]['descricao']);die();
         $codigo_produto = Produto::SCORECARD;
                
         $data_inicio = date('Y-m-d H:i:s');

         $dados = array('LogAtendimento' => array('codigo_produto' => $codigo_produto,
                       'codigo_tipo_operacao' => $codigo_tipo_operacao,
                       'observacao' =>$observacao,
                       'codigo_veiculo'=>$ocorrenciaV[0][0]['codigo_veiculo'],
                       'data_inicio' => $data_inicio,
                       'codigo_usuario_inclusao' => $codigo_usuario_inclusao));
        return $this->incluir($dados);

    }


    
    function gravaLogAtendimentoVeiculoOcorrencia($data) {
        
         $queryPlacaVeiculo = "select
                                top 1 veiculo.placa
                            from
                                dbbuonny.publico.veiculo as veiculo
                            where
                                veiculo.codigo  = ".$data['VeiculoOcorrencia']['codigo_veiculo'];
         $PlacaVeiculo = $this->query($queryPlacaVeiculo); 

         $queryOcorrencia = "select
                                top 1 ocorrencia.descricao
                            from
                                dbbuonny.publico.ocorrencia as ocorrencia
                            where 
                                ocorrencia.codigo =".$data['VeiculoOcorrencia']['codigo_ocorrencia'];
         $ocorrencia      = $this->query($queryOcorrencia);

         $observacao = "Placa: ".$PlacaVeiculo[0][0]['placa'].", ocorrência: ".$ocorrencia[0][0]['descricao'];
        
         App::import('Model', 'TipoOperacao');
         App::import('Model', 'Produto');

         $codigo_tipo_operacao = TipoOperacao::INCLUSAO_OCORRENCIA_VEICULO;
         
         $codigo_usuario_inclusao = $_SESSION['Auth']['Usuario']['codigo_usuario_inclusao'];
               
         //debug($ocorrencia[0][0]['descricao']);die();
         $codigo_produto = Produto::SCORECARD;
                
         $data_inicio = date('Y-m-d H:i:s');

         $dados = array('LogAtendimento' => array('codigo_produto' => $codigo_produto,
                       'codigo_tipo_operacao' => $codigo_tipo_operacao,
                       'observacao' =>$observacao,
                       'codigo_veiculo'=>$data['VeiculoOcorrencia']['codigo_veiculo'],
                       'data_iniciooo' => $data_inicio,
                       'codigo_usuario_inclusao' => $codigo_usuario_inclusao));
        
        return $this->incluir($dados);
    } 

    function gravaLogAtendimentoAprovacaoProfissional($ficha) {
        $this->Ficha = & ClassRegistry::init('Ficha');
        $this->Usuario = & ClassRegistry::init('Usuario');

        $codigo_usuario_inclusao = $this->Usuario->find('first', array('fields' => 'codigo',
                    'conditions' => array('apelido' => 'pesquisador_automatico')));
        $codigo_usuario_inclusao = $codigo_usuario_inclusao['Usuario']['codigo'];

        $codigo_produto = $ficha['Ficha']['codigo_produto'];
        $codigo_tipo_operacao = 61;
        $codigo_profissional = $this->Ficha->buscaCodigoProfissional($ficha['Ficha']['codigo']);
        $codigo_profissional_tipo = $ficha['Ficha']['codigo_profissional_tipo'];
        $data_inicio = date('Y-m-d H:i:s');

        $dados = array('LogAtendimento' => array('codigo_produto' => $codigo_produto,
                       'codigo_tipo_operacao' => $codigo_tipo_operacao,
                       'codigo_profissional' => $codigo_profissional,
                       'codigo_profissional_tipo' => $codigo_profissional_tipo,
                       'data_inicio' => $data_inicio, 
                       'codigo_usuario_inclusao' => $codigo_usuario_inclusao));

        unset($this->Ficha);
        unset($this->usuario);
        return $this->incluir($dados);
    }

    function converteFiltroEmConditionExclusaoVinculos($dados) {
       App::import('Model', 'Produto');
       $condition = array();
       if (isset($dados['data_inicial']) && !empty($dados["data_inicial"])  && isset($dados['data_final']) && !empty($dados["data_final"])) {
            $periodo = array( AppModel::dateToDbDate($dados["data_inicial"].' 00:00'),  AppModel::dateToDbDate($dados["data_final"].' 23:59') );
            $condition = array('LogAtendimento.data_inicio BETWEEN ? AND ?' => $periodo);
        }
        if (isset($dados['usuario']) && !empty($dados["usuario"])) {
            $condition["Usuario.apelido LIKE"] = $dados["usuario"]."%";
        }
        if (isset($dados['codigo_documento']) && !empty($dados["codigo_documento"])) {
            $condition["Profissional.codigo_documento"] = $dados["codigo_documento"];
        }
        if (isset($dados['codigo_profissional_tipo']) && !empty($dados["codigo_profissional_tipo"])) {
            if ($dados['codigo_profissional_tipo']==1 )
                $condition["LogAtendimento.codigo_profissional_tipo"] = $dados['codigo_profissional_tipo'];
            else
                $condition["LogAtendimento.codigo_profissional_tipo <> "] =  1;
        }
        $condition["LogAtendimento.codigo_tipo_operacao"] = 101;
        if (isset($dados['codigo']) && !empty($dados["codigo"])) {           
           $condition[" (select top 1 c.codigo
                                from [dbBuonny].publico.profissional_log a
                                inner join [dbTeleconsult].[informacoes].[ficha_scorecard] b on a.codigo = b.codigo_profissional_log
                                inner join [dbBuonny].vendas.cliente c on c.codigo = b.codigo_cliente 
                                where a.codigo_documento=[Profissional].[codigo_documento]
                                order by  b.codigo desc) "] = $dados["codigo"];
       }
        if (isset($dados['razao_social']) && !empty($dados["razao_social"])) {           
           $condition[" (select top 1 c.razao_social
                                from [dbBuonny].publico.profissional_log a
                                inner join [dbTeleconsult].[informacoes].[ficha_scorecard] b on a.codigo = b.codigo_profissional_log
                                inner join [dbBuonny].vendas.cliente c on c.codigo = b.codigo_cliente 
                                where a.codigo_documento=[Profissional].[codigo_documento]
                                order by  b.codigo desc) LIKE"] = "%".$dados["razao_social"]."%";
        }
        $condition['LogAtendimento.codigo_produto'] = Produto::SCORECARD;        
        return $condition; 
    }
    
    function converteFiltroEmCondition($dados){
       App::import('Model', 'Produto');
       $condition = array();
       if (isset($dados['data_inicial']) && !empty($dados["data_inicial"]) && isset($dados['data_final']) && !empty($dados["data_final"])) {
            $periodo    = array( AppModel::dateToDbDate($dados["data_inicial"].' 00:00'), AppModel::dateToDbDate($dados["data_final"].' 23:59'));
            $condition  = array('LogAtendimento.data_inicio BETWEEN ? AND ?' => $periodo);
        }
        if (isset($dados['usuario']) && !empty($dados["usuario"])) {
            $condition["Usuario.apelido LIKE"] = $dados["usuario"]."%";
        }
        if (isset($dados['codigo_documento']) && !empty($dados["codigo_documento"])) {
            $condition["Profissional.codigo_documento LIKE"] = str_replace(array('.','/','-',''), '', $dados['codigo_documento']) . '%';
        }
        if (isset($dados['codigo_profissional_tipo']) && !empty($dados["codigo_profissional_tipo"])) {
           if ($dados['codigo_profissional_tipo'] == ProfissionalTipo::CARRETEIRO )
                $condition["LogAtendimento.codigo_profissional_tipo"] = ProfissionalTipo::CARRETEIRO;
          else
            $condition["LogAtendimento.codigo_profissional_tipo <> "] =  ProfissionalTipo::CARRETEIRO;
        }        
        if (isset($dados['codigo_tipo_operacao']) && !empty($dados["codigo_tipo_operacao"])) {
            $condition["LogAtendimento.codigo_tipo_operacao"] = $dados['codigo_tipo_operacao'];
        }
        if (isset($dados['placa']) && !empty($dados["placa"])) {
            $condition["Veiculo.placa"] = str_replace('-','', $dados['placa']);
        }
        if (isset($dados['codigo_cliente']) && !empty($dados["codigo_cliente"])) {
            $condition["FichaScorecard.codigo_cliente"] = $dados["codigo_cliente"];
        }        
        $condition['LogAtendimento.codigo_produto'] = Produto::SCORECARD;        
        return $condition;
    } 
    
    public function paginate_listar_ExclusaoVinculos ($filtros){
       
        $select['joins'] = array(
                    array('table' => 'dbBuonny.publico.profissional_log',
                          'alias' => 'ProfissionalLog',
                          'type' => 'INNER',
                          'conditions' => 'ProfissionalLog.codigo_profissional = LogAtendimento.codigo_profissional'
                     ),
                     array('table' => 'dbTeleconsult.informacoes.ficha_scorecard',
                          'alias' => 'FichaScorecard',
                          'type' => 'INNER',
                          'conditions' => 'ProfissionalLog.codigo = FichaScorecard.codigo_profissional_log'
                     ),
                     array(
                        'table' => 'dbBuonny.vendas.cliente',
                        'alias' => 'VendasCliente',
                        'type' => 'INNER',
                        'conditions' => 'VendasCliente.codigo = FichaScorecard.codigo_cliente'
                    ),
                     array(
                        'table' => 'dbBuonny.publico.profissional',
                        'alias' => 'Profissional',
                        'type' => 'INNER',
                        'conditions' => 'Profissional.codigo = LogAtendimento.codigo_profissional'
                    ),

                    array(
                        'table' => 'dbBuonny.portal.usuario',
                        'alias' => 'Usuario',
                        'type' => 'INNER',
                        'conditions' => 'Usuario.codigo = LogAtendimento.codigo_usuario_inclusao'
                    ),
                    array(
                        'table' => 'dbBuonny.publico.profissional_tipo',
                        'alias' => 'ProfissionalTipo',
                        'type' => 'INNER',
                        'conditions' => 'ProfissionalTipo.codigo = LogAtendimento.codigo_profissional_tipo'
                    ),
                    array(
                        'table' => 'dbBuonny.vendas.produto',
                        'alias' => 'Produto',
                        'type' => 'INNER',
                        'conditions' => 'Produto.codigo = LogAtendimento.codigo_produto'
                    ),
                    
                  );
       
      

        
        
        $select['fields'] = ' VendasCliente.codigo as codigo,
                              VendasCliente.razao_social  as descricao,
                              [Profissional].[codigo_documento],
                              [Profissional].[nome],
                              CONVERT(VARCHAR(20), max(LogAtendimento.data_inicio), 20) as data_exclusao,
                              [Usuario].[apelido],
                              [ProfissionalTipo].[descricao],
                              [Produto].[descricao]                     

                            ';  
        
        return $select ;

    }


    public function paginate_listar ($filtros){
      if (!isset($filtros['placa'])) { 
        
        $select['joins'] = array(
                    array(
                        'table' => 'dbBuonny.portal.usuario',
                        'alias' => 'Usuario',
                        'type' => 'INNER',
                        'conditions' => 'Usuario.codigo = LogAtendimento.codigo_usuario_inclusao'
                    ),
                    array(
                        'table' => 'dbTeleconsult.informacoes.tipo_operacao',
                        'alias' => 'TipoOperacao',
                        'type' => 'INNER',
                        'conditions' => 'TipoOperacao.codigo = LogAtendimento.codigo_tipo_operacao'
                    ),
                    array(
                        'table' => 'dbBuonny.publico.profissional_tipo',
                        'alias' => 'ProfissionalTipo',
                        'type' => 'INNER',
                        'conditions' => 'ProfissionalTipo.codigo = LogAtendimento.codigo_profissional_tipo'
                    ),
                    array(
                        'table' => 'dbBuonny.publico.profissional',
                        'alias' => 'Profissional',
                        'type' => 'INNER',
                        'conditions' => 'Profissional.codigo = LogAtendimento.codigo_profissional'
                    ),
                    array(
                        'table' => 'dbBuonny.vendas.produto',
                        'alias' => 'Produto',
                        'type' => 'INNER',
                        'conditions' => 'Produto.codigo = LogAtendimento.codigo_produto'
                    ),
                    
                  );
       } else {
        $select['joins'] = array(
            array(
                'table' => 'dbBuonny.portal.usuario',
                'alias' => 'Usuario',
                'type' => 'INNER',
                'conditions' => 'Usuario.codigo = LogAtendimento.codigo_usuario_inclusao'
            ),
            array(
                'table' => 'dbTeleconsult.informacoes.tipo_operacao',
                'alias' => 'TipoOperacao',
                'type' => 'INNER',
                'conditions' => 'TipoOperacao.codigo = LogAtendimento.codigo_tipo_operacao'
            ),
            array(
                'table' => 'dbBuonny.publico.profissional_tipo',
                'alias' => 'ProfissionalTipo',
                'type' => 'INNER',
                'conditions' => 'ProfissionalTipo.codigo = LogAtendimento.codigo_profissional_tipo'
            ),
            array(
                'table' => 'dbBuonny.publico.profissional',
                'alias' => 'Profissional',
                'type' => 'INNER',
                'conditions' => 'Profissional.codigo = LogAtendimento.codigo_profissional'
            ),
            array(
                'table' => 'dbBuonny.vendas.produto',
                'alias' => 'Produto',
                'type' => 'INNER',
                'conditions' => 'Produto.codigo = LogAtendimento.codigo_produto'
            ),
            array(
                'table' => 'dbBuonny.publico.veiculo',
                'alias' => 'Veiculo',
                'type' => 'INNER',
                'conditions' => 'Veiculo.codigo = LogAtendimento.codigo_veiculo'
            ),                    
          );
       }        
            $select['fields'] = 'Usuario.apelido,TipoOperacao.descricao,ProfissionalTipo.descricao,
            LogAtendimento.data_inicio,Profissional.codigo_documento,
            Produto.descricao,Veiculo.placa';  
        
        return $select ;

    }

  function gravaLogAtendimentoFichaSCoreCard($ficha, $com_cobranca = false,$tipo_operacao =null) { 
        $this->Ficha = & ClassRegistry::init('Ficha');
        $this->FichaScorecard = & ClassRegistry::init('FichaScorecard');
        $codigo_produto = 134;//$ficha['FichaScorecard']['codigo_produto'];
        if(!empty($ficha['FichaScorecard']['codigo_tipo_operacao'])) {
            $codigo_tipo_operacao = $ficha['FichaScorecard']['codigo_tipo_operacao'];
        } else {
            $codigo_tipo_operacao = $tipo_operacao; //$com_cobranca ? 21 : 67; // 21 => atualização | 67 => 'à ser levantado o que é isto'
        }
        $codigo_profissional =    $ficha['Profissional']['codigo']; 
        if(isset($ficha['Profissional']['codigo_profissional_tipo'])){
           $codigo_profissional_tipo = $ficha['Profissional']['codigo_profissional_tipo']; //$ficha['FichaScorecard']['codigo_profissional_tipo'];
        }else{
           @$codigo_profissional_tipo = $ficha['FichaScorecard']['codigo_profissional_tipo'];
        }
        $data_inicio = date('Y-m-d H:i:s');

        $dados = array('LogAtendimento' => array(
            'codigo_produto' => $codigo_produto,
            'codigo_tipo_operacao' => $codigo_tipo_operacao,
            'codigo_profissional' => $codigo_profissional,
            'codigo_profissional_tipo' => $codigo_profissional_tipo,
            'data_inicio' => $data_inicio,
            'codigo_veiculo' => isset($ficha['FichaScorecardVeiculo']['0']['Veiculo']['codigo']) ? $ficha['FichaScorecardVeiculo']['0']['Veiculo']['codigo']:NULL
       ));
        unset($this->Ficha);
        unset($this->usuario);
        return $this->incluir($dados);
    }

    function gravaLogAtendimentoFicha($ficha, $com_cobranca = false) {
        $this->Ficha = & ClassRegistry::init('Ficha');        
        $codigo_produto = $ficha['FichaScorecard']['codigo_produto'];
        $codigo_tipo_operacao = $com_cobranca ? 21 : 67; // 21 => atualização | 67 => 'à ser levantado o que é isto'
        $codigo_profissional = $this->Ficha->buscaCodigoProfissional($ficha['FichaScorecard']['codigo']);
        $codigo_profissional_tipo = $ficha['FichaScorecard']['codigo_profissional_tipo'];
        $data_inicio = date('Y-m-d H:i:s');

        $dados = array('LogAtendimento' => array('codigo_produto' => $codigo_produto,
                       'codigo_tipo_operacao' => $codigo_tipo_operacao,
                       'codigo_profissional' => $codigo_profissional,
                       'codigo_profissional_tipo' => $codigo_profissional_tipo,
                       'data_inicio' => $data_inicio));

        unset($this->Ficha);
        unset($this->usuario);
        return $this->incluir($dados);
    }
    
    function gravaLogAtendimentoFichaInformacoes($dados_ficha, $codigo_tipo_operacao = 21) {
        $codigo_produto = $dados_ficha['Ficha']['codigo_produto'];
        $codigo_profissional = $dados_ficha['Profissional']['codigo'];
        $codigo_profissional_tipo = $dados_ficha['Ficha']['codigo_profissional_tipo'];
        $data_inicio = date('Y-m-d H:i:s');

        $dados = array('LogAtendimento' => array('codigo_produto' => $codigo_produto,
                       'codigo_tipo_operacao' => $codigo_tipo_operacao,
                       'codigo_profissional' => $codigo_profissional,
                       'codigo_profissional_tipo' => $codigo_profissional_tipo,
                       'data_inicio' => $data_inicio));

        return $this->incluir($dados);
    }

    function gravaLogAtendimentoDuplicarFicha($ficha, $com_cobranca = false) {
        $this->Ficha = & ClassRegistry::init('Ficha');
        $codigo_produto = $ficha['Ficha']['codigo_produto'];
        $codigo_tipo_operacao = $com_cobranca ? 21 : 67; // 21 => atualização | 67 => 'à ser levantado o que é isto'
        $codigo_profissional = $this->Ficha->buscaCodigoProfissional($ficha['Ficha']['codigo']);
        $codigo_profissional_tipo = $ficha['Ficha']['codigo_profissional_tipo'];
        $data_inicio = date('Y-m-d H:i:s');

        $dados = array('LogAtendimento' => array('codigo_produto' => $codigo_produto,
                       'codigo_tipo_operacao' => $codigo_tipo_operacao,
                       'codigo_profissional' => $codigo_profissional,
                       'codigo_profissional_tipo' => $codigo_profissional_tipo,
                       'data_inicio' => $data_inicio));

        unset($this->Ficha);
        unset($this->usuario);
        return $this->incluir($dados);
    }
    
    /**
     * @todo Criar um método genérico para gravar log_atendimento
     */
    function gravaLogAtendimentoLiberacaoProvisoria($ficha, $codigo_tipo_operacao=null) {
        $this->Ficha = & ClassRegistry::init('Ficha');

        $codigo_produto = $ficha['Ficha']['codigo_produto'];
        $codigo_tipo_operacao = empty($codigo_tipo_operacao) ? 124 : $codigo_tipo_operacao;
        $codigo_profissional = $this->Ficha->buscaCodigoProfissional($ficha['Ficha']['codigo']);
        $codigo_profissional_tipo = $ficha['Ficha']['codigo_profissional_tipo'];
        $data_inicio = date('Y-m-d H:i:s');
        
        $dados = array(
            'LogAtendimento' => array(
               'codigo_produto' =>  $codigo_produto,
               'codigo_tipo_operacao' => $codigo_tipo_operacao,
               'codigo_profissional' => $codigo_profissional,
               'codigo_profissional_tipo' => $codigo_profissional_tipo,
               'data_inicio' => $data_inicio
            )
        );
        unset($this->Ficha);
        return $this->incluir($dados);
    }

    function gravaLogAtendimentoAlteraMotorista($ficha, $observacao='') {
        $this->Ficha = & ClassRegistry::init('Ficha');

        $codigo_produto = $ficha['Ficha']['codigo_produto'];
        $codigo_tipo_operacao = 31;
        $codigo_profissional = $this->Ficha->buscaCodigoProfissional($ficha['Ficha']['codigo']);
        $codigo_profissional_tipo = $ficha['Ficha']['codigo_profissional_tipo'];
        $data_inicio = date('Y-m-d H:i:s');

        $dados = array(
            'LogAtendimento' => array(
               'codigo_produto' =>  $codigo_produto,
               'codigo_tipo_operacao' => $codigo_tipo_operacao,
               'codigo_profissional' => $codigo_profissional,
               'codigo_profissional_tipo' => $codigo_profissional_tipo,
               'data_inicio' => $data_inicio,
               'observacao' => $observacao,
            )
        );
        unset($this->Ficha);
        return $this->incluir($dados);
    }
}
