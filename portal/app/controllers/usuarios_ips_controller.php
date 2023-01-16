<?php
class UsuariosIpsController extends AppController {
	var $name = 'UsuariosIps';
	var $uses = array('Usuario', 'UsuarioIp');
    var $helpers = array('Paginator');

    function listar($codigo_usuario) {
        $this->layout = 'ajax';        
        $conditions['UsuarioIp.codigo_usuario'] = $codigo_usuario;
        $this->paginate['UsuarioIp'] = array(
            'conditions' => $conditions,
            'limit'  => 50,
            'order'  => 'Usuario.nome',
            'fields' => array(
                'UsuarioIp.endereco_ip',
                'UsuarioIp.codigo',
                'UsuarioIp.codigo_usuario_inclusao',
                'UsuarioIp.data_inclusao',
                'Usuario.nome'
           ),
            'joins' => array(
                array(
                    'table'      => 'RHHealth.dbo.usuario',
                    'alias'      => 'Usuario',
                    'conditions' => 'Usuario.codigo = UsuarioIp.codigo_usuario_inclusao',
                    'type'       => 'inner'
                )
            )
        );
        $usuario_ips = $this->paginate('UsuarioIp');
        $this->set(compact('usuario_ips', 'codigo_usuario'));
    }

    function excluir($codigo){
        $this->layout = 'ajax';
        if(!is_numeric($codigo))
            exit;
        $codigo = trim($codigo);
        if ($this->UsuarioIp->delete($codigo)) {
            $this->BSession->setFlash('save_success');
        } else {
            debug( $this->UsuarioIp->validationError);
            $this->BSession->setFlash('save_error');
        }
    }


    function incluir( $codigo_usuario ){
        if( $this->data ){
            $this->data['UsuarioIp']['codigo_usuario'] = $codigo_usuario;
            if ($this->UsuarioIp->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }                    
        }
        $this->set(compact('codigo_usuario'));
    }

}
?>