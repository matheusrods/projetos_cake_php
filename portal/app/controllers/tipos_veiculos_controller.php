<?php
class TiposVeiculosController extends AppController {
    public $name = 'TiposVeiculos';
    public $uses = array('TTveiTipoVeiculo');

    
    function beforeFilter() {
        parent::beforeFilter();
        //$this->BAuth->allow('*');
    }
    
    public function index() {
        $this->loadModel('TTveiTipoVeiculo');
        $this->pageTitle = 'Tipos de Veículo';
        $this->data['TTveiTipoVeiculo']   =  $this->Filtros->controla_sessao($this->data, 'TTveiTipoVeiculo');
    }   

    public function listagem() {
        $this->loadModel('TTveiTipoVeiculo');
        
        $filtros    = $this->Filtros->controla_sessao(array('TTveiTipoVeiculo' => array()), 'TTveiTipoVeiculo');

        $this->paginate['TTveiTipoVeiculo'] = Array(
            'limit' => 50,
            'conditions' => $filtros,
            'method' => 'listagem'
        );
        $tipos_veiculo = $this->paginate('TTveiTipoVeiculo');

        $this->set(compact('tipos_veiculo'));

    }

    public function incluir() {
        $this->pageTitle = 'Incluir Tipo Veículo';
        if ($this->data){            
            if ($this->TTveiTipoVeiculo->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'TiposVeiculos','action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
    }

    
    public function editar($codigo_tipo) { 
        $this->pageTitle = 'Editar Tipo Veículo'; 
        if (!empty($this->data)) {
            if ($this->TTveiTipoVeiculo->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'TiposVeiculos', 'action' => 'index'));
            } else {
               $produto = null;
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->TTveiTipoVeiculo->carregar($codigo_tipo);
        }

        $this->set(compact('codigo_tipo'));
    }    

    public function excluir($codigo) {
        $this->layout = false;
        if (empty($codigo)) {
            $this->BSession->setFlash(array(MSGT_ERROR,'Tipo de Veículo não encontrado'));
            
            exit;
        }
        $dados = $this->TTveiTipoVeiculo->read(null, $codigo);
        if (empty($dados['TTveiTipoVeiculo']['tvei_codigo'])) {
            $this->BSession->setFlash(array(MSGT_ERROR,'Tipo de Veículo já está excluído'));
            echo false;
            exit;
        }

        if (!$this->TTveiTipoVeiculo->removerTipoVeiculo($codigo)) {
            if (is_array($this->TTveiTipoVeiculo->validationErrors) && count($this->TTveiTipoVeiculo->validationErrors)>0) {
                $mensagem_erro = current($this->TTveiTipoVeiculo->validationErrors);
            } else {
                $mensagem_erro = 'Erro ao excluir Tipo de Veículo';
            }

            $this->BSession->setFlash(array(MSGT_ERROR,$mensagem_erro));
            //$this->BSession->setFlash('save_error');
            echo false;
            exit;
        }

        echo true;
        exit;
    }    
}
