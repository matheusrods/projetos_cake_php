<?php

class ClientesHistoricosController extends AppController {

    public $name = 'ClientesHistoricos';
    public $layout = 'cliente';
    public $helpers = array('Html', 'Ajax');
    public $uses = array(
        'ClienteHistorico',
        'TipoContato',
        'TipoHistorico'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(
            array(
                'listar',
            )
        );
    }

    const CODIGO_TIPO_HISTORICO = 5;

    /**
     * Ação que inclui um novo ClienteHistorico
     *
     * @param array $this->data Array contendo os dados para inclusao.
     *
     * @return void
     *
     */
    public function incluir($codigo_cliente){
        if ($this->RequestHandler->isPost()) {
            $result = $this->ClienteHistorico->incluir($this->data);
            if($result) {
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->layout = 'ajax';
        $tipos_historicos = $this->TipoHistorico->listarNomeHistoricos();
        $this->set('tipos_historicos', $tipos_historicos);
        $this->data['ClienteHistorico']['codigo_cliente'] = $codigo_cliente;
        $this->data['ClienteHistorico']['codigo_tipo_historico'] = self::CODIGO_TIPO_HISTORICO;
        $this->set('codigo_cliente', $codigo_cliente);
    }

    /**
     * Método que lista históricos de um cliente.
     *
     * @todo Dissolver essa action no controller e model.
     *
     * @param array $codigo_cliente Codigo que identifica um cliente único.
     *
     * @return void
     *
     */
    public function listar($codigo_cliente) {
        $this->layout = 'ajax';
        $historicos = $this->ClienteHistorico->listaHistorico($codigo_cliente);
        $this->set(compact('historicos'));
    }
    
    public function listar_visualizar($codigo_cliente) {
        $this->layout = 'ajax';
        $historicos = $this->ClienteHistorico->listaHistorico($codigo_cliente);
        $this->set(compact('historicos'));
    }
}
