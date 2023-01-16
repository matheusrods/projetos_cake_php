<?php echo $this->BForm->create('Configuracao', array('url' => array('controller' => 'configuracoes','action' => 'index'), 'type' => 'post'));?>

<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-xlarge">Chave</th>
            <th class="input-large">Valor</th>
            <th>Observação</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dados as $key => $value): ?>
            <tr>
                <td>
                    <?php echo $this->BForm->hidden("Configuracao.{$key}.codigo", array('value' =>  $value['Configuracao']['codigo']));?>
                    <?php echo $this->BForm->input("Configuracao.{$key}.chave",array('class' => 'input-xlarge', 'style' => 'width: 348px;', 'label' => false, 'readonly' => true, 'value' => $value['Configuracao']['chave']));?>
                </td>
                <td>
                    <?php echo $this->BForm->input("Configuracao.{$key}.valor",array('class' => 'input-xlarge', 'label' => false, 'value' => $value['Configuracao']['valor']));?>
                </td>
                <td>    
                    <?php echo $this->BForm->input("Configuracao.{$key}.observacao",array('class' => 'input-xxlarge', 'style' => 'width: 605px;', 'label' => false, 'value' => $value['Configuracao']['observacao']));?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<div class="form-actions">
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
</div>

<?php echo $this->BForm->end() ?>