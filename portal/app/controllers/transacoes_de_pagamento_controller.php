<?php
class TransacoesDePagamentoController extends AppController {

	public $name = 'TransacoesDePagamento';
	public $layout = 'cliente';
	public $uses = array('Tranpag','LojaNaveg', 'Cliente');

	function prazo_medio_recebimento() {
        $this->pageTitle = 'Prazo Médio Emissão e Pagamento';
        if($this->RequestHandler->isPost()) {
            $data = $this->data['Tranpag'];
            $dados = $this->Tranpag->prazoMedioRecebimento($data);

			if (!empty($data['codigo_cliente']))
				$cliente = $this->Cliente->find('first', array('fields'=>array('Cliente.razao_social','Cliente.codigo'), 'conditions'=>'Cliente.codigo = '.$data['codigo_cliente']));
            if (!empty($this->data['Tranpag']))
				$empresa = $this->LojaNaveg->carregar($this->data['Tranpag']['empresa']);
        } else {
			$this->data['Tranpag']['ano'] = Date('Y');
			$this->data['Tranpag']['grupo_empresa'] = 1;
        }
        $anos = Comum::listAnos();
        $empresas = $this->LojaNaveg->listEmpresas($this->data['Tranpag']['grupo_empresa']);
        $grupos_empresas = $this->LojaNaveg->listGrupos();
        $nome_grupo = $this->LojaNaveg->nomeGrupoPorId( $this->data['Tranpag']['grupo_empresa'] );
        
        $this->set(compact('dados','cliente', 'anos', 'grupos_empresas', 'empresas', 'empresa', 'nome_grupo'));		
	}
}