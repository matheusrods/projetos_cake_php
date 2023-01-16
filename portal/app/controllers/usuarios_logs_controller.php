<?php
class UsuariosLogsController extends AppController {
	var $name = 'UsuariosLogs';
	var $uses = array('UsuarioLog', 'Usuario');
    var $helpers = array('Paginator');

    function listar($codigo_usuario) {
        $this->layout = 'ajax';        
        $conditions['UsuarioLog.codigo_usuario'] = $codigo_usuario;
        $this->paginate['UsuarioLog'] = array(
            'conditions' => $conditions,
            'limit'  => 50,
            'order'  => 'UsuarioLog.data_alteracao DESC',
            'fields' => array(
                'Usuario.nome',
                'Usuario.codigo',
                'Usuario.apelido',
                'UsuarioLog.codigo_usuario_alteracao',
                'UsuarioLog.codigo_usuario_inclusao',
                'UsuarioLog.codigo_uperfil',
                'UsuarioLog.data_inclusao',
                'UsuarioLog.data_alteracao',
                'UsuarioLog.ativo',
                'UsuarioLog.nome',
                'UsuarioLog.apelido',
                'UsuarioLog.senha',
                'UsuarioLog.email',
                'Uperfil.descricao'
           ),
            'joins' => array(
                array(
                    'table'      => 'uperfis',
                    'alias'      => 'Uperfil',
                    'conditions' => 'Uperfil.codigo = UsuarioLog.codigo_uperfil',
                    'type'       => 'inner'
                ),
                array(
                    'table'      => 'usuario',
                    'alias'      => 'Usuario',
                    'conditions' => 'Usuario.codigo = UsuarioLog.codigo_usuario_alteracao',
                    'type'       => 'inner'
                )
            )
        );
        
        $usuarios_log = $this->paginate('UsuarioLog');
        $this->set(compact('usuarios_log'));
    }
}
?>