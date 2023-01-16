<?php
class ModelosController extends AppController {
	public $name = 'Modelos';
	var $uses = array('TMvecModeloVeiculo', 'VeiculoModelo');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow('carrega_combo_t_modelo', 'carrega_combo_modelo');
	}

	public function carrega_combo_t_modelo($mvei_codigo){
		$modelos = $this->TMvecModeloVeiculo->listaPorMarca($mvei_codigo);
		$this->set(compact('modelos'));
	}

	public function carrega_combo_modelo($fabricante_codigo){
		$modelos = $this->VeiculoModelo->combo($fabricante_codigo);
		$this->set(compact('modelos'));
	}


}
