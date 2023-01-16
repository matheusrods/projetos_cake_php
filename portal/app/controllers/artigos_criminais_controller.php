<?php 
class ArtigosCriminaisController extends AppController {

    public $name = 'ArtigosCriminais';
    public $uses = array('ArtigoCriminal');

    public function index() {
      $filtros = $this->Filtros->controla_sessao($this->data,'ArtigoCriminal');
      $this->data['ArtigoCriminal'] = $filtros;
    }
     
   public  function editar($codigo) {
       
        $this->pageTitle = 'Atualizar ArtigoCriminal';
        if (!empty($this->data)) {                
           
         $data_formatada = preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})(\w*)/", "$3$2$1$4", $this->data['ArtigoCriminal']['data_vigencia']);

         if (date("Ymd") <= date($data_formatada)){   
            
            $this->data['ArtigoCriminal']['vigente'] = 1;
         }else {
           $this->data['ArtigoCriminal']['vigente'] = 0;
         }  

            $this->data['ArtigoCriminal']['data_vigencia'] = preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})(\w*)/", "$3$2$1$4", $this->data['ArtigoCriminal']['data_vigencia']);
            if ($this->ArtigoCriminal->atualizar($this->data)) {               
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }else {
           
          $this->data  = $this->ArtigoCriminal->carregarParaEdicao($codigo);

       }
    }

    function incluir() {
        $this->pageTitle = 'Incluir Artigo Criminal'; 
        if($this->RequestHandler->isPost()) {
          unset($this->data['ArtigoCriminal']['codigo']);
          $this->data['ArtigoCriminal']['data_criacao'] = date('Ymd H:i:s');
          $result = $this->ArtigoCriminal->incluir($this->data);
          if ($result) {
            $this->BSession->setFlash('save_success');
            $this->redirect(array(
              'action' => 'index'
            ));
          } else {
            $this->BSession->setFlash('save_error');
          }
        }  
       
    }

    function excluir($codigo) {
        if(!$this->ArtigoCriminal->excluir($codigo))
            $this->BSession->setFlash('delete_error');
        else
            $this->redirect(array('action' => 'index'));
    }

    public function listagem() {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'ArtigoCriminal');
        $conditions = $this->ArtigoCriminal->converteFiltroEmCondition($filtros);
        $this->paginate['ArtigoCriminal'] = array(
              'conditions' => $conditions,
              'limit' => 50,
              'order' => 'ArtigoCriminal.codigo'
               );
        $artigos = $this->paginate('ArtigoCriminal');
        $count = $this->ArtigoCriminal->find('count', compact('conditions'));
        $this->set(compact('artigos','count'));

    } 

    function buscar_artigos_criminais(){
        if($this->RequestHandler->isAjax()){
            $this->autoRender = false;
            $conditions['OR'] = array( 
                array('ArtigoCriminal.descricao LIKE' => iconv('utf-8', 'iso-8859-1', $_GET['term']).'%'),
                array('ArtigoCriminal.nome LIKE' => iconv('utf-8', 'iso-8859-1', $_GET['term']).'%'),
            );
            $artigos_criminais = $this->ArtigoCriminal->find('all',array('conditions'=>$conditions, 'fields' => array('codigo', 'nome', 'descricao')));
            $response = $this->ArtigoCriminal->retiraModel($artigos_criminais);
            echo json_encode($response);
        }
    }
 }

