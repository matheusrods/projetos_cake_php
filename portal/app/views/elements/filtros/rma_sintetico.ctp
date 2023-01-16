<div class='well'>
    <h5><?= $this->Html->link((empty($this->BForm->validationErrors) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $this->Bajax->form('TOrmaOcorrenciaRma', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TOrmaOcorrenciaRma', 'element_name' => 'rma_sintetico'), 'divupdate' => '.form-procurar', 'class'=> 'formulario')) ?>
        <?php echo $this->element('rma/fields-filtros') ?>
        <div class="row-fluid inline">
            <span class="label label-info">Agrupar por:</span>
            <div id='agrupamento'>
                <?php echo $this->BForm->input('agrupamento', array('type' => 'radio', 'options' => $agrupamento, 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-small'))) ?>
            </div>
        </div>
        <?php echo $html->link('Buscar', 'javascript:submitForm()', array('class' => 'btn btn-filtrar')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div>
</div>
<?php echo $this->Javascript->codeBlock("
    jQuery(document).ready(function() {
        setup_mascaras();
    })
    function submitForm(){  
        $('.formulario').submit();
    }
"); ?>

<?php if (isset($filterValidated) && $filterValidated): ?>    
    <?php $chamada = "$.ajax({
            type: 'POST',
            url: '/portal/rma/sintetico_agrupado/%s/' + Math.random(),
            cache: false,
            data:{  
                'data[TOrmaOcorrenciaRma][data_inicial]' : '{$this->data['TOrmaOcorrenciaRma']['data_inicial']}',
                'data[TOrmaOcorrenciaRma][data_final]' : '{$this->data['TOrmaOcorrenciaRma']['data_final']}',
                'data[TOrmaOcorrenciaRma][codigo_cliente]' : '{$this->data['TOrmaOcorrenciaRma']['codigo_cliente']}',
                'data[TOrmaOcorrenciaRma][codigo_embarcador]' : '{$this->data['TOrmaOcorrenciaRma']['codigo_embarcador']}',
                'data[TOrmaOcorrenciaRma][codigo_transportador]' : '{$this->data['TOrmaOcorrenciaRma']['codigo_transportador']}',
                'data[TOrmaOcorrenciaRma][grma_codigo]' : '{$this->data['TOrmaOcorrenciaRma']['grma_codigo']}',
                'data[TOrmaOcorrenciaRma][pfis_cpf]' : '{$this->data['TOrmaOcorrenciaRma']['pfis_cpf']}',
                'data[TOrmaOcorrenciaRma][agrupamento]' : '{$this->data['TOrmaOcorrenciaRma']['agrupamento']}',
            },
            beforeSend : function(){            
                $('#graph').html('<img src=\"/portal/img/loading.gif\" title=\"carregando...\" />');
            },
            success : function(data){ 
                $('#%s').html(data);
            },
            error : function(){
     
            }
        });"; ?>
    <?php echo $this->Javascript->codeBlock("
        $(document).ready(function() {
            jQuery('div#filtros').hide();
            jQuery('a#filtros').click(function(){
                jQuery('div#filtros').slideToggle('slow');
            });

            ".
            sprintf($chamada, $this->data['TOrmaOcorrenciaRma']['agrupamento'], 'table-dados').
            "
        })") ?>
<?php endif ?>
<?php echo $this->Javascript->codeBlock("$(document).ready(function(){
  
    jQuery('#limpar-filtro').click(function(){
        bloquearDiv(jQuery('.form-procurar'));
        jQuery('.form-procurar').load(baseUrl + '/filtros/limpar/model:TOrmaOcorrenciaRma/element_name:rma_sintetico/' + Math.random())
    });
    
});"); ?>