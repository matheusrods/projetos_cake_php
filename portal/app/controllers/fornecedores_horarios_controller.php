<?php
class FornecedoresHorariosController extends AppController {
    public $name = 'FornecedoresHorarios';
    var $uses = array('FornecedorHorario');
    
    
    function listagem($codigo_fornecedor){
        $this->layout = 'ajax';

        $dados_horario = $this->FornecedorHorario->find('all', array('conditions' => array('codigo_fornecedor' => $codigo_fornecedor)));
        $this->set(compact('dados_horario','codigo_fornecedor'));
    }


    function incluir($codigo_fornecedor) {
        $this->pageTitle = 'Novos Horários';
        $this->layout = 'ajax';

        if($this->RequestHandler->isPost()) {
            
            if(!empty($this->data['FornecedorHorario']['dias_semana'])){
                $dias_semana="";
                foreach($this->data['FornecedorHorario']['dias_semana'] as $key => $value) {
                    $dias_semana .= $value.",";
                }
                $this->data['FornecedorHorario']['dias_semana'] = substr($dias_semana,0,-1);
            }

            $this->data['FornecedorHorario']['de_hora'] = Comum::soNumero($this->data['FornecedorHorario']['de_hora']);
            $this->data['FornecedorHorario']['ate_hora'] = Comum::soNumero($this->data['FornecedorHorario']['ate_hora']);

            if ($this->FornecedorHorario->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }
        $this->set(compact('codigo_fornecedor'));
    }

    public function excluir($codigo) {
      
      if ($this->FornecedorHorario->excluir($codigo)) {
          $this->BSession->setFlash('save_success');
          echo 1;
      } else {
          $this->BSession->setFlash('save_error');
          echo 0;
      }

      exit;
    }
}