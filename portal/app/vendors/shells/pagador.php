<?php
App::import('Component', 'StringView');
class PagadorShell extends Shell {
	var $uses = array('Recebsm');

	function main() {
		echo "**********************************************\n";
		echo "$ \n";
		echo "$ Pagador\n";
		echo "$ \n";
		echo "**********************************************\n\n";
		echo "=> ajustar: Verifica configurações e regrava o cliente pagador das SMs\n";
	}

	function ajustar() {
		$conditions = array('Recebsm.data_final BETWEEN ? AND ?' => array(Date('Ym01'), Date('Ymt'))) ;
		pr($conditions);
		pr(date('H:i:s'));
		$dados_contratos = array();
		$dados_pagador = $this->verificaPagador($conditions);
    	if ($this->Recebsm->ajustePagador($conditions)) {
    		$qtd_sm_sem_pagador = $this->Recebsm->smSemPagador($conditions);
    		$dados_contratos = $this->Recebsm->smSemContrato($conditions);
    		$this->notificar($dados_pagador, $dados_contratos, $qtd_sm_sem_pagador);
    		echo "Sucesso \n";
    	} else {
    		echo "Falha \n";
    	}
    	pr(date('H:i:s'));
    }

    private function verificaPagador($conditions) {
		$query_pagador = $this->Recebsm->ajustePagador($conditions, true);
		return $this->Recebsm->query($query_pagador);
    }

    private function notificar($dados_pagador, $dados_contratos, $qtd_sm_sem_pagador) {
        App::import('Component', 'Email');
        App::import('Core', 'Controller');
    	$controller =& new Controller();
		$this->StringView = new StringViewComponent();
        $this->Email =& new EmailComponent();

        $this->StringView->reset();
		$this->StringView->set(compact('dados_pagador', 'dados_contratos', 'qtd_sm_sem_pagador'));
		$mensagem = $this->StringView->renderMail('email_ajuste_pagador', 'default');

        $this->Email->startup($controller);
        $this->Email->sendAs = 'html';
	    $this->Email->to = array('nelson.ota@buonny.com.br', 'tiago.lopes@buonny.com.br');
		$this->Email->from = 'Buonny <pagador@buonny.com.br>';
		$this->Email->subject = 'Ajuste Pagador';
		$this->Email->template 	= null;
		$this->Email->layout 	= null;
		$this->Email->smtpOptions = array(
			'port'=>'25',
			'timeout'=>'30',
			'host' => 'webmail.buonny.com.br',
		);
		$this->Email->delivery = 'smtp';
		$this->Email->send($mensagem);
    }
}
?>
