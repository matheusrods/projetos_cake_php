<?php
App::import('Model', 'Usuario');
class UsuarioContato extends AppModel {
    var $name = 'UsuarioContato';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'usuario_contato';
    var $primaryKey = 'codigo';    
    var $actsAs = array('Secure');
    var $belongsTo = array(
       'TipoRetorno' => array(
           'className' => 'TipoRetorno',
           'foreignKey' => 'codigo_tipo_retorno'
       )
    );

    var $validate = array(
        'codigo_usuario' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o cliente',
            'required' => true
        ),
        'codigo_tipo_retorno' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o tipo',
                'required' => true
            )           
        ),
        'descricao' => array(
            'trataDescricao' => array(
                'rule' => 'trataDescricao',
                'message' => 'Informação inválida',
                'required' => true
            ),
            'unique' => array(
                'rule' => array("checkUnique", array("codigo_tipo_retorno", "descricao")),
                'message' => 'Contato já cadastrado para outro usuário',
            ) 
        ),
    );
    
    function checkUnique($data, $fields) { 
        if (!is_array($fields)) { 
            $fields = array($fields); 
        }
        $conditions = array();
        foreach($fields as $key) {             
            $conditions[$this->name.'.'.$key] = $this->data[$this->name][$key]; 
            if(empty($this->data[$this->name][$key]))
                return true;
        }
        $this->bindModel(
            array('belongsTo' => 
                array('Usuario' => 
                    array(
                        'className' => 'Usuario',
                        'foreignKey' => 'codigo_usuario'
                    )
                )
            )
        );
        $contato = $this->find('first', array(
                'conditions' => $conditions, 
                'fields' => array('Usuario.apelido')
            )
        );
        if($contato)
            $this->validationErrors['descricao'] = 'Contato já cadastrado para usuário: '.$contato['Usuario']['apelido'];
        return  true;
    } 
    
    function trataDescricao($check) {
        if ($this->data[$this->name]['codigo_tipo_retorno'] == TipoRetorno::TIPO_RETORNO_EMAIL)
            return Validation::email($check['descricao']);
        return !empty($check['descricao']);
    }
    
    function bindLazy() {
        $this->bindModel(
            array('belongsTo' => 
                array('Usuario' => 
                    array(
                        'className' => 'Usuario',
                        'foreignKey' => 'codigo'
                    )
                )
            )
        );
    }
    
    function contatosDoUsuario($codigo_usuario, $codigo_tipo_retorno = NULL ) {
        $conditions = array('codigo_usuario' => $codigo_usuario);
        $order = array('UsuarioContato.descricao', 'UsuarioContato.codigo_tipo_retorno');
        if( $codigo_tipo_retorno )
            array_push( $conditions, array('codigo_tipo_retorno' => $codigo_tipo_retorno));
        return $this->find('all', compact('conditions', 'order') );
    }
    


    
    function incluirContato($dados) {
        $contatos = array();
        $codigo_usuario = $dados[0]['UsuarioContato']['codigo_usuario'];
        $Usuario = ClassRegistry::init('Usuario');
        $dados_usuario =  $Usuario->findByCodigo($codigo_usuario);
        $nome = $dados_usuario['Usuario']['nome'];

        $dados[0]['UsuarioContato']['nome'] = $nome;
        for ($indice = 1; $indice < count($dados); $indice++) {
            $dados[$indice]['UsuarioContato']['codigo_usuario'] = $codigo_usuario;
            $dados[$indice]['UsuarioContato']['nome'] = $nome;
        }
        $contatos = array_merge($contatos, $dados);
        $result = $this->saveAll($contatos);
        return $result;
    }

}
?>