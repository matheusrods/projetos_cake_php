<?php echo $this->BForm->create(
    'PosObsLocal',
    array(
        'type' => 'post',
        'url'  => array(
            'controller' => 'pos_obs_local',
            'action'     => 'editar',
            $codigo_cliente, $this->data['PosObsLocal']['codigo']
        )
    )
); ?>
<?php echo $this->element('pos_obs_local/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>