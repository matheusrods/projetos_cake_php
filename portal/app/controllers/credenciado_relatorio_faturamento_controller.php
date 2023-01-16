<?php 
class CredenciadoRelatorioFaturamentoController extends AppController {
    public $name = 'CredenciadoRelatorioFaturamento';
    
    var $uses = array(
        'Credenciado'
    );

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow('*'); // TODO
    }

    /**
     * Este relatório deve listar os exames lançados e/ou baixados com o status da auditoria, 
     * dando para o Credenciado a possibilidade de saber o que ele faturará.
     * 
     * Este relatório deve se comportar para os usuários Internos e Externos
     * 
     * Regra Status Auditoria:
     * • Quando o Exame estiver com o Status “Pendente” vindo da Auditoria, não deve apresentar o Valor.
     * • Quando o Exame estivar com o Status “Pagamento Bloqueado” vindo da Auditoria, o Valor deve estar em um cinza claro, demonstrando que o valor está bloqueado.
     *     ◦ Caso este Status esteja em algum exame deve apresentar o Motivo do bloqueio
     * • Quando o Exame estiver com o Status “Liberado para Pagamento”, deve apresentar o Valor em verde, somando como total no final do grid.
     * 
     *
     * @return void
     */
    public function index() {
        
        $this->pageTitle = 'Relatório Faturamento Credenciado';

        $this->data[$this->Credenciado->name] = $this->Filtros->controla_sessao($this->data, $this->Credenciado->name);

    }

    /**
     * listagem de acordo com Filtros selecionados na index
     * 
     * Tela para apresentação do Relatório Faturamento Credenciado.
     * Filtros:
     * • Usuário Interno: o sistema deve dar a possibilidade de filtrar por Credenciado, Mês/Ano e Status Auditoria
     * • Usuário Externo: o sistema de apresentar o filtro de Mês/Ano e Status Auditoria (ao entrar na tela trazer inicialmente o status Liberados para Pagamento)
     * • O filtro Mês/Ano, deve pegar os dados do (Mês - 1).
     *
     * Listagem:
     * • Código do Credenciado Faturamento
     * • Nome Credenciado Faturamento
     * • Código Cliente (exames)
     * • Cliente (exames)
     * • Setor
     * • Cargo
     * • Funcionário
     * • Matrícula
     * • Código Pedido Exame
     * • Data Pedido de Exame
     * • Descrição Exame
     * • Data Realização
     * • Data Baixa
     * • Imagem Anexada
     * • Status da Auditoria
     * • Motivo Bloqueio
     * • Valor
     * 
     * No final da listagem deve apresentar o total do valor dos Exames emitidos dentro do mês, 
     * somando somente os valores que estão com Status Liberado para Pagamento.
     * Dentro deste relatório ainda temos a possibilidade de exportar os dados para um CSV.
     * @return void
     */
    public function listagem() {

    }

    
    /**
     * No final da listagem deve apresentar o total do valor dos Exames emitidos dentro do mês, 
     * somando somente os valores que estão com Status Liberado para Pagamento.
     *
     * @return void
     */
    public function listagem_consolida() {

    }

}