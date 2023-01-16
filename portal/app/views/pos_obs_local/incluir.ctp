<?php echo $this->BForm->create(
    'PosObsLocal',
    array(
        'url' => array(
            'controller' => 'pos_obs_local',
            'action'     => 'incluir',
            $codigo_cliente
        )
    )
); ?>

<?php echo $this->element('pos_obs_local/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>