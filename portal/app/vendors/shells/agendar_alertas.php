<?php
class AgendarAlertasShell extends Shell {
    
    private $Alerta = null;
    
    function run() {
        //$this->runEmail();
        //$this->runSms();
        
        // $this->runWs();
        $this->runAll();
    }
    
    function runAll($limit = 300) {
    	
        $this->Alerta = ClassRegistry::init('Alerta');
        $alertas = $this->Alerta->listaTodosPendentes($limit);
        
        foreach ($alertas as $alerta) {
            $retorno = ($this->agenda_todos_envios($alerta));
            
            if ($retorno['email']) {
                $this->marca_enviado_email($alerta['Alerta']);
            }
            if ($retorno['sms']) {
                $this->marca_enviado_sms($alerta['Alerta']);
            }
        }

    }

    function runEmail($limit = 300) {
        $this->Alerta = ClassRegistry::init('Alerta');
        $alertas = $this->Alerta->listaTodosPendentesEmail($limit);
        foreach ($alertas as $alerta)
            if ($this->agenda_todos_envios_email($alerta))
                $this->marca_enviado_email($alerta['Alerta']);
    }
    
    function runSms($limit = 300) {
        $this->Alerta = ClassRegistry::init('Alerta');
        $alertas = $this->Alerta->listaTodosPendentesSms($limit);
        foreach ($alertas as $alerta)
            if ($this->agenda_todos_envios_sms($alerta))
                $this->marca_enviado_sms($alerta['Alerta']);
    }

    function runWs($limit = 300) {
        $this->Alerta = ClassRegistry::init('Alerta');
        $this->WsConfiguracao = ClassRegistry::init('WsConfiguracao');
        $this->WsOutbox = ClassRegistry::init('WsOutbox');

        $clientes = $this->WsConfiguracao->localizaClientePorTipoMensagem('rma');
        if($clientes){
        	foreach($clientes as $cliente){
        		$rmas = $this->Alerta->listaTodosPendentesWsPorCliente($cliente['Cliente']['codigo'], $limit);
    			foreach($rmas as $codigo_alerta => $rma){
    				$mensagem = implode('|', $rma);
    				$ws_outbox = array(
    					'WsOutbox' => array(
    						'codigo_documento' => $cliente['Cliente']['codigo_documento'],
    						'tipo_mensagem' => 'rma',
    						'documento_origem' => $rma['sm'],
    						'mensagem' => $mensagem,
    						'data_alteracao' => date('Y-m-d H:i:s'),
    					),
    				);
    				if($this->WsOutbox->incluir($ws_outbox)){
    					$this->marca_enviado_ws($codigo_alerta);
    				}
    			}
        	}
        }
    }
    
    function agenda_todos_envios($alerta) {
    	
        $usuario = ClassRegistry::init('Usuario');
        // $UsuarioVeiculoAlerta = ClassRegistry::init('UsuarioVeiculoAlerta');
        $email_agendados = $alerta['Alerta']['email_agendados'];
        $sms_agendados = $alerta['Alerta']['sms_agendados'];

        $total_usuarios_email = 0;
        $total_usuarios_sms = 0;

        // Alerta apenas para o pai do Usuario Pai
        if( $alerta['Alerta']['model'] == 'Usuario' && $alerta['Alerta']['foreign_key'] ){
            $usuarios_to_send = $usuario->listaUsuariosResponsaveisAlerta($alerta, null, $alerta['Alerta']['foreign_key'] );
            $usuarios_to_send = Set::extract('/Usuario/.', $usuarios_to_send);            
        } else {
            $usuarios_to_send = $usuario->listaUsuariosAlerta($alerta, null, ($alerta['AlertaTipo']['interno'] == 'S'));
        }
        
        $enviados = 0;
        $enviados_email = 0;
        $enviados_sms = 0;

        foreach ($usuarios_to_send as $key => $dados_usuario) {
            $total_usuarios_email += ($dados_usuario['alerta_email'] == 1 ? 1 : 0);
            $total_usuarios_sms += ($dados_usuario['alerta_sms'] == 1 ? 1 : 0);
        }

        foreach ($usuarios_to_send as $codigo_usuario => $usuario_to_send) {
            if ($usuario_to_send['alerta_email']==true && empty($usuario_to_send['email'])) continue;
            if ($usuario_to_send['alerta_sms']==true && empty($usuario_to_send['celular'])) continue;
            
            if (!empty($usuario_to_send)){
            	
				if ((!$email_agendados) && ($usuario_to_send['alerta_email'] == true)) {
					
                	if( $this->agenda_envio_email($usuario_to_send['email'], $alerta)) {
                    	$enviados++;
                        $enviados_email++;
					}
				}
				if ((!$sms_agendados) && ($usuario_to_send['alerta_sms'] == true)) {
					if ($this->agenda_envio_sms($usuario_to_send['celular'], $alerta)) {
						$enviados++;
						$enviados_sms++;
					}
				}
            }
        }

        $retorno = Array(
            'email' => ($email_agendados || ($enviados_email >= $total_usuarios_email)),
            'sms' => ($sms_agendados || ($enviados_sms >= $total_usuarios_sms)),
        );
        
        return $retorno;
    }

