<?php

class ProcessoAnexo extends AppModel
{
    public $name = 'ProcessoAnexo';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'processos_anexos';
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
            $conditions['ProcessoAnexo.codigo'] = $data['codigo'];
        }

        if (!empty($data['codigo_processo'])) {
            $conditions['ProcessoAnexo.codigo_processo'] = $data['codigo_processo'];
        }

        if (!empty($data['arquivo_url'])) {
            $conditions['ProcessoAnexo.arquivo_url'] = $data['arquivo_url'];
        }       
        
        return $conditions;
    }

    function getLista($codigo_processo) {
		return $this->find ( 'all', array(
            'fields' => array (
                'codigo',
                'codigo_processo',
                'arquivo_url',
                'codigo_usuario_inclusao',
                'codigo_usuario_alteracao',
                'data_inclusao',
                'data_alteracao',
                'data_remocao',
            ),
            'conditions' => array (
                'codigo_processo' => $codigo_processo 
            ) 
		) );
    }
    
    /**
	 * Obtem um anexo pelo seu código
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
