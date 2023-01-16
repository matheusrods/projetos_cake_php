<?php
class AuthorizationShell extends Shell {
	var $uses = array('Uperfil', 'ObjetoAcl', 'DependenciaObjAcl');

	function startup(){
		//deverá ser passado o domínio completo, 
		//exemplo: buonny.com.br / gol.local.buonny / localhost
		$_SERVER['SERVER_NAME'] = isset($this->args[0]) ? $this->args[0] : 'localhost';
	}
	
	function main() {
		echo "Recarrega as permissoes dos perfis\n";
        echo "  cake\console\cake authorization reload\n\n";
        echo "Lista os objetos cadastrados invalidos\n";
        echo "  cake\console\cake authorization checkNodes\n";
	}

	function isRoot(){
		return (getenv('LOGNAME') == 'root');
	}

	function reload(){
		if(!$this->isRoot()){
			echo "\n";
	    	echo "Execute a funcao como root (utilize SUDO)\n";	
			return false;
		}

	    echo "\n";
	    echo "Iniciando processamento\n";
	    $perfis = $this->Uperfil->find('list');
	    foreach ($perfis as $codigo_uperfil => $descricao_perfil) {
	    	echo "Recarregando {$codigo_uperfil} - {$descricao_perfil}\n";
			$permissoes = $this->Uperfil->listaPermissoes($codigo_uperfil, true);
			$this->Uperfil->geraPermissao($codigo_uperfil, $permissoes);
	    }
		echo "Finalizado processamento\n";
	}	

	function checkNodes() {
		$this->Acl = new AclComponent();
		$objetos = $this->ObjetoAcl->listaObjetos();
		$objetos = $this->openNodes($objetos);
		$dependencias = array();
		foreach ($objetos as $objeto) {
			$sub_aco_string = str_replace('/', '__', str_replace('buonny/', '', $objeto));
			$dependencias = array_merge($dependencias, $this->DependenciaObjAcl->listaDependencias($sub_aco_string));
		}
		$objetos = array_merge($objetos, $dependencias);
		foreach ($objetos as $key => $value) {
			$aco_node = $this->Acl->Aco->node($value);
			if ($aco_node == false) {
				echo $value."\n";
			}
		}
		echo "\n\n";
	}

	private function openNodes($nodes, $pre = '') {
		$return = array();
		foreach ($nodes as $node) {
			$aco_string = trim($node['ObjetoAcl']['aco_string']);
			if (!empty($aco_string)) {
				$return[] = 'buonny/'.str_replace('__','/',$aco_string);
			}
			if ($node['children'])
				$return = array_merge($return, $this->openNodes($node['children']));
		}
		return $return;
	}
}
?>
