<?php

class ContatoRetornoBase extends AppModel {
    var $name = 'ContatoRetornoBase';
    var $tableSchema = false;
    var $databaseTable = false;
    var $useTable = false;

    public function validaPeloTipoRetorno($value){
        App::import('Model', 'TipoRetorno'); 
        //$codigo_tipo_retorno = $this->data[$this->name]['codigo_tipo_retorno'];
        
        if(isset($this->data) && $this->data != NULL){
            $codigo_tipo_retorno = $this->data[$this->name]['codigo_tipo_retorno'];
        } else{
            $codigo_tipo_retorno = $value[$this->name]['codigo_tipo_retorno'];  
            $value['descricao']  = $value[$this->name]['descricao'];
        }
                
        if($codigo_tipo_retorno == TipoRetorno::TIPO_RETORNO_TELEFONE){
            if(!preg_match('/\(\d{2}\)\d{4}-\d{4,5}/', $value['descricao']))
                return "Telefone inválido";
        }else if($codigo_tipo_retorno == TipoRetorno::TIPO_RETORNO_EMAIL){
            if (empty($value['descricao'])) {
                return "E-mail inválido";
            } 
            //if(!Validation::email($value['descricao'], true))
            $er = "/^(([0-9a-zA-Z]+[-._+&])*[0-9a-zA-Z]+@([-0-9a-zA-Z]+[.])+[a-zA-Z]{2,6}){0,1}$/";
            if (preg_match($er, $value['descricao'])){
                return true;
            } else {
                return "E-mail inválido";
            }

                
        }else if(!empty($codigo_tipo_retorno)){
            if(empty($value['descricao']))
                return "Campo obrigatório";
        }
        return true;
    }
    
    public function validarContatos($data){
        $this->validate = array(
                'descricao' => array(
                        'rule'    => array('validaPeloTipoRetorno'),
                ),
                'codigo_tipo_contato' => array(
                        'rule'    => 'tipoObrigatorio',
                        'message' => 'Selecione o tipo de contato'
                ),
                'nome' => array(
                    'rule' => 'NotEmpty',
                    'message' => 'Informe nome do contato'
                ),
        );        
        
        return $this->saveAll($data, array('validate' => 'only'));
    }

    public function validarContatosFichaScoreCard($data){

       $this->validate = array(
                'descricao' => array(
                    'validaPeloTipoRetorno' => array(
                        'rule'    => 'validaPeloTipoRetorno',
                        'message' => 'Informe um contato válido'
                    ),
                    'NotEmpty' => array(
                        'rule' => 'NotEmpty',
                        'message' => 'Informe um contato válido'
                    )
                ),
                'codigo_tipo_contato' => array(
                    'tipoObrigatorio' => array(
                        'rule'    => 'tipoObrigatorio', 
                        'message' => 'Informe um tipo contato válido'   
                    ),
                    'NotEmpty' => array(
                        'rule'    => 'NotEmpty', 
                        'message' => 'Informe um tipo contato válido'   
                    )
                ),
                'nome' => array(
                    'rule' => 'NotEmpty',
                    'message' => 'Informe nome do contato'
                ),
                'codigo_tipo_retorno' => array(
                    'rule' => 'NotEmpty',
                    'message' => 'Informe uma referência'
                )
        );        
        
        return $this->saveAll($data, array('validate' => 'only'));
    }
    
    public function tipoObrigatorio(){
        if(!empty($this->data[$this->name]['codigo_tipo_retorno']))
            return !empty($this->data[$this->name]['codigo_tipo_contato']);
        return true;
    }
    
}
