<?php

class CorretorasContatosController extends AppController {

    public $name = 'CorretorasContatos';
    public $layout = 'corretoras';
    public $components = array('RequestHandler');
    public $helpers = array('Html', 'Ajax');
    public $uses = array('CorretoraContato');

    function contatos_por_corretoras($codigo_corretora) {
        
        $this->layout = 'ajax';
        

        $contatos = $this->CorretoraContato->contatosDaCorretora($codigo_corretora);
        $this->set(compact('contatos'));
    }
    
    function incluir($codigo_corretora) {
        $this->layout = 'ajax';
        $tipos_contato = $this->CorretoraContato->TipoContato->find('list',array('conditions' => array('corretora = 1')));
        $tipos_retorno = $this->CorretoraContato->TipoRetorno->find('list');
        $this->set(compact('tipos_contato', 'tipos_retorno'));
        if($this->data['CorretoraContato']){
        
            $validate = true;
            
            if(empty($this->data['CorretoraContato']['nome'])){
                
               $this->CorretoraContato->invalidate('nome','Informe o Representante');
               $validate = false;
            } 
                                   
            if(empty($this->data['CorretoraContato']['codigo_tipo_contato'])){
                $this->CorretoraContato->invalidate('codigo_tipo_contato','Informe o tipo de contato');
                $validate = false;
            }


           

            switch ($this->data['CorretoraContato']) {
                
                case ($this->data['CorretoraContato']['codigo_tipo_retorno']==NULL): 
                    $this->CorretoraContato->invalidate('codigo_tipo_retorno','Informe o retorno.');
                    $validate = false;
                    return $validate;
                

                case ($this->data['CorretoraContato']['codigo_tipo_retorno']==1 && $this->data['CorretoraContato']['descricao']==NULL): 
                    $this->CorretoraContato->invalidate('descricao','Informe o número de telefone.');
                    $validate = false;
                    return $validate;

                case ($this->data['CorretoraContato']['codigo_tipo_retorno']==2 && $this->data['CorretoraContato']['descricao']==NULL): 
                    $this->CorretoraContato->invalidate('descricao','Informe o e-mail.');
                    $validate = false;
                    return $validate;  
                     

                case ($this->data['CorretoraContato']['codigo_tipo_retorno']==3 && $this->data['CorretoraContato']['descricao']==NULL): 
                    $this->CorretoraContato->invalidate('descricao','Informe o número de fax.');
                    $validate = false;
                    return $validate;
                        

                case ($this->data['CorretoraContato']['codigo_tipo_retorno']==4 && $this->data['CorretoraContato']['descricao']==NULL): 
                    $this->CorretoraContato->invalidate('descricao','Informe o número 0800.');
                    $validate = false;
                    return $validate;
                    

                case ($this->data['CorretoraContato']['codigo_tipo_retorno']==5 && $this->data['CorretoraContato']['descricao']==NULL): 
                    $this->CorretoraContato->invalidate('descricao','Informe o celular do motorista.');
                    $validate = false;
                    return $validate;
                
                case ($this->data['CorretoraContato']['codigo_tipo_retorno']==6 && $this->data['CorretoraContato']['descricao']==NULL): 
                    $this->CorretoraContato->invalidate('descricao','Informe o número do radio.');
                    $validate = false;
                    return $validate;

                default:    
            }
              
            if ($validate){
                if (!empty($this->data)) {
                    
                    $this->data['CorretoraContato']['codigo_corretora'] = $codigo_corretora;
                    $data = $this->formataInclusao($this->data['CorretoraContato']);
                    //debug($this->data);die();
                    if ($this->CorretoraContato->incluirContato($data)) {
                        $this->BSession->setFlash('save_success');
                        //$this->redirect(array('action' => 'contatos_por_corretoras'));
                    } else {
                        $this->BSession->setFlash('save_error');
                    }
                } 
            }else {
                $this->data['CorretoraContato']['codigo_corretora'] = $codigo_corretora;
            }    
        }    
        
    }
    
    function excluir($codigo_corretora_contato) {
        
     
        if ($this->CorretoraContato->delete($codigo_corretora_contato)) {
            $this->BSession->setFlash('delete_success');
        
        } else {
            $this->BSession->setFlash('delete_error');
        }

        exit;
    }
    
    function editar($codigo){
        $this->layout = 'ajax';
        $tipos_contato = $this->CorretoraContato->TipoContato->find('list',array('conditions' => array('corretora = 1')));
        $tipos_retorno = $this->CorretoraContato->TipoRetorno->find('list');
        $this->set(compact('tipos_contato', 'tipos_retorno'));
        
        if (!empty($this->data)) {
               
            $data = $this->formata($this->data);
            
            if ($this->CorretoraContato->atualizar($data)) {
                
                $this->BSession->setFlash('save_success');
                
            
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->CorretoraContato->read(null, $codigo);
            $this->data['CorretoraContato']['descricao'] = $this->data['CorretoraContato']['ddd'].$this->data['CorretoraContato']['descricao'];
           
        } 
         
        
    }
    
    
    function formataInclusao($data) {
        $contatos = array();
        
        $contatos[] = $this->formata(array('CorretoraContato' => $data));
        return $contatos;
    }
    
    function formata($data) {
        
        if (in_array($data['CorretoraContato']['codigo_tipo_retorno'], array(1,3,5))) {
            $fone = Comum::soNumero($data['CorretoraContato']['descricao']);
            $data['CorretoraContato']['ddd'] = substr($fone,0,2);
            $data['CorretoraContato']['descricao'] = substr($fone,2);
        }
        return $data;
    }
    
}