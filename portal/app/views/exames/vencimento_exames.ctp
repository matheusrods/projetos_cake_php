<div class='well'>
        <strong>Código: </strong><?php echo $this->Html->tag('span', $this->data['Cliente']['codigo']); ?>
        <strong>Cliente: </strong><?php echo $this->Html->tag('span', $this->data['Cliente']['razao_social']); ?>
    </div>
<div class='well'>
<?php echo $this->Form->create('Exame', array('url' => 'exportar')) ?>
<?php echo $this->Form->hidden('codigo_cliente', array('value' => $this->data['Cliente']['codigo'])); ?>
<?php echo $this->Form->hidden('razao_social', array('value' => $this->data['Cliente']['razao_social'])); ?>
<div class="row-fluid">
    <div class="span6" style="margin-left: 0">
<label><strong>Tipos de exames ocupacionais:</strong></label>
    <?php echo $this->Form->input('tipo_exame', array('required' => true, 'legend' => false, 'type' => 'radio', 'options' => $tipos_exames)); ?>
    </div>
    <div class="span6">
        <label><strong>Situação:</strong></label>
        <?php echo $this->Form->input('situacao', array('required' => true, 'legend' => false, 'type' => 'radio', 'options' => $situacoes)); ?>
    </div>
</div>
<div class="row fluid  margin-top-15">
    <div class="span12">
        <label><strong>Selecione os campos que deverão ser exibidos no relatório: </strong></label>
    </div>
</div>
<div class="row-fluid">
    <div class="span5">
        <?php echo $this->Form->input('from', array('label' => false, 'id' => 'multiselect', 'options' => $campos, 'class' => 'form-control', 'multiple' => true, 'size' => '8', 'style' => 'width: 100%')); ?>
    </div>
    
    <div class="span2">
        <button type="button" id="multiselect_rightAll" class="btn btn-block"><i class="icon-forward"></i></button>
        <button type="button" id="multiselect_rightSelected" class="btn btn-block"><i class="icon-chevron-right"></i></button>
        <button type="button" id="multiselect_leftSelected" class="btn btn-block"><i class="icon-chevron-left"></i></button>
        <button type="button" id="multiselect_leftAll" class="btn btn-block"><i class="icon-backward"></i></button>
    </div>
    
    <div class="span5">
        <?php echo $this->Form->input('to', array('label' => false, 'id' => 'multiselect_to', 'class' => 'form-control', 'options' => array(), 'multiple' => true, 'size' => '8', 'style' => 'width: 100%')); ?>
    </div>
</div>
<div class="row-fluid">
    <div class="span12">
        <label class="margin-top-10"><strong>Exibição:</strong></label>
        <?php echo $this->Form->input('exibicao', array('required' => true, 'legend' => false, 'type' => 'radio', 'options' => $visualizacao)); ?>
    </div>
    <button class="btn btn-primary margin-top-10">Gerar relatório</button>
    <?php echo $this->Html->link('Voltar', array('action' => 'exames_por_cliente'), array('class' => 'btn btn-default margin-top-10')); ?>
</div>
<?php echo $this->Form->end(); ?>
</div>

<?php echo $this->Javascript->codeBlock('
jQuery(document).ready(function($) {
    $("#multiselect").multiselectMulti();
    $("input[name=\"data[Exame][situacao]\"]").change(function(event) {
            if(this.value == "vencer_entre") {
                $("#datas_entre").remove();
                $(this).next().after($("<div>", {id: "datas_entre"})
                    .append("de: ")
                    .append(
                        $("<input>", {name: "data[Exame][data_inicial]", class: "data", style: "width:70px", required: "required"})
                        )
                    .append("&nbsp;&nbsp;&nbsp;até: ")
                    .append(
                        $("<input>", {name: "data[Exame][data_final]", class: "data", style: "width:70px;", required: "required"})
                        )
                    .append($("<div>", {class: "block margin-bottom-15"}))
                    );
                setup_datepicker();
            } else {
                $("#datas_entre").remove();
            }
        });
});
'); ?>
