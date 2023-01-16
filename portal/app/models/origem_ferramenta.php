<?php
class OrigemFerramenta extends AppModel
{
    public $name = 'OrigemFerramenta';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'origem_ferramentas';
    public $primaryKey = 'codigo';

    public $actsAs = array('Secure');

    public $validate = array(
        'descricao' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o descrição.',
            'required' => true
        ),
        'codigo_cliente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o código cliente',
            'required' => true
        ),
        'formulario' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o formulario.',
            'required' => true
        ),
    );

    public function getListaOrigemFerramenta($filtros = null)
    {
        $fields = array(
            'OrigemFerramenta.codigo',
            'OrigemFerramenta.descricao',
            'OrigemFerramenta.ativo',
            'OrigemFerramenta.codigo_cliente',
            'Produto.descricao'
        );

        $joins = array(
            array(
                'table' => 'produto',
                'alias' => 'Produto',
                'type' => 'LEFT',
                'conditions' => 'OrigemFerramenta.codigo_produto = Produto.codigo',
                ),
        );

        $conditions = $this->converteFiltroEmCondition($filtros);

        $origem_ferramenta = array(
            'fields' => $fields,
            'joins' => $joins,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'OrigemFerramenta.codigo desc',
        );

        return $origem_ferramenta;
    }

    public function retornaOrigemFerramenta($data = null)
    {

        if (isset($data['codigo_cliente']) && !empty($data['codigo_cliente'])) {
            $codigo_cliente = array(
                'OrigemFerramenta.codigo_cliente' => $data['codigo_cliente']
            );
        } else {
            $codigo_cliente = array();
        }

        $conditions = array(
            $codigo_cliente,
            'OrigemFerramenta.ativo' => 1
        );

        return $this->find('list', array('conditions' => $conditions,'fields' => array('codigo', 'descricao')));;
    }

    function getByCodigo($codigo) {
        $fields = array(
            'OrigemFerramenta.codigo',
            'OrigemFerramenta.descricao',
            'OrigemFerramenta.ativo',
            'OrigemFerramenta.codigo_cliente'
        );

        $conditions = array('OrigemFerramenta.codigo' => $codigo);

        $origem_ferramenta = $this->find('first',
            array(
                'fields' => $fields,
                'conditions' => $conditions
            )
        );

        return $origem_ferramenta;
    }

    public function getOrigemFerramenta($codigo_cliente, $interno)
    {
        $fields = array(
            'OrigemFerramenta.codigo',
            'OrigemFerramenta.descricao'
        );

        $conditions = array(
            'ativo'=>1,
            'codigo_cliente'=> $codigo_cliente,
            'interno' => $interno
        );

        //executa os dados
        $dados = $this->find('list', compact('fields','conditions'));
        return $dados;
    }

    public function converteFiltroEmCondition($data)
    {
        $conditions = array();

        if (!empty($data['codigo'])) {
            $conditions['OrigemFerramenta.codigo'] = $data['codigo'];
        }

        if (!empty($data ['descricao'])) {
            $conditions ['OrigemFerramenta.descricao LIKE'] = '%' . $data ['descricao'] . '%';
        }

        if (isset($data ['ativo'])) {
            if ($data ['ativo'] === '0') {
                $conditions [] = '(OrigemFerramenta.ativo = ' . $data ['ativo'] . ' OR OrigemFerramenta.ativo IS NULL)';
            } elseif ($data ['ativo'] == '1') {
                $conditions ['OrigemFerramenta.ativo'] = $data ['ativo'];
            }
        }

        if (isset($data ['codigo_cliente']) && !empty($data ['codigo_cliente'])) {
            $conditions ['OrigemFerramenta.codigo_cliente'] = $data['codigo_cliente'];
        }

        return $conditions;
    }
}
