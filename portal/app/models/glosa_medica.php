<?php
class GlosaMedica extends AppModel {
	
	var $name = 'GlosaMedica';
	var $useTable = false;
	

	const A_PAGAR = 'A pagar';
	const PAGO = 'Pago';
	const INDEVIDA = 'Indevida';

	/**
	 * Lista de status disponiveis
	 *
	 * @return void
	 */
	public function listaStatusDeGlosa(){
		return array(
			GlosaMedica::A_PAGAR,
			GlosaMedica::PAGO,
			GlosaMedica::INDEVIDA,
		);
	}

	/**
	 * apresentar as Glosas que foram cadastradas para a nota fiscal em especifica, em formato de listagem
	 *
	 * • Código da Glosa
     * • Código Pedido Exame
     * • Exame
     * • Valor
     * • Data da Glosa
     * • Data de vencimento
     * • Data de Pagamento
     * • Status
     * • Motivo da Glosa
	 * 
	 * 
	 * @param [type] $codigo_nota
	 * @return void
	 */
	public function obterListaPorCodigoNota( $codigo_nota = null ){

		if(!empty($codigo_nota)){

		}

	}

	/**
	 * Alterar o status de uma Glosa passando o codigo
	 *
	 * @param [int] $codigo_glosa
	 * @param [string] $status
	 * @return void
	 */
	public function alterarStatus( $codigo_glosa, $status){
		
		$lista_status = $this->listaStatus();
		if(!empty($codigo_nota) && in_array($status, $lista_status)){

		}

	}

	/**
	 * Alterar o status de uma Glosa passando o codigo da nota fiscal de serviço
	 *
	 * @param [int] $codigo_glosa
	 * @param [string] $status
	 * @return void
	 */
	public function alterarStatusPorNFS( $codigo_glosa, $status){
		
		$lista_status = $this->listaStatus();
		if(!empty($codigo_nota) && in_array($status, $lista_status)){

		}

	}

}