	function agenda_todos_envios_email($alerta) {
	    $usuario = ClassRegistry::init('Usuario');
	    $UsuarioVeiculoAlerta = ClassRegistry::init('UsuarioVeiculoAlerta');
        $usuarios_to_send = $usuario->listaUsuariosAlerta($alerta,"email",($alerta['AlertaTipo']['interno']=='S'));
        /*
        if ($alerta['AlertaTipo']['interno']=='S') {
            $usuarios_to_send = $usuario->listaUsuariosAlertaEmailInterno($alerta);
        } else {
            $usuarios_to_send = $usuario->listaUsuariosAlertaEmailPorCliente($alerta);1
        }
        */
	    $enviados = 0;
	    $veiculos = array();
        if($alerta['Alerta']['model'] == 'TViagViagem'){
            $TViagViagem = &ClassRegistry::init('TViagViagem');
            $TVveiViagemVeiculo = &ClassRegistry::init('TVveiViagemVeiculo');
            $TVeicVeiculo = &ClassRegistry::init('TVeicVeiculo');
            $Veiculo = &ClassRegistry::init('Veiculo');
            $joins = array(
                array(
                    'table' => "{$TVveiViagemVeiculo->databaseTable}.{$TVveiViagemVeiculo->tableSchema}.{$TVveiViagemVeiculo->useTable}",
                    'alias' => 'TVveiViagemVeiculo',
                    'conditions' => array('TVveiViagemVeiculo.vvei_viag_codigo = TViagViagem.viag_codigo'),
                ),
                array(
                    'table' => "{$TVeicVeiculo->databaseTable}.{$TVeicVeiculo->tableSchema}.{$TVeicVeiculo->useTable}",
                    'alias' => 'TVeicVeiculo',
                    'conditions' => array('TVeicVeiculo.veic_oras_codigo = TVveiViagemVeiculo.vvei_veic_oras_codigo'),
                ),
            );
            $placas = $TViagViagem->find('list',array('conditions' => array('TViagViagem.viag_codigo' => $alerta['Alerta']['foreign_key']),'joins' => $joins,'fields' => array('TVeicVeiculo.veic_placa')));

            foreach($placas as $placa){
            	$veiculo = $Veiculo->buscaCodigodaPlaca($placa);
            	if($veiculo){
            		$veiculos[] = $veiculo;
            	}
            }
        }elseif($alerta['Alerta']['model'] == 'Veiculo'){
        	$veiculos[] = $alerta['Alerta']['foreign_key'];
        }
        $total_usuarios_email = count($usuarios_to_send);
	    foreach ($usuarios_to_send as $codigo_usuario => $usuario_to_send){
	        if (!empty($usuario_to_send)){
                //HardCoDed RMA POR CD DE ORIGEM
                if( $alerta['Alerta']['model'] == 'TViagViagem' && $codigo_usuario == 36708 && in_array($alerta['AlertaTipo']['codigo'], array(2,18)) ) {
                    $TVlocViagemLocal     = &ClassRegistry::init('TVlocViagemLocal');
                    $verifica_alvo_origem = $TVlocViagemLocal->find('first', array(
                        'conditions' => array(
                            'vloc_viag_codigo' => $alerta['Alerta']['foreign_key'],
                            'vloc_sequencia'   => 1
                        ),
                        'fields' => array('vloc_refe_codigo')
                    ));
                    $alvo = isset($verifica_alvo_origem['TVlocViagemLocal']['vloc_refe_codigo']) ? $verifica_alvo_origem['TVlocViagemLocal']['vloc_refe_codigo']: NULL;
                    if( !in_array( $alvo, array( 140270, 140315 ))) {
                        if( $total_usuarios_email == 1 ){//SOMENTE O USUARIO ESTA CONFIGURADO PARA RECEBER O EMAIL
                            return true;
                        }
                        $enviados+=1;
                        continue;
                    }
                }
	        	$send = false;
	        	$usuario_veiculos = $UsuarioVeiculoAlerta->listarPorUsuario($codigo_usuario);
	        	$usuario_veiculos = Set::extract('/UsuarioVeiculoAlerta/codigo_veiculo', $usuario_veiculos);
	        	if(!$usuario_veiculos)
	        		$send = true;
	        	else
	        		foreach($veiculos as $veiculo){
	        			if(in_array($veiculo, $usuario_veiculos)){
	        				$send = true;
	        				break;
	        			}
	        		}
	        	if(!$send)
	        		unset($usuarios_to_send[$codigo_usuario]);
	            elseif ($this->agenda_envio_email($usuario_to_send, $alerta)) $enviados++;
	        }
	    }
	    return count($usuarios_to_send) == $enviados;
	}
	
