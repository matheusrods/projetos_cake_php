<?php
class RenovacaoAutomatica extends AppModel{
    public $useTable = 'renovacao_automatica';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    public $primaryKey = 'codigo';
    var $actsAs = array('Secure');
  
  
  public function converteFiltroEmCondition( $dados ) {
    App::import('Model', 'Produto');
    $conditions = array();    
    if (isset($dados['codigo_cliente']) && !empty($dados["codigo_cliente"]))
      $conditions["RenovacaoAutomatica.codigo_cliente"] = $dados["codigo_cliente"];
    if (isset($dados['codigo_documento']) && !empty($dados["codigo_documento"]))
      $conditions["Profissional.codigo_documento LIKE "] = '%'.$dados["codigo_documento"].'%';    
    if (isset($dados['codigo_tipo_profissional']) && !empty($dados["codigo_tipo_profissional"])) {
      if ( $dados["codigo_tipo_profissional"] == ProfissionalTipo::CARRETEIRO )
        $conditions["ProfissionalTipo.codigo"] = $dados["codigo_tipo_profissional"];
      else
        $conditions["ProfissionalTipo.codigo <> "] = ProfissionalTipo::CARRETEIRO;
    }
    if (isset($dados['contato']) && !empty($dados["contato"]))
      $conditions["RenovacaoAutomatica.contato LIKE"] = "%".$dados["contato"]."%";    
    if (isset($dados['representante']) && !empty($dados["representante"]))
      $conditions["RenovacaoAutomatica.representante LIKE"] = "%".$dados["representante"]."%";    
    if (isset($dados['codigo_produto']))
      $conditions["RenovacaoAutomatica.codigo_produto"] = $dados["codigo_produto"];
    
    if ( isset( $dados['dias_renovacao'] ) ) {
      $dias_renovacao = !empty($dados['dias_renovacao']) ? $dados['dias_renovacao'] : 0;
      $conditions['RenovacaoAutomatica.data_validade_ficha BETWEEN ? AND ?'] = array( 
        date('Y-m-d 00:00:00'),  
        date('Y-m-d 23:59:59', strtotime("+{$dias_renovacao} days",strtotime(date('Y-m-d'))))        
      );
    }

    if (isset($dados['data_inicial']) && !empty($dados["data_inicial"]) && isset($dados['data_final']) && !empty($dados["data_final"])) {
      $conditions['RenovacaoAutomatica.data_inclusao BETWEEN ? AND ?'] = array( 
        AppModel::dateToDbDate( $dados['data_inicial'].' 00:00:00'),  
        AppModel::dateToDbDate( $dados['data_final'].' 23:59:59')
      );
    }

    if (isset($dados['usuario']) && !empty($dados["usuario"]))
      $conditions["Usuario.nome LIKE"] = "%".$dados["usuario"]."%";

    return $conditions;
  }

