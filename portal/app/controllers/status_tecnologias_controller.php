<?php
class StatusTecnologiasController extends appController {
    var $name = 'StatusTecnologias';
    var $helpers = array('BForm');
    var $uses = array(
        'StatusTecnologia',
        'MonitoramentoAutotrac',
        'VMonitoramento',
		'TUposUltimaPosicao',
		'TEsisEventoSistema',
        'TMiniMonitoraInicio',
        'THbeaHeartBeat',
        'LogIntegracao',
        'TCtecContaTecnologia',
        'QCtecContaTecnologia',
        'DbGuardianConsulta'
    );

    function index() {
        $this->pageTitle = 'Status das Tecnologias';
        $macroViagem 			= $this->THbeaHeartBeat->macroViagem();
        $hora_leitura 			= date('d/m/Y H:i:s');
        $contas = $this->QCtecContaTecnologia->qtdPorConta(false);
        $macroViagem[] = $this->LogIntegracao->verificarIntegracaoGpa();
		for($cont = 0; $cont < count($macroViagem);$cont++){
			$macroViagem[$cont]['THbeaHeartBeat']['status'] = $macroViagem[$cont][0]['status'];
			unset($macroViagem[$cont][0]);
			if($macroViagem[$cont]['THbeaHeartBeat']['status'] == 'fora'){
				$macroViagem[$cont]['THbeaHeartBeat']['status'] = 'badge-empty badge badge-important';
			}else{
				$macroViagem[$cont]['THbeaHeartBeat']['status'] = 'badge-empty badge badge-success';
			}
            if(isset($macroViagem[$cont]['LogIntegracao'])){
                $macroViagem[$cont]['THbeaHeartBeat']['hbea_codigo'] = $cont+1;
                $macroViagem[$cont]['THbeaHeartBeat']['hbea_descricao'] = 'Integração GPA';
                $macroViagem[$cont]['THbeaHeartBeat']['hbea_last_run'] = $macroViagem[$cont]['LogIntegracao']['data_inclusao'];
                unset($macroViagem[$cont]['LogIntegracao']);
            }
        }

        $status_replicacao = $this->DbGuardianConsulta->status(10);
        
        if($status_replicacao['status'] =='fora' || empty($status_replicacao['status'])){
            $macroViagem[$cont]['THbeaHeartBeat']['status'] = 'badge-empty badge badge-important';
        }else{
            $macroViagem[$cont]['THbeaHeartBeat']['status'] = 'badge-empty badge badge-success';
        }
        $macroViagem[$cont]['THbeaHeartBeat']['hbea_codigo'] = $cont+1;
        $macroViagem[$cont]['THbeaHeartBeat']['hbea_descricao'] = 'Status da Replicação do Postgres';
        $macroViagem[$cont]['THbeaHeartBeat']['hbea_last_run'] = (!empty($status_replicacao['data_ultima_atualizacao'])) ? $status_replicacao['data_ultima_atualizacao'] : null;

		$this->set(compact('macroViagem','contas'));
    }

    function conta_tecnologias(){
        $this->paginate['TCtecContaTecnologia'] = array(
            'limit' => 50,
            'order' => 'ctec_descricao',
        );  
        $tecnologias = $this->paginate('TCtecContaTecnologia');  
        $this->set(compact('tecnologias'));
    }

    function editar_configuracao($codigo){
        $this->pageTitle = 'Configuração de Tecnologia';
        if (!empty($this->data)) {
            if($this->RequestHandler->isPost()) {
                if ($this->TCtecContaTecnologia->atualizar($this->data)) {
                    $this->BSession->setFlash('save_success');
                    $this->redirect(array('controller' => 'StatusTecnologias', 'action' => 'conta_tecnologias'));
                } else {
                    $this->BSession->setFlash('save_error');
                }
            }    
        }else{
            $this->data = $this->TCtecContaTecnologia->carregar($codigo);
           
        }
    }
}
