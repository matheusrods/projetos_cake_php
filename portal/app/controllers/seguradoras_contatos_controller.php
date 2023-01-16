<?php

class SeguradorasContatosController extends AppController {

    public $name = 'SeguradorasContatos';
    public $layout = 'seguradoras';
    public $components = array('RequestHandler');
    public $helpers = array('Html','Ajax');
    public $uses = array('SeguradoraContato','TipoContato');

    function contatos_por_seguradoras($codigo_seguradora) {
        $this->layout = 'ajax';
        $contatos = $this->SeguradoraContato->contatosDaSeguradora($codigo_seguradora);
        $this->set(compact('contatos'));
    }
    
    function incluir($codigo_seguradora) {
        $this->layout = 'ajax';
        $tipos_contato = $this->SeguradoraContato->TipoContato->find('list');
        $tipos_retorno = $this->SeguradoraContato->TipoRetorno->find('list');
        $this->set(compact('tipos_contato', 'tipos_retorno'));
        
        if($this->data['SeguradoraContato']){
        
            $validate = true;
            
            if(empty($this->data['SeguradoraContato']['nome'])){
                
               $this->SeguradoraContato->invalidate('nome','Informe o Representante');
               $validate = false;
            } 
                                   
            if(empty($this->data['SeguradoraContato']['codigo_tipo_contato'])){
                $this->SeguradoraContato->invalidate('codigo_tipo_contato','Informe o tipo de contato');
                $validate = false;
            }

            switch ($this->data['SeguradoraContato']) {
                
                case ($this->data['SeguradoraContato']['codigo_tipo_retorno']==NULL): 
                    $this->SeguradoraContato->invalidate('codigo_tipo_retorno','Informe o retorno.');
                    $validate = false;
                    return $validate;
                

                case ($this->data['SeguradoraContato']['codigo_tipo_retorno']==1 && $this->data['SeguradoraContato']['descricao']==NULL): 
                    $this->SeguradoraContato->invalidate('descricao','Informe o número de telefone.');
                    $validate = false;
                    return $validate;

                case ($this->data['SeguradoraContato']['codigo_tipo_retorno']==2 && $this->data['SeguradoraContato']['descricao']==NULL): 
                    $this->SeguradoraContato->invalidate('descricao','Informe o e-mail.');
                    $validate = false;
                    return $validate;  
                     

                case ($this->data['SeguradoraContato']['codigo_tipo_retorno']==3 && $this->data['SeguradoraContato']['descricao']==NULL): 
                    $this->SeguradoraContato->invalidate('descricao','Informe o número de fax.');
                    $validate = false;
                    return $validate;
                        

                case ($this->data['SeguradoraContato']['codigo_tipo_retorno']==4 && $this->data['SeguradoraContato']['descricao']==NULL): 
                    $this->SeguradoraContato->invalidate('descricao','Informe o número 0800.');
                    $validate = false;
                    return $validate;
                    

                case ($this->data['SeguradoraContato']['codigo_tipo_retorno']==5 && $this->data['SeguradoraContato']['descricao']==NULL): 
                    $this->SeguradoraContato->invalidate('descricao','Informe o celular do motorista.');
                    $validate = false;
                    return $validate;
                
                case ($this->data['SeguradoraContato']['codigo_tipo_retorno']==6 && $this->data['SeguradoraContato']['descricao']==NULL): 
                    $this->SeguradoraContato->invalidate('descricao','Informe o número do radio.');
                    $validate = false;
                    return $validate;

                default:    
            }
              
            if ($validate){
                if (!empty($this->data)) {
                    $this->data['SeguradoraContato']['codigo_seguradora'] = $codigo_seguradora;
                    $data = $this->formataInclusao($this->data['SeguradoraContato']);
                    if ($this->SeguradoraContato->incluirContato($data)) {
                    $this->BSession->setFlash('save_success');
                    
                    } else {
                      $this->BSession->setFlash('save_error');
                    }
                }
            }else {
                $this->data['SeguradoraContato']['codigo_seguradora'] = $codigo_seguradora;
            }    
        }
    }
    
    function excluir($codigo_seguradora_contato) {
        if ($this->RequestHandler->isPost()) {
            if ($this->SeguradoraContato->excluir($codigo_seguradora_contato)) {
                $this->BSession->setFlash('delete_success');
            } else {
                $this->BSession->setFlash('delete_error');
            }
        }
        exit;
    }
    
    function editar($codigo){
        $this->layout = 'ajax';
        
        
        if (!empty($this->data)) {
               
            $data = $this->formata($this->data);
            
            if ($this->SeguradoraContato->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                
            } else {
                $this->BSession->setFlash('save_error');
                
            }
        } else {
            $this->data = $this->SeguradoraContato->read(null, $codigo);
            $this->data['SeguradoraContato']['descricao'] = $this->data['SeguradoraContato']['ddd'].$this->data['SeguradoraContato']['descricao'];
           
        } 
         
        $tipos_contato = $this->SeguradoraContato->TipoContato->find('list');
        $tipos_retorno = $this->SeguradoraContato->TipoRetorno->find('list');
        $this->set(compact('tipos_contato', 'tipos_retorno'));
    }
    
    
    function formataInclusao($data) {
       
        
        $contatos[] = $this->formata(array('SeguradoraContato' => $data));
        
        return $contatos;
    }
    
    function formata($data) {
        if (in_array($data['SeguradoraContato']['codigo_tipo_retorno'], array(1,3,5))) {
            $fone = Comum::soNumero($data['SeguradoraContato']['descricao']);
            $data['SeguradoraContato']['ddd'] = substr($fone,0,2);
            $data['SeguradoraContato']['descricao'] = substr($fone,2);
        }
        return $data;
    }
    
}