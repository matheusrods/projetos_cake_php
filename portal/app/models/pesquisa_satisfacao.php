<?php
App::import('Model', 'Produto');
class PesquisaSatisfacao extends AppModel {
  var $name          = 'PesquisaSatisfacao';
  var $tableSchema   = 'vendas';
  var $databaseTable = 'dbBuonny';
  var $useTable      = 'pesquisas_satisfacao';
  var $primaryKey    = 'codigo';
  var $actsAs        = array('Secure');

  CONST ALERTA_PESQUISA_SATISFACAO_RECUSADA = 54;
  CONST AGRP_STATUS_PESQUISA_SATISFEITO = 1;
  CONST AGRP_STATUS_PESQUISA_PARCIALMENTE_SATISFEITO = 2;
  CONST AGRP_STATUS_PESQUISA_INSATISFEITO = 3;
  CONST AGRP_STATUS_PESQUISA_REAGENDAMENTO = 4;
 
  function converteFiltrosEmConditions( $filtros ){
    $conditions = array();
    if( !empty($filtros['codigo_usuario_pesquisa']) ){
      if( $filtros['codigo_usuario_pesquisa'] > 0){
        $conditions['PesquisaSatisfacao.codigo_usuario_pesquisa'] = $filtros['codigo_usuario_pesquisa'];
      } else{
        $conditions['PesquisaSatisfacao.codigo_usuario_pesquisa'] = NULL;
        $conditions['PesquisaSatisfacao.codigo_status_pesquisa']  = NULL; 
      }
    }

    if(!empty($filtros['status_pesquisa'])){
      if($filtros['status_pesquisa'] == 1){
        $conditions['PesquisaSatisfacao.data_pesquisa'] = NULL; 
      }elseif($filtros['status_pesquisa'] == 2){
        $conditions['PesquisaSatisfacao.data_pesquisa <>'] = NULL; 
      }
    }
    if( !empty($filtros['codigo_status_pesquisa']) ){
      if($filtros['codigo_status_pesquisa'] == 0) {
        $conditions['PesquisaSatisfacao.codigo_status_pesquisa'] = NULL;
      } elseif($filtros['codigo_status_pesquisa'] >= 1 && $filtros['codigo_status_pesquisa'] <= 4){
        $conditions['PesquisaSatisfacao.codigo_status_pesquisa'] = $filtros['codigo_status_pesquisa'];
       //$conditions['ClienteProdutoLog.codigo_motivo_bloqueio'] = null;
      }
    }   

    if((!empty($filtros['data_inicial']) && !empty($filtros['data_final']))){
      $conditions['PesquisaSatisfacao.data_para_pesquisa BETWEEN ? AND ?'] = array(AppModel::dateToDbDate2($filtros['data_inicial'].' 00:00:00'), AppModel::dateToDbDate2($filtros['data_final'].' 23:59:59')); 
    }
    
    if(!empty($filtros['codigo_produto'])){
      $conditions['PesquisaSatisfacao.codigo_produto'] = $filtros['codigo_produto'];
    }
    if(!empty($filtros['codigo_pesquisa'])){
      $conditions['PesquisaSatisfacao.codigo'] = $filtros['codigo_pesquisa'];
    }
    if(!empty($filtros['codigo_pai'])){
      $conditions['PesquisaSatisfacao.codigo_pai'] = $filtros['codigo_pai'];
    }    
    if(!empty($filtros['codigo_cliente'])){
      $conditions['PesquisaSatisfacao.codigo_cliente'] = $filtros['codigo_cliente'];
    }
    if(!empty($filtros['codigo_gestor'])){
      $conditions['Cliente.codigo_gestor'] = $filtros['codigo_gestor'];
    }
    if(!empty($filtros['codigo_gestor_npe'])){
      $conditions['Cliente.codigo_gestor_npe'] = $filtros['codigo_gestor_npe'];
    }

    return $conditions;
  }

