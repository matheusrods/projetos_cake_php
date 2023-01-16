<?php 
class LogsExclusaoVinculosController extends AppController {
  public $name = 'LogsExclusaoVinculos';
  public $uses = array('LogAtendimento','ProfissionalTipo','TipoOperacao', 'FichaScorecard', 'ProfissionalLog');
  var $components = array('BSession','Filtros', 'Fichas'); 


  public function index() {
    $this->pageTitle = 'Logs Exclusão Vinculos';
    $filtros = $this->Filtros->controla_sessao($this->data,'LogAtendimento');
    $this->data['LogAtendimento'] = $filtros;
    $tipos_profissional = $this->Fichas->listProfissionalTipoAutorizado();
    $statuses = ClassRegistry::init('FichaScorecardStatus')->descricoes;
    $this->data['LogAtendimento']['data_inicial'] = date('d/m/Y');
    $this->data['LogAtendimento']['data_final'] = date('d/m/Y');     
    $this->set(compact('tipos_profissional', 'statuses'));   
  }
  
  public function listagem() {          
        $this->layout = 'ajax';
        $filtros    = $this->Filtros->controla_sessao($this->data, 'LogAtendimento');
        $conditions = $this->LogAtendimento->converteFiltroEmCondition($filtros);
        $conditions["LogAtendimento.codigo_tipo_operacao"] = 101;
        $conditions["FichaScorecard.ativo"] = 2;
        $select = $this->LogAtendimento->paginate_listar_ExclusaoVinculos($filtros);
        $this->paginate['LogAtendimento'] = array(
            'fields' => $select['fields'], 
            'conditions' => $conditions,
            'joins' => $select['joins'],
            'limit' => 50,
            'group' =>array(
                'VendasCliente.codigo','VendasCliente.razao_social','Profissional.codigo_documento',
                'Profissional.nome','Usuario.apelido','ProfissionalTipo.descricao','Produto.descricao'
            ),
        );

        $count= $this->LogAtendimento->find('all', array(
            'fields' => array(
                'VendasCliente.codigo','VendasCliente.razao_social','Profissional.codigo_documento',
                'Profissional.nome','Usuario.apelido','ProfissionalTipo.descricao','Produto.descricao'
            ),
            'conditions' => $conditions,
            'joins' => $select['joins'],
            'group' =>array(
                'VendasCliente.codigo','VendasCliente.razao_social','Profissional.codigo_documento',
                'Profissional.nome','Usuario.apelido','ProfissionalTipo.descricao','Produto.descricao'
            )

        ));
        $count = count($count);
        $lista = $this->paginate('LogAtendimento');
        $this->set(compact('lista', 'count'));
    }
}
?>