  public function listagem($conditions){
       try{
           $fields = array('RenovacaoAutomatica.codigo_cliente,
                            Cliente.razao_social, 
                            Profissional.codigo_documento,
                            Profissional.nome,
                            ProfissionalTipo.descricao as tipo_profissional,
                            RenovacaoAutomatica.contato,
                            RenovacaoAutomatica.representante,
                            RenovacaoAutomatica.renovar,
                            RenovacaoAutomatica.processado,
                            Usuario.apelido');
             $joins = array( 
                                
                                array(
                                    "table"     => "dbBuonny.vendas.cliente",
                                    "alias"     => "Cliente",
                                    "type"      => "INNER",
                                    "conditions"=> array("RenovacaoAutomatica.codigo_cliente = Cliente.codigo")
                                ),
                                array(
                                    "table"     => "dbBuonny.publico.profissional",
                                    "alias"     => "Profissional",
                                    "type"      => "INNER",
                                    "conditions"=> array("RenovacaoAutomatica.codigo_profissional = Profissional.codigo")
                                ),
                                array(
                                    "table"     => "dbBuonny.publico.profissional_tipo",
                                    "alias"     => "ProfissionalTipo",
                                    "type"      => "INNER",
                                    "conditions"=> array("RenovacaoAutomatica.codigo_profissional_tipo = ProfissionalTipo.codigo")
                                ),
                                array(
                                    "table"     => "dbBuonny.portal.usuario",
                                    "alias"     => "Usuario",
                                    "type"      => "INNER",
                                    "conditions"=> array("Usuario.codigo = RenovacaoAutomatica.codigo_usuario_inclusao")
                                ),
                                
                            );
            
            //$return = $this->find('all', array('fields' => $fields,'joins'=>$joins, 'conditions' =>$conditions));
            $return = compact('fields','joins','conditions'); 
            return $return;
      }catch(Exception $e){
            echo "Exceção pega Model : RenovacaoAutomatica (Função : listagem) ->",  $e->getMessage(), "\n";
      }        

  } 
  public function salvarRenovacoesAutomaticas( $options = array() ){
        App::import('Model', 'Produto');
        App::import('Model', 'Usuario');
        $this->FichaScorecard = ClassRegistry::init('FichaScorecard');
        $this->Usuario        = ClassRegistry::init('Usuario');

        $options = isset($options['RenovacaoAutomatica']) ? $options['RenovacaoAutomatica'] : array();

        $codigo_cliente     = isset($options['codigo_cliente'])  ? $options['codigo_cliente']  : null;
        $codigo_nao_renovar = isset($options['excluir_vinculo']) ? $options['excluir_vinculo'] : null;
        $dias_renovacao     = isset($options['dias_renovacao'])  ? $options['dias_renovacao']  : 7;
        $interno            = isset($options['interno'])         ? $options['interno']         : true;
        $usuario_inclusao   = isset($_SESSION['Auth']['Usuario']['codigo']) ? 
                                    $_SESSION['Auth']['Usuario']['codigo'] : 
                                    Usuario::RENOVACAO_AUTOMATICA;
                
        $data_inicial = date('Y-m-d 00:00:00');
        $data_final   = date('Y-m-d 23:59:59', strtotime("+".$dias_renovacao." days",strtotime(date('Y-m-d'))));
        
     //   $email = 'dbTeleconsult.informacoes.ufn_obter_contato_cliente(ProfissionalLog.codigo_profissional, FichaScoreCard.codigo_cliente, 0)';
     //   $contato = 'dbTeleconsult.informacoes.ufn_obter_contato_cliente(ProfissionalLog.codigo_profissional, FichaScoreCard.codigo_cliente, 1)';

        $email = '\'\'';
        $contato = '\'\'';
        
        $insertQuery = "INSERT INTO
            ".$this->databaseTable.".".$this->tableSchema.".".$this->useTable."
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
                codigo_produto,
                codigo_usuario_inclusao,
                data_inclusao
            )";

        $codigos = (empty($codigo_nao_renovar)) ? 0 : implode(', ', $codigo_nao_renovar);

        $condicoes = array(
                'fields' => array(
                    'FichaScorecard.codigo_cliente AS codigo_cliente',
                    'ProfissionalLog.codigo_profissional AS codigo_profissional',
                    'FichaScorecard.codigo_profissional_tipo AS codigo_profissional_tipo',
                    "case
                        when max(FichaScorecard.data_alteracao) is null
                        then convert(varchar, max(FichaScorecard.data_inclusao) , 20)
                        else convert(varchar, max(FichaScorecard.data_alteracao) , 20)
                    end AS data_atualizacao_ficha",
                    'convert(varchar, FichaScorecard.data_validade, 20) AS data_validade_ficha',
                    "{$email} AS contato",
                    "{$contato} AS representante",
                    "case when ProfissionalLog.codigo_profissional in ({$codigos}) then 0 else 1 end AS renovar",
                    '0 as processado', 
                    Produto::SCORECARD. " as codigo_produto",
                    $usuario_inclusao. " as codigo_usuario_inclusao",
                    "'".date('Y-m-d H:i:s'). "' as data_inclusao"
                ),
                'group' => array(
                    'FichaScorecard.codigo_cliente',
                    'ProfissionalLog.codigo_profissional',
                    'FichaScorecard.codigo_profissional_tipo',
                    'FichaScorecard.data_validade',
                    'FichaScorecard.codigo_produto'
                ),
                'returnSQL' => true
           );
        
        $options = array(
          'codigo_cliente' => $codigo_cliente,
          'dias_renovacao' => $dias_renovacao,
          'interno'        => $interno,
          'condicoes'      => $condicoes
        );
        $resultados = $this->FichaScorecard->listarFichasARenovar( $options );        
        $cte_qtd    = $this->query("WITH qtdProfissional AS ( $resultados ) SELECT count(*) as qtde FROM qtdProfissional");
        if( $cte_qtd[0][0]['qtde'] > 0 ){          
          return $this->query($insertQuery.$resultados);
        }else
          return false;
    }

