<?php
class StatusProfissionalShell extends Shell {

		function main() {
			echo "==================================================\n";
			echo "* Incluir \n";
			echo "* \n";
			echo "* \n";
			echo "==================================================\n\n";

			echo "=> incluir_sm_basica: Realiza a inserção de SM no modo básico conforme a necessidade do cliente \n\n";
		}

		function status_profissional(){
			$handle = fopen("C:\\home\\sistemas\\portal\\portal_trunk\\app\\vendors\\shells\\teste.csv", "r");
			if ($handle) {
				$i = 0;
			    while (!feof($handle)) {
			        $codigo_documento_profissional = trim(fgets($handle, 4096));
			        $status[$i]['status'] = $this->busca_status($codigo_documento_profissional);
			        $status[$i]['codigo_documento'] = $codigo_documento_profissional;
			        $i++;
			    }
			    if(isset($status)){
			    	$this->exportProfissionais($status);
			    }

			    fclose($handle);
			}
		}

		function busca_status($codigo_documento_profissional){
			$codigo_documento_profissional = str_pad($codigo_documento_profissional, 11, "0", STR_PAD_LEFT);
			
			$this->Profissional = ClassRegistry::init('Profissional');
			$this->ProfissionalLog = ClassRegistry::init('ProfissionalLog');
			if(!$this->Profissional->buscaPorCPF($codigo_documento_profissional)){
				return 'NÃO CADASTRADO';
			}else{
				$status = $this->buscaStatusUltimaFichaPorDocumento($codigo_documento_profissional, true);
				if(empty($status)){
					return 'PROFISSIONAL CADASTRADO E SEM FICHA';
				}else{
					return $status;
				}
			}
		}

		function buscaStatusUltimaFichaPorDocumento($documento_profissional){
	        $this->Ficha = ClassRegistry::init('Ficha');
	        $this->Status = ClassRegistry::init('Status');
			$this->ProfissionalLog = ClassRegistry::init('ProfissionalLog');
	        $joins = array(
	            array(
	                'table'      => $this->Ficha->databaseTable.'.'.$this->Ficha->tableSchema.'.'.$this->Ficha->useTable,
	                'alias'      => 'Ficha',
	                'type'       => 'LEFT',
	                'conditions' => 'ProfissionalLog.codigo = Ficha.codigo_profissional_log',
	            ),

	            array(
	                'table'      => $this->Status->databaseTable.'.'.$this->Status->tableSchema.'.'.$this->Status->useTable,
	                'alias'      => 'Status',
	                'type'       => 'INNER',
	                'conditions' => 'Ficha.codigo_status = Status.codigo',
	            )
	        );
	        $condicoes['ProfissionalLog.codigo_documento']      = $documento_profissional;
	        $fields = array('Status.descricao', 'CONVERT(VARCHAR, Ficha.data_validade,120) AS data_validade');
	        $status = $this->ProfissionalLog->find('first', array(
	                'fields' 		=> $fields,
	                'conditions' 	=> $condicoes,
	                'order' 		=> array('Ficha.data_inclusao DESC'),
	                'joins' 		=> $joins,
	            )
	        );

	        return $status;
    	}

    	function exportProfissionais($dados) {

    		file_put_contents("C:\\home\\sistemas\\portal\\portal_trunk\\app\\vendors\\shells\\status_profissional.csv", '"CPF Profissional";"Status";"Data Validade";'."\n", FILE_APPEND);
			foreach ($dados as $dado) {
	           	$linha = '"'.$dado['codigo_documento'].'";';
				$linha .= '"'.(isset($dado['status']['Status']['descricao'])?$dado['status']['Status']['descricao']:$dado['status']).'";';
				$linha .= '"'.(isset($dado['status'][0]['data_validade'])?$dado['status'][0]['data_validade']:'').'";';
			    $linha .= "\n";
			    file_put_contents("C:\\home\\sistemas\\portal\\portal_trunk\\app\\vendors\\shells\\status_profissional.csv", utf8_decode($linha), FILE_APPEND);
	        }
	        die();
		}
			
	
}