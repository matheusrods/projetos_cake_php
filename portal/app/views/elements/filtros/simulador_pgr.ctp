<div class='well'>
    <?php $filtrado = (isset($this->data['TPgpgPg']['filtro']) && ($this->data['TPgpgPg']['filtro'])) ? $this->data['TPgpgPg']['filtro'] : false;?>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $this->Bajax->form('TPgpgPg', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TPgpgPg', 'element_name' => 'simulador_pgr'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_embarcador', 'Embarcador', true, 'TPgpgPg' ); ?>
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_transportador', 'Transportador', true, 'TPgpgPg' ); ?>
            <?php echo $this->BForm->input('ttra_codigo', array('empty' => 'Selecione o campo','options' => $tipo_transporte,'class' => 'input-medium', 'label' => 'Tipo do Tranporte')); ?>
        </div>    
        <div class="row-fluid inline">
        <?php echo $this->Buonny->input_referencia($this,'#TPgpgPgCodigoTransportador','TPgpgPg','refe_codigo',false,'Alvo','Alvo',false,'#TPgpgPgCodigoEmbarcador'); ?>
            <?php echo $this->BForm->input('placa', array('class' => 'input-small placa-veiculo','label' =>'Placa')); ?>
            <?php echo $this->BForm->input('valor', array('class' => 'input-medium moeda','label' =>'Valor')); ?>
        </div>    
        <div class="row-fluid inline">
            <span class="label label-info">Tipo de PGR:</span>
            <div class="row-fluid inline">
                <div id='agrupamento'>
                    <?php echo $this->BForm->input('tipo_pgr', array(
                        'options' => array(
                            1 => 'GR',
                            2 => 'Logistico'
                        ),
                        'default' => 1, 
                        'legend' => false,
                        'type' => 'radio',
                        'label' => array('class' => 'radio inline visualizacao input-medium')
                    )); ?>
                </div>
            </div>    
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
            <?php echo $this->BForm->end() ?>
        </div>
    </div>
</div>
 <?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();
        '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "simulador_pgr/listagem/" + Math.random());':'').'

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TPgpgPg/element_name:simulador_pgr/" + Math.random())
            jQuery(".lista").empty();
           
        });

        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });

        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });
 
    });', false);
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php else: ?>    
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").show()})');?> 
 <?php endif; ?>