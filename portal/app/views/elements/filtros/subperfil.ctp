<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('Subperfil', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Subperfil', 'element_name' => 'subperfil'), 'divupdate' => '.form-procurar')) ?>

        <div class="row-fluid inline">
            <?php
            $is_admin = 1;
            if($this->Buonny->seUsuarioForMulticliente()) { 
                echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'Subperfil');

            }
            else if(!empty($_SESSION['Auth']['Usuario']['codigo_cliente'])) {
                echo $this->BForm->input('nome_fantasia', array('type' => 'text', 'class' => 'input-xlarge',  'label' => 'Cliente', 'readonly' => 'readonly', 'value' => "{$nome_fantasia}"));
                echo $this->BForm->hidden('codigo_cliente', array('value' => $_SESSION['Auth']['Usuario']['codigo_cliente']));
                
            }
            else{
                echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'Subperfil', isset($codigo_cliente) ? $codigo_cliente : '');
            }


            ?>
        </div>

        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo', array('class' => 'input-small just-number', 'placeholder' => 'Código', 'label' => 'Código subperfil', 'type' => 'text')) ?>

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
        atualizaListaSubperfil();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Subperfil/element_name:subperfil/" + Math.random())
        });
        
        function atualizaListaSubperfil() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "subperfil/listagem/" + Math.random());
        }
           
    });', false);
