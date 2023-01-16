<?php
class RelatoriosEmailsController extends AppController {
	public $name = 'RelatoriosEmails';
	public $uses = array('RelatorioEmail');	
    var $helpers = array('Paginator');
	
	function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('*'));
	}
	
	public function incluir( $model = false, $action = false ){
		$authUsuario = $this->BAuth->user();
		$email = explode(';', $authUsuario['Usuario']['email'] );
		$this->data['RelatorioEmail']['email'] 		= (isset($email[0]) ? $email[0] : NULL);
		$this->data['RelatorioEmail']['conditions'] = $this->Session->read('conditions'.$model );
		$this->data['RelatorioEmail']['metodo'] 	= $action;
		$erros = NULL;
		$this->data['RelatorioEmail']['anexo_nome'] = $action.date("YmdHis").$authUsuario['Usuario']['codigo'];
		if( !$this->RelatorioEmail->incluir($this->data) ){
			$erros = $this->RelatorioEmail->validationErrors;
		}
		$this->set(compact('model', 'action', 'erros' ));
	}
}
?>