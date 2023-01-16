<?php
class ReguladoresContatosController extends AppController {
    public $name = 'ReguladoresContatos';
    public $components = array('RequestHandler');
    public $helpers = array('Html','Ajax');
    public $uses = array('ReguladorContato','TipoContato');

    function contatos_por_regulador($codigo_regulador) {
        $this->layout = 'ajax';
        $contatos = $this->ReguladorContato->contatosDoRegulador($codigo_regulador);
        $this->set(compact('contatos'));
    }

    function incluir($codigo_regulador) {
        $this->layout = 'ajax';
        $tipos_contato = $this->ReguladorContato->TipoContato->find('list');
        $tipos_retorno = $this->ReguladorContato->TipoRetorno->find('list');
        $this->set(compact('tipos_contato', 'tipos_retorno'));        
        
        if($this->data['ReguladorContato']){
            $validate = true;
            $validate = $this->ReguladorContato->valida_contato($this->data['ReguladorContato']);
                        
            if ($validate){
                if (!empty($this->data)) {
                    $this->data['ReguladorContato']['codigo_regulador'] = $codigo_regulador;
                    $data = $this->formataInclusao($this->data['ReguladorContato']);
                    if ($this->ReguladorContato->incluirContato($data)) {
                        $this->BSession->setFlash('save_success');                    
                    } else {
                      $this->BSession->setFlash('save_error');
                    }
                }
            }else {
                $this->data['ReguladorContato']['codigo_regulador'] = $codigo_regulador;
            }    
        }
    }
    
    function excluir($codigo_regulador_contato) {
        if ($this->RequestHandler->isPost()) {
            if ($this->ReguladorContato->excluir($codigo_regulador_contato))
                die();                
        }
    }
    
    function editar($codigo){
        $this->layout = 'ajax';
        if (!empty($this->data)) {
            $data = $this->formata($this->data);            
            if ($this->ReguladorContato->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');                
            } else {
                $this->BSession->setFlash('save_error');                
            }
        } else {
            $this->data = $this->ReguladorContato->read(null, $codigo);
            $this->data['ReguladorContato']['descricao'] = $this->data['ReguladorContato']['ddd'].$this->data['ReguladorContato']['descricao'];           
        }         
        $tipos_contato = $this->ReguladorContato->TipoContato->find('list');
        $tipos_retorno = $this->ReguladorContato->TipoRetorno->find('list');
        $this->set(compact('tipos_contato', 'tipos_retorno'));
    }
    
    
    function formataInclusao($data) {
        $contatos[] = $this->formata(array('ReguladorContato' => $data));        
        return $contatos;
    }
    
    function formata($data) {
        if (in_array($data['ReguladorContato']['codigo_tipo_retorno'], array(1,3,5))) {
            $fone = Comum::soNumero($data['ReguladorContato']['descricao']);
            $data['ReguladorContato']['ddd'] = substr($fone,0,2);
            $data['ReguladorContato']['descricao'] = substr($fone,2);
        }
        return $data;
    }
    
}