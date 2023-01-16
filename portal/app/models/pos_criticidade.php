<?php
class PosCriticidade extends AppModel
{
    public $name = 'PosCriticidade';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'pos_criticidade';
    public $primaryKey = 'codigo';

    public $actsAs = array('Secure');

    public $validate = array(
        'descricao' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o descrição.',
            'required' => true
        ),
        'cor' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o código cliente',
            'required' => true
        ),
        'codigo_empresa' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o código da empresa',
            'required' => true
        ),
        'codigo_cliente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o código cliente',
            'required' => true
        )
    );

    public function getCriticidades($codigo_cliente, $codigo_pos_ferramenta)
    {

        $fields = array(
            'codigo',
            'descricao',
            'cor',
            'ativo',
            'valor_inicio',
            'valor_fim',
            'codigo_pos_ferramenta',
            'observacao',
            'codigo_cliente',
        );

        $conditions = array(
            "PosCriticidade.codigo_pos_ferramenta" => $codigo_pos_ferramenta,
            "PosCriticidade.codigo_cliente" => $codigo_cliente,
        );

        $criticidades = $this->find('all',
            array(
                'fields' => $fields,
                'conditions' => $conditions
            )
        );

        if (!empty($criticidades)) {

            $arr = array();

            $arr['codigo_cliente'] = $criticidades[0]['PosCriticidade']['codigo_cliente'];
            $arr['codigo_pos_ferramenta'] = $criticidades[0]['PosCriticidade']['codigo_pos_ferramenta'];
            $arr['codigo_cliente'] = $criticidades[0]['PosCriticidade']['codigo_cliente'];

            foreach ($criticidades as $key => $obj) {

                unset($obj['PosCriticidade']['ativo']);
                unset($obj['PosCriticidade']['codigo_cliente']);
                unset($obj['PosCriticidade']['codigo_pos_ferramenta']);

                $arr['PosCriticidade'][] = $obj['PosCriticidade'];

            }
        }

        $result['Cliente'] = $arr;

        return $result;
    }

    public function verificar_criticidade($codigo_clientes, $codigo_pos_ferramenta)
    {

        $sql = "select * from pos_criticidade where codigo_cliente = {$codigo_clientes} and codigo_pos_ferramenta = {$codigo_pos_ferramenta}";
        $pos_criticidade = $this->query($sql);

        return $pos_criticidade;
    }
}
