<?php
class MedicamentosController extends AppController {
    public $name = 'Medicamentos';
    var $uses = array('Medicamento', 
      'Laboratorio'
      );
  
    public function beforeFilter() {
    	parent::beforeFilter();
    }

    function index() {
        $this->pageTitle = 'Medicamentos';
        $this->carrega_combos();
    }

    function carrega_combos(){
        $laboratorios = $this->Laboratorio->find('list', array(
          'fields' => array('codigo','descricao'), 
          'order' => 'descricao')
        );
        $apresentacoes = $this->Medicamento->Apresentacao->find('list', array(
         'fields' => array('codigo','descricao'), 
         'order' => 'descricao')
        );
        $this->set(compact('laboratorios','apresentacoes'));   
    }

    function listagem() {
    	
        $this->layout = 'ajax'; 
        
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Medicamento->name);
        $conditions = $this->Medicamento->converteFiltroEmCondition($filtros);    

        $this->Medicamento->bindModel( 
            array(
                'belongsTo' => array(
                    'Laboratorio' => array(
                        'foreignKey' => false, 
                        'conditions' => array('Laboratorio.codigo = Medicamento.codigo_laboratorio')
                        ),
                    )
                ), false
            );

        $fields = array('Medicamento.codigo', 'Medicamento.descricao','Medicamento.ativo','Medicamento.principio_ativo', 'Laboratorio.descricao');
        $order = 'Medicamento.descricao';

        $this->paginate['Medicamento'] = array(
            'conditions' => $conditions,
            'limit' => 50,
            'fields' => $fields, 
            'order' => $order
            );

        $medicamentos = $this->paginate('Medicamento');
        
        $this->set(compact('medicamentos'));

    }

    function incluir() {
        $this->pageTitle = 'Incluir Medicamentos';
        $this->carrega_combos();

        if($this->RequestHandler->isPost()) {

         $this->data ['Medicamento'] ['descricao'] = strtoupper ( $this->data['Medicamento']['descricao'] );
         $this->data ['Medicamento'] ['principio_ativo'] = strtoupper ( $this->data['Medicamento']['principio_ativo'] );


         if ($this->Medicamento->incluir($this->data)) {
            $this->BSession->setFlash('save_success');
            $this->redirect(array('action' => 'index', 'controller' => 'medicamentos'));
        } 
        else {
            $this->BSession->setFlash('save_error');
        }
    }
}

function editar() {
    $this->pageTitle = 'Editar Medicamentos'; 
    $this->carrega_combos();

    if($this->RequestHandler->isPost()) {

        $this->data ['Medicamento'] ['descricao'] = strtoupper ( $this->data ['Medicamento'] ['descricao'] );
        $this->data ['Medicamento'] ['principio_ativo'] = strtoupper ( $this->data['Medicamento']['principio_ativo'] );

        if ($this->Medicamento->atualizar($this->data)) {
            $this->BSession->setFlash('save_success');
            $this->redirect(array('action' => 'index', 'controller' => 'medicamentos'));
        } 
        else {
            $this->BSession->setFlash('save_error');
        }
    }

    if (isset($this->passedArgs[0])) {            
        $this->data = $this->Medicamento->carregar($this->passedArgs[0]);
    }        
}

function atualiza_status($codigo, $status){
    $this->layout = 'ajax';

    $this->data['Medicamento']['codigo'] = $codigo;
    $this->data['Medicamento']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->Medicamento->save($this->data, false)) {   // 0 -> ERRO | 1 -> SUCESSO  
            print 1;
        } else {
            print 0;
        }

        $this->render(false,false);
    }

    public function carregaMedicamentosParaAjax()
    {
        $this->autoRender = false;
        $html = false;
        if($this->RequestHandler->isPost()) {
            $medicamentos = $this->Medicamento->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'OR' => array(
                        'Medicamento.descricao LIKE' => '%'.$_POST['string'].'%',
                        'Medicamento.principio_ativo LIKE' => '%'.$_POST['string'].'%',
                        )
                    ),
                'limit' => 6,
                'order' => 'Medicamento.descricao ASC'
                )
            );
            if(!empty($medicamentos)) {
                $html = '<table class="table">';
                foreach ($medicamentos as $key => $medicamento) {
                    $html .= '<tr class="js-click" data-codigo="'.$medicamento['Medicamento']['codigo'].'">';
                    $html .= '<td>';
                    $html .= $medicamento['Medicamento']['descricao'];
                    $html .= '</td>';
                    $html .= '<td>';
                    $html .= $medicamento['Medicamento']['principio_ativo'];
                    $html .= '</td>';
                    $html .= '<td>';
                    $html .= $medicamento['Medicamento']['posologia'];
                    $html .= '</td>';
                    $html .= '</tr>';
                }
                $html .= '</table>';
            }
        }
        return json_encode($html);
    }
}