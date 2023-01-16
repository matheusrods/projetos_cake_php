    <?php

class StatusPropostaCredFixture extends CakeTestFixture {

    var $name = 'StatusPropostaCred';
    var $table = 'status_proposta_credenciamento';
 
	var $fields = array(
		'codigo' => array('type' => 'integer','null' => true,'default' => '','length' => 11, 'key' => 'primary', ),
		'descricao' => array('type' => 'string','null' => true,'default' => '','length' => 128,),
	);
    
    var $records = array(
	    array(
	      'codigo' => 1,
	      'descricao' => 'Pré Cadastro',
	    ),
	    array(
	      'codigo' => 2,
	      'descricao' => 'Negociação - Aguardando Análise de Valores Proposto (*)',
	    ),
	    array(
	      'codigo' => 3,
	      'descricao' => 'Negociação - Aguardando Avaliação de Contra Proposta',
	    ),
	    array(
	      'codigo' => 4,
	      'descricao' => 'Negociação - Aguardando Retorno Contra Proposta (*)',
	    ),
	    array(
	      'codigo' => 5,
	      'descricao' => 'Proposta Validada',
	    ),
	    array(
	      'codigo' => 6,
	      'descricao' => 'Termos de Faturamento Enviado (*)',
	    ),
	    array(
	      'codigo' => 7,
	      'descricao' => 'Documentação Solicitada',
	    ),
	    array(
	      'codigo' => 8,
	      'descricao' => 'Aguardando Análise de Documentos (*)',
	    ),
	    array(
	      'codigo' => 9,
	      'descricao' => 'Aprovado',
	    ),
	    array(
	      'codigo' => 10,
	      'descricao' => 'Reprovado',
	    ),
    );

}