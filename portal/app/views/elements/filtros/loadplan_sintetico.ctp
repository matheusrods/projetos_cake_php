<?php echo $this->Bajax->form('TLoadLoadplan', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TLoadLoadplan', 'element_name' => 'loadplan_sintetico'), 'divupdate' => '#filtros')) ?>
<div class="row-fluid inline">
    <div class="row-fluid inline">
        <?php if(empty($authUsuario['Usuario']['codigo_cliente'])): ?>
            <?php echo $this->Buonny->input_codigo_cliente_base($this, 'codigo_cliente', 'Cliente', false, 'TLoadLoadplan') ?>
        <?php endif; ?>
        <?php echo $this->BForm->input('load_loadplan', array('class' => 'input-small', 'label' => false, 'placeholder' => 'Loadplan')) ?>
        <?php echo $this->Buonny->input_periodo($this, 'TLoadLoadplan', 'data_inicial_load', 'data_final_load', false ) ?>
        <?php //echo $this->BForm->input('sm_codigo', array('class' => 'input-small', 'label' => 'SM')) ?>
        <?php //echo $this->Buonny->input_periodo($this, 'TLoadLoadplan', 'data_inicial_sm', 'data_final_sm', TRUE ) ?>
    </div>
    
        <!--<div class="row-fluid inline">
            <?php //echo $this->BForm->input('status_viagem', array('class' => 'input-large', 'empty' => 'Status da Viagem', 'label' => 'Status','options' => $status_viagens)) ?>
        </div>-->
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn', 'id' => 'filtrar')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $javascript->codeBlock(
    'jQuery(document).ready(function(){

        atualizaListaLoadplanSintetico();
        atualizaListaLoadplanSmSintetico();

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery("#filtros"));
            jQuery("#filtros").load(baseUrl + "/filtros/limpar/model:TLoadLoadplan/element_name:loadplan_sintetico/" + Math.random())      
        });
        
    })'
) ?>