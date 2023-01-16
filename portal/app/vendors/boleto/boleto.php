<?php
require_once('iboleto.php');
class Boleto implements iBoleto {
    private $dadosboleto = array();
    
    public function setValue($name, $var) {
        $this->dadosboleto[$name] = $var;
    }
    
    public function setValues($dados) {
        foreach($dados as $key => $value) {
            $this->dadosboleto[$key] = $value;
        }
    }
    
    public function getHtml() {
        $errors = $this->verificaDados();
        if (!empty($errors)) return false;
        $dadosboleto = $this->dadosboleto;
		if ($this->dadosboleto['codigo_banco'] == '033') {
			include("include/funcoes_santander_banespa.php"); 
			include("include/layout_santander_banespa.php");
		} else {
			include("include/funcoes_bb.php"); 
			include("include/layout_bb.php");
		}	
    }
	
    private function verificaDados() {
        $this->verificaDadosBoleto();
        $this->verificaDadosCliente();
        $this->verificaInformacoesParaCliente();
        $this->verificaInstrucoesParaCaixa();
        $this->verificaDadosOpcionais();
        $this->verificaDadosFixosEmpresa();
        $this->verificaTipoBoleto();
    }
    
    private function possuiDados($requisitos) {
        $tudo_ok = true;
        foreach ($requisitos as $requisito) {
            if (!isset($this->dadosboleto[$requisito])) {
                throw new Exception('Sem informação para: '.$requisito);
                break;
            }
        }
        return $tudo_ok;
    }
    
    private function verificaDadosBoleto() {
        $requisitos = array(
            'taxa_boleto', 
            'valor_cobrado', // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
            'valor_boleto', 
            "nosso_numero", 
            "numero_documento", 
            "data_vencimento", // Prazo de X dias OU informe data: "13/04/2006"; 
            "data_documento", 
            "data_processamento"
        );
        if (!$this->possuiDados($requisitos)) throw new Exception('Sem Dados Boleto');
        return true;
    }
    
    private function verificaDadosCliente() {
        $requisitos = array(
            "sacado", //"Nome do seu Cliente";
            "endereco1", //"Endereço do seu Cliente";
            "endereco2" //"Cidade - Estado -  CEP: 00000-000";
        );
        if (!$this->possuiDados($requisitos)) throw new Exception();
        return true;
    }
    
    private function verificaInformacoesParaCliente() {
        $requisitos = array(
            "demonstrativo1", //"Pagamento de Compra na Loja Nonononono";
            "demonstrativo2", //"Mensalidade referente a nonon nonooon nononon<br>Taxa bancária - R$ ".number_format($taxa_boleto, 2, ',', '');
            "demonstrativo3" //"BoletoPhp - http://www.boletophp.com.br";
        );
        if (!$this->possuiDados($requisitos)) throw new Exception();
        return true;
    }
    
    private function verificaInstrucoesParaCaixa() {
        $requisitos = array(
            "instrucoes1", //"- Sr. Caixa, cobrar multa de 2% após o vencimento";
            "instrucoes2", //"- Receber até 10 dias após o vencimento";
            "instrucoes3", //"- Em caso de dúvidas entre em contato conosco: xxxx@xxxx.com.br";
            "instrucoes4" //"&nbsp; Emitido pelo sistema Projeto BoletoPhp - www.boletophp.com.br";
        );
        if (!$this->possuiDados($requisitos)) throw new Exception();
        return true;
    }
    
    private function verificaDadosOpcionais() {
        $requisitos = array(
            "quantidade", 
            "valor_unitario", 
            "aceite", 
            "especie", 
            "especie_doc"
        );
        if (!$this->possuiDados($requisitos)) throw new Exception();
        return true;
    }
    
    private function verificaDadosFixosEmpresa() {
        $requisitos = array(
            "agencia", // Num da agencia, sem digito
            "conta", // Num da conta, sem digito
            "convenio", // Num do convênio - REGRA: 6 ou 7 ou 8 dígitos
            "contrato", // Num do seu contrato
            "carteira", 
            "variacao_carteira", // Variação da Carteira, com traço (opcional)
            "identificacao", //"BoletoPhp - Código Aberto de Sistema de Boletos";
            "cpf_cnpj",
            "endereco", //"Coloque o endereço da sua empresa aqui";
            "cidade_uf", //"Cidade / Estado";
            "cedente" // "Coloque a Razão Social da sua empresa aqui";
        );
        if (!$this->possuiDados($requisitos)) throw new Exception();
        return true;
    }
    
    private function verificaTipoBoleto() {
        $requisitos = array("formatacao_convenio", // REGRA: 8 p/ Convênio c/ 8 dígitos, 7 p/ Convênio c/ 7 dígitos, ou 6 se Convênio c/ 6 dígitos
                            "formatacao_nosso_numero" // REGRA: Usado apenas p/ Convênio c/ 6 dígitos: informe 1 se for NossoNúmero de até 5 dígitos ou 2 para opção de até 17 dígitos
        );
        if (!$this->possuiDados($requisitos)) throw new Exception();
        return true;
    }
}
?>