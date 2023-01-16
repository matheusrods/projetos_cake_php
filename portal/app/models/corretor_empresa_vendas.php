<?php
class CorretorEmpresaVendas extends AppModel {
    var $name = 'CorretorEmpresaVendas';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth_vendas';
    var $useTable = 'empresa';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    var $message = '';

    public $validate = array(
        'nome_empresa' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o nome.',
                'required' => true
                )
            ),
        'cnpj' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o codigo do documento.',
                'required' => true)
            ),
        'email' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o E-mail.',
                'required' => true)
            )   
        );  
    

}

?>