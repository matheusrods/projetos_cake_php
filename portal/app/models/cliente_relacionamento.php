<?php

class ClienteRelacionamento extends AppModel {

    var $name = 'ClienteRelacionamento';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $primaryKey = 'codigo';
    var $useTable = 'cliente_relacionamento';
    var $actsAs = array('Secure');
    var $validate = array(
        'codigo_cliente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Cliente',
        ),
        'codigo_cliente_relacao' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o Cliente Relacionado',
            ),
            'validarMesmoClienteRelacaoDoMesmoTipo' => array(
                'rule' => 'validarMesmoClienteRelacaoDoMesmoTipo',
                'message' => 'Você já tem relação com este cliente.',
                'on' => 'create'
            ),
            'validarClienteRelacaoValido' => array(
                'rule' => 'validarClienteRelacaoValido',
                'message' => 'O cliente que você informou é inválido.',
                'on' => 'create'
            )
        ),
        'codigo_tipo_relacionamento' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Tipo',
        ),
    );
    var $belongsTo = array(
        'TipoRelacionamento' => array(
            'className' => 'TipoRelacionamento',
            'foreignKey' => 'codigo_tipo_relacionamento',
        ),
        'Cliente' => array(
            'className' => 'Cliente',
            'foreignKey' => 'codigo_cliente_relacao'
        )
    );
    
    /**
     * Valida se o codigo do cliente informado existe na base
     * 
     * @param $model
     * @return boolean
     */
    public function validarClienteRelacaoValido() {
            $codigo_cliente_relacao = $this->data[$this->name]['codigo_cliente_relacao'];
            
            $result = $this->Cliente->find('count', array(
                'conditions' => array(
                    'Cliente.codigo' => $codigo_cliente_relacao
                )
            ));
            
            if($result > 0) {
                return true;
            } else {
                return false;
            }
    }
    
    /**
     * Valida se um cliente já possui um filho de um determinado tipo
     * 
     * @param $model
     * @return boolean
     */
    public function validarMesmoClienteRelacaoDoMesmoTipo() {
        $result = $this->find('count', array(
            'conditions' => array(
                'codigo_cliente' => $this->data[$this->name]['codigo_cliente'],
                'codigo_cliente_relacao' => $this->data[$this->name]['codigo_cliente_relacao'],
                'codigo_tipo_relacionamento' => $this->data[$this->name]['codigo_tipo_relacionamento']
                )));

        if ($result > 0) {
            return false;
        }
        return true;
    }

    /**
     * Obtem filhos de um cliente, dado um codigo_cliente e um codigo_tipo_relacionamento
     * 
     * @param int $codigo_cliente
     * @param int $codigo_tipo_relacionamento
     * @return mixed
     */
    public function filhosDe($codigo_cliente, $codigo_tipo_relacionamento) {
        $result = $this->find('all', array('conditions' => array('codigo_cliente' => $codigo_cliente, 'codigo_tipo_relacionamento' => $codigo_tipo_relacionamento)));
        return Set::extract($result, '/ClienteRelacionamento/codigo_cliente_relacao');
    }

    /**
     * Obtem todos clientes filhos dado um código cliente
     * 
     * @param int $codigo_cliente
     * @return false|array
     */
    public function obterFilhos($codigo_cliente) {
        $result = $this->find('all', array(
            'conditions' => array(
                'codigo_cliente' => $codigo_cliente
                )));

        return $result;
    }
}