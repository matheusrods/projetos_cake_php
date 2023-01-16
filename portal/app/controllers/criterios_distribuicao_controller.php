<?php
class CriteriosDistribuicaoController extends AppController {
    public $name = 'CriteriosDistribuicao';
    var $uses = array('TCdisCriterioDistribuicao','TProdProduto');
    var $components = array('DbbuonnyGuardian');
    
    function index() {
        $this->loadModel('TTecnTecnologia');
        $this->loadModel('TCdfvCriterioFaixaValor');
        $this->loadModel('TTtraTipoTransporte');
        $this->loadModel('TAatuAreaAtuacao');

        $this->pageTitle = 'Criterios Distribuição';
        $this->data['TCdisCriterioDistribuicao'] = $this->Filtros->controla_sessao($this->data, $this->TCdisCriterioDistribuicao->name);

        $tecnologias    = $this->TTecnTecnologia->listaEmUso();
        $faixas         = $this->TCdfvCriterioFaixaValor->listar();
        $ttransportes   = $this->TTtraTipoTransporte->listarParaFormulario();
        $aatuacao       = $this->TAatuAreaAtuacao->find('list',array('order' => array('aatu_descricao')));

        $this->set(compact('tecnologias','faixas','ttransportes','aatuacao'));
    }
    
    function listagem() {
        $this->layout   = 'ajax';
        $filtros        = $this->Filtros->controla_sessao($this->data, $this->TCdisCriterioDistribuicao->name);

        $this->paginate['TCdisCriterioDistribuicao'] = $this->TCdisCriterioDistribuicao->listagemParams($filtros);
        $listagem       = $this->paginate('TCdisCriterioDistribuicao');

        $this->set(compact('listagem'));
    }
    
    function incluir() {
        
        $this->pageTitle = 'Incluir Criterio de Distribuição';        

        if($this->RequestHandler->isPost()) {

            if( $this->data['TCdisCriterioDistribuicao']['cdis_emba_pjur_pess_oras_codigo'] ){
                $embarcador = $this->data['TCdisCriterioDistribuicao']['cdis_emba_pjur_pess_oras_codigo'];
                $embarcador = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardian($embarcador,false);
                $this->data['TCdisCriterioDistribuicao']['cdis_emba_pjur_pess_oras_codigo'] = $embarcador[0];
                
            }
            if( $this->data['TCdisCriterioDistribuicao']['cdis_tran_pess_oras_codigo'] ){
                $transportador = $this->data['TCdisCriterioDistribuicao']['cdis_tran_pess_oras_codigo'];
                $transportador = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardian($transportador,false);
                $this->data['TCdisCriterioDistribuicao']['cdis_tran_pess_oras_codigo'] = $transportador[0];
            }

            $novo_codigo = $this->TCdisCriterioDistribuicao->find('first', array('fields'=>'(max(cdis_nivel)+1) AS nivel'));
            $novo_codigo = $novo_codigo[0]['nivel'];            
            $this->data['TCdisCriterioDistribuicao']['cdis_nivel'] = $novo_codigo;

            if ($this->TCdisCriterioDistribuicao->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                if(isset($embarcador))
                    $this->data['TCdisCriterioDistribuicao']['cdis_emba_pjur_pess_oras_codigo'] = $embarcador;
                if(isset($transportador))
                    $this->data['TCdisCriterioDistribuicao']['cdis_tran_pess_oras_codigo'] = $transportador;
                $this->BSession->setFlash('save_error');
            }
        }

        $this->setarVariaveisView();
    }

    private function converteClientePortalParaGuardian($codigo_cliente_portal, &$data, $campo){    
        $this->loadModel('Cliente');    
        $cliente = $this->Cliente->carregar($codigo_cliente_portal);
        $base_cnpj = substr($cliente['Cliente']['codigo_documento'], 0, 8);
        $codigo_cliente_guardian = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardianComCentralizador($cliente['Cliente']['codigo'], $base_cnpj);
        $data['TCdisCriterioDistribuicao'][$campo] = $codigo_cliente_guardian[0];
    }

    private function setarVariaveisView(){
        $produtos = $this->TProdProduto->find('list');
        $this->loadModel('TTecnTecnologia');
        $this->loadModel('TCdfvCriterioFaixaValor');
        $this->loadModel('TTtraTipoTransporte');
        $this->loadModel('TAatuAreaAtuacao');

        $tecnologias     = $this->TTecnTecnologia->listaEmUso();
        $tipo_transporte = $this->TTtraTipoTransporte->listarParaFormulario();
        $faixa_valor     = $this->TCdfvCriterioFaixaValor->listar();
        $area_atuacao    = $this->TAatuAreaAtuacao->listar();

        $this->set(compact('produtos','tecnologias','tipo_transporte','faixa_valor','area_atuacao'));
    }
    
