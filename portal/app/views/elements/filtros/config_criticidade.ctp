<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cliente', 'element_name' => 'config_criticidade'), 'divupdate' => '.form-procurar')) ?>

        <div class="row-fluid inline">
            <?php
            $is_admin = 1;
            if($this->Buonny->seUsuarioForMulticliente()) { 
                echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'Cliente');
            }
            else if(!empty($_SESSION['Auth']['Usuario']['codigo_cliente'])) {
                echo $this->BForm->input('nome_fantasia', array('class' => 'input-xlarge', 'value' => $nome_cliente, 'label' => 'Cliente', 'type' => 'text','readonly' => true)); 
                echo $this->BForm->hidden('codigo_cliente', array('value' => $_SESSION['Auth']['Usuario']['codigo_cliente']));
                $is_admin = 0;
            }
            else{
                echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'Cliente', isset($codigo_cliente) ? $codigo_cliente : '');
            }
            ?>
        </div>

        <?php if ($is_admin) :?>
            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
        <?php endif; ?>

        <?php echo $this->BForm->end() ?>
    </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaCliente();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Cliente/element_name:config_criticidade/" + Math.random())
        });
            
        function atualizaListaCliente() {     
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "clientes/listagem_config_criticidade/" + Math.random()); 
        }
    });', false);
?>
