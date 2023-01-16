<div class='well'>
    <?php echo $bajax->form('TLoadLoadplan', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TLoadLoadplan', 'element_name' => 'loadplan_analitico'), 'divupdate' => '.form-loadplan_analitico')) ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('load_loadplan', array('class' => 'input-small', 'label' => 'Loadplan')) ?>
            <?php echo $this->Buonny->input_periodo($this, 'TLoadLoadplan', 'data_inicial_load', 'data_final_load', TRUE ) ?>
        <!--
        </div>
        <div class="row-fluid inline">
        -->
            <?php echo $this->BForm->input('sm_codigo', array('class' => 'input-small', 'label' => 'SM')) ?>
            <?php echo $this->Buonny->input_periodo($this, 'TLoadLoadplan', 'data_inicial_sm', 'data_final_sm', TRUE ) ?>
        </div>

        <div class="row-fluid inline">
            <?php echo $this->BForm->input('status_viagem', array('class' => 'input-large', 'empty' => 'Status da Viagem', 'label' => 'Status','options' => $status_viagens)) ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){        
        var div = jQuery("#lista");
        bloquearDiv(div);
        div.load(baseUrl + "loadplans/analitico_listagem/" + Math.random());       

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-loadplan_analitico"));
            jQuery(".form-loadplan_analitico").load(baseUrl + "/filtros/limpar/model:TLoadLoadplan/element_name:loadplan_analitico/" + Math.random())
        });
    });', false);

?>