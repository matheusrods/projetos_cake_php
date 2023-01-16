<div class='well'>
	<?php echo $bajax->form('Medico', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Medico', 'element_name' => 'corpo_clinico'), 'divupdate' => '.form-procurar', 'callback' => 'atualizaListaCorpoClinico')) ?>
    	<div class="row-fluid inline">
            <div class="row-fluid inline">
                <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'Medico'); ?>
            </div>
            <div>
                <?php echo $this->BForm->input('codigo_cliente_alocacao', array('label' => 'Unidades', 'class' => 'input-xlarge', 'options' => $data_lista_unidades, 'empty' => 'Todos')) ?>
                <?php echo $this->BForm->input('codigo_fornecedor', array('label' => 'Credenciado', 'class' => 'input-xlarge', 'options' => $data_lista_fornecedores, 'empty' => 'Todos')) ?>
            </div>
    	</div> 
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end() ?>
</div>
<div class="well">
    <div class='actionbar-right'>
        <?php
            $codigo_cliente = (!empty($this->data['Medico']['codigo_cliente']) ? $this->data['Medico']['codigo_cliente'] : 0);
            $codigo_cliente = (!empty($this->data['Medico']['codigo_cliente_alocacao']) ? $this->data['Medico']['codigo_cliente_alocacao'] : $codigo_cliente);
            $codigo_fornecedor = (!empty($this->data['Medico']['codigo_fornecedor']) ? $this->data['Medico']['codigo_fornecedor'] : 0);
        ?>
        <?php echo $this->Html->link('<i class="icon-print"></i>', array( 'controller' => 'medicos', 'action' => 'corpo_clinico_imprimir', $codigo_cliente, $codigo_fornecedor), array('escape' => false, 'title' =>'Imprimir Corpo Clínico', 'target' => '_blank'));?>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Medico/element_name:corpo_clinico/" + Math.random());
        });

        jQuery("#MedicoCodigoClienteAlocacao").on('change', function(){
           let codigo = jQuery(this).val();
           if(codigo != ''){
               jQuery("#MedicoCodigoFornecedor").empty().append("<option value=\"\">Carregando, aguarde..</option>");
                jQuery.get(baseUrl + "/fornecedores/ajax_get_por_codigo_cliente/" + codigo + "/" + Math.random(), function(data){
                    jQuery("#MedicoCodigoFornecedor").empty().append('<option value="">Selecione..</option>');
                    jQuery.each(data, function(id, razao_social){
                        jQuery("#MedicoCodigoFornecedor").append('<option value="'+id+'">'+razao_social+'</option>');
                    });
                })
                .fail(function(){
                    swal("ERROR","Não foi possivel buscar os fornecedores!", "error");
                })
           }
        });

    });
</script>