<div class='well'>
    <div id='filtros'>
        <?php 
            echo $bajax->form(
                'Ghe',
                array(
                    'autocomplete' => 'off', 
                    'url' => array(
                        'controller' => 'filtros', 
                        'action' => 'filtrar', 
                        'model' => 'Ghe', 
                        'element_name' => 'ghe'
                    ), 
                    'divupdate' => '.form-procurar'
                )
            ) 
        ?>

        <div class="row-fluid inline">
            <?php
                if ($is_admin) {
                    if ($this->Buonny->seUsuarioForMulticliente()) {
                        echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false, 'Cliente');
                    } else {
                        echo $this->Buonny->input_codigo_cliente2($this, 
                            array(
                                'input_name' => 'codigo_cliente',
                                'label' => 'Código',
                                'name_display' => array(
                                    'label' => 'Cliente',
                                ), 
                                'checklogin' => false
                            ), 
                            'Ghe'
                        );
                    }
                } else {
                    if ($this->Buonny->seUsuarioForMulticliente()) {
                        echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false, 'Cliente');
                    } else {
                        echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => "{$codigo_cliente}"));
                        echo $this->BForm->input('nome_fantasia', array('type' => 'text', 'class' => 'input-xlarge',  'label' => 'Cliente', 'readonly' => 'readonly', 'value' => "{$nome_fantasia['Cliente']['nome_fantasia']}"));
                    }
                }
            ?>

            <?php 
                echo $this->BForm->input(
                    'codigo_unidade', 
                    array(
                        'label' => "Unidade", 
                        'class' => 'input-xlarge', 
                        'options' => (count($unidades)) ? $unidades : array("" => "Selecione uma unidade"),
                        // 'empty' => 'Selecione uma unidade',
                        'default' => isset($this->data["codigo_unidade"]) ? $this->data["codigo_unidade"] : " "
                    )
                ); 
            ?>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo', array('class' => 'input-medium just-number', 'placeholder' => 'Código', 'label' => 'Código GHE', 'type' => 'text')) ?>

            <?php 
                # Falta definição dos campos / PC - 2532
                // echo $this->BForm->input('codigo_externo_ghe', array('class' => 'input-medium', 'placeholder' => 'Código externo GHE', 'label' => 'Código externo GHE')) 
            ?>

            <?php echo $this->BForm->input('chave_ghe', array('class' => 'input-medium', 'placeholder' => 'Chave GHE', 'label' => 'Chave GHE')) ?>

            <?php echo $this->BForm->input('aprho_parecer_tecnico', array('label' => 'APRHO parecer técnico','class' => 'input-xlarge', 'options'=> array('Agente abaixo da tolerância', 'Agente acima da tolerância', 'Agente acima do nível de ação'), 'empty' => 'Todos', 'default' => ' ')); ?>

            <?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => 'Ativo', 'options' => array('1' => 'Ativos', '0' => 'Inativos'), 'empty' => 'Todos', 'default' => ' ')); ?>
        </div>

        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>

        <?php echo $this->BForm->end() ?>
    </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaGhe();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Ghe/element_name:ghe/" + Math.random())
        });
        
        function atualizaListaGhe() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "ghe/listagem/" + Math.random());
        }
    });', false);     
?>