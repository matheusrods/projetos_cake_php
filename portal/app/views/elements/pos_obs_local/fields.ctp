<div class='well'>
    <strong>Código: </strong><?php echo $this->Html->tag('span', $codigo_cliente); ?>
    <strong>Cliente: </strong><?php echo $this->Html->tag('span', $nome_empresa); ?>
</div>
<div class='row-fluid inline'>
    <?php
    if ($edit_mode) :
        echo $this->BForm->input(
            'PosObsLocal.codigo',
            array(
                'label'    => 'Codigo',
                'class'    => 'input-mini just-number',
                'value'    =>  !empty($this->data['PosObsLocal']['codigo']) ? $this->data['PosObsLocal']['codigo'] : '',
                'readonly' => true,
                'type'     => 'text'
            )
        );
    endif;
    ?>

    <?php
    echo $this->BForm->input(
        'PosObsLocal.descricao',
        array(
            'label'       => 'Descrição (*)',
            'class'       => 'input-xxlarge',
            'placeholder' => 'Insira a descrição do local de observação',
            'required'    => true
        )
    );
    ?>
    <?php
    if (!$edit_mode) :
        echo $this->BForm->input(
            'PosObsLocal.ativo',
            array(
                'label'    => 'Status (*)',
                'class'    => 'input',
                'disabled' => true,
                'selected' => 1,
                'empty'    => 'Status',
                'options'  => array(1 => 'Ativo', 0 => 'Inativo')
            )
        );
    endif;
    ?>
</div>

<div class='form-actions'>
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?php echo $html->link(
        'Voltar',
        array('controller' => 'pos_obs_local', 'action' => 'index_locais', $codigo_cliente),
        array('class' => 'btn')
    ); ?>
</div>