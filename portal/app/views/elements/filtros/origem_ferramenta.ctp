<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('OrigemFerramenta', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'OrigemFerramenta', 'element_name' => 'origem_ferramenta'), 'divupdate' => '.form-procurar')) ?>

        <div class="row-fluid inline">
            <?php
            $is_admin = 1;
            if($this->Buonny->seUsuarioForMulticliente()) { 
                echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'OrigemFerramenta');

            }
            else if(!empty($_SESSION['Auth']['Usuario']['codigo_cliente'])) {
                echo $this->BForm->hidden('codigo_cliente', array('value' => $_SESSION['Auth']['Usuario']['codigo_cliente']));
                echo $this->BForm->input('nome_fantasia', array('type' => 'text', 'class' => 'input-xlarge',  'label' => 'Cliente', 'readonly' => 'readonly', 'value' => "{$nome_fantasia}"));
                $is_admin = 0;
            }
            else{
                echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'OrigemFerramenta', isset($codigo_cliente) ? $codigo_cliente : '');
            }

            ?>
        </div>

        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo', array('class' => 'input-small just-number', 'placeholder' => 'Código', 'label' => 'Código Origem', 'type' => 'text')) ?>

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
        atualizaListaOrigemFerramenta();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:OrigemFerramenta/element_name:origem_ferramenta/" + Math.random())
        });
        
        function atualizaListaOrigemFerramenta() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "origem_ferramenta/listagem/" + Math.random());
        }
           
    });', false);
