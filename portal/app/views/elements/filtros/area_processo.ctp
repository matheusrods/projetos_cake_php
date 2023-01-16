<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('AreaProcesso', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'AreaProcesso', 'element_name' => 'area_processo'), 'divupdate' => '.form-procurar')) ?>

        <div class="row-fluid inline">
            <?php

            if ($is_admin) {
                if ($this->Buonny->seUsuarioForMulticliente()) {
                    echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'AreaProcesso');
                } else {
                    echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código (*)', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'AreaProcesso');
                }
            } else {

                if (isset($_SESSION['Auth']['Usuario']['multicliente']) && !empty($_SESSION['Auth']['Usuario']['multicliente'])) {

                    if ($this->Buonny->seUsuarioForMulticliente()) {
                        echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'AreaAtuacao');
                    } else {
                        echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código (*)', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'AreaProcesso');
                    }
                } else {
                    echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => "{$codigo_cliente}"));
                    echo $this->BForm->input('nome_fantasia', array('type' => 'text',  'label' => 'Cliente', 'readonly' => 'readonly', 'value' => "{$nome_fantasia}"));

                }
            }
            ?>
        </div>

        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo', array('class' => 'input-small just-number', 'placeholder' => 'Código', 'label' => 'Código área', 'type' => 'text')) ?>

            <?php echo $this->BForm->input('descricao', array('class' => 'input-xlarge', 'placeholder' => 'Descrição', 'label' => 'Descrição')) ?>

            <?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => 'Status', 'options' => array('Inativos', 'Ativos'), 'empty' => 'Todos', 'default' => ' ')); ?>

        </div>

        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>

        <?php echo $this->BForm->end() ?>
    </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaAreaProcesso();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:AreaProcesso/element_name:area_processo/" + Math.random())
        });
        
        function atualizaListaAreaProcesso() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "area_processo/listagem/" + Math.random());
        }
           
    });', false);
