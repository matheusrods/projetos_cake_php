<?php 
class LogsExclusaoVinculosController extends AppController {

    public $name = 'LogsExclusaoVinculos';
    public $uses = array('LogAtendimento','ProfissionalTipo','TipoOperacao');
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
        $filtros = $this->Filtros->controla_sessao($this->data, 'LogAtendimento');
        $filtros['CPF'] = str_replace('-','',str_replace('.','',$filtros['CPF']));         
        $conditions = $this->LogAtendimento->converteFiltroEmConditionExclusaoVinculos($filtros);
        $select = $this->LogAtendimento->paginate_listar_ExclusaoVinculos($filtros);
        $this->paginate['LogAtendimento'] = array(
                'fields' => $select['fields'], 
                'conditions' => $conditions,
                'joins' => $select['joins'],
                'limit' => 50,
                'group' =>array('VendasCliente.codigo','VendasCliente.razao_social','Profissional.codigo_documento',
                                'Profissional.nome','Usuario.apelido','ProfissionalTipo.descricao','Produto.descricao'),
                'order' => 'codigo'
                 );
        $logsexclusaovinculos = $this->paginate('LogAtendimento');
        $count_for = $this->LogAtendimento->find('all', array('fields' => array('DISTINCT LogAtendimento.codigo_profissional'),
                            'conditions' => $conditions,
                            'joins' => $select['joins']
                 ));
        foreach ($count_for as $key=>$count_pag) {
          $count = $key;
        }
        @$count = $key + 1 ; // Soma um pois o key começa com 0
        //$logatendimentos 
        //debug($count);
        $this->set(compact('logsexclusaovinculos','count'));

    }

 }

