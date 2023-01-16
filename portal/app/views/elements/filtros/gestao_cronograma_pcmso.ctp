<div class='well'>
	<?php echo $bajax->form('CronogramaGestaoPcmso', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'CronogramaGestaoPcmso', 'element_name' => 'gestao_cronograma_pcmso'), 'divupdate' => '.form-procurar', 'callback' => 'atualizaListaGestaoCronogramaPcmso')) ?>
    	<div class="row-fluid inline">
            <div class="row-fluid inline">
                <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'CronogramaGestaoPcmso'); ?>
            </div>
            <div class="row-fluid inline">
                <?php echo $this->BForm->input('codigo_cliente_alocacao', array('label' => 'Unidades', 'class' => 'input-xlarge', 'options' => $data_lista_unidades, 'empty' => 'Todos')) ?>
                <?php echo $this->BForm->input('codigo_setor', array('label' => 'Setores', 'class' => 'input-xlarge', 'options' => $data_lista_setores, 'empty' => 'Todos')); ?>
                <?php echo $this->BForm->input('codigo_tipo_acao', array('label' => 'Tipos de Ação', 'class' => 'input-xlarge', 'options' => $data_tipo_acoes, 'empty' => 'Todos')); ?>
                <?php echo $this->BForm->input('status', array( 'label' => 'Status', 'class' => 'input-medium', 'options' => array('NULL' => 'Pendente', 0 => 'Concluido', 1 => 'Cancelado'), 'empty' => 'Todos')); ?>
            </div>
            <div class="row-fluid inline">
                <?php echo $this->BForm->input('data_inicial', array( 'label' => 'Periodo Inicial', 'class' => 'data input-small')); ?>
                <?php echo $this->BForm->input('data_final', array( 'label' => 'Periodo final', 'class' => 'data input-small')); ?>
            </div>
    	</div> 
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end() ?>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        setup_datepicker();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:CronogramaGestaoPcmso/element_name:gestao_cronograma_pcmso/" + Math.random());
        });

        /*jQuery("#CronogramaGestaoPcmsoCodigoCliente").on('blur', function(){
            gestao_pcmso_get_unidades(jQuery(this).val());
            gestao_pcmso_get_setores(jQuery(this).val());
        });*/
    });
    /*
    function gestao_pcmso_get_unidades(codigo_cliente){
        let object_id = "#CronogramaGestaoPcmsoCodigoClienteAlocacao";
        bloquearDiv(jQuery(object_id));
        jQuery.get(baseUrl + "grupos_economicos_clientes/por_cliente/" + codigo_cliente + "/" + Math.random(), function(data){
            if(data != null){
                jQuery(object_id).empty();
                jQuery.each(data, function(){
                    if(this.value != '')
                        jQuery(object_id).append($('<option />').val(this.codigo).text(this.descricao));
                });
            }
        }, "json")
        .fail(function() {
            alert("FATAL ERROR - Não foi possivel carregar unidades!");
        })
        .always(function() {
            desbloquearDiv(jQuery(object_id));
        });
    }
    */
    /*
    function gestao_pcmso_get_setores(codigo_cliente){
        let object_id = "#CronogramaGestaoPcmsoCodigoClienteAlocacao";
        bloquearDiv(jQuery(object_id));
        jQuery.get(baseUrl + "setores/por_cliente/" + codigo_cliente + "/" + Math.random(), function(data){
            if(data != null){
                jQuery(object_id).empty();
                jQuery.each(data, function(){
                    if(this.value != '')
                        jQuery(object_id).append($('<option />').val(this.codigo).text(this.descricao));
                });
            }
        }, "json")
            .fail(function() {
                alert("FATAL ERROR - Não foi possivel carregar unidades!");
            })
            .always(function() {
                desbloquearDiv(jQuery(object_id));
            });
    }
    */
</script>