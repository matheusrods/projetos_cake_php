<?php
class ClientesIpsController extends AppController {
    var $name = 'ClientesIps';
    var $uses = array('ClienteIp');
    var $helpers = array('Paginator');

    function index(){
        $this->Filtros->controla_sessao($this->data, $this->ClienteIp->name);
    }

    function incluir(  ){
        if( $this->data ){
            if ($this->ClienteIp->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }                    
        }        
    }

    function listar( ) {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteIp->name);
        $conditions = array();
        if( !empty($filtros['codigo_cliente']) )
            $conditions['ClienteIp.codigo_cliente'] = $filtros['codigo_cliente'];
        if( !empty($filtros['descricao']) )
            $conditions['ClienteIp.descricao'] = $filtros['descricao'];

        $this->paginate['ClienteIp'] = array(
            'conditions' => $conditions,
            'limit'  => 50,
            'order'  => 'Usuario.nome',
            'fields' => array(
                'ClienteIp.descricao',
                'ClienteIp.codigo',
                'ClienteIp.codigo_cliente',
                'ClienteIp.codigo_usuario_inclusao',
                'ClienteIp.data_inclusao',
                'Usuario.nome',
                'Cliente.razao_social'
           ),
            'joins' => array(
                array(
                    'table'      => 'dbBuonny.portal.usuario',
                    'alias'      => 'Usuario',
                    'conditions' => 'Usuario.codigo = ClienteIp.codigo_usuario_inclusao',
                    'type'       => 'inner'
                ),
                array(
                    'table'      => 'dbBuonny.vendas.cliente',
                    'alias'      => 'Cliente',
                    'conditions' => 'Cliente.codigo = ClienteIp.codigo_cliente',
                    'type'       => 'inner'
                )                
            )
        );
        $enderecos_ips = $this->paginate('ClienteIp');
        $this->set(compact('enderecos_ips'));
    }

    function excluir( $codigo ){        
        if(!is_numeric($codigo))
            exit;
        $codigo = trim($codigo);
        if ($this->ClienteIp->delete($codigo)) {
            $this->BSession->setFlash('save_success');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->BSession->setFlash('save_error');
        }
    }

}
?>