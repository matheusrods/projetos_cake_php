<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('Risco', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Risco', 'element_name' => 'riscos_esocial'), 'divupdate' => '.form-procurar')) ?>

        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo', array('class' => 'input-small just-number', 'placeholder' => 'Código', 'label' => 'Código', 'type' => 'text')) ?>

            <?php echo $this->BForm->input('nome_agente', array('class' => 'input-xlarge', 'placeholder' => 'Nome agente', 'label' => 'Nome agente')) ?>

            <?php echo $this->BForm->input('codigo_agente_nocivo_esocial', array('class' => 'input-xlarge', 'placeholder' => 'Código agente nocivo e-Social', 'label' => 'Código agente nocivo e-Social')) ?>

            <?php echo $this->BForm->input('codigo_grupo', array('label' => 'Grupo risco','class' => 'input-xlarge', 'options'=> $combo_grupo_risco, 'empty' => 'Todos', 'default' => ' ')); ?>

        </div>

        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>

        <?php echo $this->BForm->end() ?>
    </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaRiscosEsocial();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Risco/element_name:riscos_esocial/" + Math.random())
        });
        
        function atualizaListaRiscosEsocial() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "riscos_esocial/listagem/" + Math.random());
        }
           
    });', false);
