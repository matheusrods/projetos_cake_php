<div class='well'>
    <?php echo $bajax->form('AuditoriaExames', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'AuditoriaExames', 'element_name' => 'relatorio_fat_cred'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">            
            <?php echo $this->Buonny->input_codigo_fornecedor($this, 'codigo_fornecedor', 'Código','Fornecedor','AuditoriaExames');?>
        </div>
        <div class="row-fluid inline"> 
            <?php echo $this->BForm->input('mes', array('options' => $meses, 'class' => 'input-medium', 'label' => 'Mês')); ?>
            <?php echo $this->BForm->input('ano', array('label' => 'Ano', 'placeholder' => 'Ano','class' => 'input-mini numeric just-number', 'title' => 'Ano')) ?>

            <?php echo $this->BForm->input('status', array('label' => 'Status', 'class' => 'input-small', 'default' => '','options' => $status)); ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        
        atualizaListaAuditoriaExames();
        setup_mascaras();
        setup_datepicker(); 

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:AuditoriaExames/element_name:relatorio_fat_cred/" + Math.random())
        });


        function atualizaListaAuditoriaExames() {
            //verifica se existe algum codigo para pesquisar
            if($("#AuditoriaExamesCodigoFornecedor").val() != "") {
                var div = jQuery("div.lista");
                bloquearDiv(div);
                div.load(baseUrl + "fornecedores/relatorio_fat_cred_listagem/"+ Math.random());
            }
        }

    });    
    
', false);

?> 