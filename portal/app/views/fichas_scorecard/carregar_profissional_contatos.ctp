<?php echo $this->element('fichas_scorecard/lista_contatos', array(
	'titulo'				=> 'Contatos do Profissional', 
	'listaContatos' 		=> isset($this->data['ProfissionalContato']) ? $this->data['ProfissionalContato'] : array(), 
	'tipo'					=> 'profissional', 
	'model'					=> 'ProfissionalContato',
	'tipo_retorno' 			=> $tipo_retorno_profissional,
	'tipos_retorno_fixo' 	=> array(TipoRetorno::TIPO_RETORNO_CELULAR_MOTORISTA)
))?>