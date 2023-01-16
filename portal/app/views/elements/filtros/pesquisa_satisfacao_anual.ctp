<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $bajax->form('PesquisaSatisfacaoAnual', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PesquisaSatisfacaoAnual', 'element_name' => 'pesquisa_satisfacao_anual'), 'divupdate' => '.form-procurar')) ?>
         <div class="row-fluid inline">                
                <?php echo $this->Buonny->input_codigo_cliente($this,'codigo_cliente','Cliente',true,'PesquisaSatisfacaoAnual'); ?>
                <?php echo $this->BForm->input('ano', array('class' => 'input-small', 'options' => $anos, 'label' => 'Ano')); ?>
                <?php echo $this->BForm->input('codigo_produto', array('class' => 'input-medium','label' => 'Produto','options' => array('Todos os produtos','1' => 'Teleconsult','82' => 'BuonnySat'))) ?>
                <?php echo $this->BForm->input('codigo_status_pesquisa', array('class' => 'input-medium','label' => 'Status da Pesquisa','options' => $status_pesquisa , 'empty'=>'Status Pesquisa' )) ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn' )) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-anual', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div>    
</div>
<?php echo $this->Javascript->codeBlock('
jQuery(document).ready(function(){
   
    '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "pesquisas_satisfacao/listagem_pesquisa_satisfacao_anual/" + Math.random());':'').'
    jQuery("#limpar-filtro-anual").click(function(){
        bloquearDiv(jQuery(".form-procurar"));
        jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PesquisaSatisfacaoAnual/element_name:pesquisa_satisfacao_anual/" + Math.random());
        var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "pesquisas_satisfacao/listagem_pesquisa_satisfacao_anual/" + Math.random());

    });
    jQuery("a#filtros").click(function(){
        jQuery("div#filtros").slideToggle("slow");
    });
    jQuery("#FiltroSalvarFiltro").click(function(){
        jQuery("#FiltroNomeFiltro").parent().toggle()
    });
});', false);?>
<?php
if (!empty($filtrado)):
    echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');
endif; 
?>