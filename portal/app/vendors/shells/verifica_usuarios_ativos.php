<?php
App::import('Component', 'Auth');
class VerificaUsuariosAtivosShell extends Shell {
	var $ldap = null;
	var $uses = array(
		'Usuario',
		'LdapUser',
		);


	/** *
		Codigos Ldap useraccountcontrol
			Normal Day to Day Values:
			===========================
			512 - Enable Account
			514 - Disable account
			544 - Account Enabled - Require user to change password at first logon
			4096 - Workstation/server
			66048 - Enabled, password never expires
			66050 - Disabled, password never expires
			262656 - Smart Card Logon Required
			532480 - Domain controller

			All Other Values:
			===========================
			1 - script
			2 - accountdisable
			8 - homedir_required
			16 - lockout
			32 - passwd_notreqd
			64 - passwd_cant_change
			128 - encrypted_text_pwd_allowed
			256 - temp_duplicate_account
			512 - normal_account
			2048 - interdomain_trust_account
			4096 - workstation_trust_account
			8192 - server_trust_account
			65536 - dont_expire_password
			131072 - mns_logon_account
			262144 - smartcard_required
			524288 - trusted_for_delegation
			1048576 - not_delegated
			2097152 - use_des_key_only
			4194304 - dont_req_preauth
			8388608 - password_expired
			16777216 - trusted_to_auth_for_delegation
		* **/


	function main() {
		echo "verifica_usuarios_ativos [sincroniza] \n";
	}

	private function im_running($tipo) {
		$cmd = shell_exec("ps aux | grep 'verifica_usuarios_ativos {$tipo}'");
		// 1 execução é a execução atual
		return substr_count($cmd, 'cake.php -working') > 1;
	}

	function sincroniza() {
		$contagem_usuarios_ativos_ldap_desativados_portal = 0;
		$contagem_usuarios_desativados_ldap_ativos_portal = 0;
		$contagem_somente_portal = 0;
		$usuario_sistema = $this->Usuario->find('first', array('conditions' => array('Usuario.apelido' => array('sistema'))));
		$_SESSION['Auth']['Usuario'] = $usuario_sistema['Usuario'];

		//sincroniza usuarios ativos portal com ldap
		if (!$this->im_running('sincroniza')) {
			$usuarios = $this->Usuario->find('all', array('conditions' => array('Usuario.codigo_cliente' => NULL), 'order' => 'apelido DESC'));
			foreach ($usuarios as $key => $usuario) {
				$userObj = $this->LdapUser->find('first', array('conditions' => array('samaccountname' => $usuario['Usuario']['apelido']), 'scope'=>'sub'));
				if(!empty($userObj['User']['useraccountcontrol']) && ($userObj['User']['useraccountcontrol'] == 512 || $userObj['User']['useraccountcontrol'] == 544 || $userObj['User']['useraccountcontrol'] == 66048)) {
					if($usuario['Usuario']['ativo'] == FALSE) {
						$contagem_usuarios_ativos_ldap_desativados_portal++;
					}
				}elseif(!empty($userObj)) {
					if($usuario['Usuario']['ativo'] == TRUE) {
						echo "Mudando para ->DESATIVADO \t".$usuario['Usuario']['apelido']."\n";
						$usuario['Usuario']['ativo'] = FALSE;
						if($this->Usuario->atualizar($usuario)){
							echo "Alterado para ->DESATIVADO \t".$usuario['Usuario']['apelido']."\n";
							$contagem_usuarios_desativados_ldap_ativos_portal++;
						}else {
							echo "ERRO Tratando...:".$usuario['Usuario']['apelido']."\n";

							//Trata erros
							$errors = $this->Usuario->invalidFields();
							if(!empty($errors['email'])) {
								$usuario['Usuario']['email'] = 'desativado@buonny.com.br';
							}
							if(!empty($errors['codigo_uperfil'])) {
								$usuario['Usuario']['codigo_uperfil'] = 38; 
							}

							if($this->Usuario->atualizar($usuario)){
								echo "TRATADO \n";
								echo "Alterado para ->DESATIVADO \t".$usuario['Usuario']['apelido']."\n";
							}else {
								$this->log($usuario , 'error_shell_verifica_usuarios_ativos');
								echo "ERRO:".$usuario['Usuario']['apelido']."\n";
							}
							$contagem_usuarios_desativados_ldap_ativos_portal++;
						}
					}
				}else {
					$contagem_somente_portal ++;
				}
			}

			echo "\n";
			echo "Total:". $key."\n";
			echo "Usuarios ativos no LDAP e desativados no Portal:". $contagem_usuarios_ativos_ldap_desativados_portal."\n";
			echo "Usuarios desativados no LDAP e ativos no Portal:". $contagem_usuarios_desativados_ldap_ativos_portal."\n";
			echo "Somente Portal:". $contagem_somente_portal."\n";
		}
	}
}
?>