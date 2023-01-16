<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
    	<?php echo $bajax->form('Alerta', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Alerta', 'element_name' => 'alertas'), 'divupdate' => '.form-procurar')) ?>
            <div class="row-fluid inline">
                <?php echo $this->Buonny->input_codigo_cliente($this) ?>
            	<?php echo $this->BForm->input('codigo_usuario_tratamento', array('options'=>$usuarios, 'empty'=>'Todos os usuários', 'label'=>false)) ?>
                <?php echo $this->BForm->input('nao_tratados', array('label' => 'Apenas alertas não tratados', 'type'=>'checkbox')); ?>
            </div>
   
            <div class="row-fluid inline">
                <div class="control-group input">
                    <label>Data do alerta</label>
            	    <?php echo $this->Buonny->input_periodo($this, 'Alerta', 'data_inclusao_inicial', 'data_inclusao_final') ?>
            	</div>
            	<div class="control-group input">
                    <label>Data do tratamento</label>
            	    <?php echo $this->Buonny->input_periodo($this, 'Alerta', 'data_tratamento_inicial', 'data_tratamento_final') ?>
            	</div>
            </div>

            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        $.placeholder.shim();
        atualizaLista("div.lista", "alertas/listagem");
        setup_datepicker();
        jQuery("#AlertaCodigoCliente").change(function(){
            $.getJSON(baseUrl + "/usuarios/json_por_cliente/" + jQuery("#AlertaCodigoCliente").val(), function(data){
                $("#AlertaCodigoUsuarioTratamento option").each(function() {
                    if ($(this).val() != "") $(this).remove();
                });
                $.each(data, function(codigo, nome) {
                    $("#AlertaCodigoUsuarioTratamento").append($("<option></option>").val(codigo).html(nome));
                });
            });
        });
        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Alerta/element_name:alertas/" + Math.random())
        });
    });', false);
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>