  function converteFiltrosEmConditionsCte($filtros) {
    $conditions = array();

    if( !empty($filtros['codigo_status_pesquisa']) ){
      if($filtros['codigo_status_pesquisa'] == 0) {
        $conditions['PesquisaSatisfacao.codigo_status_pesquisa'] = NULL;
      } elseif($filtros['codigo_status_pesquisa'] >= 1 && $filtros['codigo_status_pesquisa'] <= 4){
        $conditions['PesquisaSatisfacao.codigo_status_pesquisa'] = $filtros['codigo_status_pesquisa'];
      } elseif($filtros['codigo_status_pesquisa'] == 5 ){
        $conditions['ClienteProdutoLog.codigo_motivo_bloqueio'] = 17;
      } elseif($filtros['codigo_status_pesquisa'] == 6) {
        $conditions['ClienteProdutoLog.codigo_motivo_bloqueio'] = 8;
      } elseif($filtros['codigo_status_pesquisa'] == 7){
        $conditions['ClienteProdutoLog.codigo_motivo_bloqueio'] = null;
      }
    }   

    if(!empty($filtros['ano'])) {
      $conditions['PesquisaSatisfacao.ano'] = $filtros['ano']; 
    }

    if(!empty($filtros['codigo_produto'])){
      $conditions['ClienteProdutoLog.codigo_produto'] = $filtros['codigo_produto'];
    }
    if(!empty($filtros['codigo_cliente'])){
      $conditions['ClienteProdutoLog.codigo_cliente'] = $filtros['codigo_cliente'];
    }
    return $conditions;

  }

  function carregar_cliente_pesquisa_satisfacao( $options ){
    // $this->carregar_para_pesquisa( $options, 1 );//Retirado TELECONSULT da PESQUISA DE SATISFACAO
    $this->carregar_para_pesquisa( $options, 82 );
  }  

  private function carregar_para_pesquisa( $options, $codigo_produto ){ 
    if( !in_array( $codigo_produto, array( 1, 82 )) || empty( $options['codigo_usuario_inclusao'] ) )
      return false;
    $this->Notaite  =& ClassRegistry::init('Notaite');
    $mes  = date('m', strtotime('-1 month', strtotime( date("Ymd") )));
    $ano  = date('Y', strtotime('-1 month', strtotime( date("Ymd") )));
    $dtemissao = comum::periodoMensal( "$ano-$mes" );
    $data_cadastro = comum::periodoMensal( date("Y-m") );
    $bindOptions = array(
      'belongsTo' => array(
        'Notafis' => array(
          'foreignKey' => false, 
          'conditions' => array(
            'Notafis.numero = Notaite.nnotafis',
            'Notafis.empresa = Notaite.empresa',
            'Notafis.seq = Notaite.seq',
            'Notafis.serie = Notaite.serie',
            "Notafis.cancela = 'N'"
          )
        ),
        'Cliente'         => array('foreignKey' => 'cliente'),
        'ClienteProduto'  => array('foreignKey' => FALSE, 'conditions'=> array('ClienteProduto.codigo_cliente = Cliente.codigo')),
      )
    );      
    if ($this->useDbConfig != 'test_suite') {
      $bindOptions['hasOne']['NProduto'] = array( 
        'foreignKey' => false, 
        'conditions' => 'Notaite.produto = NProduto.codigo'
      );
    } else {
      $bindOptions['hasOne']['NProduto'] = array(
        'className'  => 'NProdutoTest', 
        'foreignKey' => false, 
        'conditions' => 'Notaite.produto = NProduto.codigo'
      );
    }
    $this->Notaite->bindModel( $bindOptions );       
    $sub_query  = $this->find('sql', array(
        'conditions' => array(//pegando data atual -> data de cadastro da pesquisa
          'PesquisaSatisfacao.codigo_cliente = Cliente.codigo AND PesquisaSatisfacao.data_pesquisa IS NULL',
          'PesquisaSatisfacao.data_cadastro BETWEEN ? AND ?' => array( $data_cadastro['inicio'], $data_cadastro['fim'] ),
          'PesquisaSatisfacao.codigo_produto' => $codigo_produto
        ), 
        'fields' =>'PesquisaSatisfacao.codigo_cliente as codigo_cliente'
    ));

    $subquery = $this->find('sql', array(
        'conditions' => array(
          'PesquisaSatisfacao.data_cadastro BETWEEN ? AND ?' => array( $data_cadastro['inicio'], $data_cadastro['fim'] ),
          'PesquisaSatisfacao.codigo_produto' => $codigo_produto
        ), 
        'fields' =>'CASE WHEN count(PesquisaSatisfacao.codigo) > 0 THEN 1 ELSE 0 END as qtde'
    ));

    $conditions = array(//Pegando o mes anterior -> mes de faturamento
      'Notafis.dtemissao BETWEEN ? AND ?' => array( $dtemissao['inicio'], $dtemissao['fim'] ),
      'ClienteProduto.codigo_produto' => $codigo_produto,
      'ClienteProduto.codigo_motivo_bloqueio' => 1,      
      "Cliente.codigo NOT IN ( {$sub_query} )",
      "({$subquery})" => 0
    );
    if( $codigo_produto == 1 ){//TLC
      array_push( $conditions, array( 
        'NProduto.grupo'      =>  '050',
        'NProduto.subgrupo'   =>  array('01', '02'),
        'NProduto.referencia LIKE ' => 'TELECONSULT%'
        ) 
      );
    } else {//BuonnySat
      array_push( $conditions, array( 
        'NProduto.grupo'      =>  '250',
        'NProduto.subgrupo'   =>  array('01', '03', '09'),          
        'NProduto.referencia LIKE ' => 'BUONNY%SAT%'
        ) 
      );
    }
    $limit = 100;
    $codigo_usuario_inclusao = $options['codigo_usuario_inclusao'];
    $fields  = array( 
        "NULL AS codigo_pai", "getdate() as data_cadastro", "CONVERT(datetime, convert(varchar,CURRENT_TIMESTAMP,103),103) as data_para_pesquisa",  
        "$codigo_produto as codigo_servico", 
        "Cliente.codigo as codigo_cliente", "ROW_NUMBER() OVER(ORDER BY SUM(Notaite.preco) DESC) AS ranking",  
        "NULL AS codigo_status_pesquisa", "NULL AS data_pesquisa", "NULL AS observacao", 
        "$mes AS mes_referencia", "$ano AS ano_referencia", "NULL AS usuario_pesquisa","$codigo_usuario_inclusao as codigo_usuario_inclusao"            
    );
    $group = array('Cliente.codigo');
    $cliente_carregados = $this->Notaite->find( 'sql', compact('fields', 'conditions', 'limit', 'group') );
    $query = "INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable}";
    $query.= "(codigo_pai, data_cadastro, data_para_pesquisa, codigo_produto, codigo_cliente, ranking, ";
    $query.= "codigo_status_pesquisa, data_pesquisa, observacao, mes_referencia, ano_referencia, usuario_pesquisa, codigo_usuario_inclusao) ";
    $query.= "{$cliente_carregados};";
    // debug($cliente_carregados);
    return $this->query( $query );
  }

