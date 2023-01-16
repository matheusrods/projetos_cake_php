<?php 
class ContasMedicasController extends AppController {
    public $name = 'ContasMedicas';
    
    var $uses = array(
        'ContaMedica'
    );

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow('*'); // TODO
    }


    /**
     * Demonstrativo Contas Médicas
     * A tela deve gerar o demonstrativo de Contas Médicas, deve trazer as notas fiscais do 
     * Mês a ser pago com o status “Processado”, mostrando algumas informações do credenciado 
     * e expandindo os Exames da nota fiscal.
     * 
     * Filtros:
     * • Código do Credenciado
     * • Número nota fiscal
     * • Status (Todos, Pago e Não Pago)
     * • Mês/Ano
     *
     * @return void
     */
    public function demonstrativos() {
        
        $this->pageTitle = 'Demonstrativo Contas Médicas';

        $this->data[$this->ContaMedica->name] = $this->Filtros->controla_sessao($this->data, $this->ContaMedica->name);

    }

    /**
     * listagem de acordo com Filtros selecionados no metodo demonstrativos
     *
     * Listagem:
     * • Código do Credenciado
     * • Nome Fantasia
     * • CNPJ
     * • Número Nota Fiscal
     * • Data da Emissão
     * • Data de Vencimento
     * • Data de Recebimento
     * • Data de Pagamento
     * • Valor a Pagar
     * • Status 
     *   ◦ Buscar no Naveg se o título foi pago ou não
     * • Ação
     *   ◦ Exibir detalhes da Nota Fiscal, listar os Exames;
     *       ▪ Abrir uma modal com os exames relacionados a está nota fiscal, 
     *          segue os campos da listagem:
     *           • Código Cliente
     *           • Nome fantasia
     *           • Código Credenciado
     *           • Nome fantasia Credenciado
     *           • Funcionário
     *           • Cpf
     *           • Exame
     *           • Data realizado
     *           • Data baixa
     *           • Valor de Custo
     *           • Glosado (Sim/Não)
     * 
     * @return void
     */
    public function demonstrativos_listagem() {

    }

    /**
     * Detalhes da Nota Fiscal
     *  
     */
    public function demonstrativos_listagem_detalhe( ){

    }


}