<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
            
          echo $javascript->codeBlock("close_dialog();carrega_endereco_cliente(".$this->data['ClienteEndereco']['codigo_cliente'].")");
        exit;
    }
    
    $edit_mode = false;
    if(isset($this->data['ClienteEndereco']['codigo_endereco']))
        $edit_mode = true;
?>
<div id="clientes_salvar_endereco">
    <div id="error">
        <ul></ul>
    </div>

    <?php echo $bajax->form('ClienteEndereco',array('id' => 'FormEndereco', 'url' => array('controller' => 'clientes', 'action' => 'salvar_endereco'))) ?>

    <?php echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'class' => 'codigo-cliente')); ?>
    
    <?php 
        $array_tipo_contato_salvar = array('type' => 'select', 'options' => $tipo_contato);
        $array_tipo_contato_editar = array('type' => 'hidden');
        $array_endereco_cep = array('class' => 'endereco-cep');
   
        if ($edit_mode) {    
            $array_tipo_contato = $array_tipo_contato_editar;      
            $array_endereco_cep += array('readonly' => 'readonly');
            echo $this->BForm->input('tipo_contato_fake', array('type' => 'text', 'label' => 'Tipo do Contato', 'class' => '', 'readonly' => 'readonly', 'value' => $tipo_contato_nome, 'class' => 'tipo_contato_fake'));
        } else {
            $array_tipo_contato = $array_tipo_contato_salvar;
        }

        echo $this->BForm->input('codigo', array('type' => 'hidden', 'id' => 'endereco-codigo'));
        echo $this->BForm->input('codigo_tipo_contato', $array_tipo_contato);
        echo $this->BForm->input('endereco_cep', $array_endereco_cep); 
    ?>
    
    
        
    

    <?php
        $endereco_logradouro_hidden = array('type' => 'hidden', 'class' => 'endereco-codigo', 'readonly' => 'readonly');
        $endereco_logradouro_select = array('class' => 'endereco-codigo', 'options' => array(), 'empty' => 'Selecione um endereço..');

        if($edit_mode) {
            $endereco_logradouro = $endereco_logradouro_hidden;
        } else {
            $endereco_logradouro = $endereco_logradouro_select;
        }
    ?>
    
    <?php
        if($edit_mode) {
            echo $this->BForm->input('ClienteEndereco.endereco_logradouro', array('class' => 'endereco-logradouro', 'readonly' => 'readonly'));
        }
    ?>
    
    
    <?php echo $this->BForm->input('numero', array('class' => 'numero', 'id' => 'endereco-numero')); ?>
    <?php echo $this->BForm->input('complemento', array('class' => 'complemento')); ?>

    <div class="clear"></div>
    
    <?php echo $this->BForm->end(array('label' => 'Salvar', 'div' => array('class' => 'salvar_endereco'))); ?>
</div>
