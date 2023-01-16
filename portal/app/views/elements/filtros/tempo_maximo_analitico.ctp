<?php $filtrado = (isset($this->data['TViagViagem']['codigo_cliente']) && $this->data['TViagViagem']['codigo_cliente'] != null
   && isset($this->data['TViagViagem']['maximo_minutos']) && $this->data['TViagViagem']['maximo_minutos'] != null
   && isset($this->data['TViagViagem']['data_inicial']) && $this->data['TViagViagem']['data_inicial'] != null
   && isset($this->data['TViagViagem']['data_final']) && $this->data['TViagViagem']['data_final'] != null); 
?>
<div class='well'>
    <h5><?= $this->Html->link(($filtrado ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $this->BForm->create('TViagViagem', array('autocomplete' => 'off', 'url' => array('controller' => 'viagens', 'action' => 'tempo_maximo_analitico'))) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente_base($this, 'codigo_cliente', 'Cliente', false, 'TViagViagem') ?>
            <?php echo $this->BForm->input('mesclar_prazo_adiantado', array('type'=>'checkbox', 'label' => 'Mesclagem "No Prazo" e "Adiantado"', 'class' => 'input-small', 'id' => 'mesclar_prazo_adiantado_checkbox')) ?>
        </div>

        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_periodo($this) ?>
            <?php echo $this->BForm->input('maximo_minutos', array('label' => false, 'class' => 'input-mini numeric tempo', 'placeholder' => 'Minutos', 'title' => 'Minutos Máximo no Local')) ?>
            <?php echo $this->BForm->input('status_permanencia', array('label' => false, 'options' => $status_permanencia, 'empty' => 'Status permanência', 'class' => 'input-medium')) ?>
            <?php echo $this->BForm->input('status_viagem', array('label' => false, 'options' => $status_viagem, 'empty' => 'Status viagem', 'class' => 'input-medium')) ?>
            <?php echo $this->BForm->input('status_alvo', array('label' => false, 'options' => $status_alvo, 'empty' => 'Status alvo', 'class' => 'input-medium')) ?>
            <?php echo $this->BForm->input('status_janela', array('label' => false, 'options' => $status_janelas, 'empty' => 'Status janela', 'class' => 'input-medium', 'id' => 'status_janela')) ?>
        </div>
        <div class="row-fluid inline" id="div-tipo-alvo">
            <?= $this->Buonny->input_alvos_bandeiras_regioes($this, array_merge($alvos_bandeiras_regioes, array('div' => '#div-tipo-alvo', 'force_model' => 'TViagViagem', 'input_codigo_cliente' => 'codigo_cliente')))?>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('alvo_critico', array('label' => false, 'class' => 'checkbox inline input-medium','options' => array(01 => 'Alvos Críticos'), 'multiple' => 'checkbox')); ?>
            <?php echo $this->BForm->input('proximo_alvo', array('label' => false, 'class' => 'checkbox inline input-medium','options' => array(01 => 'Mostrar Próximos Alvos'), 'multiple' => 'checkbox')); ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end();?>
    </div>
</div>
<?php if(!$filtrado): ?>
    <div class="alert">
        Defina os critérios de filtros.
    </div>
<?php else: ?>
    <div class='well'>
        <strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
        <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
        <span class="pull-right">
            <?php $data = $this->data['TViagViagem'] ?>
            <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array('controller' => 'viagens', 'action' => 'tempo_maximo_analitico', 'export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>
        </span>
    </div>

<?php endif ?>
<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){
    $.placeholder.shim();
    setup_mascaras();
    remover_adiantado();

    function remover_adiantado() {
        if ($("#mesclar_prazo_adiantado_checkbox").is(":checked")) {
            $("#status_janela option[value=2]").remove();
        } else {
            $("#status_janela").append("<option value=2>Adiantado</option>");
        }
    }

    jQuery("a#filtros").click(function(){
        jQuery("div#filtros").slideToggle("slow");
    });

    jQuery("#limpar-filtro").click(function(){
        bloquearDiv(jQuery(".form-procurar"));
        jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TViagViagem/element_name:tempo_maximo_analitico/" + Math.random())
    });

    '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "viagens/tempo_maximo_analitico_listagem/" + Math.random());':'').'

    $(".multiselect-classe-alvo").multiselect({
        maxHeight: 300,
        nonSelectedText: "Classe Alvos",
        numberDisplayed: 1,
        includeSelectAllOption: true
    });

    $(document).on("change","#mesclar_prazo_adiantado_checkbox",function(){
        remover_adiantado();
    });

})');?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
<?php endif; ?>