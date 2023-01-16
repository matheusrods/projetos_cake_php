<?php 
class LogsAtendimentoController extends AppController { 
  public $name = 'LogsAtendimento';
  public $uses = array('LogAtendimento','ProfissionalTipo','TipoOperacao');
  public $components = array('BSession','Filtros','RequestHandler', 'Fichas');  
  public function beforeFilter(){
    parent::beforeFilter();
    $this->BAuth->allow("*");
  }
  
  public function index() {
    $this->Filtros->limpa_sessao('LogAtendimento');
    $this->data['LogAtendimento'] = $this->Filtros->controla_sessao( $this->data, 'LogAtendimento' );
    $tipos_profissional = $this->Fichas->listProfissionalTipoAutorizado();
    $tipos_operacoes    = $this->TipoOperacao->listaTodosTiposOperacao();
    $this->set(compact('tipos_operacoes', 'tipos_profissional'));   
  }

  public function vinculos_excluidos(){
    $this->Filtros->limpa_sessao('FichaScorecard');
    $this->data['LogAtendimento'] = $this->Filtros->controla_sessao( $this->data, 'LogAtendimento' );
    $tipos_profissional = $this->Fichas->listProfissionalTipoAutorizado();
    $tipos_operacoes    = $this->TipoOperacao->listaTodosTiposOperacao();
    $this->set(compact('tipos_operacoes', 'tipos_profissional'));   
  }

  public function listagem() {
    $this->layout = 'ajax';
    $filtros    = $this->Filtros->controla_sessao($this->data, 'LogAtendimento');
    $conditions = $this->LogAtendimento->converteFiltroEmCondition($filtros);
    $joins      = array(
      array(
        'table' => 'dbBuonny.portal.usuario',
        'alias' => 'Usuario',
        'type' => 'LEFT',
        'conditions' => 'Usuario.codigo = LogAtendimento.codigo_usuario_inclusao'
      ),
      array(
        'table' => 'dbTeleconsult.informacoes.tipo_operacao',
        'alias' => 'TipoOperacao',
        'type' => 'LEFT',
        'conditions' => 'TipoOperacao.codigo = LogAtendimento.codigo_tipo_operacao'
      ),
      array(
        'table' => 'dbBuonny.publico.profissional_tipo',
        'alias' => 'ProfissionalTipo',
        'type' => 'LEFT',
        'conditions' => 'ProfissionalTipo.codigo = LogAtendimento.codigo_profissional_tipo'
      ),
      array(
        'table' => 'dbBuonny.publico.profissional',
        'alias' => 'Profissional',
        'type' => 'LEFT',
        'conditions' => 'Profissional.codigo = LogAtendimento.codigo_profissional'
      ),
      array(
        'table' => 'dbBuonny.publico.veiculo',
        'alias' => 'Veiculo',
        'type' => 'LEFT',
        'conditions' => 'Veiculo.codigo = LogAtendimento.codigo_veiculo'
      )                    
    );
    $this->paginate['LogAtendimento'] = array( 
      'fields' => array(
      'Usuario.apelido','TipoOperacao.descricao','ProfissionalTipo.descricao', 
      'LogAtendimento.data_inicio', 'LogAtendimento.data_inclusao', 'Profissional.codigo_documento', 'Veiculo.placa' 
      ),
      'conditions' => $conditions,
      'joins'      => $joins,
      'limit'      => 50,
      'order'      => 'LogAtendimento.data_inclusao DESC'
    );
    $logatendimentos = $this->paginate('LogAtendimento');
    $this->set(compact('logatendimentos'));
  }

  public function imprimir_logs_atendimento(){
    $this->layout = 'ajax';
    $filtros = $_SESSION['filtros_atendimento'];
    unset($_SESSION['filtros_atendimento']);
    $filtros['CPF'] = str_replace('-','',str_replace('.','',$filtros['CPF'])); 
    if (!isset($filtros['Placa'])){
      $filtros['Placa'] = '';
    }
    $filtros['Placa'] = str_replace('-','',$filtros['Placa']); 
    $conditions = $this->LogAtendimento->converteFiltroEmCondition($filtros);
    $select = $this->LogAtendimento->paginate_listar($filtros);
    $fields = array ("Usuario.apelido AS 'UsuarioInclusao.apelido'",
    "TipoOperacao.descricao AS 'TipoOperacao.descricao'",
    "ProfissionalTipo.descricao AS 'ProfissionalTipo.descricao'",
    "Profissional.codigo_documento AS 'Profissional.codigo_documento'",
    "CONVERT(VARCHAR(20), LogAtendimento.data_inicio, 20) AS 'LogAtendimento.data_inicio'",
    "CONVERT(VARCHAR(20), LogAtendimento.data_inicio, 20) AS 'LogAtendimento.data_inclusao'",
    "'SCORECARD' AS 'Produto.descricao'");
    $query = $this->LogAtendimento->find('sql',array('fields'=>$fields,'joins'=>$select['joins'],'conditions'=>$conditions));
    header(sprintf('Content-Disposition: attachment; filename="%s"', basename('log_atendimento.pdf')));
    header('Pragma: no-cache');   
    require_once APP . 'vendors' . DS . 'buonny' . DS . 'Jasper.php';
    $clienteJasper = new Jasper(); 
    $clienteJasper->credenciais();
    $caminhoDoArquivo = 'nomeDoRelatorio.pdf';
    $pastaDoRelatorio = '/reports/Teleconsult2/';
    $nomeDoRelatorio  = 'log_atendimento';
    $parametros       =  array("QUERY" => $query);
    $resultado = $clienteJasper->printReport($pastaDoRelatorio, $nomeDoRelatorio, 'PDF', $parametros);
    //file_put_contents($caminhoDoArquivo, $resultado); salva arquivo
    echo $resultado;
    exit;      
  }
}
?>