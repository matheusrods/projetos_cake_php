<?php
class ResultadosController extends AppController {

	public function alterar($codigo_questionario = null)
	{
		//Nome que aparecerá na página
		$this->pageTitle = 'Resultados';

		//Testa se veio dados por POST para inserir
		if($this->RequestHandler->isPost()) {
			//Chama a função de inclusão do MODEL
			if($this->Resultado->incluir($this->data)) {
				//Se inserção deu certo envia mensagem de salvo com sucesso e redireciona para método INDEX do Controller Questionarios
				$this->BSession->setFlash('save_success');
				return $this->redirect(array('controller' => 'questionarios'));
			} else {
				//Se inserção deu errado, envia mensam de erro na tela
				$this->BSession->setFlash('save_error');
			}
		} else {

		//Faz a query que carregará os RESULTADOS na tela para futura alteração
			$this->data = $this->Resultado->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'Resultado.codigo_questionario' => $codigo_questionario
					),
				'fields' => array(
					'Resultado.descricao',
					'Resultado.valor',
					'Resultado.codigo_questionario',
					)
				)
			);
		}
		$this->set(compact('codigo_questionario'));
	}

}