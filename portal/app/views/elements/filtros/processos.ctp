<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('Processo', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Processo', 'element_name' => 'processos'), 'divupdate' => '.form-procurar')) ?>

        <div class="row-fluid inline">
            <?php

                if ($is_admin) {
                    if ($this->Buonny->seUsuarioForMulticliente()) {
                        echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'Processo');
                    } else {
                        echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'Processo');
                    }
                } else {
                    echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => "{$codigo_cliente}"));
                    echo $this->BForm->input('nome_fantasia', array('type' => 'text', 'class' => 'input-xlarge',  'label' => 'Cliente', 'readonly' => 'readonly', 'value' => "{$nome_fantasia['Cliente']['nome_fantasia']}"));
                }
            ?>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo', array('class' => 'input-medium just-number', 'placeholder' => 'Código', 'label' => 'Código do Processo', 'type' => 'text')) ?>

            <?php echo $this->BForm->input('titulo', array('class' => 'input-xlarge', 'placeholder' => 'Titulo', 'label' => 'Titulo')) ?>

            <?php echo $this->BForm->input('codigo_processo_tipo', array('label' => 'Tipo','class' => 'input-medium', 'options'=> $combo_processo_tipo, 'empty' => 'Todos', 'default' => ' ')); ?>
        </div>

        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>

        <?php echo $this->BForm->end() ?>
    </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaChamados();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Processo/element_name:processos/" + Math.random())
        });
        
        function atualizaListaChamados() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "processos/listagem/" + Math.random());
        }
        
        $(".multiselect-grupo").multiselect({
            maxHeight: 300,
            nonSelectedText: "Grupo",
            numberDisplayed: 1,
            includeSelectAllOption: true
        });        
        
    });', false);
