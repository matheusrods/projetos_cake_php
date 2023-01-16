<?php
class DiretoriaController extends AppController {
    public $name = 'Diretoria';
    public $uses = array(
        'Diretoria',
    );

    function carregaCombos(){
        $ativos_inativos = array(1 => 'Ativo',2 => 'Inativo');
        $this->set(compact('ativos_inativos'));
    }

    function index() {
        $filtrado = true;
        $this->carregaCombos();
        $this->data['Diretoria'] = $this->Filtros->controla_sessao($this->data, "Diretoria");
        $this->set(compact('filtrado'));        
    } 
    
    function listagem(){
        $this->data['Diretoria'] = $this->Filtros->controla_sessao($this->data, "Diretoria");
        $conditions = $this->Diretoria->converteFiltrosEmConditions($this->data['Diretoria']);
     
        $order = 'descricao ASC';   
        $this->paginate['Diretoria'] = array(
            'limit' => 50,
            'conditions' => $conditions,
            'order' => $order
        );
        $listagem = $this->paginate('Diretoria');
        $this->set(compact('listagem'));
    }

    function incluir(){
        $this->pageTitle = 'Incluir Diretoria';
        if (!empty($this->data)) {
            if ($this->Diretoria->incluir($this->data)) {              
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
         }
    }

    function editar($codigo){
        $this->pageTitle = 'Atualizar Diretoria';
        if($this->RequestHandler->isPost()) {
            $this->data['Diretoria']['codigo'] = $codigo;
            if ($this->Diretoria->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }    
        $this->data = $this->Diretoria->carregar($codigo);
    }

    function inativar_ativar($codigo,$ativo_inativo){
        if($codigo){
            $dados['Diretoria']['codigo'] = $codigo;
            $dados['Diretoria']['ativo'] = $ativo_inativo;
            if($this->Diretoria->atualizar($dados)){
                $this->BSession->setFlash('save_success');
            }else{
                $this->BSession->setFlash('delete_error');
            }
            $this->redirect(array('action' => 'index'));
        }
    }

   
}
?>  