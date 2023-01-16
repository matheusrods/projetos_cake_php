<?php

class PrestadoresContatosController extends AppController {
    public $name = 'PrestadoresContatos';
    public $components = array('RequestHandler');
    public $helpers = array('Html','Ajax');
    public $uses = array('PrestadorContato','TipoContato');

    function contatos_por_prestador($codigo_prestador) {
        $this->layout = 'ajax';
        $contatos = $this->PrestadorContato->contatosDoPrestador($codigo_prestador);
        $this->set(compact('contatos'));
    }
    
    function incluir($codigo_prestador) {
        $this->layout = 'ajax';
        $tipos_contato = $this->PrestadorContato->TipoContato->find('list');
        $tipos_retorno = $this->PrestadorContato->TipoRetorno->find('list');
        $this->set(compact('tipos_contato', 'tipos_retorno'));        
        if($this->data['PrestadorContato']){
        
            $validate = true;
            
            if(empty($this->data['PrestadorContato']['nome'])){
                
               $this->PrestadorContato->invalidate('nome','Informe o Representante');
               $validate = false;
            } 
                                   
            if(empty($this->data['PrestadorContato']['codigo_tipo_contato'])){
                $this->PrestadorContato->invalidate('codigo_tipo_contato','Informe o tipo de contato');
                $validate = false;
            }

            switch ($this->data['PrestadorContato']) {
                
                case ($this->data['PrestadorContato']['codigo_tipo_retorno']==NULL): 
                    $this->PrestadorContato->invalidate('codigo_tipo_retorno','Informe o retorno.');
                    $validate = false;
                    return $validate;
                

                case ($this->data['PrestadorContato']['codigo_tipo_retorno']==1 && $this->data['PrestadorContato']['descricao']==NULL): 
                    $this->PrestadorContato->invalidate('descricao','Informe o número de telefone.');
                    $validate = false;
                    return $validate;

                case ($this->data['PrestadorContato']['codigo_tipo_retorno']==2 && ($this->data['PrestadorContato']['descricao']==NULL || (COMUM::validaEmail($this->data['PrestadorContato']['descricao']) == NULL) ) ) : 
                    $this->PrestadorContato->invalidate('descricao','Informe um e-mail válido.');
                    $validate = false;
                    return $validate;  
                     

                case ($this->data['PrestadorContato']['codigo_tipo_retorno']==3 && $this->data['PrestadorContato']['descricao']==NULL): 
                    $this->PrestadorContato->invalidate('descricao','Informe o número de fax.');
                    $validate = false;
                    return $validate;
                        

                case ($this->data['PrestadorContato']['codigo_tipo_retorno']==4 && $this->data['PrestadorContato']['descricao']==NULL): 
                    $this->PrestadorContato->invalidate('descricao','Informe o número 0800.');
                    $validate = false;
                    return $validate;
                    

                case ($this->data['PrestadorContato']['codigo_tipo_retorno']==5 && $this->data['PrestadorContato']['descricao']==NULL): 
                    $this->PrestadorContato->invalidate('descricao','Informe o celular do motorista.');
                    $validate = false;
                    return $validate;
                
                case ($this->data['PrestadorContato']['codigo_tipo_retorno']==6 && $this->data['PrestadorContato']['descricao']==NULL): 
                    $this->PrestadorContato->invalidate('descricao','Informe o número do radio.');
                    $validate = false;
                    return $validate;

                default:    
            }
            if ($validate){
                if (!empty($this->data)) {
                    $this->data['PrestadorContato']['codigo_prestador'] = $codigo_prestador;
                    $data = $this->formataInclusao($this->data['PrestadorContato']);
                    if ($this->PrestadorContato->incluirContato($data)) {
                        $this->BSession->setFlash('save_success');                    
                    } else {
                      $this->BSession->setFlash('save_error');
                    }
                }
            }else {
                $this->data['PrestadorContato']['codigo_prestador'] = $codigo_prestador;
            }    
        }
    }
    
    function excluir($codigo_prestador_contato) {
        if ($this->RequestHandler->isPost()) {
            if ($this->PrestadorContato->excluir($codigo_prestador_contato))
                die();                
        }
    }
    
    function editar($codigo){
        $this->layout = 'ajax';
        if (!empty($this->data)) {
            $data = $this->formata($this->data);            
            if ($this->PrestadorContato->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');                
            } else {
                $this->BSession->setFlash('save_error');                
            }
        } else {
            $this->data = $this->PrestadorContato->read(null, $codigo);
            $this->data['PrestadorContato']['descricao'] = $this->data['PrestadorContato']['ddd'].$this->data['PrestadorContato']['descricao'];           
        }         
        $tipos_contato = $this->PrestadorContato->TipoContato->find('list');
        $tipos_retorno = $this->PrestadorContato->TipoRetorno->find('list');
        $this->set(compact('tipos_contato', 'tipos_retorno'));
    }
    
    
    function formataInclusao($data) {
        $contatos[] = $this->formata(array('PrestadorContato' => $data));        
        return $contatos;
    }
    
    function formata($data) {
        if (in_array($data['PrestadorContato']['codigo_tipo_retorno'], array(1,3,5))) {
            $fone = Comum::soNumero($data['PrestadorContato']['descricao']);
            $data['PrestadorContato']['ddd'] = substr($fone,0,2);
            $data['PrestadorContato']['descricao'] = substr($fone,2);
        }
        return $data;
    }
    
}