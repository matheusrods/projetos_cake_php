<?php

class FichaRetorno extends AppModel {

    var $name = 'FichaRetorno';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'ficha_retorno';
    var $primaryKey = 'codigo';
    var $displayField = '';
    var $actsAs = array('Secure');   
    
   function selecaoEmails($codigoFicha){        
         $condicoes = array('codigo_tipo_retorno' => '2',
            'codigo_ficha' => $codigoFicha,
            );
        return $this->find('all', array('fields' => array('FichaRetorno.descricao'),
                    'conditions' => $condicoes));        
    }
    
    public function duplicar($codigo_ficha, $codigo_ficha_nova) {
        $codigos = $this->find('all', array('conditions' => array(
            'codigo_ficha' => $codigo_ficha
        )));
        
        if (count($codigos) == 0) {
            return true;
        }
        try {
            foreach ($codigos as $ficha_retorno) {
                $ficha_retorno[$this->name]['codigo_ficha'] = $codigo_ficha_nova;
                if (!$this->incluir($ficha_retorno)) {
                    throw new Exception();
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
        
    }
    
    function incluir($dados){ 
        if (!isset($dados[$this->name])) {
            return false;
        }
        unset($dados[$this->name]['codigo']);
        $this->create();
        return $this->save($dados);
    }
    
    function salvarTodosFicha($data, $codigo_ficha){
    	$this->deleteAll(array('codigo_ficha'=>$codigo_ficha));
    	foreach($data as $key=>$value){
    		if(empty($value['codigo_tipo_retorno']))
    			unset($data[$key]);
    		else
    			$data[$key]['codigo_ficha'] = $codigo_ficha;
    	}
    	return $this->saveAll($data, array('validate' => false));
    }

    function salvarRetornoUsuario($codigo_usuario, $codigo_ficha) {
        $this->Usuario = ClassRegistry::init('Usuario');
        $this->Usuario->bindUsuarioContato();
        $dados_usuario = $this->Usuario->find('first',Array('conditions'=>Array('codigo' => $codigo_usuario)));
        //$this->log(var_export($dados_usuario,true),'ws_teleconsult');

        if (!empty($dados_usuario['Usuario']['email'])) {
            $dados_ficha_retorno = Array(
                'codigo_ficha' => $codigo_ficha,
                'codigo_tipo_contato' => 2,
                'codigo_tipo_retorno' => 2,
                'ddi' => 55,
                'nome' => $dados_usuario['Usuario']['nome'],
                'descricao' => $dados_usuario['Usuario']['email']
            );
            if (!$this->incluir(Array('FichaRetorno'=>$dados_ficha_retorno))) {
                $this->invalidates('','Erro ao incluir contato: '.$dados_usuario['Usuario']['email']);
                return false;
            }
        }

        if (isset($dados_usuario['UsuarioContato']) && is_array($dados_usuario['UsuarioContato']) && count($dados_usuario['UsuarioContato'])>0) {
           
            foreach ($dados_usuario['UsuarioContato'] as $seq => $dados_contato) {
                $dados_ficha_retorno = Array(
                    'codigo_ficha' => $codigo_ficha,
                    'codigo_tipo_contato' => 2,
                    'codigo_tipo_retorno' => $dados_contato['codigo_tipo_retorno'],
                    'ddi' => 55,
                    'ddd' => $dados_contato['ddd'],
                    'nome' => $dados_contato['nome'],
                    'descricao' => $dados_contato['descricao']
                );
                if (!$this->incluir(Array('FichaRetorno'=>$dados_ficha_retorno))) {
                    $this->invalidates('','Erro ao incluir contato: '.$dados_contato['descricao']);
                    return false;
                }
            }
        }

        return true;
    }
    
}
