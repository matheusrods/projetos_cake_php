<?php
class AclCase extends CakeTestCase {
	var $fixtures = array('app.uperfil', 'app.acos', 'app.aros', 'app.aros_acos');
	
	public function startTest($method){
		Configure::write('Acl.database', 'test');
		App::import('Component', 'Acl');
		$this->Acl = new AclComponent();
	}
	
	function testAclGrants() {
		$codigo_perfil = 1;
		$this->assertTrue($this->Acl->check(array('model' => 'UPerfil', 'foreign_key' => $codigo_perfil), 'buonny'));
		$this->assertTrue($this->Acl->check(array('model' => 'UPerfil', 'foreign_key' => $codigo_perfil), 'buonny/Painel'));
		$this->assertTrue($this->Acl->check(array('model' => 'UPerfil', 'foreign_key' => $codigo_perfil), 'buonny/Painel/modulo_admin'));
		$codigo_perfil = 2;
		$this->assertFalse($this->Acl->check(array('model' => 'UPerfil', 'foreign_key' => $codigo_perfil), 'buonny'));
		$this->assertFalse($this->Acl->check(array('model' => 'UPerfil', 'foreign_key' => $codigo_perfil), 'buonny/Painel'));
		$this->assertFalse($this->Acl->check(array('model' => 'UPerfil', 'foreign_key' => $codigo_perfil), 'buonny/Painel/modulo_admin'));
		$this->assertTrue($this->Acl->check(array('model' => 'UPerfil', 'foreign_key' => $codigo_perfil), 'buonny/Painel/modulo_financeiro'));
	}
	
	function endTest() {
        unset($this->Acl);
        ClassRegistry::flush();
    }
}