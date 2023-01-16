<?php
class ExamesFuncoesController extends AppController {
    public $name = 'ExamesFuncoes';
    var $uses = array( 'ExameFuncao',
    	'Exame',
    	'Funcao');
    

    function beforeFilter() {
        parent::beforeFilter();
    }

    function index() {
        $this->pageTitle = 'Exames e Funções';
        $this->carrega_combos();
        $this->data['ExameFuncao'] = $this->Filtros->controla_sessao($this->data, $this->ExameFuncao->name);
    }

    function carrega_combos(){
    	$conditions = array('ativo'=> 1);
    	$fields = array('codigo', 'descricao');
    	$order = 'descricao';

    	$exames = $this->Exame->find('list', array('conditions' => $conditions, 'order' => $order, 'fields' => $fields));  


    	$funcoes = $this->Funcao->find('list', array('conditions' => $conditions, 'order' => $order, 'fields' =>  $fields));  

    	$this->set(compact('exames', 'funcoes'));
    }

    function listagem() {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->ExameFuncao->name);
        $conditions = $this->ExameFuncao->converteFiltroEmCondition($filtros);

        $fields = array('ExameFuncao.codigo', 'ExameFuncao.codigo_exame', 'ExameFuncao.codigo_funcao', 
                        'Exame.descricao', 'Funcao.descricao','ExameFuncao.ativo');
        $order = 'ExameFuncao.codigo';

        $this->paginate['ExameFuncao'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order
        );

        $exames_funcoes = $this->paginate('ExameFuncao');

        $this->set(compact('exames_funcoes'));    
    }

    function incluir() {
        $this->pageTitle = 'Incluir Exame e Função';
        $this->carrega_combos();

        if($this->RequestHandler->isPost()) {
            if ($this->ExameFuncao->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'exames_funcoes'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }
    }

    function editar() {
        $this->pageTitle = 'Editar Exame e Função'; 
        $this->carrega_combos();

        if($this->RequestHandler->isPost()) {

            if ($this->ExameFuncao->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'exames_funcoes'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }

        if (isset($this->passedArgs[0])) {            
            $this->data = $this->ExameFuncao->carregar($this->passedArgs[0]);
        }        
    }

    function atualiza_status($codigo, $status){
		$this->layout = 'ajax';

		$this->data['ExameFuncao']['codigo'] = $codigo;
		$this->data['ExameFuncao']['ativo'] = ($status == 0) ? 1 : 0;

		if ($this->ExameFuncao->atualizar($this->data, false)) {   
		    echo 1;
		} else {
		    echo 0;
		}
		$this->render(false,false);
    }

}