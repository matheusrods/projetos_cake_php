<?php
class PrevencaoRiscosAmbientaisController extends AppController {

    public $name = 'PrevencaoRiscosAmbientais';
    public $uses = array('PrevencaoRiscoAmbiental', 'Medico', 'TipoAcao');

    public function editar($codigo_matriz, $codigo_cliente = null, $codigo = null){
    	$this->pageTitle = 'Programa de Prevenção de Riscos Ambientais';
    	if($this->RequestHandler->isPost()) {

            if($this->PrevencaoRiscoAmbiental->incluir($this->data)) {
    			$this->BSession->setFlash('save_success');
    			return $this->redirect(array('controller' => 'clientes_implantacao', 'action' => 'gerenciar_ppra', $codigo_matriz));
    		} else {
    			$this->BSession->setFlash('save_error');
    		}
    	}

    	$dados_cliente = $this->PrevencaoRiscoAmbiental->obterDadosDoCliente($codigo_cliente);
        $profissionais = $this->Medico->lista_somente_engenhgeiros_por_cliente($codigo_cliente);
        $setores = $this->PrevencaoRiscoAmbiental->obterSetoresDaEmpresa($codigo_cliente);
    	if(!is_null($codigo)) {
    		$this->data = $this->PrevencaoRiscoAmbiental->Gpra->findByCodigo($codigo);
    	}
        $data_tipo_acoes = $this->TipoAcao->get_all_ppra_list();

    	$this->set(compact('codigo_grupo_exposicao', 'profissionais', 'codigo_matriz', 'codigo_cliente', 'dados_cliente', 'setores', 'data_tipo_acoes'));
    }


}
