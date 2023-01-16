<div class='well'>   
    <?php echo $bajax->form('GrupoExposicao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'GrupoExposicao', 'element_name' => 'buscar_grupo_exposicao', 'unidade' => $codigo_cliente), 'divupdate' => '.form-procurar')) ?>
        
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('GrupoExposicao.codigo_setor', array('class' => 'input', 'label' => false, 'options' => $setor, 'empty' => 'Setores')); ?>
            <?php echo $this->BForm->input('GrupoExposicao.codigo_cargo', array('class' => 'input', 'label' => false, 'options' => $cargo, 'empty' => 'Cargos')); ?>
            <?php echo $this->BForm->input('GrupoExposicao.codigo_risco', array('class' => 'input', 'label' => false, 'options' => $risco, 'empty' => 'Riscos')); ?>
        </div>
            <?php echo $this->BForm->hidden('GrupoExposicao.unidade', array('value' => $codigo_cliente)); ?>

        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    
    <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_datepicker();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "filtros/limpar/model:GrupoExposicao/element_name:buscar_grupo_exposicao/unidade:'.$codigo_cliente.'/" + Math.random())
        });
        atualizaLista("buscar_grupo_exposicao", "'.$codigo_cliente.'");
    });


    function atualizaLista(destino, codigo_cliente) {
        var div = jQuery("div#busca-lista");
        bloquearDiv(div);
        div.load(baseUrl + "grupos_exposicao/buscar_listagem/" + codigo_cliente + "/" + Math.random());
    }',false);
?>

