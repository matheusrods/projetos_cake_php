<?php
class PromocoesController extends AppController {
    var $name = 'Promocoes';
    var $uses = array('Promocao');

    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow('cadastro_buonnycredit');
    }

    public function index() {
        $this->pageTitle = 'Promoções';
        $this->set('promocoes',  $this->Promocao->find('all'));
    }

    public function incluir() {
        $this->pageTitle = 'Incluir Promoção';
        if ($this->RequestHandler->isPost()) {
            if ($this->Promocao->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else
                $this->BSession->setFlash('save_error');
        } else {
            $this->data['Promocao']['ativo'] = 1;
            $this->data['Promocao']['quantidade'] = 0;
            $this->data['Promocao']['quantidade_por_beneficiado'] = 0;
            $this->data['Promocao']['valor'] = '0,00';
            $this->data['Promocao']['valor_por_beneficiado'] = '0,00';
        }
        $this->set('regras', array(Promocao::REGRA_BUONNYCREDIT => 'Regra Buonny Credit'));
    }

    public function editar($codigo_promocao) {
        $this->pageTitle = 'Editar Promoção';
        if ($this->RequestHandler->isPut()) {
            if ($this->Promocao->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else
                $this->BSession->setFlash('save_error');
        } else {
            $this->data = $this->Promocao->carregar($codigo_promocao);
            $this->data['Promocao']['valor'] = number_format($this->data['Promocao']['valor'],2,',','.');
            $this->data['Promocao']['valor_por_beneficiado'] = number_format($this->data['Promocao']['valor_por_beneficiado'],2,',','.');
        }
        $this->set('regras', array(Promocao::REGRA_BUONNYCREDIT => 'Regra Buonny Credit'));
    }

    public function cadastro_buonnycredit() {
		$this->layout = "";
        $this->loadModel('Usuario');
        $this->loadModel('BeneficiadoPromocao');
        $info = array('class' => '', 'text' => '');
        if (!empty($this->data)) {
            $this->data['BeneficiadoPromocao']['codigo_promocao'] = $this->Promocao->codigoPromocaoAtiva(Promocao::REGRA_BUONNYCREDIT);
            $this->data['BeneficiadoPromocao']['codigo_usuario_inclusao'] = 1;
            $usuario = $this->Usuario->autenticarCliente($this->data['Usuario']['apelido'], $this->data['Usuario']['senha']);
            if ($usuario) {
                $this->data['BeneficiadoPromocao']['codigo_cliente'] = $usuario['Usuario']['codigo_cliente'];
                if ($this->BeneficiadoPromocao->incluir($this->data)) {
                    $info = array('text' => 'Cliente cadastrado com sucesso!', 'class' => 'label label-success');
                } else {
                    $errors = $this->BeneficiadoPromocao->invalidFields();
                    if (!isset($errors['codigo_documento'])) {
                        $info = array('text' => 'Promoção esgotada', 'class' => 'label label-info');
                    }
               }
            } else
                $this->BSession->setFlash('invalid_login');
        }
        $this->set(compact('info'));
    }
}
