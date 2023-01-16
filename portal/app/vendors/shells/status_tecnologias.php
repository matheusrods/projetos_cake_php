<?php
App::import('Component', 'StringView');
App::import('Core', 'Controller');
App::import('Component', 'Email');
class StatusTecnologiasShell extends Shell {
    var $uses = array(
        'TCtecContaTecnologia',
        'TEsisEventoSistema',
        'THbeaHeartBeat',
        'LogIntegracao',
        'SmsOutbox',
        'DbGuardianConsulta'
    );
    const OMNILINK_GPA = 5;
    const POINTER = 31;

    function startup(){
        App::import('Component', 'Email');
        $this->Email = new EmailComponent();
        $this->Email->xMailer = 'Buonny Mailer';
    }
    
        
    function main() {
        $tecnologias = array();
        $contas = $this->TCtecContaTecnologia->qtdPorConta(false);
		
        foreach($contas as $conta) {
            if (empty($conta['0']['qtd_total_em_viagem'])) {
                $percentual = 0;
            } else {
                $percentual = round($conta['0']['qtd_atualizado_em_viagem'] / $conta['0']['qtd_total_em_viagem'] * 100,1);
            }
            $notificar = false;
            if ($conta['0']['ctec_codigo'] == self::OMNILINK_GPA) {
                $notificar = (empty($conta['0']['atualizado_data']) || empty($conta['0']['atualizado']));
            } elseif ($conta['0']['ctec_codigo'] == self::POINTER) {
                $notificar = empty($conta['0']['atualizado']);
            } else {
                $notificar = empty($conta['0']['atualizado_data']);
            }
            if ($notificar) {
                $tecnologias[] = $conta['0']['ctec_descricao'].' Ultima Atualizacao '.$conta['0']['ultima_atualizacao'].' SMs Atualizadas:'.$percentual.'%';
            }
        }

		if(!empty($tecnologias)) $this->dispararEmailDeAlerta($tecnologias);
        
        $macros = array();
		$macroViagem = $this->THbeaHeartBeat->macroViagem();
        $macroViagem[] = $this->LogIntegracao->verificarIntegracaoGpa();
        for($cont = 0; $cont < count($macroViagem);$cont++){        
            $macroViagem[$cont]['THbeaHeartBeat']['status'] = $macroViagem[$cont][0]['status'];
            unset($macroViagem[$cont][0]);
            if(isset($macroViagem[$cont]['LogIntegracao'])){
                $macroViagem[$cont]['THbeaHeartBeat']['hbea_codigo'] = $cont+1;
                $macroViagem[$cont]['THbeaHeartBeat']['hbea_descricao'] = 'Integração GPA';
                $macroViagem[$cont]['THbeaHeartBeat']['hbea_last_run'] = $macroViagem[$cont]['LogIntegracao']['data_inclusao'];
                unset($macroViagem[$cont]['LogIntegracao']);
            }
            if($macroViagem[$cont]['THbeaHeartBeat']['status'] =='fora'){
                $macros[] = $macroViagem[$cont]['THbeaHeartBeat']['hbea_descricao'].' - '.'Ultima Atualização '.date('d/m/Y H:i:s',strtotime(str_replace('/','-',$macroViagem[$cont]['THbeaHeartBeat']['hbea_last_run'])));
            }
        } 
        if(!empty($macros)) $this->dispararEmailDeAlertaMacros($macros);

        $tempo = $this->DbGuardianConsulta->status(10);
        if($tempo['status'] == 'fora'){            
            $this->dispararEmailStatusReplicacao($tempo);
        }
    }
	

    function dispararEmailDeAlerta($tecnologias) {
        App::import('Component', array('StringView', 'Mailer.Scheduler'));
        $this->StringView = new StringViewComponent();
        $this->Scheduler = new SchedulerComponent();		
        
        $this->StringView->reset();
        $this->StringView->set(compact('tecnologias'));
        $content = $this->StringView->renderMail('email_alerta_tecnologias', 'default');
		
        $lista_emails = 'tid@ithealth.com.br';
		$options = array(
            'sent' => null,
			'to' => $lista_emails,
			'subject' => 'Alerta de Status das Tecnologias',
		);
        $fones = array(
            '11972307233',
            '11987866444',
            '11987866555',
            '11993886111',
            '11987866566',
            '11985065849',
            '11987866644',
            '11976453970'
        );
        foreach ($fones as $fone) {
            $texto = implode(',', $tecnologias);
            $this->SmsOutbox->agendar($fone, $texto);
        }
        return $this->enviarEmail($content, $options) ? true: false;
    }

    function dispararEmailStatusReplicacao($tempo) {
        App::import('Component', array('StringView', 'Mailer.Scheduler'));
        $this->StringView = new StringViewComponent();
        $this->Scheduler = new SchedulerComponent();        
        
        $this->StringView->reset();
        $this->StringView->set(compact('tempo'));
        $content = $this->StringView->renderMail('email_alerta_replicacao_postgres', 'default');                
        $lista_emails = 'tid@ithealth.com.br';
        $options = array(
            'sent' => null,
            'to' => $lista_emails,
            'subject' => 'Alerta de Status da Replicação do Postgres',
        );
       
        return $this->enviarEmail($content, $options) ? true: false;
    }

    function dispararEmailDeAlertaMacros($macros) {
        App::import('Component', array('StringView', 'Mailer.Scheduler'));
        $this->StringView = new StringViewComponent();
        $this->Scheduler = new SchedulerComponent();        
        
        $this->StringView->reset();
        $this->StringView->set(compact('macros'));
        $content = $this->StringView->renderMail('email_alerta_macros', 'default');
        
        $lista_emails = 'grupo.tisuporte@buonny.com.br;elcio.gallo@buonny.com.br;nelson.ota@buonny.com.br';
        $options = array(
            'from' => 'portal@rhhealth.com.br',
            'to' => $lista_emails,
            'subject' => 'Alerta  Macros de Viagens',
        );
        return $this->enviarEmail($content, $options) ? true: false;
    }

    public function enviarEmail($content,$options) {      
        $controller =& new Controller();
        $this->StringView = new StringViewComponent();
        $this->Email =& new EmailComponent();

        $this->StringView->reset();
       
        $this->Email->startup($controller);
        $this->Email->sendAs = 'html';
        $this->Email->from = 'portal@rhhealth.com.br>';
   
        $this->Email->subject = 'Alertas Consulta';
        $this->Email->template  = null;
        $this->Email->layout    = null;
        $this->Email->smtpOptions = array(
            'port'=>'25',
            'timeout'=>'30',
            'host' => 'webmail.buonny.com.br',
        );
        $this->Email->delivery = 'smtp';
        $this->Email->from = 'portal@rhhealth.com.br';

        if (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO) {
            $this->Email->to = explode(';', $options['to']);
            $this->Email->subject = "[RHHealth]" . $options['subject'];
        }else{
            $this->Email->subject = "[teste]" . $options['subject'];          
            $this->Email->to = array('tid@ithealth.com.br');
        }          
        return $this->Email->send($content);
    }
}