<?php
class PesquisaVeiculo extends AppModel {

  var $name = 'PesquisaVeiculo';
  var $tableSchema = 'informacoes';
  var $databaseTable = 'dbTeleconsult';
  var $useTable = 'pesquisa_veiculo';
  var $primaryKey = 'codigo';
  var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_pesquisa_veiculo'));
  var $validate = array(
      'codigo_status' => array(
          'notEmpty' => array(
          'rule' => 'notEmpty',
            'message' => 'Informe o status da ficha'
          )
      ),
  );
  const CADASTRADA = 1;
  const PESQUISA   = 2;
  const APROVADA   = 3;
  const REPROVADA  = 4;
  function getStatus(){
    return array(
        $this::CADASTRADA => 'Cadastrado',
        $this::PESQUISA   => 'Em Pesquisa',
        $this::APROVADA   => 'Aprovado',
        $this::REPROVADA  => 'Reprovado'
      );
  }

  public  function parametros_veiculos_a_pesquisar($filtros){    
    $conditions = array();   
    $this->Usuario = ClassRegistry::init('Usuario');    
    if(isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente']))
      $conditions['Cliente.codigo'] = $filtros['codigo_cliente'];    
    
    if(isset($filtros['codigo_cliente_embarcador']) && !empty($filtros['codigo_cliente_embarcador']))
      $conditions['PesquisaVeiculo.codigo_cliente_embarcador'] = $filtros['codigo_cliente_embarcador'];    
    
    if(isset($filtros['codigo_cliente_transportador']) && !empty($filtros['codigo_cliente_transportador']))
      $conditions['PesquisaVeiculo.codigo_cliente_transportador'] = $filtros['codigo_cliente_transportador'];    
    
    if(isset($filtros['codigo_status']) && !empty($filtros['codigo_status']))
        $conditions['PesquisaVeiculo.codigo_status'] = $filtros['codigo_status'];
    
    if(isset($filtros['placa']) && !empty($filtros['placa']))
      $conditions['Veiculo.placa'] = str_replace('-', '', $filtros['placa']);

    if(isset($filtros['codigo_veiculo']) && !empty($filtros['codigo_veiculo']))
      $conditions['Veiculo.codigo'] = $filtros['codigo_veiculo'];

    if(isset($filtros['codigo_veiculo']) && !empty($filtros['codigo_veiculo']))
      $conditions['Veiculo.codigo'] = $filtros['codigo_veiculo'];    

    if(isset($filtros['pesquisa']) && $filtros['pesquisa']){
      if(isset($filtros['data_inicial']) && !empty($filtros['data_inicial']))
        $conditions['PesquisaVeiculo.data_inclusao >='] = date('Ymd 00:00:00', Comum::dateToTimestamp($filtros['data_inicial']));      
      if(isset($filtros['data_final']) && !empty($filtros['data_final']))
        $conditions['PesquisaVeiculo.data_inclusao <='] = date('Ymd 23:59:59', Comum::dateToTimestamp($filtros['data_final']));      
    }

    if(isset($filtros['finaliza']) && $filtros['finaliza']){
      if(isset($filtros['data_inicial']) && !empty($filtros['data_inicial']))
        $conditions['PesquisaVeiculo.data_alteracao >='] = date('Ymd 00:00:00', Comum::dateToTimestamp($filtros['data_inicial']));      
      if(isset($filtros['data_final']) && !empty($filtros['data_final']))
        $conditions['PesquisaVeiculo.data_alteracao <='] = date('Ymd 23:59:59', Comum::dateToTimestamp($filtros['data_final']));      
    }

    $this->Veiculo = classRegistry::init('Veiculo');
    $this->Cliente = classRegistry::init('Cliente');
    $this->Usuario = classRegistry::init('Usuario');
    
    $joins = array(
        array(
            'table' => $this->Veiculo->databaseTable.'.'.$this->Veiculo->tableSchema.'.'.$this->Veiculo->useTable,
            'alias' => 'Veiculo',
            'type'  => 'INNER',
            'conditions' => array('Veiculo.codigo = PesquisaVeiculo.codigo_veiculo')
        ),
        array(
            'table' => $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
            'alias' => 'Cliente',
            'type'  => 'INNER',
            'conditions' => array('Cliente.codigo = PesquisaVeiculo.codigo_cliente')
        ),
        array(
            'table' => $this->Usuario->databaseTable.'.'.$this->Usuario->tableSchema.'.'.$this->Usuario->useTable,
            'alias' => 'UsuarioPesquisa',
            'type'  => 'LEFT',
            'conditions' => array('UsuarioPesquisa.codigo = PesquisaVeiculo.codigo_usuario_em_pesquisa')
        ),
        array(
            'table' => $this->Usuario->databaseTable.'.'.$this->Usuario->tableSchema.'.'.$this->Usuario->useTable,
            'alias' => 'UsuarioAprovacao',
            'type'  => 'LEFT',
            'conditions' => array('UsuarioAprovacao.codigo = PesquisaVeiculo.codigo_usuario_em_aprovacao')
        ),     
        array(
            'table' => $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
            'alias' => 'ClienteEmbarcador',
            'type'  => 'LEFT',
            'conditions' => array('ClienteEmbarcador.codigo = PesquisaVeiculo.codigo_cliente_embarcador')
        ), 
        array(
            'table' => $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
            'alias' => 'ClienteTransportador',
            'type'  => 'LEFT',
            'conditions' => array('ClienteTransportador.codigo = PesquisaVeiculo.codigo_cliente_transportador')
        ) 
    );  

    $fields = array(        
        'Cliente.codigo                              AS codigo_cliente',
        'Cliente.razao_social                        AS cliente',
        'PesquisaVeiculo.tempo_sla                   AS tempo_sla',
        'PesquisaVeiculo.codigo                      AS codigo_ficha',
        'PesquisaVeiculo.codigo_status               AS codigo_status',
        'PesquisaVeiculo.codigo_usuario_em_pesquisa  AS codigo_usuario_em_pesquisa',
        'PesquisaVeiculo.codigo_usuario_em_aprovacao AS codigo_usuario_em_aprovacao',
        'PesquisaVeiculo.codigo_usuario_inclusao     AS codigo_usuario_inclusao',
        'CONVERT(VARCHAR(20), PesquisaVeiculo.data_alteracao, 20) AS data_alteracao',
        'CONVERT(VARCHAR(20), PesquisaVeiculo.data_inclusao, 20) AS data_inclusao',
        'Veiculo.codigo                              AS codigo_veiculo',
        'Veiculo.placa                               AS placa',
        'Veiculo.ano                                 AS ano',
        'UsuarioPesquisa.apelido                     AS usuario_pesquisa',
        'UsuarioAprovacao.apelido                    AS usuario_aprovacao',
        'ClienteEmbarcador.razao_social              AS embarcador',
        'ClienteTransportador.razao_social           AS transportador',
    );   
  
    $limit = 10;
    $order = 'data_inclusao';  
    return compact('conditions','fields','limit','order','joins');
  }

  public function nova_ficha($data){    
      $this->Cliente =& ClassRegistry::init('Cliente');      
      
      if(!empty($data['Veiculo']['codigo']) && !empty($data['Cliente']['codigo'])){
        
        $cliente = $this->Cliente->carregar($data['Cliente']['codigo']);  
        
        if(!empty($data['Veiculo']['codigo_cliente_transportador_default']))
          $transportador = $data['Veiculo']['codigo_cliente_transportador_default'];
        else if(in_array( $cliente['Cliente']['codigo_cliente_sub_tipo'], array(1,7,13,19)))
          $transportador = $cliente['Cliente']['codigo'];
        else
          $transportador = null;
        
        if(!in_array( $cliente['Cliente']['codigo_cliente_sub_tipo'], array(1,7,13,19)))
          $embarcador = $cliente['Cliente']['codigo'];
        else
          $embarcador = null;
           
        $dados_pesquisa = array(
            'PesquisaVeiculo' => array(
                'codigo_veiculo'               => $data['Veiculo']['codigo'],
                'codigo_cliente'               => $data['Cliente']['codigo'],
                'tempo_sla'                    => 90,
                'codigo_status'                => 1,
                'codigo_cliente_transportador' => $transportador,
                'codigo_cliente_embarcador'    => $embarcador
              )
          );        
        if($this->incluir($dados_pesquisa))
          return true;
      }
      return false;
  }
}
?>