<?php
class IntEsocialCertificado extends AppModel {

	public $name		   	= 'IntEsocialCertificado';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'int_esocial_certificado';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_int_esocial_certificado'));

	public $validate = array(
		'codigo_empresa' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Empresa!'
		),
		'codigo_cliente' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Cliente!'
		),
		'caminho_arquivo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Arquivo do Certificado!'
		),
		'senha_certificado' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Senha do Certificado!'
		),
		'ativo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe se está ativo!'
		),

		'email_responsavel' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o email do responsável do certificado!'
		),
		'razao_social' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a razão social do certificado!'
		),
	);
	
	/**
	 * [getCertificadosCliente metodo para buscar se tem certificado configurado, retornando os dados do certificado importado]
	 * @param  [type] $codigo_cliente [description]
	 * @return [type]                 [description]
	 */
	public function getCertificadosCliente($codigo_cliente)
	{

		$certificados = $this->find('first', array(
    				'fields' => array('codigo','ambiente_esocial'), 
    				'conditions' => array(
    					'codigo_cliente' => $codigo_cliente,
    					'ativo' => 1,
    					'data_integracao IS NOT NULL',
    					'codigo_retorno_tecnospeed IS NOT NULL'
    				) 
    			) );

		if(!empty($certificados)) {
			return $certificados;
		}

		//caso não tenha certificado configurado busca pelo cnpj real para saber se o cliente que essa empresa responde tem certificado.
		// $this->Cliente = ClassRegistry::init('Cliente');

		// $joins = array(
		// 	array(
		// 		'table' => 'RHHealth.dbo.grupos_economicos',
		// 		'alias' => 'GrupoEconomico',
		// 		'type' => 'INNER',
		// 		'conditions' => 'Cliente.codigo_documento_real = ClienteReal.codigo_documento',
		// 	),
		// );

		// $cliente = $this->Cliente->find('sql',array(
		// 	'fields' => array('ClienteReal.codigo','ClienteReal.codigo_documento','Cliente.codigo','Cliente.codigo_documento_real','Cliente.codigo_documento'),
		// 	'joins' => $joins,
		// 	'conditions' => array('Cliente.codigo' => $codigo_cliente)
		// ));
		
		$this->GrupoEconomicoCliente = ClassRegistry::init('GrupoEconomicoCliente');
		$cliente = $this->GrupoEconomicoCliente->find('first',array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente)));
		// debug($cliente);exit;
		//verifica se achou algum resultado
		if(!empty($cliente)) {
			$codigo_cliente = $cliente['GrupoEconomico']['codigo_cliente'];

			$certificados = $this->find('first', array(
	    				'fields' => array('codigo','ambiente_esocial'), 
	    				'conditions' => array(
	    					'codigo_cliente' => $codigo_cliente,
	    					'ativo' => 1,
	    					'data_integracao IS NOT NULL',
	    					'codigo_retorno_tecnospeed IS NOT NULL'
	    				) 
	    			) );

			if(!empty($certificados)) {
				return $certificados;
			}
		}//fim cliente empty

		return false;


	}//fim getCertificadosCliente

    public function getCertificados(){

        $codigo_cliente = "";
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
            if(empty($filtros['codigo_cliente'])) {
                $codigo_cliente = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        if(empty($codigo_cliente)){
            $conditions[] = 'IntEsocialCertificado.codigo_cliente IS NOT NULL';
        } else {
            $conditions[] = 'IntEsocialCertificado.codigo_cliente = '.$codigo_cliente;
        }

        $certificados = array();
        $certificados = $this->find('list', array('conditions' => $conditions, 'fields' => array('codigo', 'nome_arquivo')));

        return $certificados;
    }



}//FINAL CLASS IntEsocialCertificado