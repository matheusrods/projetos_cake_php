<?php
class SmsOutbox extends AppModel {
    var $name           = 'SmsOutbox';
	var $tableSchema    = 'dbo';
    var $databaseTable  = 'RHHealth';
    var $useTable       = 'sms_outbox';
    var $primaryKey     = 'codigo';
    var $actsAs         = array('Secure');

	var $validate       = array(
        'fone_para' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o telefone',
                'required' => true
            )
		),
        'mensagem' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe a mensagem',
                'required' => true
                )
            ),
        'liberar_envio_em' => array(
                'rule' => 'validaLiberarEnvio',
                'message' => 'Data de agendamento não pode ser inferior a data atual'
             )
	); 

    const MANUAL = 'RhHealth_Manual'; 
    const PLANILHA = 'RhHealth_Planilha'; 
    
    public function agendar($fone_para, $mensagem) {
        return $this->incluir(array(
            'fone_para'=>preg_replace('/[^\d]*/', '', $fone_para),
            'mensagem'=>$mensagem,
            'codigo_usuario_inclusao'=>0,
            'sistema_origem'=>'Portal Buonny'
        ));
    }

    public function agendarSMParaMotorista($sm,$cpf) {
        $this->Profissional =& ClassRegistry::init('Profissional');
        $profissional       =& $this->Profissional->buscaEspecificaPorCPF($cpf);
        if(!$profissional || !preg_replace('/[^\d]*/', '', $profissional['ProfissionalCelular']['descricao']))
            return FALSE;

        $mensagem = "A Buonny esta acompanhando sua viagem, o numero de sua solicitação {$sm} informe este numero no seu contato. Boa Viagem!";

        return $this->incluir(array(
            'fone_para'                 => preg_replace('/[^\d]*/', '', $profissional['ProfissionalCelular']['descricao']),
            'mensagem'                  => $mensagem,
            'codigo_usuario_inclusao'   => 0,
            'sistema_origem'            => 'Portal Buonny'
        ));        
    }

    public function converteFiltroEmConditions($filtros){           
        //$conditions = array();


        if(!empty($filtros['sistema_origem'])){         
            $conditions['SmsOutbox.sistema_origem'] = $filtros['sistema_origem'];
        }
        else{
            $conditions['SmsOutbox.sistema_origem'] = array(SmsOutbox::MANUAL, SmsOutbox::PLANILHA);
        }

        if(!empty($filtros['fone_de'])){  
            $conditions['SmsOutbox.fone_de'] = $filtros['fone_de'];
        }        
        if(!empty($filtros['fone_para'])){         
            $conditions['SmsOutbox.fone_para'] = COMUM::soNumero($filtros['fone_para']);
        }        
        if((!empty($filtros['data_inicial'])) && (!empty($filtros['data_final']))) {         
            $inicio = $filtros['data_inicial'] .' 00:00:00';
            $final = $filtros['data_final'] .' 23:59:59';
            $inicial = AppModel::dateToDbDate2($inicio);
            $final = AppModel::dateToDbDate2($final);
            $conditions['SmsOutbox.data_inclusao BETWEEN ? AND ?'] = array($inicial,$final);
        }                  
       return $conditions;
    }

  function validaLiberarEnvio() {
      if  (!empty($this->data['SmsOutbox']['liberar_envio_em'])) {
         $this->data['SmsOutbox']['liberar_envio_em'] = AppModel::dateToDbDate($this->data['SmsOutbox']['liberar_envio_em']);
        if (strtotime($this->data['SmsOutbox']['liberar_envio_em']) < strtotime(date('Ymd'))) {
          return false;  
        }         
      }
      return true;
    }    
    
}
?>