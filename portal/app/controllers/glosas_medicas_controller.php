<?php 
class GlosasMedicasController extends AppController {
    public $name = 'GlosasMedicas';
    
    var $uses = array(
        'GlosaMedica'
    );

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow('*'); // TODO
    }
    

    /**
     * Este relatório tem que apresentar todas as Glosas cadastradas e seus status.
     * 
     * Filtros:
     * • Código do Credenciado
     * • Número Nota Fiscal
     * • Status (A Pagar, Pago, Indevida)
     * 
     * @return void
     */
    public function relatorio() {
        
        $this->pageTitle = 'Relatório de Glosas';

        $this->data[$this->GlosaMedica->name] = $this->Filtros->controla_sessao($this->data, $this->GlosaMedica->name);

    }

    /**
     * Listagem:
     * numero nota fiscal
     * • Código da Glosa
     * • Código Pedido Exame
     * • Exame
     * • Valor
     * • Data da Glosa
     * • Data de vencimento
     * • Data de Pagamento
     * • Status
     * • Motivo da Glosa
     * • Ação
     *     ◦ Trocar Status da Glosa (Aberta/Fechada)
     *
     * @return void
     */
    public function relatorio_listagem() {

        $filtros = $this->Filtros->controla_sessao($this->data, $this->GlosaMedica->name);

        $listagem = $this->GlosaMedica->listar( $filtros );

        $this->set("listagem");

    }


    /**
     * Ação para trocar status da Glosa (Aberta/Fechada) 
     *
     * @param [int] $id
     * * @param [string] $status
     * @return void
     */
    public function relatorio_trocar_status( $codigo_glosa, $status = null ) {
        
        $status_alterado = $this->GlosaMedica->alterarStatus($codigo_glosa, $status);

        return $status_alterado;

    }

}