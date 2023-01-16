<?php
class AutotracFaturamento extends AppModel {
	var $name = 'AutotracFaturamento';
	var $primaryKey = 'codigo';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
	var $useTable = false;
	var $actsAs = array('Secure');	

	var $validate = array(
        'mes_referencia' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o mes de referencia.',
             ),

        ),
        'ano_referencia' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o ano de referencia.',
             ),
        ),
    );
    var $belongsTo = array(
        'Transportadora' => array(
            'className' => 'Cliente',
            'foreignKey' => false,
            'conditions' => array('Transportadora.codigo = AutotracFaturamento.codigo_transportadora')
        )
    );
    public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {                
        if( isset($extra['extra']['autotrac_faturamento_analitico']) && $extra['extra']['autotrac_faturamento_analitico'] )
            $dados = $this->listagem_analitico($conditions, $limit, $page, $order);
        return $dados;
    }
    public function listagem_analitico($conditions, $limit = null, $page = null, $order = null){
        $this->Transportadora = ClassRegistry::init('Cliente');
        $this->Transportadora->unbindAll();       
        $fields = array(
                'AutotracFaturamento.*',
                'Transportadora.codigo',
                'Transportadora.razao_social'
            );

        $retorno = $this->find('all',
                array(
                    'conditions' => $conditions,
                    'limit'      => $limit,
                    'page'       => $page,
                    'fields'     => $fields,
                    'order'      => $order,
                )
            );   

        return $retorno;
    }
    public function valida_nome_colunas($linha){
        $valida_titulo      = array_fill(1,109,'');
        $valida_titulo[1]   = utf8_decode('Conta'); 
        $valida_titulo[5]   = utf8_decode('Ass. Básica'); 
        $valida_titulo[10]  = utf8_decode('Mensagem'); 
        $valida_titulo[14]  = utf8_decode('Caracter'); 
        $valida_titulo[19]  = utf8_decode('Comando/Alerta'); 
        $valida_titulo[24]  = utf8_decode('Caracter OBC');                                    
        $valida_titulo[30]  = utf8_decode('Msg. Prioritária');
        $valida_titulo[35]  = utf8_decode('Posição Adicional'); 
        $valida_titulo[42]  = utf8_decode('Macro'); 
        $valida_titulo[47]  = utf8_decode('Def. Grupo'); 
        $valida_titulo[51]  = utf8_decode('Alarm Pânico'); 
        $valida_titulo[58]  = utf8_decode('Msg Grupo'); 
        $valida_titulo[63]  = utf8_decode('Prior. Grupo'); 
        $valida_titulo[73]  = utf8_decode('Transf MCT'); 
        $valida_titulo[78]  = utf8_decode('Desativ/Reat'); 
        $valida_titulo[84]  = utf8_decode('QMass'); 
        $valida_titulo[89]  = utf8_decode('Inc./Exc. A.C'); 
        $valida_titulo[93]  = utf8_decode('Macro AC'); 
        $valida_titulo[100]  = utf8_decode('QTWEB'); 
        $valida_titulo[104] = utf8_decode('Perm. A.C.'); 
        $valida_titulo[109] = utf8_decode('Total'); 
        unset($valida_titulo[9]);   
        return ($valida_titulo == $linha);
    }

    public function converteParametroEmCondition($link) {
        $parametros = explode('|' , $link);
        $codigo_cliente = $parametros[1];
        $ano_mes = $parametros[2];
        $periodo = Comum::periodo($ano_mes);
        $condition = array(
            'MES'            => substr($ano_mes, 4, 2),
            'ANO'            => substr($ano_mes, 0, 4),
            'CODIGOCLIENTE' => intval($codigo_cliente)
        );
        return $condition;
    }
}
