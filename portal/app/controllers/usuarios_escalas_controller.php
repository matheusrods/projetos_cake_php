<?php
class UsuariosEscalasController extends AppController {
	var $name = 'UsuariosEscalas';
	var $uses = array('Usuario', 'UsuarioEscala');

    function carrega_usuario_escala( $codigo_usuario ){
        $escala  = $this->UsuarioEscala->find('all', array(
            'conditions'=>array('codigo_usuario'=>$codigo_usuario),
            'order' => 'data_entrada ASC'
        ));
        $this->set(compact('escala', 'codigo_usuario'));
    }

    function incluir( $codigo_usuario ){
        if( $this->data ){
            $this->data['UsuarioEscala']['codigo_usuario'] = $codigo_usuario;

            if($this->UsuarioEscala->incluir($this->data)){
                $this->BSession->setFlash('save_success');
            }else{
                $this->BSession->setFlash('save_error');
            }
            $this->set(compact('codigo_usuario'));
        }
    }
    function excluir( $codigo ) {
        $this->layout = 'ajax';
        if (!empty($codigo)) {
            $this->UsuarioEscala->excluir($codigo);
        }
        exit;
    }

}
?>
