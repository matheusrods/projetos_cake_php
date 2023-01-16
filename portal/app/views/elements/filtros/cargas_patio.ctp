<?php $filtrado = $this->data['TCpatCargasPatio']['cpat_pjur_pess_oras_codigo'];?>
<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $bajax->form('TCpatCargasPatio', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TCpatCargasPatio', 'element_name' => 'cargas_patio'), 'divupdate' => '.form-procurar')) ?>
        <div class='row-fluid inline'>    
            <?php echo $this->Buonny->input_codigo_cliente($this, 'cpat_pjur_pess_oras_codigo', 'Cliente', 'Cliente', 'TCpatCargasPatio'); ?>
            <?php echo $this->BForm->input('cpat_placa_carreta', array('class' => 'input-small placa-veiculo','label' =>'Placa da Carreta')); ?>
            <?php echo $this->BForm->input('cpat_loadplan', array('class' => 'input-medium','label' =>'loadplan')); ?>
        </div>
        <div class='row-fluid inline'>
            <div id="div-tipo-alvo">
                <?= $this->Buonny->input_alvos_bandeiras_regioes($this, array_merge($alvos_bandeiras_regioes, array('div' => '#div-tipo-alvo','somente_cd'=> true,'exibir_regiao'=> false,'input_codigo_cliente'=> 'cpat_pjur_pess_oras_codigo','exibe_label'=> false, 'exibe_classes'=>false, 'exibe_veiculo'=> false,'exibe_transportador'=> false)))?>
            </div>
            <div>
                <?php echo $this->BForm->input('sem_data_saida', array('checked' => 'checked','type'=>'checkbox', 'div' => 'input checkbox input-large', 'label' => 'Sem data saída','value' => 1)); ?>
            </div>
        </div>    
        <div class='row-fluid inline'>
            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        </div>
        <?php echo $this->BForm->end() ?>
    </div>    
</div>
<?php echo $this->Javascript->codeBlock('
    $(document).ready(function(){
        setup_mascaras();   
        '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "cargas_patio/listagem/" + Math.random());':'').'

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TCpatCargasPatio/element_name:cargas_patio/" + Math.random())
           
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