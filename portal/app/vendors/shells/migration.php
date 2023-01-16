<?php
class MigrationShell extends Shell {
	var $uses = array('MigrationDefault');
	
	function startup(){
	}
	
	function main() {
		echo "Use: update\n";
		echo "Os arquivos script devem estar com o nome <versao>.mig \n";
		echo "localizados no diretorio \\migration\n";
	}
	
	function update() {
		$modelControle = new MigrationDefault();
		$filesTMP = glob('./app/tmp/cache/models/cake_model*');
		foreach ($filesTMP as $fileTMP) {
			unlink($fileTMP);
		}
		
		$filesMIG = glob('./migration/*.mig');
		
		$currentVersion = $modelControle->query('select version from RHHealth.dbo.migration');
		$currentVersion = $currentVersion[0][0]['version'];

		$lastVersion = $currentVersion;
		
		foreach ($filesMIG as $key => $fileMIG) {
			$ordemMIG[$key] = str_replace('.mig','',str_replace('./migration/','',$fileMIG));
			$nomeMIG[$key] = $fileMIG;
		}

		array_multisort($ordemMIG, SORT_ASC, $nomeMIG, SORT_ASC, $filesMIG);
		$modelControle->query('BEGIN TRANSACTION');
		foreach ($filesMIG as $fileMIG) {
			$fileVersion = str_replace('.mig','',str_replace('./migration/','',$fileMIG));
			
			if ($fileVersion > $currentVersion)	{
				$lastVersion = $fileVersion;
				$script = file($fileMIG);
				$this->executaScriptMIG($script, $fileMIG);
				$lastVersion = $fileVersion;
				$modelControle->query('update RHHealth.dbo.migration set version = '.$lastVersion);
			}
		}
		$modelControle->commit();
		echo "Versao inicial ".$currentVersion."\n";
		echo "Versao atual ".$lastVersion."\n";
	}
	
	function perfil_admin() {
		if (!isset($this->args[0]))
			return false;
		if ($this->args[0] == 'create') {
			$this->Uperfil = ClassRegistry::init('Uperfil');
			$_SESSION['Auth']['Usuario']['codigo'] = 1;
			$criado = $this->Uperfil->criarAdmin();
			echo ($criado ? 'Admin criado' : 'Nao criado');
		}
	}
	
	private function executaScriptMIG($script, $fileMIG) {
		$action = null;
		foreach ($script as $line) {
			$line = trim($line);
			if ($line == '[ACO]') {
				App::import('Component', 'CachedAcl');
				$this->Acl = new CachedAclComponent();
				$action = 'Aco';
				$aroaco =& new Aco();
			} else if ($line == '[MigrationDefault]') {
				$action = 'SQL';
				$model = trim(str_replace('[','',str_replace(']','',$line)));
				$this->Model =& ClassRegistry::init($model);
				$comando = '';
			} else if ($line == '[PermissionAdmins]') {
				App::import('Component', 'CachedAcl');
				$this->Acl = new CachedAclComponent();
				$this->Perfil =& ClassRegistry::init('Perfil');
				$action = 'PermissionAdmins';
			} else {
				if (($action == "Aco") || ($action == "Aro_barrado")) {
					$comando = explode(";", $line);
					if ($comando[0] == 'create') {
						if (count($comando) == 3) {
							if ($comando[1] == '/') {
								$parent = null;
								$parentNode = null;
							} else {
								$parent = $comando[1] . '/';
								$parentNode = $aroaco->node($comando[1]);
							}
						
							$newNode = $aroaco->node($parent.$comando[2]);
							if (!$newNode) {
								echo $action.' Criando '.$parent.$comando[2].' parentId '.$parentNode[0][$action]['id']."\n";
								$aroaco->{$comando[0]}(array('parent_id' => $parentNode[0][$action]['id'], 'model' => null, 'alias' => $comando[2]));
								$aroaco->save();
							} else
								echo $action.' Ja existe '.$parent.$comando[2].' parentId '.$parentNode[0][$action]['id']."\n";
						}
					} /*else if ($comando[0] == 'delete') {
						$node = $aroaco->node($comando[1]);
						echo $action.' Excluindo '.$comando[1].' Id '.$node[0][$action]['id']."\n";
						$aroaco->delete($node[0][$action]['id']);
					}*/
				} else if ($action == 'PermissionAdmins') {
					$comando = explode(";", $line);
					$admins = $this->Perfil->find('all', array('conditions' => array('Perfil.admin' => 1)));
					foreach ($admins as $admin){
						eval("\$this->Perfil->id = ".$admin['Perfil']['id'].";");
						$this->Acl->{$comando[0]}($this->Perfil, $comando[1]);
						echo 'Configurando permissao '.$comando[0].' para '.$comando[1].' no objeto '.$admin['Perfil']['empresa_id']."\n";
					}
				} else if ($action == 'SQL') {
					if (substr($line,0,2) != '--') {
						if (strpos($line,';')>-1) {
							$comando .= trim($line).' ';
							echo ' Executanto SQL: '.$comando."\n";
							$result = $this->Model->query($comando);
							if ($result === false) {
							  $fileHandle = fopen('migration.log', 'w');
								fwrite($fileHandle, $fileMIG.' Erro: '.$model.' -> '.$comando."\n");
								exit;
							}
							$comando = '';
						} else {
							$comando .= trim($line).' ';
						}
					}
				}
			}
		}
	}
}
?>
