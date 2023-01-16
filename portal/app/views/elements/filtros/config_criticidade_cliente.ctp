<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cliente', 'element_name' => 'config_criticidade_cliente'), 'divupdate' => '.form-procurar')) ?>

        <div class="row-fluid inline">
            <?php

            echo $this->BForm->input('codigo_cliente', array('type' => 'text', 'label' => 'Código Cliente', 'class' => 'input-mini', 'readonly' => 'readonly', 'value' => "{$codigo_cliente}"));
            echo $this->BForm->input('nome_fantasia', array('type' => 'text', 'class' => 'input-xlarge',  'label' => 'Cliente', 'readonly' => 'readonly', 'value' => "{$nome_fantasia}"));

            ?>
        </div>

        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>

        <?php echo $this->BForm->end() ?>
    </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaCliente();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Cliente/element_name:config_criticidade_cliente/" + Math.random())
        });
            
        function atualizaListaCliente() {     
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "clientes/listagem_config_criticidade_cliente/'.$codigo_cliente.'/" + Math.random()); 
        }
    });', false);
?>