  public function atualizar( $dados ){
      $dados['PesquisaSatisfacao']['data_pesquisa'] = date("d/m/Y H:i:s");
      $this->query('BEGIN TRANSACTION');
      parent::atualizar( $dados );
      if( !$this->insereProximaPesquisa( $dados ) ){
          $this->rollback();
          return false;
      }
      $this->commit();
      return true;
  }
  
  public function diasParaNovaPesquisa( $status_pesquisa ){
      $qtde_dias = ($status_pesquisa == 2 ? 15 : 7);
      if( $status_pesquisa > 1 )
          return date("d/m/Y H:i:s", strtotime("+$qtde_dias days", strtotime( date("Ymd") )));
  }

  public function insereProximaPesquisa( $dados ){
      if( $dados['PesquisaSatisfacao']['codigo_status_pesquisa'] > 1 ){
        $pesquisa_pai = $this->carregar( $dados['PesquisaSatisfacao']['codigo'] );  
        $pesquisa_pai['PesquisaSatisfacao']['codigo_pai']         = $dados['PesquisaSatisfacao']['codigo'];
        unset( $pesquisa_pai['PesquisaSatisfacao']['codigo'] );
        $pesquisa_pai['PesquisaSatisfacao']['data_cadastro']      = date("d/m/Y H:i:s");
        $pesquisa_pai['PesquisaSatisfacao']['codigo_usuario_inclusao'] = NULL;
        $pesquisa_pai['PesquisaSatisfacao']['usuario_pesquisa']        = NULL;
        $pesquisa_pai['PesquisaSatisfacao']['codigo_usuario_pesquisa'] = NULL;
        $pesquisa_pai['PesquisaSatisfacao']['data_pesquisa']           = NULL;
        $pesquisa_pai['PesquisaSatisfacao']['observacao']              = NULL;
        $pesquisa_pai['PesquisaSatisfacao']['codigo_status_pesquisa']  = NULL;
        $pesquisa_pai['PesquisaSatisfacao']['codigo_cliente_contato']  = NULL;
        if( isset($dados['PesquisaSatisfacao']['data_reagendamento']) && isset($dados['PesquisaSatisfacao']['hora_reagendamento']) )
          $pesquisa_pai['PesquisaSatisfacao']['data_para_pesquisa'] = $dados['PesquisaSatisfacao']['data_reagendamento'].' '.$dados['PesquisaSatisfacao']['hora_reagendamento'];
        else
          $pesquisa_pai['PesquisaSatisfacao']['data_para_pesquisa'] = $this->diasParaNovaPesquisa( $dados['PesquisaSatisfacao']['codigo_status_pesquisa'] );
        if( !$this->incluir( $pesquisa_pai ) )
          return false;
      }
      return true;
  }

