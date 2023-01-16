<?php
class PosSwtFormQuestao extends AppModel {
	var $name = 'PosSwtFormQuestao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pos_swt_form_questao';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_form_questao'));

	public function PosSwtPerguntas($codigo_cliente){		
		
		$query = "
			SELECT 
				tit.codigo as codigo_titulo,
				tit.titulo as titulo,
				qest.codigo as codigo_questao,
				qest.questao as questao
			FROM pos_swt_form_questao qest
			INNER JOIN pos_swt_form_titulo tit on qest.codigo_form_titulo = tit.codigo
			WHERE qest.codigo_cliente IN (".$codigo_cliente.");
		";

		$dados = $this->query($query);

		return $dados;
	}


	public function PosSwtPerguntasAnalise($codigo_cliente, $tipo){		
		
		$query = "
			SELECT 
				tit.codigo as codigo_titulo,
				tit.titulo as titulo,
				qest.codigo as codigo_questao,
				qest.questao as questao,
				psf.form_tipo
			FROM pos_swt_form_questao qest
			INNER JOIN pos_swt_form_titulo tit on qest.codigo_form_titulo = tit.codigo
			INNER join pos_swt_form psf on qest.codigo_form = psf.codigo
			WHERE qest.codigo_cliente IN (".$codigo_cliente.") 
			and psf.form_tipo = '".$tipo."'
		";

		// debug($query);exit;

		$dados = $this->query($query);

		return $dados;
	}

	
}
