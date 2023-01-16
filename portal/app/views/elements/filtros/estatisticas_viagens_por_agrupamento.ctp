<div class='well'>
    <?php echo $bajax->form('TEviaEstaViagem', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TEviaEstaViagem', 'element_name' => 'estatisticas_viagens_por_agrupamento'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_embarcador', 'Embarcador', false, 'TEviaEstaViagem'); ?>
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_transportador', 'Transportador', false, 'TEviaEstaViagem'); ?>
            <?php echo $this->BForm->input('codigo_seguradora', array('type' => 'select', 'options' => $seguradoras, 'class' => 'input-large', 'label' => false, 'empty' => 'Todas Seguradoras')); ?>
            <?php echo $this->BForm->hidden('codigo_corretora_pjur'); ?>
            <?php echo $this->Buonny->input_codigo_corretora($this, 'codigo_corretora', 'Corretora', false, 'TEviaEstaViagem'); ?>
            <?php echo $this->BForm->input('tecn_codigo', array('class' => 'input-medium', 'label' => false, 'options' => $tecnologias,'empty' => 'Tecnologia')) ?>
            <?php echo $this->BForm->input('usua_oras_codigo', array('class' => 'input-medium', 'label' => false, 'options' => $operadores, 'empty' => 'Operador')) ?>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('tipo', array('class' => 'input-medium', 'label' => false, 'options' => $tipos)) ?>
            <?php echo $this->BForm->input('data', array('class' => 'data input-small', 'placeholder' => 'Data', 'label' => false, 'type' => 'text')) ?>
        </div>
        <div class="row-fluid inline">
            <span class="label label-info">Agrupar por:</span>
            <div id='agrupamento'>
                <?php echo $this->BForm->input('agrupamento', array('type' => 'radio', 'options' => $agrupamento, 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-small'))) ?>
            </div>
        </div>
        <?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')); ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn btn-filtro')); ?>
    <?php echo $this->BForm->end() ?>
</div>
<?php if($isPost) echo $this->Javascript->codeBlock('$(document).ready(function(){atualizaListaEstatisticasViagens();});'); ?>
<?php echo $this->Javascript->codeBlock('
    $(document).ready(function(){
        setup_datepicker();
        setup_mascaras();

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TEviaEstaViagem/element_name:estatisticas_viagens_por_agrupamento/" + Math.random())
        }); 
    });
'); ?>