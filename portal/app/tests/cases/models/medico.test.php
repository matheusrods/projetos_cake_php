<?php
App::import('Model', 'Medico');
class MedicoTestCase extends CakeTestCase {
	var $fixtures = array(
        'app.medico',
    );

    function startTest() {
		$this->Medico = & ClassRegistry::init('Medico');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
    }
    function testInclusao(){
        $dados = array(
                    'codigo' => 1,
                    'codigo_conselho_profissional' => 1,
                    'data_inclusao' => '2016-04-26 10:00:00',
                    'nome' => 'MARCIA C. BRAGA',
                    'numero_conselho' => '52874442',
                    'conselho_uf' => 'RJ',
                );

        $resultado = $this->Medico->incluir($dados);
        $this->assertTrue($resultado);       
    }
    

    function endTest() {
        unset($this->Medico);
        ClassRegistry::flush();
    }
}
?>