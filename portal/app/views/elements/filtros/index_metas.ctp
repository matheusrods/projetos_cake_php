<div class='well'>
  	<?php echo $bajax->form('PosMetas', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PosMetas', 'element_name' => 'index_metas'), 'divupdate' => '.form-procurar')) ?>

    <div class="row-fluid inline">
        <?php //echo $this->Buonny->input_grupo_economico_setor($this, 'PosMetas', $setores);?>
        <?php //echo $this->BForm->input('codigo_matriz', array('label' => "Código Matriz", 'class' => 'input-medium', 'maxlength' => 10, 'placeholder' => 'Código matriz'));?>

        <?php

        if ($is_admin) {
            echo $this->Buonny->input_grupo_economico3($this, 'PosMetas', $unidades2, $setores2);
            echo "</div>";
        } else {

            if (isset($_SESSION['Auth']['Usuario']['multicliente']) && !empty($_SESSION['Auth']['Usuario']['multicliente'])) {
                echo $this->Buonny->input_grupo_economico3($this, 'PosMetas', $unidades2, $setores2);
                echo "</div>";
            } else {
                echo $this->BForm->input('codigo_matriz', array('type' => 'hidden', 'class' => 'input-mini',  'label' => false, 'readonly' => 'readonly', 'value' => "{$_SESSION['Auth']['Usuario']['codigo_cliente']}"));
                echo $this->BForm->input('nome_fantasia', array('type' => 'text', 'class' => 'input-xlarge',  'label' => 'Cliente', 'readonly' => 'readonly', 'value' => "{$nome_fantasia}"));

            ?>
                </div>

                <div class="row-fluid inline">
                    <?php echo $this->BForm->input('codigo_cliente', array('label' => "Unidades", 'class' => 'input-xlarge','options' => $unidades2, 'empty' => 'Selecione a unidade'));?>
                    <?php echo $this->BForm->input('codigo_setor', array('label' => "Setores", 'class' => 'input-xlarge','options' => $setores2, 'empty' => 'Selecione o setor'));?>
                </div>
        <?php
                }
            }
        ?>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('codigo_cliente_opco', array('label' => "Opco", 'class' => 'input-xlarge','options' => $combo_opco, 'empty' => 'Selecione a Opco'));?>
        <?php echo $this->BForm->input('codigo_cliente_bu', array('label' => "Business Unit", 'class' => 'input-xlarge','options' => $combo_bu, 'empty' => 'Selecione a Business Unit'));?>
    </div>

  	<?php echo $this->BForm->submit('Buscar', array("id" => "buscarMetas", 'div' => false, 'class' => 'btn')) ?>
  	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  	<?php echo $this->BForm->end() ?>

<?php
    echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){

        setup_datepicker();
        listagem();

		function listagem(){
            var div = jQuery(".lista");
            bloquearDiv(div);
            div.load(baseUrl + "swt/listagem_metas/" + Math.random());
        }

		jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PosMetas/element_name:index_metas/" + Math.random())
       
        });

 
	});', false);
?>

</div>

<script>
    $(function(){
        // $("#buscarMetas").on("click", function(){
        //     $("#PosMetasFiltrarForm").submit();
        //     return true;
        // });

        $("#PosMetasCodigoMatriz").on('change',function(e){
            const codigo_matriz = $(this).val();

            const input_opco = $('#PosMetasCodigoClienteOpco');
            const input_bu = $('#PosMetasCodigoClienteBu');

            input_opco.html('');
            input_opco.append($('<option />').val('').text('Selecione a opco'));

            input_bu.html('');
            input_bu.append($('<option />').val('').text('Selecione a Business Unit'));

            carregaClientes(codigo_matriz);
            carregaSetores2(codigo_matriz);
        });

        $("#PosMetasCodigoCliente").on("change",function(e){
            const codigo_cliente = $(this).val();

            carregaOpco(codigo_cliente);
            carregaBu(codigo_cliente);
        });

        carregaClientes = function(codigo_matriz) {
            var input = $('#PosMetasCodigoCliente');

            input.html('');
            input.append($('<option />').val('').text('Selecione a unidade'));

            bloquearDiv(input.parent());
            $.ajax({
                'url': baseUrl + 'swt/combo_clientes_ajax/' + codigo_matriz + '/' + Math.random(),
                'dataType': 'json',
                'success': function(result) {
                    if (result != null) {
                        $.each(result, function(i, r) {
                            input.append($('<option />').val(r['Cliente']['codigo']).text(r['Cliente']['nome_fantasia']));
                        });
                    }
                    input.parent().unblock();
                }
            });
        }

        carregaSetores2 = function(codigo_cliente) {
            var input = $('#PosMetasCodigoSetor');

            input.html('');
            input.append($('<option />').val('').text('Selecione o Setor'));

            bloquearDiv(input.parent());
            $.ajax({
                'url': baseUrl + 'swt/combo_setores_ajax/' + codigo_cliente + '/' + Math.random(),
                'dataType': 'json',
                'success': function(result) {
                    if (result != null) {
                        $.each(result, function(i, r) {
                            input.append($('<option />').val(result[i]['Setor']['codigo']).text(result[i]['Setor']['descricao']));
                        });
                    }
                    input.parent().unblock();
                }
            });
        }

        carregaOpco = function(codigo_cliente) {
            var input = $('#PosMetasCodigoClienteOpco');

            input.html('');
            input.append($('<option />').val('').text('Selecione a opco'));

            bloquearDiv(input.parent());
            $.ajax({
                'url': baseUrl + 'swt/combo_opco_ajax/' + codigo_cliente + '/' + Math.random(),
                'dataType': 'json',
                'success': function(result) {
                    if (result != null) {
                        $.each(result, function(i, r) {
                            input.append($('<option />').val(r['ClienteOpco']['codigo']).text(r['ClienteOpco']['descricao']));
                        });
                    }
                    input.parent().unblock();
                }
            });
        }

        carregaBu = function(codigo_cliente) {
            var input = $('#PosMetasCodigoClienteBu');

            input.html('');
            input.append($('<option />').val('').text('Selecione a Business Unit'));

            bloquearDiv(input.parent());
            $.ajax({
                'url': baseUrl + 'swt/combo_bu_ajax/' + codigo_cliente + '/' + Math.random(),
                'dataType': 'json',
                'success': function(result) {
                    if (result != null) {
                        $.each(result, function(i, r) {
                            input.append($('<option />').val(r['ClienteBu']['codigo']).text(r['ClienteBu']['descricao']));
                        });
                    }
                    input.parent().unblock();
                }
            });
        }
    });
</script>
