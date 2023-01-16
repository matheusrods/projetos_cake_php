<?php
class ClienteProdutoDesconto extends AppModel {
	
	var $name = 'ClienteProdutoDesconto';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHhealth';
	var $useTable = 'cliente_produto_desconto';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
    var $validate = array(
        'ano' => array(
            'validaAno' => array(
                'rule' => 'validaAno',
                'message' => 'O ano deve ser maior ou igual ao ano atual'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o ano',
             ),
        ),
        //mes
        'codigo_produto' => array(
            array(
                'rule' => 'validaProdutoCadastrado',
                'message' => 'Já existe um desconto cadastrado para esse cliente, mês e ano'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o produto',
             ),
        ),
        'valor' => array(
            'money' => array(
                'rule' => array('money', 'left'),
                'message' => 'O valor deve ser numérico',
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o valor do desconto',
            )
        ),
    );
    
    var $belongsTo = array(
        'Produto' => array(
            'className' => 'Produto',
            'foreignKey' => false,
            'conditions' => 'Produto.codigo = ClienteProdutoDesconto.codigo_produto'
        )
    );
    
    function descontosDoCliente($codigo_cliente) {
        return $this->find('all', array('conditions' => array('codigo_cliente' => $codigo_cliente), 'order' => array('mes_ano DESC')));
    }//FINAL FUNCTION descontosDoCliente

    function converterFiltrosEmConditions($filtro){

        $conditions = array();

        if( isset($filtro['ClienteProdutoDesconto']['data_inicial']) && !empty($filtro['ClienteProdutoDesconto']['data_inicial']) &&
            isset($filtro['ClienteProdutoDesconto']['data_final']) && !empty($filtro['ClienteProdutoDesconto']['data_final']) 
            ){
            $conditions['ClienteProdutoDesconto.data_inclusao BETWEEN ? AND ?'] = array(
                AppModel::dateToDbDate($filtro['ClienteProdutoDesconto']['data_inicial']).' 00:00:00',
                AppModel::dateToDbDate($filtro['ClienteProdutoDesconto']['data_final']).' 23:59:59',
            );
        }

        return $conditions;
    }//FINAL FUNCTION converterFiltrosEmConditions

    function carregarDescontoPorPeriodo($conditions){

        $Cliente = ClassRegistry::init('Cliente');
        $Usuario = ClassRegistry::init('Usuario');
		
        $joins = array(
            array(
                'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
                'alias' => 'Cliente',
                'conditions' => 'ClienteProdutoDesconto.codigo_cliente = Cliente.codigo'
            ),
            array(
                'table' => "{$Usuario->databaseTable}.{$Usuario->tableSchema}.{$Usuario->useTable}",
                'alias' => 'Usuario',
                'conditions' => 'ClienteProdutoDesconto.codigo_usuario_inclusao = Usuario.codigo'
            ),
            
        );

        $result = $this->find('all', array(
            'fields' => array(
                'ClienteProdutoDesconto.valor',
                'ClienteProdutoDesconto.data_inclusao',
                'ClienteProdutoDesconto.observacao',
                'Cliente.codigo',
                'Cliente.razao_social',
                'Produto.descricao',
                'Usuario.nome',
            ),
            'joins' => $joins,
            'conditions' => $conditions,
        ));

        return $result;
    }//FINAL FUNCTION carregarDescontoPorPeriodo

    public function incluir($dados){  

        $ano = $dados[$this->name]['ano'];

        if((!empty($ano)) && ($ano >= date('Y'))){
            $dados[$this->name]['mes_ano'] = $dados[$this->name]['ano'].str_pad($dados[$this->name]['mes'], 2, '0', STR_PAD_LEFT).'01 00:00:00';
            unset($dados[$this->name]['ano']);
            unset($dados[$this->name]['mes']);
        }

        return parent::incluir($dados[$this->name]);
    }///FINAL FUNCTION incluir

    public function validaAno(){
            
        $ano = $this->data[$this->name]['ano'];
        
        if(!empty($ano) && ($ano < date('Y'))) {
            return false;
        }

    }//FINAL FUNCTION validaAno

    /**
     * [validaProdutoCadastrado Função para verificar se existe desconto cadastrado para o cliente com mesmo produto, ano e mês]
     * @return [boolen] 
     */
    public function validaProdutoCadastrado(){

        $dados = $this->data;

        if(isset($dados[$this->name]['mes_ano'])){

            $mes = date("m",strtotime($dados[$this->name]['mes_ano']));
            $ano = date("Y",strtotime($dados[$this->name]['mes_ano']));
            
            $conditions = array('codigo_cliente' => $dados[$this->name]['codigo_cliente'],
                                'codigo_produto' => $dados[$this->name]['codigo_produto'],
                                'MONTH(mes_ano)' => $mes,
                                'YEAR(mes_ano)' => $ano,
            );

            $retorno = $this->find('count', array('conditions' => $conditions));

            return ($retorno > 0 ) ? false : true;
        }

        return true;
    }//FINAL FUNCTION validaProdutoCadastrado

}//FINAL CLASS ClienteProdutoDesconto