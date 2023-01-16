<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('UnidadesMedicao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'UnidadesMedicao', 'element_name' => 'unidades_medicao'), 'divupdate' => '.form-procurar')) ?>

<!--        <div class="row-fluid inline">-->
<!--            --><?php
//            if ($this->Buonny->seUsuarioForMulticliente()) {
//                echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'UnidadesMedicao');
//            } else {
//                echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'UnidadesMedicao');
//            }
//            ?>
<!--        </div>-->

        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo', array('class' => 'input-small just-number', 'placeholder' => 'Código', 'label' => 'Código', 'type' => 'text')) ?>

            <?php echo $this->BForm->input('descricao', array('class' => 'input-xlarge', 'placeholder' => 'Descrição', 'label' => 'Descrição')) ?>

            <?php echo $this->BForm->input('inteiro', array('class' => 'input-small just-number', 'placeholder' => 'Inteiro', 'label' => 'Inteiro')) ?>

        </div>

        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>

        <?php echo $this->BForm->end() ?>
    </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaUnidadesMedicao();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:UnidadesMedicao/element_name:unidades_medicao/" + Math.random())
        });
        
        function atualizaListaUnidadesMedicao() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "unidades_medicao/listagem/" + Math.random());
        }
           
    });', false);
