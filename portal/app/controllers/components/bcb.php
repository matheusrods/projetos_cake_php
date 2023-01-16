<?php
class BcbComponent {
    
	var $name = 'Bcb';
	
	public function avaliar($cpf_profissional, $cpf_proprietario) {
	    $response = array();
	    $response['serasa_profissional_nada_consta'] = $this->avaliaNadaConsta($this->consulta($cpf_profissional));
        $response['serasa_proprietario_nada_consta'] = $this->avaliaNadaConsta($this->consulta($cpf_proprietario));
        return $response;
	}
	
	public function consulta($cpf) {
	    if (empty($cpf))
	        return '';
	    return file_get_contents("http://informacoes.buonny.com.br/bcb/index/consulta-informacoes-rest?codigoDocumento={$cpf}");
    }
	
	public function avaliaNadaConsta($jsonData) {
	    $data = json_decode($jsonData);
	    return !isset($data->pendenciaPagamento) && !isset($data->chequeSemFundo) && !isset($data->protesto);
	}

	public function resultado($stringsRecebidas=null, $cpf=null, $consulta='Resumo')  {

        if (!empty($stringsRecebidas)) {
            //Json para Array
            $dados_profissional = json_decode($stringsRecebidas, true);
            if($consulta=='Resumo'){
                $resultado =$this->formatarDadosResumo($dados_profissional);
            }
     

            return $resultado;
        }
        return ' --- Não foi possível identificar o profissional --- ';
    }

    // Formata o Resumo do SERASA
    public function formatarDadosResumo($arrayRecebido=null)  {
        $linha = array(); $total_geral=0;
        if(!empty($arrayRecebido['pendenciaPagamento']['B357'])) {
            //Pendencias Financeira
            $total=0; $qtde=0; $descricao=''; 
            foreach ($arrayRecebido['pendenciaPagamento']['B357'] as $dados['B357']) {
                $descricao .= $dados['B357']['descricao'].'/';
                $qtde += $dados['B357']['quantidadeTotal'];
            }
            $periodo = $arrayRecebido['pendenciaPagamento']['B357'][0]['dataMenor']. ' a '.$arrayRecebido['pendenciaPagamento']['B357'][0]['dataMaior'];
        }
        if(!empty($arrayRecebido['pendenciaPagamento']['B358'])) {
            foreach ($arrayRecebido['pendenciaPagamento']['B358'] as $dados['B358']) { 
                $total +=  $dados['B358']['valor'];
            }
        }  
        if(!empty($arrayRecebido['pendenciaPagamento']['B357']) || !empty($arrayRecebido['pendenciaPagamento']['B358'])) {
            $linha[]=$qtde.';'.$descricao.';'.$periodo.';'. $total;
            $total_geral += $total;
        }
        if(!empty($arrayRecebido['chequeSemFundo']['B359'])) {
            //Cheque Sem Fundo
            $total=0; $qtde=0; $descricao='';
            $qtde = $arrayRecebido['chequeSemFundo']['B359'][0]['quantidadeTotal'];
            $descricao = $arrayRecebido['chequeSemFundo']['B359'][0]['tipoOcorrencia'];
            $periodo = $arrayRecebido['chequeSemFundo']['B359'][0]['dataMenor']. ' a '.$arrayRecebido['chequeSemFundo']['B359'][0]['dataMaior'];
        }
        if(!empty($arrayRecebido['chequeSemFundo']['B360'])) {
            foreach ($arrayRecebido['chequeSemFundo']['B360'] as $dados['B360']) { 
                $total +=  $dados['B360']['valor'];
            }
        }
        if(!empty($arrayRecebido['chequeSemFundo']['B359']) || !empty($arrayRecebido['chequeSemFundo']['B360'])) {
            $linha[]=$qtde.';'.$descricao.';'.$periodo.';'. $total;
            $total_geral += $total;
        }
        if($total_geral > 0) {
            $linha[]=';'.';Total Geral;'. $total_geral;
        }
        return $linha;
    }
	
}