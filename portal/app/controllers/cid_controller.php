<?php
class CidController extends AppController {
    public $name = 'Cid';
    public $uses = array('Cid');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow('buscar_cid', 'buscar_listagem', 'listagem_visualizar');
    }   
    
    public function index() {
        $this->pageTitle = 'CID';
        $this->data['Cid'] = $this->Filtros->controla_sessao($this->data, $this->Cid->name);
    }

    public function listagem() {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Cid->name);
        $conditions = $this->Cid->converteFiltroEmCondition($filtros);

        $fields = array('Cid.codigo', 'Cid.codigo_cid10', 'Cid.descricao', 'Cid.ativo');
        $order = 'Cid.descricao';

        $this->paginate['Cid'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order
        );

        $cid= $this->paginate('Cid');

        $this->set(compact('cid'));    
    }

    public function incluir() {
        $this->pageTitle = 'Incluir CID';

        if($this->RequestHandler->isPost()) {
            
            $this->data['Cid']['codigo_cid10'] = $this->formata_codigo_cid10($this->data['Cid']['codigo_cid10']);   

            if ($this->Cid->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'cid'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }
    }

    public function editar() {
        $this->pageTitle = 'Editar CID'; 

        if($this->RequestHandler->isPost()) {

            $this->data['Cid']['codigo_cid10'] = $this->formata_codigo_cid10($this->data['Cid']['codigo_cid10']);   

            if ($this->Cid->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'cid'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }

        if (isset($this->passedArgs[0])) {            
            $this->data = $this->Cid->carregar($this->passedArgs[0]);
        }        
    }

    public function atualiza_status($codigo, $status) {
        $this->layout = 'ajax';

        $this->data['Cid']['codigo'] = $codigo;
        $this->data['Cid']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->Cid->atualizar($this->data, false)) {   
            echo 1;
        } else {
            echo 0;
        }
        $this->render(false,false);
    } 

   public function buscar_cid($codigo_atestado){
        $this->layout = 'ajax_placeholder';
        $this->data['Cid'] = $this->Filtros->controla_sessao($this->data, $this->Cid->name);
        $this->set(compact('codigo_atestado'));
    }
    
   public function buscar_listagem($destino, $codigo_atestado){
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->Cid->name);
        $conditions = $this->Cid->converteFiltroEmCondition($filtros);
        
        $this->paginate['Cid'] = array(
            'conditions' => $conditions,
            'joins' => null,
            'order' => 'Cid.descricao ASC',
            'limit' => 10,
        );

        $cids = $this->paginate('Cid');        
        $this->set(compact('cids', 'destino','codigo_atestado'));
    }

    public function carregaCidsParaAjax()
    {
        $this->autoRender = false;
        $html = false;
        if($this->RequestHandler->isPost()) {
            $cids = $this->Cid->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'Cid.descricao LIKE' => '%'.$_POST['string'].'%'
                    ),
                // 'limit' => ,
                'order' => 'Cid.descricao ASC'
                )
            );
            if(!empty($cids)) {
                $html = '<table class="table">';
                foreach ($cids as $key => $cid) {
                    $html .= '<tr class="js-cid-click" data-codigo="'.$cid['Cid']['codigo'].'">';
                    $html .= '<td>';
                    $html .= $cid['Cid']['descricao'];
                    $html .= '</td>';
                    $html .= '<td>';
                    $html .= $cid['Cid']['codigo_cid10'];
                    $html .= '</td>';
                    $html .= '</tr>';
                }
                $html .= '</table>';
            }
        }
        return json_encode($html);
    }

    public function formata_codigo_cid10 ($codigo) {

        $codigo = strtoupper(str_replace(array('.','-','/'), '',  $codigo)); 
        return $codigo;
    }

    public function busca_cid() {
        $this->layout = 'ajax_placeholder';
        $searcher = !empty($this->passedArgs['searcher']) ? $this->passedArgs['searcher'] : '';
        $display = !empty($this->passedArgs['display']) ? $this->passedArgs['display'] : $this->data['Cid']['display'];
    
        $this->data['Cid'] = $this->Filtros->controla_sessao($this->data, $this->Cid->name);
    
        $this->set(compact('searcher', 'display'));
    }

    public function listagem_visualizar($destino) {
        
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Cid->name);
        $conditions = $this->Cid->converteFiltroEmCondition($filtros);
        $this->paginate['Cid'] = array(
                'recursive' => 1,
                'joins' => null,
                'conditions' => $conditions,
                'limit' => 10,
                'order' => 'Cid.codigo_cid10',
        );
        
        $cids = $this->paginate('Cid');
        $this->set(compact('cids', 'destino'));
        
        if (isset($this->passedArgs['searcher']))
            $this->set('input_id', str_replace('-search', '', $this->passedArgs['searcher']));
        
        if (isset($this->passedArgs['display']))
            $this->set('input_display', str_replace('-search', '', $this->passedArgs['display']));      
        
    }
}