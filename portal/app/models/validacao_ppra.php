<?php
class ValidacaoPpra extends AppModel {
	var $name = 'ValidacaoPpra';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'validacao_ppra';
	var $primaryKey = 'codigo';
    var $actsAs = array('Secure','Containable');


    /**
     * [inserir_validacao_ppra description]
     * 
     * metodo para inserir que deve validar o pcmso
     * 
     * @param  [type] $dados [description]
     * @return [type]        [description]
     */
    public function inserir_validacao_ppra($dados)
    {
    	//variavel de retorno
    	$retorno = true;

    	//verifica se existe esse registro com essa configuração para nao colocar registro duplicado
    	$validacao_ppra = $this->find('first', array('conditions' => $dados));

    	//verifica se existe registro na tabela
    	if(empty($validacao_ppra)) {
	    	//monta os dados para insercao na tabela
	    	$validar['ValidacaoPpra'] = array( 
	            'codigo_grupo_exposicao' => $dados['codigo_grupo_exposicao'],
	            'codigo_funcionario'     => $dados['codigo_funcionario'],
	            'codigo_cliente_alocacao'=> $dados['codigo_cliente_alocacao'],
	            'codigo_setor'           => $dados['codigo_setor'],
	            'codigo_cargo' 			 => $dados['codigo_cargo'],
	            'status_validacao'		 => 0
	        );

	    	//debug($validar);exit;
	        if(!$this->incluir($validar)){
	        	$retorno = false;	
	        }

    	}//fim validacao ppra

        return $retorno;

    }//fim inserir_validacao_ppra



    public function valida_pcmso($codigo_cliente_alocacao, $codigo_setor, $codigo_cargo, $codigo_funcionario = null)
    {   
        
        $retorno = true;

        $conditions = array(
            'status_validacao' => '0',
            'codigo_cliente_alocacao'=> $codigo_cliente_alocacao, 
            'codigo_setor' => $codigo_setor, 
            'codigo_cargo' => $codigo_cargo,            
        );
        
        if(!empty($codigo_funcionario) ){            
            if($codigo_funcionario != 'null') {
                $conditions[] = 'codigo_funcionario = '.$codigo_funcionario;
            }
        }

        $fields = 'codigo';

        //buscar o codigo da validacao ppra
        $codigo_validacao_ppra = $this->find('first', array('conditions' => $conditions, 'fields' => $fields));

        // debug($codigo_validacao_ppra);exit;

        if(!empty($codigo_validacao_ppra)) {
            //alterar o status_validacao de 0 para 1
            $validar_pcmso['ValidacaoPpra'] = array( 
               'codigo'             => $codigo_validacao_ppra['ValidacaoPpra']['codigo'],
               'status_validacao'   => 1
            );

            if(!$this->atualizar($validar_pcmso)) {
                //retornar erro
                $retorno = false;
            }
        }

        // debug($validar_pcmso);exit;
        return $retorno;
    }


}