<?php class ExcecoesFormulasController extends AppController {
    public $name = 'ExcecoesFormulas';
    var $uses = array('ExcecaoFormula', 'Produto');

    function index() {
        $this->pageTitle = 'Exceções para Fórmulas Naveg';
        $excecoes = $this->ExcecaoFormula->find('all');
        $this->set(compact('excecoes'));
    }

    private function carrega_combos() {
        $produtos = $this->Produto->find('list', array('conditions' => array('ativo' => 1)));
        $this->set(compact('produtos'));
    }

    function incluir() {
        $this->pageTitle = 'Incluir Exceção para Fórmulas Naveg';
        if (!empty($this->data)) {
            if ($this->ExcecaoFormula->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');                
            }
        } else {
            $this->data = array('ExcecaoFormula' => array(
                'valor_acima_irrf' => 0,
                'valor_acima_formula' => 0,
                'percentual_irrf' => 0,
            ));
        }
        $this->carrega_combos();
    }

    function editar($codigo) {
        $this->pageTitle = 'Editar Excecão para Fórmulas Naveg';
        $this->carrega_combos();
        if (!empty($this->data)) {
            if ($this->ExcecaoFormula->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');                
            }
        } else {
            $this->data = $this->ExcecaoFormula->carregar($codigo);
        }
    }

    function excluir($codigo) {
        if ($this->ExcecaoFormula->excluir($codigo)) {
            $this->BSession->setFlash('delete_success');
        } else {
            $this->BSession->setFlash('delete_error');
        }
        $this->redirect(array('action' => 'index'));
    }
}