<?php
class LogsAplicacoesController extends AppController {
    public $name = 'LogsAplicacoes';
    var $uses = array('LogAplicacao');

    public function index(){
        $this->pageTitle = "Logs das Aplicações";
        $this->data['LogAplicacao'] = $this->Filtros->controla_sessao(null, 'LogAplicacao');        
        $sistemas = $this->LogAplicacao->listarSistemas();
        $codigo_cliente = '';
        $authUsuario    =& $this->authUsuario;
        if(!empty($authUsuario['Usuario']['codigo_cliente'])) {            
            $codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];
        }
        $tipo = 'completo';
        $this->set(compact('codigo_cliente','sistemas', 'tipo'));
    }

    public function resumido(){
        $this->pageTitle = "Logs das Aplicações";
        $this->data['LogAplicacao'] = $this->Filtros->controla_sessao(null, 'LogAplicacao');        
        $sistemas = $this->LogAplicacao->listarSistemasResumido();
        $codigo_cliente = '';
        $authUsuario    =& $this->authUsuario;
        if(!empty($authUsuario['Usuario']['codigo_cliente'])) {            
            $codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];
        }
        $tipo = 'resumido';
        $this->set(compact('codigo_cliente','sistemas', 'tipo'));
    }

    public function listagem() {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'LogAplicacao');

        $authUsuario = $this->BAuth->user();
        if(!empty($authUsuario['Usuario']['codigo_cliente'])) {            
            $filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
        }
        $conditions = $this->LogAplicacao->converteFiltrosEmConditions($filtros);
        
        $this->paginate['LogAplicacao'] = array(
            'conditions' => $conditions, 
            'limit' => 50,
            'order' => 'LogAplicacao.codigo DESC',
        );
        $logs_aplicacoes = $this->paginate('LogAplicacao');
        $this->set(compact('logs_aplicacoes'));
    }
    public function listagem_resumido() {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'LogAplicacao');

        $authUsuario = $this->BAuth->user();
        if(!empty($authUsuario['Usuario']['codigo_cliente'])) {            
            $filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
        }
        
        if(empty($filtros['sistema'])){
            $filtros['sistema'] = array('Finalizador', 'Reprogramador', 'Inicializador');
        }
            
        $conditions = $this->LogAplicacao->converteFiltrosEmConditions($filtros);
        
        $this->paginate['LogAplicacao'] = array(
            'conditions' => $conditions, 
            'limit' => 50,
            'order' => 'LogAplicacao.codigo DESC',
        );
        $logs_aplicacoes = $this->paginate('LogAplicacao');
        $this->set(compact('logs_aplicacoes'));
    }

    public function mudar_status_tratado() {
        $log_aplicacao = $this->LogAplicacao->carregar($this->data['LogAplicacao']['codigo']);
        if ($log_aplicacao['LogAplicacao']['tratado']) {
            $log_aplicacao['LogAplicacao']['tratado'] = 0;
        } else {
            $log_aplicacao['LogAplicacao']['tratado'] = 1;
        }
        $this->LogAplicacao->atualizar($log_aplicacao);
        echo json_encode($log_aplicacao['LogAplicacao']['tratado']);
        die;
    }
}