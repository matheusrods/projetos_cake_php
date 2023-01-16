<div class='well'>
<?php echo $this->BForm->create('AtendimentoSac',array('url' => array('controller' => 'atendimentos_sacs','action' => 'visualizar'))) ?>
    <div class='row-fluid inline'>
        <?php echo $this->BForm->input('codigo_usuario_inclusao',array('class' => 'input-small', 'placeholder' => false, 'label' => 'Usuario', 'readonly' => true)) ?>
        <?php echo $this->BForm->input('ramal_encaminhado',array('class' => 'input-small', 'placeholder' => false, 'label' => 'Ramal', 'readonly' => true)) ?>
        <?php echo $this->BForm->input('nome_usuario_inclusao',array('class' => 'input-medium', 'placeholder' => false, 'label' => 'Atendente', 'value' => $nome_usuario_inclusao[0]['nome'],'readonly' => true)) ?>
        <?php echo $this->BForm->input('apelido_usuario_inclusao',array('class' => 'input-medium','placeholder' => false, 'label' => '&nbsp', 'value' => $nome_usuario_inclusao[0]['apelido'],'readonly' => true)) ?>
        <?php echo $this->BForm->input('codigo_tecnologia',array('class' => 'input-xlarge', 'placeholder' => false, 'label' => 'Tecnologia' , 'options' => $tecnologia, 'empty' => 'Selecione a Tecnologia', 'disabled' => true)); ?>
    </div>
    <div class='row-fluid inline'>
        <?php echo $this->BForm->input('codigo_sm',array('class' => 'input-small', 'placeholder' => false, 'label' => 'SM', 'maxlength' => 10, 'readonly' => true)) ?>
        <?php echo $this->BForm->input('placa',array('class' => 'input-small', 'placeholder' => false, 'label' => 'Placa', 'readonly' => true)) ?>
        <?php echo $this->BForm->input('nome_motorista',array('class' => 'input-medium', 'placeholder' => false, 'label' => 'Motorista', 'value' => $this->data['Motorista']['Nome'],'readonly' => true)) ?>
        <?php echo $this->BForm->input('data_inclusao',array('class' => 'input-medium', 'placeholder' => false, 'label' => 'Data da inclusão' , 'type' => 'text', 'readonly' => true)) ?>
        <?php echo $this->BForm->input('codigo_motivo_atendimento',array('class' => 'input-xlarge', 'label' => 'Motivo da ligação', 'options' => $motivos, 'empty' => 'Selecione um motivo' , 'disabled' => true)); ?>
    </div>
    <div class='row-fluid inline'>
        <?php echo $this->BForm->input('codigo_cliente_embarcador',array('class' => 'input-small', 'placeholder' => false, 'label' => 'Embarcador', 'readonly' => true)) ?>
        <?php echo $this->BForm->input('nome_cliente_embarcador',array('class' => 'input-xxlarge', 'placeholder' => false, 'label' => '&nbsp', 'value' => $nome_cliente_embarcador[0]['nome'], 'readonly' => true)) ?>
    </div>
    <div class='row-fluid inline'>
        <?php echo $this->BForm->input('codigo_cliente_transportador',array('class' => 'input-small', 'placeholder' => false, 'label' => 'Transportador', 'readonly' => true)) ?>
        <?php echo $this->BForm->input('nome_cliente_transportador',array('class' => 'input-xxlarge', 'placeholder' => false, 'label' => '&nbsp', 'value' => $nome_cliente_transportador[0]['nome'],'readonly' => true)) ?>
    </div>
    <div class='row-fluid inline'>
        <?php echo $this->BForm->input('observacao',array('class' => 'input-xxlarge','placeholder' => false, 'label' => 'Observação', 'maxlength' => null,  'type' => 'textarea', 'readonly' => true)) ?>   
    </div>
    <?php echo $html->link('Fechar', '#', array('class' => 'btn closeDialog', 'id' => 'voltar')); ?>
    <?php echo $this->BForm->end();?>
</div>


<?php echo $this->Javascript->codeBlock('
    $(function() {
        $("#voltar").click(function(){
            close();
        })
    });
');