<div class='well'>
    <?php echo $bajax->form('NotaFiscalServico', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'NotaFiscalServico', 'element_name' => 'relatorio_glosas'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">            
            <?php echo $this->Buonny->input_codigo_fornecedor($this, 'codigo_fornecedor', 'Código','Fornecedor','NotaFiscalServico');?>
        </div>
        <div class="row-fluid inline"> 
            <?php echo $this->BForm->input('numero_nfs', array('label' => 'Número Nfs', 'placeholder' => '','class' => 'input-mini numeric just-number', 'title' => 'Numero Nfs')) ?>
            <?php echo $this->BForm->input('status', array('label' => 'Status', 'class' => 'input-small','empty' => 'Todos', 'default' => '','options' => $status_glosas)); ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        
        atualizaListaNotaFiscalServico();
        setup_mascaras();
        setup_datepicker(); 

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:NotaFiscalServico/element_name:relatorio_glosas/" + Math.random())
        });


        function atualizaListaNotaFiscalServico() {
            //verifica se existe algum codigo para pesquisar
            if($("#NotaFiscalServicoCodigoFornecedor").val() != "") {
                var div = jQuery("div.lista");
                bloquearDiv(div);
                div.load(baseUrl + "notas_fiscais_servico/relatorio_glosas_listagem/"+ Math.random());
            }
        }

    });    
    
', false);

?> 