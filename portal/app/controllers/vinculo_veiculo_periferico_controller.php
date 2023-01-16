<?php
class VinculoVeiculoPerifericoController extends AppController {
	public $name = 'VinculoVeiculoPeriferico';
	public $uses = array(
		'TPtvePerifericoTipoVeiculo',
        'TTveiTipoVeiculo',
        'TPpadPerifericoPadrao',
	
	);

    private function carregaCombos(){
        $tipo_veiculo = $this->TTveiTipoVeiculo->listaFormatada('ASC');
        $periferico = $this->TPpadPerifericoPadrao->find('list',array('conditions' => array('ppad_ativo' => 'S')));
        $veiculo_sem_vinculo = $this->comboVeiculoSemVinculo(TRUE);
        $this->set(compact('tipo_veiculo','periferico','veiculo_sem_vinculo'));
    }

    private function comboVeiculoSemVinculo(){     
        $tipo_veiculo = $this->TPtvePerifericoTipoVeiculo->list_tipo_veiculo_sem_vinculo();
        $periferico = $this->TPpadPerifericoPadrao->find('list',array('conditions' => array('ppad_ativo' => 'S')));
        $this->set(compact('tipo_veiculo','periferico'));
    }

    function index(){
        $this->pageTitle = 'Vínculo tipo de veiculo e periféricos';
        $this->carregaCombos();
        $this->data['TPtvePerifericoTipoVeiculo'] = $this->Filtros->controla_sessao($this->data, "TPtvePerifericoTipoVeiculo");
        $filtrado = true;
        $this->set(compact('filtrado'));
    }

    function listagem(){
        $this->data['TPtvePerifericoTipoVeiculo'] = $this->Filtros->controla_sessao($this->data, "TPtvePerifericoTipoVeiculo");
        $conditions = $this->TPtvePerifericoTipoVeiculo->convertFiltrosEmConditions($this->data['TPtvePerifericoTipoVeiculo']);
        $dados = $this->TPtvePerifericoTipoVeiculo->agrupa_perifericos_veiculo($conditions);
        $veiculo_sem_vinculo  = count($this->TPtvePerifericoTipoVeiculo->list_tipo_veiculo_sem_vinculo());
        $this->set(compact('dados','veiculo_sem_vinculo'));
    }

    function incluir(){
        $this->pageTitle = 'Incluir vínculo tipo de veiculo e periféricos';
        $this->comboVeiculoSemVinculo();
        if($this->data) {
            if($this->TPtvePerifericoTipoVeiculo->incluir($this->data)){    
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            }else{
                if(count($this->data['TPtvePerifericoTipoVeiculo']['ptve_ppad_codigo']) > 0){
                    $this->BSession->setFlash('save_error');
                }
                $this->BSession->setFlash(array(MSGT_ERROR, 'Informe pelo menos um periférico'));
            }
        }
    }
    function editar($codigo){
        $this->pageTitle = 'Editar vínculo tipo de veiculo e periféricos';    
        $this->carregaCombos();      
        if($this->RequestHandler->isPost()) {
            if($this->TPtvePerifericoTipoVeiculo->alterar($this->data)){    
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            }else{
                if(count($this->data['TPtvePerifericoTipoVeiculo']['ptve_ppad_codigo']) > 0){
                    $this->BSession->setFlash('save_error');
                }
                $this->BSession->setFlash(array(MSGT_ERROR, 'Informe pelo menos um periférico'));
            }
        }else{
            $this->data['TPtvePerifericoTipoVeiculo']['ptve_tvei_codigo'] = $codigo;
            $this->data['TPtvePerifericoTipoVeiculo']['ptve_ppad_codigo'] = $this->TPtvePerifericoTipoVeiculo->lista_somente_codigo_perifericos($codigo);       
        }
        $this->set(compact('codigo'));
    }

    function excluir($codigo){
        if($codigo){
            if($this->TPtvePerifericoTipoVeiculo->excluir($codigo)){
                $this->BSession->setFlash('save_success');
            }else{
                $this->BSession->setFlash('delete_error');
            }
            $this->redirect(array('action' => 'index'));
        }
    }

}

