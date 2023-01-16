<?php

if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
    $session->delete('Message.flash');
    echo $javascript->codeBlock("$('#".$id_campo_retorno."').blur();close_dialog();");
    exit;
}
$cep = isset($cep) ? $cep : NULL;
if( $cep ) {
    echo $this->Bajax->form('Endereco', array('autocomplete' => 'off', 'url' => array('controller' => 'enderecos', 'action' => 'incluir')));
    echo $this->BForm->input('dialog', array('type' => 'hidden','value' => TRUE ));
    echo $this->BForm->input('campo_retorno', array('type' => 'hidden','value' => $id_campo_retorno ));
} else {
    echo $this->BForm->create('Endereco');
}?>
<div class='row-fluid inline'>
    <?php echo $this->BForm->input('codigo', array('type' => 'hidden')); ?>
    <?php echo $this->BForm->input('codigo_endereco_cep', array('type' => 'hidden')); ?>
    <?php echo $this->BForm->input('EnderecoCep.cep', array('placeholder' => 'CEP','label' => false,'class' => 'evt-carrega-dados input-small formata-cep', 'value'=> $cep,  'readonly' => ($cep?TRUE:FALSE)  )); ?>
    <?php
    if (!empty($cidade_estado['VEndereco']['endereco_codigo_cidade'])) {
        $cidade = isset($cidade_estado['VEndereco']['endereco_codigo_cidade']) ? $cidade_estado['VEndereco']['endereco_codigo_cidade'] : NULL;
        $estado = isset($cidade_estado['VEndereco']['endereco_codigo_estado']) ? $cidade_estado['VEndereco']['endereco_codigo_estado'] : NULL;
        if (isset($cidade_estado['VEndereco']['endereco_codigo_cidade'])){
            $cidade_estado['VEndereco']['endereco_codigo_cidade'] = '';
        }
        $cidade_estado['VEndereco']['endereco_codigo_cidade']= $cidade;
    }else{
        $estado = false;
        $cidade_estado['VEndereco']['endereco_codigo_cidade']='';
    }
    echo $this->BForm->input('codigo_endereco_cidade', array('type' => 'hidden','value'=> $this->data['Endereco']['codigo_endereco_cidade'] ));    
    echo $this->BForm->input('codigo_endereco_estado', array('type' => 'hidden','value'=> $this->data['EnderecoCidade']['codigo_endereco_estado'] ));
    echo $this->BForm->input('EnderecoCidade.codigo_endereco_estado', array('placeholder' => 'Estado','label' => false, 'options' => $estados,'disabled' => ($estado?TRUE:FALSE),'empty' => 'Estado','class' => 'evt-carrega-cidade evt-estado input-small'));
    echo $this->BForm->input('codigo_endereco_cidade', array('options' => $cidades,'empty' => 'Cidade', 'placeholder' => 'Cidade','label' => false, 'type' => 'select', 'disabled' => ($cep?TRUE:FALSE), 'class' => 'evt-carrega-bairro evt-cidade input-xlarge'));?>
</div>
<div class='row-fluid inline'>
    <?php echo $this->BForm->input('options_bairro', array('type' => 'radio', 'options' => array('Bairro cadastrado', 'Novo Bairro'), 'default' => 0, 'legend' => false,  'label' => array('class' => 'radio inline input-medium'))) ?>
    <?php echo $this->BForm->input('codigo_endereco_bairro_inicial', array('options' => $bairros,'empty' => 'Bairro','placeholder' => 'Bairro','label' => false, 'type' => 'select', 'class' => 'evt-bairro input-xlarge')); ?>
    <?php echo $this->BForm->input('nome_bairro', array('placeholder' => 'Bairro', 'label' => false, 'class' => 'input-xlarge', 'style' => 'display:none')); ?>
</div>
<div class='row-fluid inline'>
    <?php echo $this->BForm->input('codigo_endereco_tipo', array('label' => false, 'type' => 'select', 'options' => $tipos, 'empty' => 'Tipo de logradouro', 'class' => 'input-medium')); ?>
    <?php echo $this->BForm->input('descricao', array('placeholder' => 'Logradouro', 'label' => false, 'class' => 'input-xxlarge')); ?>
</div>
<div class="form-actions">
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?if( $cep ):?>
    <?php echo $this->Form->button('Cancelar', array('id'=>'fechardialogo','type' => 'button','class' => 'btn' ,'title'=>'Fechar')); ?>
    <?else:?>
    <?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
    <?endif;?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $javascript->codeblock('$("#fechardialogo").click(function(){$("#modal_dialog").dialog("close");})'); ?>
<?php echo $javascript->codeblock('
    $("#fechardialogo").click(
        function(){$("#modal_dialog").dialog("close");
    });
    jQuery(document).ready(function() {
        setup_mascaras();
        $("#EnderecoOptionsBairro0").click(function() {
            $("#EnderecoNomeBairro").hide(); 
            $("#EnderecoCodigoEnderecoBairroInicial").show();
        });
        $("#EnderecoOptionsBairro1").click(function() {
            $("#EnderecoNomeBairro").show(); 
            $("#EnderecoCodigoEnderecoBairroInicial").hide();
        });
    });'
); ?>


