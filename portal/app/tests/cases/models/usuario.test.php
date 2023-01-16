<?php
App::import('Model', 'Departamento');
class UsuarioTestCase extends CakeTestCase {
	var $fixtures = array(
        'app.usuario', 
        'app.usuario_log', 
        'app.usuario_perfil', 
        'app.uperfil', 
    );

    function startTest() {
        Configure::write('Acl.database', 'test');
        App::import('Component', 'Acl');
        $this->Acl = new AclComponent();
        $this->Usuario =& ClassRegistry::init('Usuario');
        $this->UsuarioPerfil =& ClassRegistry::init('UsuarioPerfil');
        $_SESSION['Auth']['Usuario']['codigo'] = 1;
    }

    
    function endTest() {
        unset($this->Usuario);
        ClassRegistry::flush();
    }
}
?>