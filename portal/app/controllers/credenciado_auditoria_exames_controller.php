<?php 
class CredenciadoAuditoriaExamesController extends AppController {
    public $name = 'CredenciadoAuditoriaExames';
    
    var $uses = array(
        'Credenciado'
    );

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow('*'); // TODO
    }
    /**
     * A Auditoria de Exames, é o momento onde acontece a auditoria dos exames aplicados 
     * pelo Credenciado, avaliando se o Exame está em conformidade.
     * 
     * Filtros:
     * • Código Credenciado
     * • Mês/Ano
     * • Status (Pendente, Pagamento Bloqueado, Liberado para Pagamento)
     * 
     *
     * @return void
     */
    public function index() {
        
        $this->pageTitle = 'Auditoria Exames';

        $this->data[$this->Credenciado->name] = $this->Filtros->controla_sessao($this->data, $this->Credenciado->name);

    }


    /**
     * listagem de acordo com Filtros selecionados na index
     *
     * Listagem:
     * • Código Credenciado
     * • Nome Fantasia Credenciado
     * • Status
     *     ◦ Pendente, Pagamento Bloqueado, Liberado para Pagamento
     *         ▪ Quando o status estiver “Pagamento Bloqueado”, deve apresentar com tolltip o motivo do mesmo 
     * • Código Pedido Exame
     * • Exame
     * • Data Baixa
     * • Anexo Exame
     *     ◦ Link para o anexo do exame
     * • Anexo Ficha Clínica
     *     ◦ Link para o anexo da ficha clínica
     * • Valor
     * • Ação
     *    ◦ Auditar
     *        ▪ Quando acionar a ação Auditar, deve abrir uma modal com os dados do Exame onde
     *           poderá alterar o Status do mesmo, caso o status seja “Pagamento Bloqueado”, incluir o motivo do bloqueio. 
     * 
     *        ▪ Neste modal deve existir um botão salvar onde registrará a auditoria.
     * 
     * Logar das alterações de status da auditoria.
     * 
     * @return void
     */
    public function listagem() {

    }

    public function auditar() {

    }

    /**
     * Obter dados de exame 
     *
     * @param [int] $codigoExame
     * @return void
     */
    public function obterExameDados( $codigoExame ) {

    }
    

    public function atualizarExameMotivo( $codigoExame, $status, $motivo ) {

    }    

    
    public function atualizarStatusExame( $codigoExame, $status ) {

    }

    

}