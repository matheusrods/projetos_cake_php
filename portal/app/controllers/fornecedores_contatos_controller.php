<?php

class FornecedoresContatosController extends AppController {

    public $name = 'FornecedoresContatos';
    public $layout = 'fornecedores';
    public $components = array('RequestHandler');
    public $helpers = array('Html', 'Ajax');
    public $uses = array(
                        'FornecedorContato', 
                        'Fornecedor');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array(
            'contatos_por_fornecedores_agendamento'
        ));
    }

    function contatos_por_fornecedores($codigo_fornecedor) {
        $this->layout = 'ajax';
        $contatos = $this->FornecedorContato->find('all', array('conditions' => array('codigo_fornecedor' => $codigo_fornecedor)));
      
        $this->set(compact('codigo_fornecedor','contatos'));
    }
   
    function contatos_por_fornecedores_agendamento($codigo_fornecedor) {
        $this->layout = 'ajax';
      
        //Retorna contatos de fornecedores que foram checados
        $contatos_checados = $this->FornecedorContato->find('all', array('conditions' => array('codigo_fornecedor' => $codigo_fornecedor, 'checado' => 1, "codigo_tipo_retorno in (1,2,7,12,13)")));

        if (empty($contatos_checados)) {
            //Se não houver contatos checados, pega o primeiro contato do tipo retorno 'TELEFONE' e altera para checado
            $primeiro_contato = $this->FornecedorContato->find('first', array('conditions' => array('codigo_fornecedor' => $codigo_fornecedor, "codigo_tipo_retorno" => 1, "codigo_tipo_retorno in (1,2,7,12,13)"), 'order' => "FornecedorContato.codigo DESC"));

            if (!empty($primeiro_contato)) {

                $primeiro_contato['FornecedorContato']['checado'] = 1;

                $this->FornecedorContato->atualizar($primeiro_contato);
            };
        }

        //Retorna todos os contatos
        $contatos = $this->FornecedorContato->find('all', array('conditions' => array('codigo_fornecedor' => $codigo_fornecedor, "codigo_tipo_retorno in (1,2,7,12,13)")));
        
        //Retorna a descrição do fornecedor para popular o textarea
        $sql = "select top(1) descricao_contato from  fornecedores where codigo = {$codigo_fornecedor}";
        $retorno_fornecedor = $this->Fornecedor->query($sql);

        $descricao_contato = "";
        if (!empty($retorno_fornecedor)) {
            $descricao_contato = $retorno_fornecedor[0][0]['descricao_contato'];
        }
        
        $this->set(compact('codigo_fornecedor','contatos', 'descricao_contato'));
    }

    function incluir($codigo_fornecedor) {
        $this->layout = 'ajax';
        if($this->data['FornecedorContato']){
        
            $validate = true;
            
            if(empty($this->data['FornecedorContato']['nome'])){
                
               $this->FornecedorContato->invalidate('nome','Informe o Representante');
               $validate = false;
            } 
                                   
            if(empty($this->data['FornecedorContato']['codigo_tipo_contato'])){
                $this->FornecedorContato->invalidate('codigo_tipo_contato','Informe o tipo de contato');
                $validate = false;
            }


           

            switch ($this->data['FornecedorContato']) {
                
                case ($this->data['FornecedorContato']['codigo_tipo_retorno']==NULL): 
                    $this->FornecedorContato->invalidate('codigo_tipo_retorno','Informe o retorno.');
                    $validate = false;
                    return $validate;
                

                case ($this->data['FornecedorContato']['codigo_tipo_retorno']==1 && $this->data['FornecedorContato']['descricao']==NULL): 
                    $this->FornecedorContato->invalidate('descricao','Informe o número de telefone.');
                    $validate = false;
                    return $validate;

                case ($this->data['FornecedorContato']['codigo_tipo_retorno']==2 && $this->data['FornecedorContato']['descricao']==NULL): 
                    $this->FornecedorContato->invalidate('descricao','Informe o e-mail.');
                    $validate = false;
                    return $validate;  
                     

                case ($this->data['FornecedorContato']['codigo_tipo_retorno']==3 && $this->data['FornecedorContato']['descricao']==NULL): 
                    $this->FornecedorContato->invalidate('descricao','Informe o número de fax.');
                    $validate = false;
                    return $validate;
                        

                case ($this->data['FornecedorContato']['codigo_tipo_retorno']==4 && $this->data['FornecedorContato']['descricao']==NULL): 
                    $this->FornecedorContato->invalidate('descricao','Informe o número 0800.');
                    $validate = false;
                    return $validate;
                    

                case ($this->data['FornecedorContato']['codigo_tipo_retorno']==5 && $this->data['FornecedorContato']['descricao']==NULL): 
                    $this->FornecedorContato->invalidate('descricao','Informe o celular do motorista.');
                    $validate = false;
                    return $validate;
                
                case ($this->data['FornecedorContato']['codigo_tipo_retorno']==6 && $this->data['FornecedorContato']['descricao']==NULL): 
                    $this->FornecedorContato->invalidate('descricao','Informe o número do radio.');
                    $validate = false;
                    return $validate;

                default:    
            }
              
            if ($validate){
                if (!empty($this->data)) {
                    
                    $this->data['FornecedorContato']['codigo_fornecedor'] = $codigo_fornecedor;
                    $data = $this->formataInclusao($this->data['FornecedorContato']);
                    if ($this->FornecedorContato->incluirContato($data)) {
                        $this->BSession->setFlash('save_success');
                    } else {
                        $this->BSession->setFlash('save_error');
                    }
                } 
            }else {
                $this->data['FornecedorContato']['codigo_fornecedor'] = $codigo_fornecedor;
            }    
        }    
        
        $tipos_contato = $this->FornecedorContato->TipoContato->find('list',array('conditions' => array('fornecedor = 1')));
        $tipos_retorno = $this->FornecedorContato->TipoRetorno->find('list', array("conditions" => array('ativo' => 1)));
        $this->set(compact('tipos_contato', 'tipos_retorno', 'codigo_fornecedor'));
    }
      
    public function excluir($codigo) {
   
    if ($this->FornecedorContato->excluir($codigo)) {
        $this->BSession->setFlash('save_success');
        echo 1;
    } else {
        $this->BSession->setFlash('save_error');
        echo 0;
    }

    exit;
    }

    function editar($codigo_fornecedor, $codigo){
        $this->layout = 'ajax';
        if(!empty($this->data['FornecedorContato'])) {

            $data = $this->formata($this->data);

            if ($this->FornecedorContato->atualizar($data)) {
                
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->FornecedorContato->find('first', array('conditions' => array('FornecedorContato.codigo' => $codigo)));

            if($this->data['TipoRetorno']['codigo'] == 7 || $this->data['TipoRetorno']['codigo'] == 12 || $this->data['TipoRetorno']['codigo'] == 1){
                $this->data['FornecedorContato']['descricao'] = Comum::formatarTelefone($this->data['FornecedorContato']['ddd'].$this->data['FornecedorContato']['descricao']);                
            } else {
                $this->data['FornecedorContato']['descricao'] = $this->data['FornecedorContato']['ddd'].$this->data['FornecedorContato']['descricao'];
            }
    
        }

        $tipos_contato = $this->FornecedorContato->TipoContato->find('list',array('conditions' => array('fornecedor = 1')));
        $tipos_retorno = $this->FornecedorContato->TipoRetorno->find('list', array('conditions' => array("ativo" => 1)));
        $this->set(compact('tipos_contato', 'tipos_retorno', 'codigo_fornecedor', 'codigo'));
        
    }
    
    
    function formataInclusao($data) {
        $contatos = array();
        
        $contatos[] = $this->formata(array('FornecedorContato' => $data));
        return $contatos;
    }
    
    function formata($data) {
        
        if (in_array($data['FornecedorContato']['codigo_tipo_retorno'], array(1,3,5))) {
            $fone = Comum::soNumero($data['FornecedorContato']['descricao']);
            $data['FornecedorContato']['ddd'] = substr($fone,0,2);
            $data['FornecedorContato']['descricao'] = substr($fone,2);
        }
        return $data;
    }
    
}