<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
    	<?php echo $bajax->form('LogConsulta', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'LogConsulta', 'element_name' => 'log_consultas'), 'divupdate' => '.form-procurar')) ?>
            <div class="row-fluid inline">
                <?php echo $this->BForm->input('login', array('label'=>'Login', 'class'=>'input-large')) ?>
            	<?php echo $this->BForm->input('codigo_tipo_consulta', array('options'=>$tipos_consulta, 'empty'=>'Todos', 'label'=>'Tipo da Consulta')) ?>
                <div class='row-fluid inline divFK' style="display: none;">
                <?php echo $this->BForm->input('foreign_key', array('label'=>'Cod. Registro', 'class'=>'input-medium')) ?>
                </div>
            </div>
   
            <div class="row-fluid inline">
                <div class="control-group input">
                    <label>Data da Consulta</label>
            	    <?php echo $this->Buonny->input_periodo($this, 'LogConsulta', 'data_inclusao_inicial', 'data_inclusao_final') ?>
                    <?php echo $this->BForm->input('hora_inclusao_inicial', array('label' => false, 'class' => 'hora input-mini')) ?>
                    <?php echo $this->BForm->input('hora_inclusao_final', array('label' => false, 'class' => 'hora input-mini')) ?>         
            	</div>
            </div>

            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div>
</div>
<?php echo $this->Javascript->codeBlock('
    function altera_tipo_consulta(changed) {
        var codigo_tipo_consulta = $("#LogConsultaCodigoTipoConsulta").val();
        var div = $(".divFK");
        var label = div.find("label");
        var obj = $("#LogConsultaForeignKey");
        
        if (codigo_tipo_consulta=="") {
            div.hide();
        } else {
            div.show();
        }
        obj.unmask();
        obj.attr("class","input-medium");
        obj.off("keyup");
        if (changed) obj.val("");

        if (codigo_tipo_consulta==1) {
            label.html("Num. SM");
            obj.addClass("just-number");
        }

        if (codigo_tipo_consulta==2) {
            label.html("Placa");
            obj.addClass("placa-veiculo");
        }

        setup_mascaras();
    }

    jQuery(document).ready(function(){

        $("#LogConsultaCodigoTipoConsulta").change(function () {
            altera_tipo_consulta(true);
        });

        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:LogConsulta/element_name:log_consultas/" + Math.random())
        });

        altera_tipo_consulta(false);
        $(".hora").mask("99:99");  
        setup_mascaras();
        setup_datepicker();

        $.placeholder.shim();
        atualizaLista("div.lista", "log_consultas/listagem");

    });', false);
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>