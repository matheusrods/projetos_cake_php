<?php
class PropostaCredDocumento extends AppModel {

    var $name = 'PropostaCredDocumento';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'propostas_credenciamento_documentos';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

	var $validate = array(
        'codigo_proposta_credenciamento' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código de Proposta!'
		),	
        'codigo_tipo_documento' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código do Tipo de Documento!'
		),
        'caminho_arquivo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Caminho do Arquivo!'
		)		
	);  

	function retorna_proposta_pendente_documento(){
		$PropostaCredenciamento =& ClassRegistry::Init('PropostaCredenciamento');
		$TipoDocumento =& ClassRegistry::Init('TipoDocumento');

		$dados = $PropostaCredenciamento->query('SELECT PropostaCredenciamento.codigo, 
														PropostaCredenciamento.razao_social,
														PropostaCredenciamento.email,
														convert(varchar(10),PropostaCredenciamento.data_inclusao, 126) AS data_inclusao, 
								 		    (SELECT 	count(*) FROM propostas_credenciamento_documentos PropostaCredDocumento 
										    LEFT JOIN 	tipos_documentos TipoDocumento ON TipoDocumento.codigo = PropostaCredDocumento.codigo_tipo_documento 
										    WHERE 		codigo_proposta_credenciamento = PropostaCredenciamento.codigo AND 
										    			TipoDocumento.obrigatorio = 1 AND 
										    			TipoDocumento.status = 1) AS qtd_obrigatorio
									FROM propostas_credenciamento PropostaCredenciamento 
									WHERE	PropostaCredenciamento.codigo_status_proposta_credenciamento = 7
									ORDER BY PropostaCredenciamento.codigo'); //Documentação Solicitada

		if(!empty($dados)){			
			$dados_email = array();
			$docs_pendentes = array();
			$status_pendente = array();
			$c = 0;
			$i = 0;

			$docs_obrigatorios = $TipoDocumento->find('count', array('conditions' => array('obrigatorio' => 1, 'status' => 1)));			

			foreach ($dados as $key => $value) {	
				$data_inclusao  = strtotime($dados[$key][0]['data_inclusao']);
	    		$data_atual    = strtotime(date("Y-m-d"));

				$qtd_dias = Comum::diffDate($data_inclusao, $data_atual);

				if($qtd_dias['dia'] > 0){

					if(($qtd_dias['dia'] % 5) == 0){ //envia e-mail a cada 5 dias;
						debug('Proposta: '.$dados[$key][0]['codigo'].' - '.'Credenciado: '.$dados[$key][0]['razao_social'].' - '.' Pendente: '.$qtd_dias['dia'].' dias-'.$dados[$key][0]['email']);
						
						$PropostaCredenciamento->disparaEmail($dados[$key][0], $dados[$key][0]['razao_social'] . ' - Documentação Pendente', 'envio_documentacao_pendente_credenciado', $dados[$key][0]['email']);
					}
				}
			}
		}
	}

	function retorna_proposta_pendente_documento_rhhealth(){
		$PropostaCredenciamento =& ClassRegistry::Init('PropostaCredenciamento');
		$TipoDocumento =& ClassRegistry::Init('TipoDocumento');
		$Configuracao =& ClassRegistry::Init('Configuracao');
		$Usuario =& ClassRegistry::Init('Usuario');
		
		$dados = $PropostaCredenciamento->query('SELECT 
													PropostaCredenciamento.codigo, 
									                PropostaCredenciamento.razao_social,
									                (CASE WHEN PropostaCredenciamento.codigo_usuario_inclusao is not null 
									                    THEN PropostaCredenciamento.codigo_usuario_inclusao 
									                    	ELSE PropostaCredenciamento.codigo_usuario_alteracao
									                 END ) AS  codigo_usuario,
									                convert(varchar(10),PropostaCredenciamento.data_inclusao, 126) AS data_inclusao, 
												    (SELECT 	count(*) FROM propostas_credenciamento_documentos PropostaCredDocumento 
												    LEFT JOIN 	tipos_documentos TipoDocumento ON TipoDocumento.codigo = PropostaCredDocumento.codigo_tipo_documento 
												    WHERE 		codigo_proposta_credenciamento = PropostaCredenciamento.codigo AND 
												                TipoDocumento.obrigatorio = 1 AND 
												                TipoDocumento.status = 1) AS qtd_obrigatorio
												FROM propostas_credenciamento PropostaCredenciamento 
												WHERE PropostaCredenciamento.codigo_status_proposta_credenciamento = 8'); ////Aguardando Análise de Documentos
		if(!empty($dados)){			
			$dados_email = array();
			$docs_pendentes = array();
			$status_pendente = array();
			$c = 0;
			$i = 0;

			$docs_obrigatorios = $TipoDocumento->find('count', array('conditions' => array('obrigatorio' => 1, 'status' => 1)));			

			foreach ($dados as $key => $value) {
				if($dados[$key][0]['qtd_obrigatorio'] != $docs_obrigatorios){ //SE A QUANTIDADE DE DOCUMENTOS ENVIADOS FOR DIFERENTE DA OBRIGATORIA.				

					$data_inclusao  = strtotime($dados[$key][0]['data_inclusao']);
		    		$data_atual    = strtotime(date("Y-m-d"));

					$qtd_dias = Comum::diffDate($data_inclusao, $data_atual);

					if($qtd_dias['dia'] > 0){
						if(($qtd_dias['dia'] % 5) == 0){//VERIFICA A CADA 5 DIAS AS PROPOSTAS PENDENTES.
							debug('Proposta: '.$dados[$key][0]['codigo'].' Pendente: '.$qtd_dias['dia'].' dias');

							$email = $Usuario->find('first', array('conditions'=> array('codigo' => $dados[$key][0]['codigo_usuario'])));
							$c++;
							$credenciado = array( 
								$c => array(
									'codigo' => $dados[$key][0]['codigo'],
									'razao_social' => $dados[$key][0]['razao_social'],
									'dias_pendente' => $qtd_dias['dia'],
									'email' => $email['Usuario']['email']
									)
								);
							
							$docs_pendentes = array_merge($docs_pendentes, $credenciado);							
						}
					}
				}
				else{//A QUANTIDADE DE DOCUMENTOS ESTA OK, POREM AINDA FALTA APROVAR.
					debug('Proposta: '.$dados[$key][0]['codigo'].' Status Pendente');
					$i++;
					$email = $Usuario->find('first', array('conditions'=> array('codigo' => $dados[$key][0]['codigo_usuario'])));
					$cred = array( 
						$i => array(
							'codigo' => $dados[$key][0]['codigo'],
							'razao_social' => $dados[$key][0]['razao_social'],
							'dias_pendente' => 'Pendete Aprovação',
							'email' => $email['Usuario']['email']
							)
						);
							
					$status_pendente = array_merge($status_pendente, $cred);			
				}
			}

			$dados_email = array_merge($docs_pendentes, $status_pendente);		

			//$configuracao = $Configuracao->find('first', array('conditions' => array('chave' => 'PROPOSTA_DOCUMENTACAO_PENDENTE')));			
			//$emails = explode(';', $configuracao['Configuracao']['valor']);
			if($c>0 || $i>0){

				foreach ($dados_email as $key => $email) {
					if(!empty($email))
						$PropostaCredenciamento->disparaEmail($dados_email[$key], 'Documentação do Credenciamento Pendente', 'envio_documentacao_pendente_rhhealth', trim($dados_email[$key]['email']));
				}
			}				
		}
	}
}
