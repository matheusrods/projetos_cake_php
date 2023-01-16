<?php
class FaixasValoresController extends appController {
	var $name = 'FaixasValores';
    var $uses = array(
		'TCdfvCriterioFaixaValor',
		'TCdisCriterioDistribuicao'
	);

	function index() {
		$this->pageTitle = 'Faixas de Valores';
		$this->data['TCdfvCriterioFaixaValor'] = $this->Filtros->controla_sessao($this->data, $this->TCdfvCriterioFaixaValor->name);
	}

	function listagem() {
        $this->layout   = 'ajax';
        $filtros        = $this->Filtros->controla_sessao($this->data, $this->TCdfvCriterioFaixaValor->name);

        $this->paginate['TCdfvCriterioFaixaValor'] = $this->TCdfvCriterioFaixaValor->listagemParams($filtros);
        $listagem       = $this->paginate('TCdfvCriterioFaixaValor');

        $this->set(compact('listagem'));
    }

    function incluir() {
        
        $this->pageTitle = 'Incluir Faixa de Valor';        

        if($this->RequestHandler->isPost()) {

            if ($this->TCdfvCriterioFaixaValor->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

    }

    function editar($cdfv_codigo) {

        $this->pageTitle = 'Atualizar Faixa de Valor';        

        if($this->RequestHandler->isPost()) {

            if ($this->TCdfvCriterioFaixaValor->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
        	$this->data = $this->TCdfvCriterioFaixaValor->carregar($cdfv_codigo);
        	$this->data['TCdfvCriterioFaixaValor']['cdfv_valor_minimo']	= number_format($this->data['TCdfvCriterioFaixaValor']['cdfv_valor_minimo'], 2, ',', '.');
        	$this->data['TCdfvCriterioFaixaValor']['cdfv_valor_maximo']	= number_format($this->data['TCdfvCriterioFaixaValor']['cdfv_valor_maximo'], 2, ',', '.');
        }
    }

    function excluir($cdis_codigo) {
		
        try{
			
			$cdis_cdfv_codigo = $this->TCdisCriterioDistribuicao->find('count', array('conditions' => array('cdis_cdfv_codigo' => $cdis_codigo)));
			if ($cdis_cdfv_codigo)
				throw new Exception();
			
            if (!$this->TCdfvCriterioFaixaValor->excluir($cdis_codigo)) 
                throw new Exception();
            
            $this->TCdfvCriterioFaixaValor->query("CREATE TEMPORARY SEQUENCE seq_recnum
                  INCREMENT 1
                  MINVALUE 1  
                  NO MAXVALUE
                  START 1
                  CACHE 1;");

            $this->TCdfvCriterioFaixaValor->query("ALTER SEQUENCE seq_recnum START 1;");

            $this->TCdfvCriterioFaixaValor->query("
                UPDATE cdis_criterio_distribuicao SET cdis_nivel = sq_tabela1.sequencia
                FROM (SELECT nextval('seq_recnum') AS sequencia
                             ,  sq_tabela2.*
                         FROM (SELECT *
                                 FROM cdis_criterio_distribuicao
                             ORDER BY cdis_nivel
                              ) sq_tabela2
                       ) sq_tabela1
                WHERE (sq_tabela1.cdis_codigo = cdis_criterio_distribuicao.cdis_codigo);"
            );
            
            $this->TCdfvCriterioFaixaValor->query("DROP SEQUENCE seq_recnum;");                
            
            $this->BSession->setFlash('delete_success');

        } catch( Exception $ex ) {
            $this->BSession->setFlash('delete_error');
            
        }

       $this->redirect(array('action' => 'index'));
    }
}
