<?php echo $this->element('fichas_scorecard/lista_contatos', 
	array(
		'titulo'=>'Contatos do Proprietário', 
		'listaContatos'=>isset($this->data['FichaScorecardVeiculo'][$index]['ProprietarioContato']) ? $this->data['FichaScorecardVeiculo'][$index]['ProprietarioContato'] : array(), 
		'tipo'=>"proprietario", 
		'model'=>"FichaScorecardVeiculo.{$index}.ProprietarioContato", 
		'tipo_retorno'=>$tipo_retorno_proprietario, 
		'index'=>$index
	)
)?>