  public function carregarPesquisa( $codigo_pesquisa ){
    $fields = array(
      'PesquisaSatisfacao.codigo',
      'PesquisaSatisfacao.data_cadastro',
      'PesquisaSatisfacao.data_para_pesquisa',      
      'PesquisaSatisfacao.data_pesquisa',
      'PesquisaSatisfacao.codigo_produto',
      'PesquisaSatisfacao.codigo_cliente',
      'PesquisaSatisfacao.ranking',
      'PesquisaSatisfacao.codigo_status_pesquisa',
      'PesquisaSatisfacao.observacao',      
      'StatusPesquisaSatisfacao.descricao_pesquisa',
      'Cliente.razao_social',
      'Usuario.apelido',
      'ClienteContato.ddd',
      'ClienteContato.descricao',
      'ClienteContato.nome',    
    ); 
    $this->bindModel(
      array(
        'hasOne' => array(
          'Cliente' => array(
            'foreignKey' => false,
            'conditions' => array("Cliente.codigo = PesquisaSatisfacao.codigo_cliente"),
            'type' => 'INNER'
          ),
          'StatusPesquisaSatisfacao' => array(
            'foreignKey' => false,
            'conditions' => array("StatusPesquisaSatisfacao.codigo = PesquisaSatisfacao.codigo_status_pesquisa"),
            'type' => 'LEFT'
          ),
          'Usuario' => array(
            'foreignKey' => false,
            'conditions' => array("Usuario.codigo = PesquisaSatisfacao.codigo_usuario_pesquisa"),
            'type' => 'LEFT'
          ),
          'ClienteContato' => array(
            'foreignKey' => false,
            'conditions' => array("ClienteContato.codigo = PesquisaSatisfacao.codigo_cliente_contato"),
            'type' => 'LEFT'
          ),
        ),
    ));
    $conditions = $this->converteFiltrosEmConditions( array( 'codigo_pesquisa' => $codigo_pesquisa ) );    
    $consulta   = $this->find('all', array('conditions' => $conditions,'fields'=> $fields, 'order' => 'PesquisaSatisfacao.data_pesquisa DESC'));
    return $consulta;
  }

  public function atualizar_observacao( $dados ){
    return parent::atualizar( $dados );
  }

  public function listaAgrupamento(){
    return array( 1=>'Produto', 2=>'Operador', 3=>'Status da Pesquisa');
  }

