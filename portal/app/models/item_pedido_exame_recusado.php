<?php
class ItemPedidoExameRecusado extends AppModel {
    public $name            = 'ItemPedidoExameRecusado';
    public $tableSchema     = 'dbo';
    public $databaseTable   = 'RHHealth';
    public $useTable        = 'itens_pedidos_exames_recusados';
    public $primaryKey      = 'codigo';
    public $actsAs          = array('Secure', 'Containable');
    public $recursive       = -1;

    public $belongsTo = array(
        'ItemPedidoExame' => array(
            'ClassName' => 'ItemPedidoExame',
            'foreignKey' => 'codigo_item_pedido_exame'
        ),
        'MotivoRecusaExame' => array(
            'ClassName' => 'MotivoRecusaExame',
            'foreignKey' => 'codigo_motivo_recusa_exame'
        ),
    );

    public $validate = array(
        'codigo_item_pedido_exame' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o codigo do item pedido exame',
                'required' => true
            ),
        ),
        'codigo_motivo_recusa_exame' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o motivo recusa do exame',
                'required' => true
            ),
        ),
    );


    public function incluir(array $data){
        try{
            $return = null;

            $where = array('ItemPedidoExameRecusado.codigo_item_pedido_exame' => $data['ItemPedidoExameRecusado']['codigo_item_pedido_exame']);
            $has = $this->find('first', array('fields' => array('ItemPedidoExameRecusado.codigo'), 'conditions' => $where));

            if((!is_null($has) && !empty($has)) && count($has) >= 1){
                return array(
                    'status' => 'warning',
                    'message' => 'Já foi registrado um Motivo Recusa de Exame para teste item!'
                );
            }

            if(!parent::incluir($data['ItemPedidoExameRecusado'])){
                $return = array(
                    'status' => 'warning',
                    'message' => 'Oops, Algo inesperado aconteceu: Não foi possível inserir o motivo agora. Tente novamente mais tarde!'
                );
            }

            self::enviaEmailAlertaToxicologico($this->getLastInsertId());

            $return = array(
                'status' => 'success',
                'message' => 'Sucesso, Motivo Recusa Exame Inserido!',
            );
            return $return;
        }catch(Exception $ex){
            return array(
                'status' => 'error',
                'message' => 'Oops, algo de inesperado aconteceu: ' . $ex->getMessage(),
            );
        }
    }

    private function enviaEmailAlertaToxicologico($codigo_iper){
        $this->PedidoExame = ClassRegistry::Init('PedidoExame');
        $fields = array('PedidoExame.codigo_cliente');
        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.itens_pedidos_exames',
                'alias' => 'ItemPedidoExame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExameRecusado.codigo_item_pedido_exame = ItemPedidoExame.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.pedidos_exames',
                'alias' => 'PedidoExame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
            ),
        );
        $where = array('ItemPedidoExameRecusado.codigo' => $codigo_iper, "ItemPedidoExame.codigo_exame IN(2195, 134, 135)");
        $codigo_cliente = $this->find('first', array('fields' => $fields, 'joins' => $joins, 'conditions' => $where));
        if(!empty($codigo_cliente['PedidoExame']['codigo_cliente']) && !is_null($codigo_cliente['PedidoExame']['codigo_cliente'])){
            $this->PedidoExame->enviaEmailClienteESocial($codigo_cliente['PedidoExame']['codigo_cliente'], 'email_esocial_s2221');
            $this->PedidoExame->alerta_esocial($codigo_cliente['PedidoExame']['codigo_cliente'], 's2221', 'email_esocial_s2221');
        }
    }

    public function get($codigo){
        $fields = array(
            'ItemPedidoExameRecusado.codigo_motivo_recusa_exame as codigo_motivo_recusa_exame',
            'CAST(ItemPedidoExameRecusado.descricao as TEXT) as descricao'
        );
        return $this->find('first', array('fields' => $fields, 'conditions' => array('ItemPedidoExameRecusado.codigo' => $codigo)));
    }

}