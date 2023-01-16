<?php
class UsuariosContatosController extends AppController {
	public $name = 'UsuariosContatos';
    public $layout = 'cliente';
    public $components = array('RequestHandler');
    public $helpers = array('Html', 'Ajax');
	public $uses = array('UsuarioContato');

    function contatos_por_usuario($codigo_usuario) {
        $this->layout = 'ajax';
        $this->data = $this->UsuarioContato->contatosDoUsuario($codigo_usuario);
    }
    
    function contatos_por_usuario_visualizar($codigo_usuario) {
        $this->layout = 'ajax';
        $this->data = $this->UsuarioContato->contatosDoUsuario($codigo_usuario);
    }
    
    function incluir($codigo_usuario) {
        $this->layout = 'ajax';
        if (!empty($this->data)) {
            $data = $this->formataInclusao($this->data['UsuarioContato']);
            if ($this->UsuarioContato->incluirContato($data)) {
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data['UsuarioContato'][0]['codigo_usuario'] = $codigo_usuario;
        }
        $conditions['TipoRetorno.usuario_interno'] = true;
        $tipos_retorno = $this->UsuarioContato->TipoRetorno->find('list', array('conditions' => $conditions));
        $this->set(compact('tipos_retorno'));
    }
    
    function excluir($codigo_usuario_contato) {
        if ($this->RequestHandler->isPost()) {
            if ($this->UsuarioContato->excluir($codigo_usuario_contato)) {
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
            if ($this->UsuarioContato->atualizar($data)) {
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->UsuarioContato->read(null, $codigo);
            $this->data['UsuarioContato']['descricao'] = $this->data['UsuarioContato']['ddd'].$this->data['UsuarioContato']['descricao'];
        } 
        $conditions['TipoRetorno.usuario_interno'] = true;
        $tipos_retorno = $this->UsuarioContato->TipoRetorno->find('list', array('conditions' => $conditions));
        $this->set(compact('tipos_contato', 'tipos_retorno'));
    }
    
    function formataInclusao($data) {
        $contatos = array();
        foreach ($data as $usuario_contato) {
            $contatos[] = $this->formata(array('UsuarioContato' => $usuario_contato));
        }
        return $contatos;
    }
    
    function formata($data) {
        if (in_array($data['UsuarioContato']['codigo_tipo_retorno'], array(1,3,5,7,8,9,11))) {
            $fone = Comum::soNumero($data['UsuarioContato']['descricao']);
            $data['UsuarioContato']['ddd'] = substr($fone,0,2);
            $data['UsuarioContato']['descricao'] = substr($fone,2);
        } elseif ($data['UsuarioContato']['codigo_tipo_retorno']==10) {
            $data['UsuarioContato']['descricao'] = Comum::soNumero($data['UsuarioContato']['descricao']);
        }
        return $data;
    }

}
?>