  public function listagem_pesquisas_sintetica( $options ){
    $conditions  = $options['conditions'];
    $agrupamento = isset($options['agrupamento']) ? $options['agrupamento'] : 1;
    switch ($agrupamento) {
      case 1:
        $fields = array(
          "CASE WHEN PesquisaSatisfacao.codigo_produto = 1 THEN 'Teleconsult' ELSE 'Buonnysat' END AS nome", 
          "PesquisaSatisfacao.codigo_produto AS codigo",
          "count(*) AS total"
          );
        $group  = array('PesquisaSatisfacao.codigo_produto' );
        break;
      case 2:
        $fields = array("Usuario.apelido AS nome", 'Usuario.codigo AS codigo', 'count(*) AS total' );
        $group  = array('Usuario.apelido', 'Usuario.codigo' );
        break;
      case 3:
        $fields = array(
          "CASE WHEN StatusPesquisaSatisfacao.descricao_pesquisa IS NULL THEN 'Não Realizada' ELSE StatusPesquisaSatisfacao.descricao_pesquisa END AS nome", 
          "StatusPesquisaSatisfacao.codigo AS codigo", 
          "count(*) AS total"
          );
        $group  = array('StatusPesquisaSatisfacao.descricao_pesquisa', 'StatusPesquisaSatisfacao.codigo' );
        break;      
    }
    $conditions2 = array();    
    foreach ($conditions as $campo => $valor ) {
      $campo = str_replace('PesquisaSatisfacao.', '', $campo);
      if( $campo != 'codigo_status_pesquisa')
      $conditions2[$campo] = $valor;
    }
    $conditions = $conditions2;
    $dbo = $this->getDataSource();
    $pesquisa_filho = $dbo->buildStatement(
      array(
        'fields' => array('*'),
        'table' => $this->databaseTable.'.'.$this->tableSchema.'.'.$this->useTable,
        'alias' => 'PSF',
        'conditions' => $conditions,
      ), $this
    );
    $this->Cliente = ClassRegistry::init('Cliente');
    $this->Usuario = ClassRegistry::init('Usuario');
    $this->StatusPesquisaSatisfacao = ClassRegistry::init('StatusPesquisaSatisfacao');
    $joins = array(
        array(
          "table" => $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
          "alias" => "Cliente",
          "type"  => "INNER",
          "conditions" => array("Cliente.codigo = PesquisaSatisfacao.codigo_cliente")
        ),
        array(
          "table" => $this->StatusPesquisaSatisfacao->databaseTable.'.'.$this->StatusPesquisaSatisfacao->tableSchema.'.'.$this->StatusPesquisaSatisfacao->useTable,
          "alias" => "StatusPesquisaSatisfacao",
          "type"  => "LEFT",
          'conditions' => array("StatusPesquisaSatisfacao.codigo = PesquisaSatisfacao.codigo_status_pesquisa"),
        ),
        array(
          "table" => $this->Usuario->databaseTable.'.'.$this->Usuario->tableSchema.'.'.$this->Usuario->useTable,
          "alias" => "Usuario",
          "type"  => "LEFT",
          'conditions' => array("Usuario.codigo = PesquisaSatisfacao.codigo_usuario_pesquisa"),
        ),        
        array(
          'table' => "({$pesquisa_filho})",
          'alias' => 'PesquisaSatisfacaoFilho',
          'type' => 'LEFT',
          'conditions' => array(
            'PesquisaSatisfacaoFilho.codigo_pai = PesquisaSatisfacao.codigo'
          ),
        ),
    );
    $conditions = $options['conditions'];
    $conditions['PesquisaSatisfacaoFilho.codigo'] = NULL;
    $result = $this->find('all', compact('conditions', 'fields', 'group', 'joins'));
    return $result;
  }

