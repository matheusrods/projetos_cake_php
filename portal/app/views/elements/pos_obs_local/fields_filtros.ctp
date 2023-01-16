<div class="row-fluid inline">
    <?php echo $this->BForm->input(
        'codigo_local',
        array(
            'class'       => 'input-mini just-number',
            'placeholder' => 'Código',
            'label'       => false,
            'type'        => 'text'
        )
    ); ?>

    <?php echo $this->BForm->input(
        'descricao',
        array(
            'class'       => 'input-xlarge',
            'placeholder' => 'Descrição',
            'label'       => false
        )
    ) ?>
    <?php echo $this->BForm->input(
        'ativo',
        array(
            'class'   => 'input-small',
            'label'   => false,
            'empty'   => 'Status',
            'default' => ' ',
            'options' => array('0' => 'Inativos', '1' => 'Ativos'),
        )
    ); ?>
</div>