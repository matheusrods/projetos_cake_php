
<?php
if (isset($nome_fantasia['Cliente']['nome_fantasia'])) {
    $nome_fantasia = $nome_fantasia['Cliente']['nome_fantasia'];
}
?>
<div class="well">
    Código: <b><?= $codigo_matriz; ?></b> | Cliente: <b><?= $nome_fantasia; ?></b>
</div>

<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cliente', 'element_name' => 'matriz_responsabilidade_unidades', 'codigo_matriz' => $codigo_matriz), 'divupdate' => '.form-procurar')) ?>

        <div class="row-fluid inline">
            <?php
            echo $this->BForm->input('codigo_matriz', array('type' => 'hidden', 'value' => "{$codigo_matriz}"));
            echo $this->BForm->input('nome_matriz', array('type' => 'hidden', 'value' => "{$nome_fantasia}"));

            echo $this->BForm->input('codigo_cliente', array('type' => 'text', 'label' => 'Código', 'class' => 'input-mini'));
            echo $this->BForm->input('razao_social', array('type' => 'text', 'class' => 'input-xlarge',  'label' => 'Razão social'));
            echo $this->BForm->input('nome_fantasia', array('type' => 'text', 'class' => 'input-xlarge',  'label' => 'Nome fantasia'));

            ?>
        </div>

        <?php if ($is_admin) :?>
            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
        <?php endif; ?>

        <?php echo $this->BForm->end() ?>
    </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaCliente();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            var codigo_matriz = $("#codigo_matriz2").val();
            var codigo_grupo_economico = $("#codigo_grupo_economico2").val();  
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Cliente/element_name:matriz_responsabilidade_unidades/"+ codigo_matriz +"/"+ codigo_grupo_economico +"/" + Math.random());      
        });
            
        function atualizaListaCliente(codigo_matriz) {     
            var div = jQuery("div.lista-matriz");
            bloquearDiv(div);        
            var codigo_matriz = $("#codigo_matriz2").val(); 
            var codigo_grupo_economico = $("#codigo_grupo_economico2").val();  
            div.load(baseUrl + "clientes/listagem_matriz_responsabilidade_unidades/"+ codigo_grupo_economico +"/" + Math.random()); 
        }
    });', false);
?>

<script>
    $(function(){

        // $("#ClienteCodigoMatriz").val($("#codigo_matriz2").val())
    })
</script>
