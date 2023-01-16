<?php
class CcjaPjurPessOrasCodigoShell extends Shell {

	var $uses = array(
		'TCcjaConfClienteJanela',
		'TCppjConfiguracaoPgrPjur'
	);
	
	function main() {
		echo "==================================================\n";
		echo "* Incluir \n";
		echo "* \n";
		echo "* \n";
		echo "==================================================\n\n";

		echo "=> Vincular pjur a tabela ccja_configuracao_cliente_janela para excluir referencia a cppj \n\n";
	}

	function run() {	    
	    $this->populaDadosColuna();
	}

	function populaDadosColuna(){
		$this->TCcjaConfClienteJanela->bindModel(array(
            'belongsTo' => array(
                'TCppjConfiguracaoPgrPjur' => array(
                    'className'  => 'TCppjConfiguracaoPgrPjur',
                    'type' => 'INNER',
                    'foreignKey' => 'ccja_cppj_codigo'
                ),
            ),
        ));
        $fields =  array(
        	'TCcjaConfClienteJanela.ccja_codigo',
        	'TCcjaConfClienteJanela.ccja_cppj_codigo ',
        	'TCcjaConfClienteJanela.ccja_pjur_pess_oras_codigo',
        	'TCppjConfiguracaoPgrPjur.cppj_pjur_pess_oras_codigo',
        );
		$retorno = $this->TCcjaConfClienteJanela->find('all',array('fields' => $fields));
		foreach ($retorno as $dados) {
			$dados['TCcjaConfClienteJanela']['ccja_codigo'];
			$dados['TCcjaConfClienteJanela']['ccja_pjur_pess_oras_codigo'] = $dados['TCppjConfiguracaoPgrPjur']['cppj_pjur_pess_oras_codigo'];
			$this->TCcjaConfClienteJanela->atualizar($dados);
		}

	}					
	
}