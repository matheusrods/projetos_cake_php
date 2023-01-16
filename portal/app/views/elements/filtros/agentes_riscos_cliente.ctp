<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('AgentesRiscosClientes', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'AgentesRiscosClientes', 'element_name' => 'agentes_riscos_cliente'), 'divupdate' => '.form-procurar')) ?>

        <div class="row-fluid inline">
            <?php
                if ($is_admin) {
                    if ($this->Buonny->seUsuarioForMulticliente()) {
                        echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'AgentesRiscosClientes');
                    } else {
                        echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'RiscosTipo');
                    }
                } else {
                    echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => "{$codigo_cliente}"));
                    echo $this->BForm->input('nome_fantasia', array('type' => 'text', 'class' => 'input-xlarge',  'label' => 'Cliente', 'readonly' => 'readonly', 'value' => "{$nome_fantasia['Cliente']['nome_fantasia']}"));
                }
            ?>
        </div>

        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo_arrtpa_ri', array('class' => 'input-small just-number', 'placeholder' => 'Código Agente de Risco', 'label' => 'Código', 'type' => 'text')) ?>

            <?php echo $this->BForm->input('descricao', array('class' => 'input-xlarge', 'placeholder' => 'Risco/Impacto', 'label' => 'Risco/Impacto')) ?>

        </div>

        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>

        <?php echo $this->BForm->end() ?>
    </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaAgentesRiscosClientes();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:AgentesRiscosClientes/element_name:agentes_riscos_cliente/" + Math.random())
        });
        
        function atualizaListaAgentesRiscosClientes() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "agentes_riscos/listagem/" + Math.random());
        }
           
    });', false);
