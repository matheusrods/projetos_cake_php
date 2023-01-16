<?php 
App::import('Helper', 'Buonny');
App::import('Helper', 'Form');
App::import('Helper', 'Html');
App::import('Helper', 'BForm');
App::import('Helper', 'Javascript');
class BuonnyTest extends CakeTestCase {
	function startTest() {
		$this->buonny = new BuonnyHelper();
		$this->view = ClassRegistry::init('view');
		$this->view->Form = new FormHelper();
		$this->view->Html = new HtmlHelper();
		$this->view->BForm = new BFormHelper();
		$this->view->BForm->Html = new HtmlHelper();
		$this->view->Javascript = new JavascriptHelper();
	}

	function testBuonnyInstance() {
		$this->assertTrue(is_a($this->buonny, 'BuonnyHelper'));
	}
	
	function testComboEstadoCidade() {
		$result = $this->buonny->combo_estado_cidade($this->view, 'Teste.estado', 'Teste.cidade', array('1'=>'Sampa'), array('1'=>'Santos'));
		
		$expected = '<div class="control-group input select"><label for="TesteEstado">Estado</label><select name="data[Teste][estado]" class="input-mini estado" id="TesteEstado">
<option value="">Estado</option>
<option value="1">Sampa</option>
</select></div><div class="control-group input select"><label for="TesteCidade">Cidade</label><select name="data[Teste][cidade]" class="input-large cidade" id="TesteCidade">
<option value="">Cidade</option>
<option value="1">Santos</option>
</select></div><script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function(){ $(\'#TesteEstado\').change(function() { buscar_cidade(this, \'#TesteCidade\'); }); });
//]]>
</script>';
		$this->assertEqual($expected, $result);
	}
}
?>