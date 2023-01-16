<?php
class Configuracao extends AppModel {
	var $name = 'Configuracao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'configuracao';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_configuracao'), 'Containable');

	var $validate = array(
		'valor' => array(
			'rule' 		=> 'notEmpty',
			'message' 	=> 'Informe o Valor',
			'required'	=> true
		),
		'observacao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Observação',
			'required' => true
		)
	);

    public function getChave($chave){
        $codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];
        $valor = $this->field('valor', array('chave' => strtoupper($chave), 'codigo_empresa' => $codigo_empresa));
        if(empty($valor) || is_null($valor) || $valor == 0)
            return null;
        return $valor;
    }

    public function getChaveEmpresa($chave,$codigo_empresa=1){
        
        $valor = $this->field('valor', array('chave' => strtoupper($chave), 'codigo_empresa' => $codigo_empresa));
        if(empty($valor) || is_null($valor) || $valor == 0)
            return null;
        return $valor;
    }

    public function verificaPlanoDeEmpresa()
    {
        if ($_SESSION['Auth']['Usuario']['codigo_uperfil'] == 1) {
            $codigo_empresa = 1;
        } else {
            $codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];
        }

        $valor = $this->field('chave',
            array(
                "chave" => 'PLANO_DE_ACAO',
                'codigo_empresa' => $codigo_empresa
            )
        );

        if (!empty($valor)) {
            $valor = 1;
        } else {
            $valor = 0;
        }

        return $valor;
    }

    public function getProdutos($codigo_empresa,$assinatura)
    {
        $fields = array(
            "Configuracao.codigo",
            "Configuracao.chave",
            "Produto.codigo",
            "Produto.descricao"
        );

        $joins = array(
            array(
                "alias" => "Produto",
                "table" => "produto",
                "type" => "INNER",
                "conditions" => "Configuracao.valor = Produto.codigo"
            )
        );

        $in_assinaturas = implode(",",$assinatura);

        //Inserir a chave do produto manualmente aqui
        $conditions = array(
            "Configuracao.chave in ({$in_assinaturas})",
            "Configuracao.codigo_empresa" => $codigo_empresa
        );

        $produtos = $this->find("all", array(
            'fields' => $fields,
            'joins' => $joins,
            'conditions' => $conditions
        ));

        foreach ($produtos as $key => $p) {

            $produtos[$key]['OrigemFerramentaFormulario'] = array();

            if (!empty($p['Produto']['codigo'])) {
                $sql = "select codigo, descricao, campo_tipo, endpoint_url, codigo_produto from origem_ferramenta_formulario
                        where codigo_produto = ".$p['Produto']['codigo']." ";

                $result = $this->query($sql);

                foreach ($result as $r) {

                    $produtos[$key]['OrigemFerramentaFormulario'][] = $r[0];
                }
            }
        }

        return $produtos;
    }


    /**
     * [get_servico_assinatura verifica se tem assinatura configurada para mensageria]
     * @return [type] [description]
     */
    public function get_produto_assinatura($codigo_cliente,$chave_a_buscar)
    {
        $this->layout = false;

        //retorno do metodo
        $return = false;

        //verifica se tem o codigo do cliente
        if(empty($codigo_cliente)) {
            return $return;
        }

        //chave para mensageria do esocial
        $chave = $chave_a_buscar;

        //pega o codigo do servico configurado
        $codigo_produto = $this->getChave($chave);

        //verfica se tem codigo configurado
        if(!empty($codigo_produto)) {

            $this->ClienteProdutoServico2 = ClassRegistry::init('ClienteProdutoServico2');

            //verifica se na assintura do cliente tem esse servico configurado
            $servico_cliente = $this->ClienteProdutoServico2->produtosEServicos($codigo_cliente, $codigo_produto);
            
            //verifica se tem o servico na assinatura do cliente
            if(!empty($servico_cliente)) {
                $return = true;
            }

        }//fim codigo_produto

        return $return;

    }//fim get_servico_assinatura

    public function getValidacaoFaturamento()
    {
        $fields = array(
            'chave',
            'valor',
            'observacao'
        );

        $conditions = array(
            "codigo_empresa" => 1,
            "chave" => 'VALIDACAO_FATURAMENTO'
        );

        $configuracao = $this->find("first", array(
            'fields' => $fields,
            'conditions' => $conditions
        ));

        return $configuracao;
    }
}
