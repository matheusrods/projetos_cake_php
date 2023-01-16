<?php
    $filtrado = (isset($this->data['TViagViagem']['codigo_cliente']) && $this->data['TViagViagem']['codigo_cliente'] != null
    && isset($this->data['TViagViagem']['maximo_minutos']) && $this->data['TViagViagem']['maximo_minutos'] != null
    && isset($this->data['TViagViagem']['data_inicial']) && $this->data['TViagViagem']['data_inicial'] != null
    && isset($this->data['TViagViagem']['data_final']) && $this->data['TViagViagem']['data_final'] != null);
?>
<div class='well'>
    <h5><?= $this->Html->link(($filtrado ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $this->BForm->create('TViagViagem', array('autocomplete' => 'off', 'url' => array('controller' => 'viagens', 'action' => 'tempo_maximo_sintetico'))) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente_base($this, 'codigo_cliente', 'Cliente', false, 'TViagViagem') ?>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_periodo($this) ?>
            <?php echo $this->BForm->input('maximo_minutos', array('label' => false, 'class' => 'input-mini numeric tempo', 'placeholder' => 'Minutos', 'title' => 'Minutos Máximo no Local')) ?>
            <?php echo $this->BForm->input('status_viagem', array('label' => false, 'class' => 'input-medium', 'options' => $status_viagem, 'empty' => 'Status Viagem')) ?>
            <?php echo $this->BForm->input('UFOrigem', array('label' => false,'class' => 'input-mini','empty'=>'UF','title'=>'UF Origem', 'options' => $UFOrigem)) ?>
        </div>
        <div class="row-fluid inline" id="div-tipo-alvo">
            <?= $this->Buonny->input_alvos_bandeiras_regioes($this, array_merge($alvos_bandeiras_regioes, array('div' => '#div-tipo-alvo', 'force_model' => 'TViagViagem', 'input_codigo_cliente' => 'codigo_cliente')))?>
        </div>
        <div class="row-fluid inline">
            <span class="label label-info">Agrupar por:</span>
            <div id='agrupamento'>
                <?php echo $this->BForm->input('agrupamento', array('type' => 'radio', 'options' => $agrupamento, 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-small'))) ?>
            </div>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end();?>
    </div>
</div>
<?= $this->addScript($this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        $.placeholder.shim();
        setup_mascaras();
        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TViagViagem/element_name:tempo_maximo_sintetico/" + Math.random())
        });

        '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "viagens/tempo_maximo_sintetico_listagem/" + Math.random());':'').'

        jQuery("table.alvos").tablesorter();
        $(".multiselect-classe-alvo").multiselect({
            maxHeight: 300,
            nonSelectedText: "Classe Alvos",
            numberDisplayed: 1,
            includeSelectAllOption: true
        });

    });
')) ?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
<?php endif; ?>