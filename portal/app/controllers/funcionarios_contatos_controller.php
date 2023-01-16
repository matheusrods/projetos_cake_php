<?php

class FuncionariosContatosController extends AppController {

    public $name = 'FuncionariosContatos';
    // public $layout = 'cliente';
    public $components = array('RequestHandler');
    public $helpers = array('Html', 'Ajax');
    
    public $uses = array(
    	'FuncionarioContato'
    );    

    function contatos_por_funcionario($codigo_funcionario) {
        $this->layout = 'ajax';
        $this->data = $this->FuncionarioContato->contatosDoFuncionario($codigo_funcionario);
    }
    
    function contatos_por_funcionario_visualizar($codigo_funcionario) {
        $this->layout = 'ajax';
        $this->data = $this->FuncionarioContato->contatosDoFuncionario($codigo_funcionario);
    }
    
    function excluir($codigo_funcionario_contato) {
    	if ($this->RequestHandler->isPost()) {
    		if ($this->FuncionarioContato->excluir($codigo_funcionario_contato)) {
    			$this->BSession->setFlash('delete_success');
    		} else {
    			$this->BSession->setFlash('delete_error');
    		}
    	}
    	exit;
    }
    
    function incluir($codigo_funcionario) {
    	$this->layout = 'ajax';
    	if (!empty($this->data)) {
    		$data = $this->formataInclusao($this->data['FuncionarioContato']);
    		if ($this->FuncionarioContato->incluirContato($data)) {
    			$this->BSession->setFlash('save_success');
    		} else {
    			$this->BSession->setFlash('save_error');
    		}
    	} else {
    		$this->data['FuncionarioContato'][0]['codigo_funcionario'] = $codigo_funcionario;
    	}
    
    	$tipos_contato = $this->FuncionarioContato->TipoContato->find('list',array('conditions' => array('funcionario = 1')));
    	$tipos_retorno = $this->FuncionarioContato->TipoRetorno->find('list');
    	$this->set(compact('tipos_contato', 'tipos_retorno'));
    }  
    
    function formataInclusao($data) {
    	$contatos = array();
    	foreach ($data as $funcionario_contato) {
    		$contatos[] = $this->formata(array('FuncionarioContato' => $funcionario_contato));
    	}
    	return $contatos;
    }

    function formata($data) {
    	if (in_array($data['FuncionarioContato']['codigo_tipo_retorno'], array(1,3,5))) {
    		$fone = Comum::soNumero($data['FuncionarioContato']['descricao']);
    		$data['FuncionarioContato']['ddd'] = substr($fone,0,2);
    		$data['FuncionarioContato']['descricao'] = substr($fone,2);
    	}
    	return $data;
    } 
    
    function editar($codigo){
    	$this->layout = 'ajax';
    	if (!empty($this->data)) {
    		$data = $this->formata($this->data);
    		if ($this->FuncionarioContato->atualizar($data)) {
    			$this->BSession->setFlash('save_success');
    		} else {
    			//$this->BSession->setFlash('save_error');
    		}
    	} else {
    		$this->data = $this->FuncionarioContato->read(null, $codigo);
    		$this->data['FuncionarioContato']['descricao'] = $this->data['FuncionarioContato']['ddd'].$this->data['FuncionarioContato']['descricao'];
    	}
    	$tipos_contato = $this->FuncionarioContato->TipoContato->find('list',array('conditions' => array('funcionario = 1')));
    	$tipos_retorno = $this->FuncionarioContato->TipoRetorno->find('list');
    	$this->set(compact('tipos_contato', 'tipos_retorno'));
    }    
    
    
       
    function listagem_data_cadastro($codigo_funcionario) {
        $contatos = $this->FuncionarioContato->contatosDoFuncionario($codigo_funcionario);
        $this->set(compact('contatos'));
    }

    function lista_contatos_funcionario( ){
        $codigo_funcionario      = $this->data['codigo_funcionario'];
        $codigo_tipo_retorno = $this->data['codigo_tipo_retorno'];
        $codigo_funcionario_contato = $this->data['codigo_funcionario_contato'];
        $tipo_exibicao       = (!empty($this->data['tipo_exibicao']) ? $this->data['tipo_exibicao']: NULL );
        $disabled_contato    = $this->data['disabled_contato'];        
        $incluir_contato     = (!empty($this->data['incluir_contato']) ? TRUE : FALSE);
        $listagem            = $this->FuncionarioContato->contatosDoFuncionario( $codigo_funcionario, $codigo_tipo_retorno );
        $tipos_contato       = $this->FuncionarioContato->TipoContato->find('list',array('conditions' => array('funcionario = 1')));
        $tipos_retorno       = $this->FuncionarioContato->TipoRetorno->find('list', array( 'conditions'=> array('codigo'=>$codigo_tipo_retorno )));
        $this->set(compact('listagem', 'tipo_exibicao', 'tipos_contato', 'tipos_retorno', 'codigo_funcionario', 'disabled_contato', 'incluir_contato', 'codigo_funcionario_contato' ));
    }    
}