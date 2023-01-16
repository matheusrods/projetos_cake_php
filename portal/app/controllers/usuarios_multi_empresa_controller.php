<?php
class UsuariosMultiEmpresaController extends AppController {
	var $name = 'UsuariosMultiEmpresa';
	var $uses = array('UsuarioMultiEmpresa', 'MultiEmpresa');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(array('*'));
	}
	
    function listar($codigo_usuario) {
    	
        $this->layout = 'ajax';

        $options['conditions'] = array('UsuarioMultiEmpresa.codigo_usuario' => $codigo_usuario);
        $options['fields'] = array('MultiEmpresa.codigo', 'MultiEmpresa.razao_social');
        $options['joins'] = array(
       		array(
   				'table'      => 'multi_empresa',
   				'alias'      => 'MultiEmpresa',
      			'conditions' => 'MultiEmpresa.codigo = UsuarioMultiEmpresa.codigo_multi_empresa',
       			'type'       => 'inner'
        	),
        );
        
        $empresas_marcadas = $this->UsuarioMultiEmpresa->find('list', $options);
        $todas_empresas = $this->MultiEmpresa->find('all', array('conditions' => array('codigo_status_multi_empresa' => '1')));

        $this->set(compact('empresas_marcadas', 'todas_empresas'));
    }
}
?>