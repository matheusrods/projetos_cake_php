<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('AcoesMelhoriasTipo', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'AcoesMelhoriasTipo', 'element_name' => 'acoes_melhorias_tipo'), 'divupdate' => '.form-procurar')) ?>

        <div class="row-fluid inline">
            <?php
//                        pr($codigo_cliente);
            if ($is_admin) {
                if ($this->Buonny->seUsuarioForMulticliente()) {
                    echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'AcoesMelhoriasTipo');
                } else {
                    echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'AcoesMelhoriasTipo');
                }
            } else {

                if ($this->Buonny->seUsuarioForMulticliente()) {
                    echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'AcoesMelhoriasTipo');
                } else {
                    echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => "{$codigo_cliente}"));
                    echo $this->BForm->input('nome_fantasia', array('type' => 'text', 'class' => 'input-xlarge',  'label' => 'Cliente', 'readonly' => 'readonly', 'value' => "{$nome_fantasia}"));
                }

            }
            ?>
        </div>

        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo', array('class' => 'input-medium just-number', 'placeholder' => 'Código ação tipo', 'label' => 'Código ação tipo', 'type' => 'text')) ?>

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
        atualizaListaAcoesMelhoriasTipo();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:AcoesMelhoriasTipo/element_name:acoes_melhorias_tipo/" + Math.random())
        });
        
        function atualizaListaAcoesMelhoriasTipo() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "acoes_melhorias_tipo/listagem/" + Math.random());
        }
           
    });', false);