  public function paginate( $conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array() ) {
        if( isset( $extra['method'] ) && $extra['method'] == 'pesquisa_anual' ){
            return $this->pesquisa_anual(compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive' ,'extra' ));
        }
        $joins = null;
        if (isset($extra['joins']))
            $joins = $extra['joins'];
        if (isset($extra['group']))
            $group = $extra['group'];
        return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
    }

    public function paginateCount( $conditions = null, $recursive = 0, $extra = array() ) {
        if( isset( $extra['method'] ) && $extra['method'] == 'pesquisa_anual' ){
            return $this->pesquisa_anual_count(compact('conditions', 'extra'), 'count' );
        }
        $joins = null;
        if (isset($extra['joins']))
            $joins = $extra['joins'];   
        return $this->find('count', compact('conditions', 'recursive', 'joins'));
    }



    public function gerar_sql_pesquisa_anual($options = array()) {
      $this->ClienteProdutoLog    = ClassRegistry::init('ClienteProdutoLog');
      $this->MotivoBloqueio    = ClassRegistry::init('MotivoBloqueio');
      $conditions_cte = array();
      $ano = (isset($options['conditions']['PesquisaSatisfacao.ano']) ? $options['conditions']['PesquisaSatisfacao.ano'] : date('Y'));
      if(!empty($options['conditions']['ClienteProdutoLog.codigo_produto'])) {
        $conditions_cte['ClienteProdutoLog.codigo_produto'] = $options['conditions']['ClienteProdutoLog.codigo_produto']; 
      }
      if(!empty($options['conditions']['ClienteProdutoLog.codigo_cliente'])) {
        $conditions_cte['ClienteProdutoLog.codigo_cliente'] = $options['conditions']['ClienteProdutoLog.codigo_cliente']; 
      }
      unset($options['conditions']['PesquisaSatisfacao.ano']);

      $sub_query_pesquisa = "(SELECT MAX(pesquisas_satisfacao.codigo) 
                            FROM {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} 
                            WHERE pesquisas_satisfacao.codigo_cliente = ClienteProdutoLog.codigo_cliente 
                                AND pesquisas_satisfacao.codigo_produto = ClienteProdutoLog.codigo_produto
                                AND %s = (
                                CASE 
                                    WHEN data_pesquisa IS NULL 
                                      THEN YEAR(data_para_pesquisa) 
                                    ELSE YEAR(data_pesquisa)
                                END ) 
                                AND %s = (
                                CASE 
                                    WHEN data_pesquisa IS NULL 
                                      THEN MONTH(data_para_pesquisa) 
                                    ELSE MONTH(data_pesquisa)
                                END )) AS %s";

      $sub_query_bloqueio = "(SELECT MAX(ClienteProdutoLog.codigo)        
                              FROM {$this->MotivoBloqueio->databaseTable}.{$this->MotivoBloqueio->tableSchema}.{$this->MotivoBloqueio->useTable}
                            WHERE MAX(ClienteProdutoLog.codigo_motivo_bloqueio) = motivo_bloqueio.codigo
                                  AND MAX(ClienteProdutoLog.data_inclusao) BETWEEN '%s%s01 00:00:00' 
                                  AND DATEADD(mm, DATEDIFF(m,0,'%s%s01 00:00:00')+1,0) ) AS %s_bloqueio";

      $fields_base = array('ClienteProdutoLog.codigo_cliente AS codigo_cliente',
                           'ClienteProdutoLog.codigo_produto AS codigo_produto',
                           sprintf($sub_query_pesquisa, $ano, '01', 'janeiro'),
                           sprintf($sub_query_pesquisa, $ano, '02', 'fevereiro'),
                           sprintf($sub_query_pesquisa, $ano, '03', 'marco'),
                           sprintf($sub_query_pesquisa, $ano, '04', 'abril'),
                           sprintf($sub_query_pesquisa, $ano, '05', 'maio'),
                           sprintf($sub_query_pesquisa, $ano, '06', 'junho'),
                           sprintf($sub_query_pesquisa, $ano, '07', 'julho'),
                           sprintf($sub_query_pesquisa, $ano, '08', 'agosto'),
                           sprintf($sub_query_pesquisa, $ano, '09', 'setembro'),
                           sprintf($sub_query_pesquisa, $ano, '10', 'outubro'),
                           sprintf($sub_query_pesquisa, $ano, '11', 'novembro'),
                           sprintf($sub_query_pesquisa, $ano, '12', 'dezembro'),
                           sprintf($sub_query_bloqueio, $ano, '01', $ano, '01', 'janeiro'),
                           sprintf($sub_query_bloqueio, $ano, '02', $ano, '02', 'fevereiro'),
                           sprintf($sub_query_bloqueio, $ano, '03', $ano, '03', 'marco'),
                           sprintf($sub_query_bloqueio, $ano, '04', $ano, '04', 'abril'),
                           sprintf($sub_query_bloqueio, $ano, '05', $ano, '05', 'maio'),
                           sprintf($sub_query_bloqueio, $ano, '06', $ano, '06', 'junho'),
                           sprintf($sub_query_bloqueio, $ano, '07', $ano, '07', 'julho'),
                           sprintf($sub_query_bloqueio, $ano, '08', $ano, '08', 'agosto'),
                           sprintf($sub_query_bloqueio, $ano, '09', $ano, '09', 'setembro'),
                           sprintf($sub_query_bloqueio, $ano, '10', $ano, '10', 'outubro'),
                           sprintf($sub_query_bloqueio, $ano, '11', $ano, '11', 'novembro'),
                           sprintf($sub_query_bloqueio, $ano, '12', $ano, '12', 'dezembro'),
                 );

