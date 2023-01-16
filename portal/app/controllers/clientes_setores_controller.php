<?php
class ClientesSetoresController extends AppController {
    public $name = 'ClientesSetores';
    var $uses = array('ClienteSetor','GrupoExposicao');
    
    
    function buscar_caracteristicas(){
		$this->render(false, false);

		$codigo_cliente = $_POST['codigo_cliente'];
		$codigo_setor = $_POST['codigo_setor'];
		$codigo_grupo_homogeneo = $_POST['codigo_grupo_homogeneo'];
		$codigo_ghe = empty($codigo_grupo_homogeneo) ? ' is null ' : ' = '.$codigo_grupo_homogeneo;

		$grupo_homogeneo ='(SELECT codigo_cliente_setor 
							FROM '.$this->GrupoExposicao->databaseTable.'.'.$this->GrupoExposicao->tableSchema.'.'.$this->GrupoExposicao->useTable.'
							WHERE codigo_grupo_homogeneo'.$codigo_ghe .')';


		$conditions = array('codigo_cliente' => $codigo_cliente, 'codigo_setor' => $codigo_setor, 'codigo IN '.$grupo_homogeneo);
		$fields = array(
			'ClienteSetor.codigo',
			'ClienteSetor.codigo_cliente',
			'ClienteSetor.codigo_setor',
			'ClienteSetor.pe_direito',
			'ClienteSetor.cobertura',
			'ClienteSetor.iluminacao',
			'ClienteSetor.ventilacao',
			'ClienteSetor.piso',
			'ClienteSetor.estrutura'
		);
	                                                              
		$dados = $this->ClienteSetor->find('first', array('conditions' => $conditions, 'fields' => $fields));

 		echo json_encode($dados);
	}
}