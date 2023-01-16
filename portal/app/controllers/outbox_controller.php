<?php 
class OutboxController extends AppController {
    public $name = 'Outbox';
    var $uses = array('Mailer.Outbox');

    function envios_por_nota_fiscal($nota_fiscal, $codigo_cliente) {
        $this->autoRender = false;
        $this->Outbox->bindModel(array('belongsTo' => array(
            'RetornoNf' => array('foreignKey' => false, 'type' => 'INNER', 'conditions' => array('Outbox.model' => 'RetornoNf', 'Outbox.foreign_key = RetornoNf.codigo', "RetornoNf.nota_fiscal = {$nota_fiscal}")),
        )));
        $envios = $this->Outbox->find('all', array('fields' => array('Outbox.sent', 'Outbox.to', 'Outbox.created'), 'order' => array('Outbox.sent DESC')));
        
        foreach($envios as $key => $item) {
        	$envios[$key]['Outbox']['sent'] = (trim($item['Outbox']['sent'])) ? $item['Outbox']['sent'] : '<span style="color: red;">Na fila desde: ' . AppModel::dbDateToDate($item['Outbox']['created']) . '!</span>'; 
        }
        
        $envios = AppModel::retiraModel($envios);
        echo json_encode($envios);
    }
}