    function editar($cdis_codigo) {

        $this->pageTitle = 'Atualizar Criterio de Distribuição';

        if($this->data) {

            if( $this->data['TCdisCriterioDistribuicao']['cdis_emba_pjur_pess_oras_codigo'] ){
                $embarcador = $this->data['TCdisCriterioDistribuicao']['cdis_emba_pjur_pess_oras_codigo'];
                $embarcador = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardian($embarcador,false);
                $this->data['TCdisCriterioDistribuicao']['cdis_emba_pjur_pess_oras_codigo'] = $embarcador[0];
            }
            if( $this->data['TCdisCriterioDistribuicao']['cdis_tran_pess_oras_codigo'] ){
                $transportador = $this->data['TCdisCriterioDistribuicao']['cdis_tran_pess_oras_codigo'];
                $transportador = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardian($transportador,false);
                $this->data['TCdisCriterioDistribuicao']['cdis_tran_pess_oras_codigo'] = $transportador[0];                
            }
            
            if ($this->TCdisCriterioDistribuicao->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }else{
            $this->data = $this->TCdisCriterioDistribuicao->findByCdisCodigo($cdis_codigo);
            if( $this->data['TCdisCriterioDistribuicao']['cdis_emba_pjur_pess_oras_codigo'] ){
                $embarcador = $this->data['TCdisCriterioDistribuicao']['cdis_emba_pjur_pess_oras_codigo'];
                $this->data['TCdisCriterioDistribuicao']['cdis_emba_pjur_pess_oras_codigo'] = $this->DbbuonnyGuardian->converteClienteGuardianEmBuonny($embarcador);
            }
            if( $this->data['TCdisCriterioDistribuicao']['cdis_tran_pess_oras_codigo'] ){
                $transportador = $this->data['TCdisCriterioDistribuicao']['cdis_tran_pess_oras_codigo'];
                $this->data['TCdisCriterioDistribuicao']['cdis_tran_pess_oras_codigo'] = $this->DbbuonnyGuardian->converteClienteGuardianEmBuonny($transportador);
            }                        
        }       

        $this->setarVariaveisView();
    }

    function excluir($cdis_codigo) {
        try{

            if (!$this->TCdisCriterioDistribuicao->excluir($cdis_codigo)) 
                throw new Exception();
            
            $this->TCdisCriterioDistribuicao->query("CREATE TEMPORARY SEQUENCE seq_recnum
                  INCREMENT 1
                  MINVALUE 1  
                  NO MAXVALUE
                  START 1
                  CACHE 1;");

            $this->TCdisCriterioDistribuicao->query("ALTER SEQUENCE seq_recnum START 1;");

            $this->TCdisCriterioDistribuicao->query("
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
            
            $this->TCdisCriterioDistribuicao->query("DROP SEQUENCE seq_recnum;");                
            
            $this->BSession->setFlash('delete_success');

        } catch( Exception $ex ) {
            $this->BSession->setFlash('delete_error');
            
        }

       $this->redirect(array('action' => 'index'));
    }

    function sobe_nivel($cdis_nivel) {
        if(!$this->TCdisCriterioDistribuicao->ajustarNivel($cdis_nivel,TRUE))
            echo "Não foi possível subir um nível!";

        exit;
    }

    function desce_nivel($cdis_nivel) {
        if(!$this->TCdisCriterioDistribuicao->ajustarNivel($cdis_nivel,FALSE))
            echo "Não foi possível descer um nível!";

        exit;
    }

    function visualizar($cdis_codigo) {
        $this->loadModel('Cliente');
        $this->layout = 'ajax';

        $this->TCdisCriterioDistribuicao->bindTTecnTecnologia();
        $this->TCdisCriterioDistribuicao->bindTAatuAreaAtuacao();
        $this->TCdisCriterioDistribuicao->bindTCdfvCriterioFaixaValor();
        $this->TCdisCriterioDistribuicao->bindTProdProduto();
        $this->TCdisCriterioDistribuicao->bindTTtraTipoTransporte();
        $this->TCdisCriterioDistribuicao->bindTPjurEmbarcador();
        $this->TCdisCriterioDistribuicao->bindTPjurTransportador();

        $this->data = $this->TCdisCriterioDistribuicao->carregar($cdis_codigo);

        if($this->data){
            $this->data['TCdfvCriterioFaixaValor']['cdfv_valor_maximo'] = number_format($this->data['TCdfvCriterioFaixaValor']['cdfv_valor_maximo'], 2, ',', '.');
            $this->data['TCdfvCriterioFaixaValor']['cdfv_valor_minimo'] = number_format($this->data['TCdfvCriterioFaixaValor']['cdfv_valor_minimo'], 2, ',', '.');

            $this->data['ClienteEmbarcador'] = array('codigo' => NULL,'razao_social' => NULL);

            if( $this->data['TCdisCriterioDistribuicao']['cdis_emba_pjur_pess_oras_codigo'] ){
                $cliente = $this->Cliente->carregarPorDocumento($this->data['TPjurEmbarcador']['pjur_cnpj']);
                if($cliente)
                    $this->data['ClienteEmbarcador'] = $cliente['Cliente'];
            }

            $this->data['ClienteTransportador'] = array('codigo' => NULL,'razao_social' => NULL);
            if( $this->data['TCdisCriterioDistribuicao']['cdis_tran_pess_oras_codigo'] ){
                $cliente = $this->Cliente->carregarPorDocumento($this->data['TPjurTransportador']['pjur_cnpj']);
                if($cliente)
                    $this->data['ClienteTransportador'] = $cliente['Cliente'];
            }
        }

    }

}