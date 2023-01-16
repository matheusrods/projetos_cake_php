<?php
/** 
 * Shell para carregar os arquivos de setores e cargos da simens
 * 
 * @author Willians Paulo Pedroso <williansbuonny@gmail.com>
 * @version 0.1 
 * @package Cron
 * @example cake/console/cake -app ./app carregar_codigo_externo (cargo/setor)
 */


class ImportarFuncionarioShell extends Shell {
	var $uses = array('Importar','Exame','Servico', 'Esocial','GrupoEconomicoCliente','Atestados','PedidoExame','Cliente','ImportacaoEstrutura','GrupoEconomico','RegistroImportacao');
	var $arquivo;

	public function main() {
		echo "*******************************************************************\n";
		echo "* importar planilha gigante de funcionarios 						 \n";
		echo "* cake/console/cake -app ./app importar_funcionario importar codigo_cliente nome_planilha\n";
		echo "* COLOCAR OS ARQUIVOS NO CAMINHO APP/TMP\n";
		echo "*******************************************************************\n";
	}

	public function importacao()
	{
		//pega o segundo parametro
		$codigo_cliente = (isset($this->args[0])) ? $this->args[0] : '';
		$nome_arquivo = (isset($this->args[1])) ? $this->args[1] : '';

		if(empty($codigo_cliente)) {
			echo "PRECISA SER SETADO O CODIGO_CLIENTE PARA RELACIONAR CORRETAMENTE OS SETORES.\n";
			exit;
		}

		//busca os arquivo para ler na tmp
		$path = TMP.DS.$nome_arquivo;

		//verifica se o arquivo existe
		if(!is_file($path)) {
			echo "FAVOR COLOCAR O ARQUIVO {$path} NO CAMINHO APP/TMP\n";
			exit;
		}//fim is_file

        $path_destino = APP.'tmp'.DS;
        $arquivo_destino = $nome_arquivo;

        $_SESSION['Auth']['Usuario']['codigo'] = 1;
        $_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
        
        if ($this->ImportacaoEstrutura->incluir($path_destino, $arquivo_destino, $codigo_cliente)) {
            echo "carregou com sucesso!\n";
        } else {            
        	$error = $this->ImportacaoEstrutura->invalidFields();
            print_r($error);
            echo "erro ao carregar!\n";
        }
        
	}

	


}
?>
