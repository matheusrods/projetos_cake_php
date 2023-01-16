<?php

class MensagemDeAcessoPerfil extends AppModel {

    var $name 			= 'MensagemDeAcessoPerfil';
    var $tableSchema 	= 'dbo';
    var $databaseTable 	= 'RHHealth';
    var $useTable 		= 'mensagens_de_acesso_perfis';
    var $primaryKey 	= 'codigo';
    var $actsAs         = array('Secure');   
    var $displayField   = 'codigo_tipos_perfis';

    public function incluirMultiplo($codigo_mensagem, $codigos, $in_another_transaction = false) {
    	try{            
    		if (!$in_another_transaction) 
                $this->query('begin transaction');    		
	        foreach($codigos as $codigo){
	        	$mensagem_perfil = array(
	        			'MensagemDeAcessoPerfil' => array(
	        				'codigo_mensagens_de_acesso' => $codigo_mensagem,
	        				'codigo_tipos_perfis' => $codigo
	    				)
	        		);	        	
	        	if(!$this->incluir($mensagem_perfil)) 
                    throw new Exception("Error Processing Request", 1);
	        }
	        if (!$in_another_transaction) 
                $this->commit();            
        	return true;
    	} catch (Exception $ex) {
            if (!$in_another_transaction) 
                $this->rollback();
            return false;
        }
    }

    public function atualizarMultiplo($codigo_mensagem, $codigos, $in_another_transaction = false) {
        try{            
            if (!$in_another_transaction) 
                $this->query('begin transaction');            
            if (!$this->deletarPorMensagem($codigo_mensagem)) 
                throw new Exception("Error Processing Request", 1);
            if (!$this->incluirMultiplo($codigo_mensagem, $codigos, $in_another_transaction)) 
                throw new Exception("Error Processing Request", 1);            
            if (!$in_another_transaction) 
                $this->commit();
            return true;
        } catch (Exception $ex) {
            if (!$in_another_transaction) 
                $this->rollback();
            return false;
        }
    }

    public function deletarPorMensagem($codigo_mensagem){
        return $this->deleteAll(array('codigo_mensagens_de_acesso' => $codigo_mensagem));
    }

    public function findByMensagem($codigo_mensagem){        
        return $this->find('list', 
            array('conditions' => array(
                    'MensagemDeAcessoPerfil.codigo_mensagens_de_acesso' => $codigo_mensagem
                )
            )
        );
    }
}