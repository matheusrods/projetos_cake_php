<div class="row-fluid inline">
    <?php echo $this->Buonny->input_periodo($this,'PrestadoresPostgres','data_envio_prestador_inicial','data_envio_prestador_final', true) ?>
    <?php echo $this->Buonny->input_codigo_prestador($this, 'codigo_prestador', 'Prestador', 'Prestador', 'PrestadoresPostgres'); ?>
    <?php echo $this->BForm->input('nome_prestador', array('label' => 'Nome', 'class' => 'input-large', 'readonly' => 'readonly')); ?>
</div>
<div class="row-fluid inline">
    <?php echo $this->Buonny->input_embarcador_transportador($this, $embarcadores, $transportadores, 'codigo_cliente', 'Cliente', false, 'PrestadoresPostgres'); ?>
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('codigo_sm',         array('placeholder' => 'SM', 'label' => 'SM', 'class' => 'input-small just-number' )); ?>
    <?php echo $this->BForm->input('placa',             array('placeholder' => 'Placa', 'label' => 'Placa', 'class' => 'input-small placa-veiculo')); ?>
    <?php echo $this->BForm->input('codigo_tecnologia', array('empty' => 'Tecnologia', 'label' => 'Tecnologia' ,'class' => 'input-medium', 'options' => $tecnologia)) ?>
    <?php if(isset($exibir_valores) && $exibir_valores): ?>
        <?php echo $this->BForm->input('somente_valores', array('empty' => 'Todos', 'class' => 'input-small', 'label' => 'Somente com valores', 'options' => $valores)); ?>
    <?php endif; ?>
</div>
<?php echo $this->Javascript->codeBlock('
     function busca_nome_prestador(prestador){
        if(prestador.val() != "") {
            $("#PrestadoresPostgresNomePrestador").addClass("ui-autocomplete-loading");
        }else {
            $("#PrestadoresPostgresNomePrestador").val("");                    
            $("#PrestadoresPostgresNomePrestador").removeClass("ui-autocomplete-loading");
        }
        $.ajax({
            type: "POST",
            url: "/portal/prestadores/busca_por_codigo",
            dataType : "json",
            data:{"codigo": prestador.val()},
            success : function(data) {                                           
               $("#PrestadoresPostgresNomePrestador").val(data["Prestador"]["nome"]);
            },
            error : function(data){
                if(prestador.val() != "") {
                    prestador.val("");
                    $("#PrestadoresPostgresNomePrestador").val("");                    
                    alert("Não foi possível incluir prestador. Tente novamente");
                }
            },
            complete : function(){
                $("#PrestadoresPostgresNomePrestador").removeClass("ui-autocomplete-loading");
            }
        }); 
    }

    $(document).ready(function(){
	  	busca_nome_prestador($("#PrestadoresPostgresCodigoPrestador"));
        $("#PrestadoresPostgresCodigoPrestador").change(function() {
            busca_nome_prestador($(this));
        });
        $("#modal_dialog").dialog(this).dialog("ui-id-2");
		setup_mascaras();

	});', false);
?>