    function verificaRenovacaoMes( $codigo_cliente, $codigo_produto ){      
      $data_inicial  = date('Y-m-d 00:00:00', strtotime('first day next month'));
      $data_final    = date('Y-m-d 23:59:59', strtotime('last day next month'));
      return $this->find('count', array('conditions'=>array(
        'data_validade_ficha BETWEEN ? AND ? ' => array($data_inicial, $data_final), 
        'codigo_cliente'  => $codigo_cliente,
        'codigo_produto'  => $codigo_produto
      )));
    }


    function listaProfissionaisRenovar(){
      App::import('Model', 'Produto');
      $this->ClienteProdutoServico2 = ClassRegistry::init('ClienteProdutoServico2');
      $this->ClienteProduto         = ClassRegistry::init('ClienteProduto');
      $this->Cliente                = ClassRegistry::init('Cliente');

      $this->Cliente->unbindAll();
      $this->ClienteProduto->unbindAll();

      $fields = array(
                  'codigo',
                  'codigo_cliente',
                  'codigo_profissional',
                  'codigo_profissional_tipo',
                  'contato',
                  'representante',
                  'data_validade_ficha',
                  'codigo_produto'
                );      
      $subsubquery = $this->ClienteProduto->find('sql',
                        array(
                            'fields'     => array('ClienteProduto.codigo'),
                            'joins'      => array(
                                array(
                                  'table'     => $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
                                  'alias'     => 'Cliente',
                                  'conditions'=> 'Cliente.codigo = ClienteProduto.codigo_cliente'
                                )
                            ),
                            'conditions' => array(
                                'ClienteProduto.codigo_cliente = RenovacaoAutomatica.codigo_cliente',
                                'ClienteProduto.codigo_produto'         => Produto::SCORECARD,
                                'ClienteProduto.codigo_motivo_bloqueio' => 1,
                                'Cliente.ativo'                         => 1
                              )
                        )
                      );
      $subquery    = $this->ClienteProdutoServico2->find('sql', 
                        array(
                          'fields'     => 'ClienteProdutoServico2.codigo',
                          'conditions' => array(
                              'ClienteProdutoServico2.codigo_cliente_produto in ('.$subsubquery.')',
                              'ClienteProdutoServico2.codigo_servico' => 4
                            ),
                          'limit'      => 1
                        )
                      );
      $condicoes = array(
                  'RenovacaoAutomatica.processado'             => 0,
                  'RenovacaoAutomatica.renovar'                => 1,
                  'RenovacaoAutomatica.codigo_produto'         => Produto::SCORECARD,
                  //'data_inclusao between ? and ?' => array($data_inicial, $data_final)
                  //'RenovacaoAutomatica.data_validade_ficha >=' => date('Ymd 00:00:00'),
                  'exists ('.$subquery.')'
                );
      $resultado = $this->find('all', array(
              'fields'     => $fields,
              'conditions' => $condicoes
            )
          );      
      return $resultado;
    }
}
?>
