<?php
class SincronizaPortalCrmShell extends Shell {
	var $uses = array(
		'Cliente',
	);

	function main() {
		echo "==================================================\n\n";
		echo "=> Shell inclui clientes que existam no Portal e não existam no CRM \n\n";
	}

	function run() {
		if (!$this->im_running('sincroniza_portal_crm'))
        	$this->verificaClientes('2015-03-31 00:00:00','2015-04-02 23:59:59');
    }
    

	private function im_running($tipo) {
		$cmd = shell_exec("ps aux | grep '{$tipo}'");
		// 1 execução é a execução atual
		return substr_count($cmd, 'cake.php -working') > 1;
	}

    function verificaClientes($data_inicio,$data_fim){
    	$this->Cliente->bindModel(
	 		array(
	 			'hasOne' => array(
		            'ClienteEndereco' => array(
		            	'foreignKey' => false,
		            	'conditions' => array('Cliente.codigo = ClienteEndereco.codigo_cliente')
		            ),
		            'VEndereco' => array(
		            	'foreignKey' => false,
		            	'conditions' => array('ClienteEndereco.codigo_endereco = VEndereco.endereco_codigo')
		            ),
        		)
	 		)
	 	);
    	if(!empty($data_inicio) && !empty($data_fim)){
	    	$clientes = $this->Cliente->find('all',array(
	    		'conditions' => array(
	    			'Cliente.data_inclusao BETWEEN ? AND ?' => array(
	    				$data_inicio,
	    				$data_fim
	    			)
	    		)
	    	));
	    }
    	$this->insereCliente($clientes);

    }


    public function insereCliente($dados){
	    foreach ($dados as $dado) {
	    	$corporacao = $dado['Cliente']['codigo_corporacao'];
	    	$codigo_endereco_regiao = $dado['Cliente']['codigo_endereco_regiao'];
	    	$codigo_documento = $dado['Cliente']['codigo_documento'];
	    	$razao_social = $dado['Cliente']['razao_social'];
	    	$inscricao_estadual = $dado['Cliente']['inscricao_estadual'];
	    	$codigo_gestor = $dado['Cliente']['codigo_gestor'];
	    	$codigo_corretora = $dado['Cliente']['codigo_corretora'];
	    	$codigo_seguradora = $dado['Cliente']['codigo_seguradora'];
	    	$codigo_endereco_regiao = $dado['Cliente']['codigo_endereco_regiao'];
	    	$codigo_usuario_inclusao = $dado['Cliente']['codigo_usuario_inclusao'];
	    	$ccm = $dado['Cliente']['ccm'];
	    	$codigo_cliente_tipo = $dado['ClienteTipo']['codigo'];
	    	$codigo_cliente_sub_tipo = $dado['ClienteSubTipo']['codigo'];

	    	$endereco_cep = $dado['ClienteSubTipo']['codigo'];
	    	$codigo_endereco = $dado['ClienteSubTipo']['codigo'];
	    	$numero = $dado['ClienteSubTipo']['codigo'];
	    	$complemento = $dado['ClienteSubTipo']['codigo'];
	    	
	    	$url = "http://tstcrm.buonny.com.br/index.php?module=Accounts&action=AccountsAjax&file=Save&cf_905=$codigo_documento&dup_check=true&record=&accountname=$razao_social&cf_1156=$inscricao_estadual&cf_1155=$ccm&cf_1138=$endereco_cep&cf_1139=$codigo_endereco&cf_1140=$numero&cf_1141=$complemento&cf_1142=$codigo_endereco_regiao&cf_1143=$codigo_gestor&cf_1144=$codigo_cliente_tipo&cf_1145=$codigo_cliente_sub_tipo&cf_1146=$codigo_corretora&cf_1147=$codigo_seguradora&cf_1148=$corporacao";
	    	exec('C://Mozilla//firefox.exe "'.$url.'"');
	    	sleep(6);
	    }
    }


}