        $group_base= array('ClienteProdutoLog.codigo_cliente',
                          'ClienteProdutoLog.codigo_produto');
        //debug($this->ClienteProdutoLog->find('sql',array('fields'=> $fields_base ,'conditions' => $conditions_cte, 'group'=> $group_base )));
        return $this->ClienteProdutoLog->find('sql',array('fields'=> $fields_base ,'conditions' => $conditions_cte, 'group'=> $group_base ));
    }


    public function conditions_cte($options) {
        $options['conditions']['PesquisaSatisfacao.codigo_status_pesquisa'] = (isset($options['conditions']['PesquisaSatisfacao.codigo_status_pesquisa']) ? $options['conditions']['PesquisaSatisfacao.codigo_status_pesquisa'] : NULL);
        $conditions = array();
        $conditions += array(
                          array("
                            NOT (janeiro IS NULL 
                            AND fevereiro IS NULL
                            AND marco IS NULL
                            AND abril IS NULL
                            AND maio IS NULL                    
                            AND junho IS NULL
                            AND julho IS NULL
                            AND agosto IS NULL
                            AND setembro IS NULL
                            AND outubro IS NULL
                            AND novembro IS NULL
                            AND dezembro IS NULL)"
                          ),
                        ); 
        if(isset($options['conditions']['ClienteProdutoLog.codigo_motivo_bloqueio'])) {
          $condition_produtos = array(
                              array(
                                "NOT (janeiro_bloqueio IS NULL 
                                AND fevereiro_bloqueio IS NULL 
                                AND marco_bloqueio IS NULL 
                                AND abril_bloqueio IS NULL 
                                AND maio_bloqueio IS NULL 
                                AND junho_bloqueio IS NULL 
                                AND julho_bloqueio IS NULL 
                                AND agosto_bloqueio IS NULL 
                                AND setembro_bloqueio IS NULL 
                                AND outubro_bloqueio IS NULL 
                                AND novembro_bloqueio IS NULL 
                                AND dezembro_bloqueio IS NULL)"
                              )
                            );
          $conditions = array_merge($conditions, $condition_produtos);
        }
        if(($options['conditions']['PesquisaSatisfacao.codigo_status_pesquisa'] >= 1 && 
            $options['conditions']['PesquisaSatisfacao.codigo_status_pesquisa'] <= 4) || 
            $options['conditions']['PesquisaSatisfacao.codigo_status_pesquisa'] == 7) {
            $status = ($options['conditions']['PesquisaSatisfacao.codigo_status_pesquisa'] == 7 ? NULL :  $options['conditions']['PesquisaSatisfacao.codigo_status_pesquisa'] );
            $condicao_status_pesquisa = array(
                  array('OR' => array('janeiro.codigo_status_pesquisa' => $status,
                        'fevereiro.codigo_status_pesquisa' => $status,
                        'marco.codigo_status_pesquisa' => $status,
                        'abril.codigo_status_pesquisa' => $status,
                        'maio.codigo_status_pesquisa' => $status,
                        'junho.codigo_status_pesquisa' => $status,
                        'julho.codigo_status_pesquisa' => $status,
                        'agosto.codigo_status_pesquisa' => $status,
                        'setembro.codigo_status_pesquisa' => $status,
                        'outubro.codigo_status_pesquisa' => $status,
                        'novembro.codigo_status_pesquisa' => $status,
                        'dezembro.codigo_status_pesquisa' => $status)
                        ),
                  array("(  janeiro_bloqueio IS NULL 
                          AND fevereiro_bloqueio IS NULL 
                          AND marco_bloqueio IS NULL 
                          AND abril_bloqueio IS NULL 
                          AND maio_bloqueio IS NULL 
                          AND junho_bloqueio IS NULL 
                          AND julho_bloqueio IS NULL 
                          AND agosto_bloqueio IS NULL 
                          AND setembro_bloqueio IS NULL 
                          AND outubro_bloqueio IS NULL 
                          AND novembro_bloqueio IS NULL 
                          AND dezembro_bloqueio IS NULL)")
                      );
          $conditions = array_merge($conditions, $condicao_status_pesquisa);
        }
        return $conditions;
      }


    public function pesquisa_anual($options) {
      $conditions = $this->conditions_cte($options);
      $cte_query = $this->gerar_sql_pesquisa_anual($options);
        
        //$offset = (empty($options['page']) ? array() : $options['limit'] * ($options['page'] -1));
        $dbo = $this->getDataSource();
        $query = $dbo->buildStatement(
          array(
            'table' => "({$cte_query})",
            'alias' => 'base',
            'joins' => $options['extra']['joins'],
            'fields' => $options['fields'],
            'conditions' => $conditions,
            'order' => array('razao_social', 'codigo_produto'),
            'limit' => NULL,//$options['limit'],
            //'offset' => $offset,
            'group' => null
          )
        , $this);
        //debug($query);
        return $this->query($query);
    }

    public function pesquisa_anual_count($options) {
      $fields = array(
        'count(0) AS "count"',
      );

      $conditions = $this->conditions_cte($options);
      $cte_query = $this->gerar_sql_pesquisa_anual($options);

      $dbo = $this->getDataSource();
      $query = $dbo->buildStatement(
        array(
          'table' => "({$cte_query})",
          'alias' => 'base',
          'joins' => $options['extra']['joins'],
          'fields' => $fields,
          'conditions' => $conditions,
          'order' => null,
          'limit' => null,
          'offset' => null,
          'group' => null,
          'recursive' => -1
        )
      , $this);
      $retorno = $this->query($query);
      $count = $retorno[0][0]['count'];
      return $count;

    }

    public function enviarAlertaPesquisaInsastifeita($dados) {
      $this->Alerta = & ClassRegistry::init('Alerta');
      $this->Cliente = & ClassRegistry::init('Cliente');

      if(!empty($dados['PesquisaSatisfacao']['codigo_produto']) && $dados['PesquisaSatisfacao']['codigo_produto'] == Produto::TELECONSULT_STANDARD) {
        $dados['PesquisaSatisfacao']['nome_produto'] = 'Teleconsult';
      }elseif(!empty($dados['PesquisaSatisfacao']['codigo_produto']) && $dados['PesquisaSatisfacao']['codigo_produto'] == Produto::BUONNYSAT) {
        $dados['PesquisaSatisfacao']['nome_produto'] = 'BuonnySat';
      }

      App::import('Component', array('StringView', 'Mailer.Scheduler'));
      $this->stringView = new StringViewComponent();
      $this->scheduler = new SchedulerComponent();
      $this->stringView->set('dados', $dados);
      $content = $this->stringView->renderMail('email_alerta_pesquisa_satisfacao_recusada');
      $subject = 'Pesquisa de Satifação - Notificação de Insastifação';
      $gestor = $this->Cliente->localiza_gestor_cliente($dados['PesquisaSatisfacao']['codigo_cliente']);
      $this->log('gestor_comercial_selecionado', 'alertas');
      $this->log($gestor, 'alertas');
      if(!empty($gestor[0]['gestor_comercial'])) {
        $alerta = array(
            'Alerta' => array(
                'codigo_cliente' => $dados['PesquisaSatisfacao']['codigo_cliente'],
                'descricao' => utf8_decode($subject),
                'descricao_email' => $content,
                'codigo_alerta_tipo' => self::ALERTA_PESQUISA_SATISFACAO_RECUSADA,
                'model' =>  'Usuario',
                'foreign_key' => $gestor[0]['gestor_comercial'],
            ),
        );
        $this->Alerta->query('begin transaction');
        if($this->Alerta->incluir($alerta)){
            $this->log('alerta_incluido', 'alertas');
            $this->log($alerta, 'alertas');
            $this->Alerta->commit();
            return TRUE;           
        }else{
            $this->Alerta->rollback();
            $this->log('ocorreu_um_problema_no_alerta', 'alertas');
            $this->log($alerta, 'alertas');
            return FALSE;   
        }
      }
    }
}
?>