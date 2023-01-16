<?php
class Motorista extends AppModel {

	var $name = 'Motorista';
	var $tableSchema = 'dbo';
	var $databaseTable = 'Monitora';
	var $useTable = 'motorista';
	var $primaryKey = 'Codigo';
	var $actsAs = array('Secure');

	function bindRecebsm(){
		$this->bindModel(array(
		   'hasOne' => array(
			   'Recebsm' => array('foreignKey' => 'MotResp')
		   )
		));
	}

	function carregar($codigo_motorista,$fields = array('Codigo', 'Nome')) {
		return $this->find('first', array('conditions' => array('codigo' => $codigo_motorista), 'fields' => $fields));
	}

	function buscaPorCPF($codigo_documento, $fields = null) {
		$simbolos = array('.','/','-');
		$fields		= (is_null($fields))?array('Nome','CPF','RG','CNH','Codigo','Nacionalidade', 
											'CONVERT(VARCHAR(10),Data,103) + " " + CONVERT(VARCHAR(10),Data,108) AS data',
											'CONVERT(VARCHAR(10),CNH_Validade,103) + " " + CONVERT(VARCHAR(10),CNH_Validade,108) AS data_cnh'):$fields;
		$conditions = array('REPLACE(REPLACE(CPF,".",""),"-","")' => str_replace($simbolos, '', $codigo_documento));
		
		return $this->find('first',compact('conditions','fields'));
	}

	public function retornaNovoCodigo(){

		$novo_codigo = $this->find('first', array('fields'=>array('MAX(Codigo)+1 AS novo_codigo')));
		$novo_codigo = str_pad($novo_codigo[0]['novo_codigo'], 6, "0", STR_PAD_LEFT);		
		return $novo_codigo;
	}

	public function inserirMotoristaSM($data){
		$data['CPF'] = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $data['CPF']);
		$result = $this->buscaPorCPF($data['CPF']);
		if( !$result ){		  
			$data['Codigo'] = $this->retornaNovoCodigo();
			if(!$this->save($data))
				throw new Exception('Motorista não cadastrado!');
		}
	}

	public function historicoSinistro($dados){
		
		$motorista	 = $this->buscaPorCPF($dados['pfis_cpf']);
		if(!$motorista)return FALSE;

		$this->bindRecebsm();
		$this->bindModel(array(
		   'belongsTo' => array(
			   'Sinistro' => array(
					'foreignKey' => false,
					'conditions' => 'Recebsm.SM = Sinistro.sm')
		   )
		));


		return $this->Sinistro->historicoPorMotorista($motorista['Motorista']['Codigo'],$dados);
	}

	public function historicoOrigemDestino($dados){
		
		$motorista	 = $this->buscaPorCPF($dados['pfis_cpf']);
		if(!$motorista)return FALSE;

		$this->bindRecebsm();

		return $this->Recebsm->historicoOrigemDestinoPorMotorista($motorista['Motorista']['Codigo'],$dados);
	}

	public function historicoEmbarcadorTransportador($dados){
		
		$motorista	 = $this->buscaPorCPF($dados['pfis_cpf']);
		if(!$motorista)return FALSE;

		$this->bindRecebsm();

		return $this->Recebsm->historicoEmbarcadorTransportadorPorMotorista($motorista['Motorista']['Codigo'],$dados);
	}

	public function atualizaCelular($cpf, $celular)
	{
		//Método integrado com Model ProfissionalContato 
		//Parametros distintos devido :
		// 1. Às diferenças de nomes entre as bases/tabelas 
		// 2. Reutilização de código e minimização de uso de memória em passagens de parâmetros por valor

		$cpf         = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);

		$motorista	 = $this->buscaPorCPF($cpf, "Codigo");

		if(!$motorista) return FALSE;

		$motorista['Motorista']['Celular'] = $celular;

		if(!$this->save($motorista))
			throw new Exception('Motorista não cadastrado!');		

	}

}

?>