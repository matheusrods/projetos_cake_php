<?php

class ProcessoFerramenta extends AppModel
{
    public $name = 'ProcessoFerramenta';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'processos_ferramentas';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');

    public $validate = array(
        'codigo_processo' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo do processo',
            'required' => true
        )
    );

    public function converteFiltroEmCondition($data)
    {
        $conditions = array();

        if (!empty($data['codigo'])) {
            $conditions['ProcessoFerramenta.codigo'] = $data['codigo'];
        }

        if (!empty($data['codigo_processo'])) {
            $conditions['ProcessoFerramenta.codigo_processo'] = $data['codigo_processo'];
        }
        
        return $conditions;
    }

    function getLista($codigo_processo) {
		return $this->find ( 'all', array(
            'fields' => array (
                'codigo',
                'codigo_processo',
                'descricao',
                'equipamentos',
                'finalidades',
                'posicao',
            ),
            'conditions' => array (
                'codigo_processo' => $codigo_processo 
            ) 
		) );
    }
    
    /**
	 * Obtem um Processo_ferramenta pelo seu código
	 *
	 * @param int $codigo        	
	 * @return array
	 */
	function getByCodigo($codigo) {
		$produtos = $this->find('first', array(
            'conditions' => array(
                'codigo' => $codigo 
            ) 
		) );
		return $produtos;
    }
}
