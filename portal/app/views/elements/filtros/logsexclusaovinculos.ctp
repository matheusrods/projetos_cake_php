<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $bajax->form('LogAtendimento', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'LogAtendimento', 'element_name' => 'logsexclusaovinculos'), 'divupdate' => '.form-procurar')) ?>
        <?php echo $this->BForm->hidden('campoImpressao',array('value'=>'tela','id'=>'campoImpressao')); ?>
            <div class="row-fluid inline">
                <?php echo empty($authUsuario['Usuario']['codigo_cliente']) ? $this->Buonny->input_codigo_cliente($this) : $this->BForm->input('codigo', array('readonly' => true,'id'=>'ClienteCodigoCliente', 'class' => 'input-mini', 'placeholder' => 'Código', 'label' => false,'value'=>$authUsuario['Usuario']['codigo_cliente'], 'type' => 'text')); ?>
                <?php if ($authUsuario['Usuario']['codigo_cliente']=='') { ?>
                <?php echo $this->BForm->input('razao_social', array('class' => 'input-large', 'placeholder' => 'Nome','visible'=>'false', 'label' => false)) ;
                     }?>
               
             </div>
             <div class="row-fluid inline">
             <!-- Retirado o tipo de profissional, pois carreteiro não possuí vinculo, regra passada pelo Camarinho -->
             <?php //echo $this->BForm->input('codigo_profissional_tipo',array('label' => false,'label'=>'Categoria de Profissional','empty' => 'Selecione uma Categoria','options' => $tipos_profissional,'class'=>'input-large' ));?>
    
                <?php echo $this->BForm->input('CPF', array('class' => 'input-mediun cpf', 'label'=>'CPF')) ?>
               <?php echo $this->BForm->input('usuario', array('class' => 'input-mediun', 'label'=>'Usuário')) ?>
                <?php echo $this->BForm->input('data_inicial', array('id'=>'data_inicial' ,'label' => false,'label'=>'Data Inicial', 'placeholder' => 'Data Inicial', 'type' => 'text', 'class' => 'data input-small')); ?>
                <?php echo $this->BForm->input('data_final', array('id'=>'data_final','label' => false,'label'=>'Data Final','placeholder' => 'Data Final', 'type' => 'text', 'class' => 'data input-small')); ?>
             </div>


            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'id'=>'btn-filtrar','class' => 'btn')) ?>
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
           
             <?php echo $html->link('Impressão', 'javascript:self.print()', array('id' => 'impressao', 'class' => 'btn','escape' => false)) ;?>
        
        <?php echo $this->BForm->end() ?>
    </div> 
</div> 

<script>  
$("#btn-filtrar").click(function(){
    var erros = '';
    if(new Date($('#data_final').val()) < new Date($("#data_inicial").val())) {
        erros += "Data Final é maior que Data Inicial\n";
    }
    if(($('#data_final').val() == '' || $('#data_final').val() == undefined) || ($('#data_inicial').val() == undefined || $('#data_inicial').val() == '')) {
        erros += "Data Inicial e Data Final são obrigatórios\n";

    }

    
    if(erros != '') {
        alert(erros);
        return false;
    } else {
        return true;
    }

});
</script>


  <?php echo $this->Javascript->codeBlock('
       jQuery(document).ready(function(){
           setup_datepicker();   
        });', false); 
    ?>  
<?php echo $this->Javascript->codeBlock('

    jQuery(document).ready(function(){ 
        setup_mascaras();
        atualizaListaLogsExclusaoVinculo();
        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
     jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Logatendimento/element_name:logsatendimento/" + Math.random()) 
        });
        
        
    });
      
    
', false); 
    

?>



    <?php echo $this->Javascript->codeBlock('
        $(document).on("click", ".btn-modal", function(e){
            e.preventDefault();
            var link  = $(this).prop("href");
            var title = $(this).attr("title");
            open_dialog(link, title, 640);
        });
        
        

    });', false);
?>
<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>

<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>
   
           