	private function agenda_envio_email($usuario, $alerta) {
	    App::import('Component', 'Mailer.Scheduler');
	    $this->Scheduler = new SchedulerComponent();
        if (!empty($alerta['Alerta']['assunto'])) {
            $assunto = $alerta['Alerta']['assunto'];
        } else {
            $assunto = $alerta['AlertaTipo']['descricao'];
        }
	    return $this->Scheduler->schedule(utf8_decode($alerta['Alerta']['descricao_email']), array('subject'=>utf8_decode($assunto), 'to'=>$usuario, 'from'=>'portal@rhhealth.com.br'),'Alerta',$alerta['Alerta']['codigo']);
	}
	
	private function marca_enviado_email($alerta) {
	    $this->Alerta->id = $alerta['codigo'];
	    $this->Alerta->saveField('email_agendados', true);
	}

	private function marca_enviado_ws($codigo_alerta) {
	    $this->Alerta->id = $codigo_alerta;
	    $this->Alerta->saveField('ws_agendados', true);
	}
	
	function agenda_todos_envios_sms($alerta) {
	    $usuario = ClassRegistry::init('Usuario');
	    $UsuarioVeiculoAlerta = ClassRegistry::init('UsuarioVeiculoAlerta');
        $usuarios_to_send = $usuario->listaUsuariosAlerta($alerta,"sms",($alerta['AlertaTipo']['interno']=='S'));
	    $enviados = 0;

		$veiculos = array();
        if($alerta['Alerta']['model'] == 'TViagViagem'){
            $TViagViagem = &ClassRegistry::init('TViagViagem');
            $TVveiViagemVeiculo = &ClassRegistry::init('TVveiViagemVeiculo');
            $TVeicVeiculo = &ClassRegistry::init('TVeicVeiculo');
            $Veiculo = &ClassRegistry::init('Veiculo');

            $joins = array(
                array(
                    'table' => "{$TVveiViagemVeiculo->databaseTable}.{$TVveiViagemVeiculo->tableSchema}.{$TVveiViagemVeiculo->useTable}",
                    'alias' => 'TVveiViagemVeiculo',
                    'conditions' => array('TVveiViagemVeiculo.vvei_viag_codigo = TViagViagem.viag_codigo'),
                ),
                array(
                    'table' => "{$TVeicVeiculo->databaseTable}.{$TVeicVeiculo->tableSchema}.{$TVeicVeiculo->useTable}",
                    'alias' => 'TVeicVeiculo',
                    'conditions' => array('TVeicVeiculo.veic_oras_codigo = TVveiViagemVeiculo.vvei_veic_oras_codigo'),
                ),
            );
            $placas = $TViagViagem->find('list',array('conditions' => array('TViagViagem.viag_codigo' => $alerta['Alerta']['foreign_key']),'joins' => $joins,'fields' => array('TVeicVeiculo.veic_placa')));

            foreach($placas as $placa){
            	$veiculo = $Veiculo->buscaCodigodaPlaca($placa);
            	if($veiculo){
            		$veiculos[] = $veiculo;
            	}
            }
        }elseif($alerta['Alerta']['model'] == 'Veiculo'){
        	$veiculos[] = $alerta['Alerta']['foreign_key'];
        }

	    foreach ($usuarios_to_send as $codigo_usuario => $usuario_to_send){
	        if (!empty($usuario_to_send)){
	            $send = false;
	        	$usuario_veiculos = $UsuarioVeiculoAlerta->listarPorUsuario($codigo_usuario);
	        	$usuario_veiculos = Set::extract('/UsuarioVeiculoAlerta/codigo_veiculo', $usuario_veiculos);
	        	if(!$usuario_veiculos || $alerta['Alerta']['model'] != 'TViagViagem')
	        		$send = true;
	        	else
	        		foreach($veiculos as $veiculo){
	        			if(in_array($veiculo, $usuario_veiculos)){
	        				$send = true;
	        				break;
	        			}
	        		}
	        	if(!$send)
	        		unset($usuarios_to_send[$codigo_usuario]);
	            elseif ($this->agenda_envio_sms($usuario_to_send, $alerta)) $enviados++;
	        }
	    }
	    return count($usuarios_to_send) == $enviados;
	}
	
	private function agenda_envio_sms($telefone, $alerta) {
	    // $this->SmsOutbox = ClassRegistry::init('SmsOutbox');
	    // return $this->SmsOutbox->agendar($telefone, utf8_decode($alerta['Alerta']['descricao']));
        return false; 
	}
	
	private function marca_enviado_sms($alerta) {
	    $this->Alerta->id = $alerta['codigo'];
	    $this->Alerta->saveField('sms_agendados', true);
	}
	
}
?>