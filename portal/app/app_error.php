<?php
class AppError extends ErrorHandler {
    public function missingController($params) {
	    $this->renderError404($params);
	}
	
	public function missingAction($params) {
		$this->renderError404($params);
	}
	
	private function renderError404($params){
		$this->controller->layout = 'erro';
		parent::error404($params);
	}

	function error401($params){
		
		if(isset($_GET['url']) && isset($_SESSION['Auth']['Usuario']['apelido'])) {
			$this->log($_SESSION['Auth']['Usuario']['apelido'] . ": " . $_GET['url'], 'permissao');
		}
		
		header('HTTP/1.1 401 Unauthorized');
		$this->controller->layout = 'ajax';
		$this->_outputMessage('error401');
	}

	private function redirectToPainel() {
		$this->controller->Session->setFlash('O caminho que vc está tentando acessar não existe!');
		$this->controller->redirect(array('controller'=>'painel', 'action'=>'admin_index'));
	}
}
?>