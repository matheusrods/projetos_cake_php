<?php
class FornecedorDocumento extends AppModel {

    var $name = 'FornecedorDocumento';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'fornecedores_documentos';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    var $validate = array(
        'codigo_fornecedor' => array(
            'rule' => 'notEmpty',
			'message' => 'Fornecedor não informado.'
        ),
        'codigo_tipo_documento' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Tipo de Documento!'
				),
			'verificaUnique' => array(
                'rule' => 'verificaUnique',
                'message' => 'Documento já existente!',
            )
		),	
        'caminho_arquivo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Arquivo!'
		),
		//trecho comentado por que nem todos os documentos precisam ter a data de validade
		// 'data_validade' => array(
		// 	'rule' => 'notEmpty',
		// 	'message' => 'Informe a Data de Validade!'
		// ),
    );

	function verificaUnique() {
	    $verifica = $this->find('count', array('conditions' => array('codigo_tipo_documento' => $this->data['FornecedorDocumento']['codigo_tipo_documento'], 'codigo_fornecedor' => $this->data['FornecedorDocumento']['codigo_fornecedor']))) < 1 ;

	    if($verifica == 0){
            return false;
		 }       
		 else{
            return true;        	
		}
    }


function verifica_validade_documento(){
		$Fornecedor =& ClassRegistry::Init('Fornecedor');
		$TipoDocumento =& ClassRegistry::Init('TipoDocumento');
		$Configuracao =& ClassRegistry::Init('Configuracao');
		$MailerOutbox =& ClassRegistry::Init('MailerOutbox');

		$conditions = array('data_validade IS NOT NULL AND data_validade < getdate()');
		$fields = array('FornecedorDocumento.codigo_fornecedor', 'count(*) as qtd');
		$order = array('FornecedorDocumento.codigo_fornecedor ASC');

        $group = array('FornecedorDocumento.codigo_fornecedor');

		$dados = $this->find('all', compact('conditions', 'fields','order', 'group'));
		$dados_email = array();
		$c = 0;
		if(!empty($dados)){	

			foreach ($dados as $key => $value) {
				$conditions = array(
					'FornecedorDocumento.codigo_fornecedor' => $dados[$key]['FornecedorDocumento']['codigo_fornecedor'],
					'data_validade IS NOT NULL AND data_validade < getdate()'
				);

				$joins 	= array(
						array(
							'table'	=> $Fornecedor->databaseTable.'.'.$Fornecedor->tableSchema.'.'.$Fornecedor->useTable,
							'alias'	=> 'Fornecedor',
							'conditions' => 'Fornecedor.codigo = FornecedorDocumento.codigo_fornecedor',
						),
						array(
							'table'	=> $TipoDocumento->databaseTable.'.'.$TipoDocumento->tableSchema.'.'.$TipoDocumento->useTable,
							'alias'	=> 'TipoDocumento',
							'conditions' => 'TipoDocumento.codigo = FornecedorDocumento.codigo_tipo_documento',
						),
					);

				$fields = array(
					'Fornecedor.codigo', 
					'Fornecedor.razao_social', 
					'TipoDocumento.descricao', 
					'FornecedorDocumento.data_validade' 
					);

				$documentos = $this->find('all', compact('conditions', 'joins', 'fields'));
				
				if(!empty($documentos)){
					
					foreach ($documentos as $k => $valor) {
						$c++;
						$documentos_vencidos = array(
							$k => array(
										'codigo_fornecedor' => $documentos[$k]['Fornecedor']['codigo'],
										'razao_social' => $documentos[$k]['Fornecedor']['razao_social'],
										'descricao_documento' => $documentos[$k]['TipoDocumento']['descricao'],
										'data_validade' => $documentos[$k]['FornecedorDocumento']['data_validade']
							)
						);

						$dados_email = array_merge($dados_email, $documentos_vencidos);							
						debug('Fornecedor: '.$documentos[$k]['Fornecedor']['codigo'].'-'.$documentos[$k]['Fornecedor']['razao_social'].'; Documento:'.$documentos[$k]['TipoDocumento']['descricao'].'; Data de Validade: '.$documentos[$k]['FornecedorDocumento']['data_validade']);
					}
				
				}		
			}
					
		}

		$configuracao = $Configuracao->find('first', array('conditions' => array('chave' => 'FORNECEDOR_DOCUMENTACAO_VENCIDA')));			
		$emails = explode(';', $configuracao['Configuracao']['valor']);

		if($c > 0){
			foreach ($emails as $email) {
				if(!empty($email)){		
				$dados = $dados_email;
					$MailerOutbox->enviaEmail($dados_email, 'Documentação Vencida', 'envio_documentacao_validade_vencida', trim($email));
				}
			}
		}
	}

	function retorna_documentos_enviados($codigo_fornecedor){
		 $this->bindModel(array(
           'belongsTo' => array(
               'TipoDocumento' => array(
                   'alias' => 'TipoDocumento',
                   'foreignKey' => FALSE,
                   'type' => 'LEFT',
                   'conditions' => 'TipoDocumento.codigo = FornecedorDocumento.codigo_tipo_documento'
               ),
               'Fornecedor' => array(
               		'alias' => 'Fornecedor',
               		'foreignKey' => FALSE,
               		'type' => 'LEFT',
               		'conditions' => 'Fornecedor.codigo = FornecedorDocumento.codigo_fornecedor'
               ),
               'PropostaCredenciamento' => array(
               		'alias' => 'PropostaCredenciamento',
               		'foreignKey' => FALSE,
               		'type' => 'LEFT',
               		'conditions' => 'Fornecedor.codigo_documento = PropostaCredenciamento.codigo_documento'
               ),
            )
        ));

		$data_inicio = date('d/m/Y');

        $this->virtualFields['validade'] = "CASE WHEN FornecedorDocumento.data_validade < '".AppModel::dateToDbDate2($data_inicio)."' THEN 'VENCIDO' ELSE 'OK' END";
        $documentos_enviados = $this->find('all', array('conditions' => array('codigo_fornecedor' => $codigo_fornecedor), 'order' => 'ordem_exibicao'));
        return $documentos_enviados;

	}

	function retorna_documentos_pendentes($codigo_fornecedor){
		$TipoDocumento =& ClassRegistry::Init('TipoDocumento');
		
		$documentos_pendentes = $TipoDocumento->find('all', array(
			'conditions' => array(
				'status' => 1,
				'codigo NOT IN (
					SELECT codigo_tipo_documento 
					FROM '.$this->databaseTable.'.'.$this->tableSchema.'.'.$this->useTable.' 
					WHERE codigo_fornecedor = '.$codigo_fornecedor.' )'
				)
			)
		);
		
        return $documentos_pendentes;
	}
}
