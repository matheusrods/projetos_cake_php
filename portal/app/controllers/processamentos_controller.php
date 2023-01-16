<?php
class ProcessamentosController extends AppController{

    public $name = "Processamentos";
    var $uses = array(
        "Processamento",
        "ProcessamentoTipoArquivo",
        "ProcessamentoStatus"
    );

    public function __construct(){
        parent::__construct();
        $this->pageTitle = 'Processamentos';
    }

    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array(
            'index',
            'listagem',
            'contagem'
        ));
    }

    public function index(){
        
        $filtros = $this->Filtros->controla_sessao($this->data, 'Processamento');
        
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $tipos_arquivos = $this->ProcessamentoTipoArquivo->find('list', array('fields' => array('ProcessamentoTipoArquivo.codigo', 'ProcessamentoTipoArquivo.descricao')));
        $status = $this->ProcessamentoStatus->find('list', array('fields' => array('ProcessamentoStatus.codigo', 'ProcessamentoStatus.descricao')));

        //verifica para seta a data do começo do mes padrao
        if(empty($this->data['Processamento']['data_de'])) {
            //seta as datas
            $filtros['data_de'] = '01/'.date('m/Y');
            $filtros['data_ate'] = date('d/m/Y');
        }

        $this->data['Processamento'] = $filtros;

        $this->set(compact('tipos_arquivos', 'status'));
    }

    public function listagem(){
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Processamento->name);
        $filtros = (!is_array($filtros) ? array() : $filtros);

        if (!empty($filtros['codigo_cliente'])) {
            $this->paginate['Processamento'] = $this->Processamento->getByUser($this->BAuth->user('codigo'), $filtros, true);
            $processamentos = $this->paginate('Processamento');

            $this->set(compact('processamentos'));            
        }
    }

    public function contagem(){
        $this->autoRender = false;

        $codigo = $this->params['url']['codigo'];
        if($this->Processamento->contagem($codigo)){
            return json_encode(array('status' => 'success'));
        }else{
            return json_encode(array('status' => 'error'));
        }
    }

}