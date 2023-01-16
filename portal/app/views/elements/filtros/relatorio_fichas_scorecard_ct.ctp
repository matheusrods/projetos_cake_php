<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
    	 <?php echo $this->BForm->create('RelatorioFichasScorecard', array('autocomplete' => 'off', 'url' => array('controller' => 'RelatorioFichasScorecard', 'action' => 'index_ct'))) ?>
              <?php echo $this->BForm->hidden('DemonstrativosCT.geraPdf') ?>
            <div class="row-fluid inline"> 
                <?php if (empty($authUsuario['Usuario']['codigo_cliente'])) {
                           echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente','Cliente', 'Cliente','DemonstrativosCT') ;
                        }else {
                           echo  $this->BForm->input('DemonstrativosCT.codigo_cliente', array('readonly' => true,'id'=>'ClienteCodigoCliente', 'class' => 'input-mini', 'placeholder' => 'Código', 'label' => 'Cliente','value'=>$authUsuario['Usuario']['codigo_cliente'], 'type' => 'text'));
                        }    

                    ?>     
                
                     <?php //echo $this->BForm->input("Cliente.razao_social", array('label' => false, 'class' => 'input-xxlarge', 'readonly'=>true,'label' => 'Razão Social')) ?> 
                  <?php echo $this->BForm->input('DemonstrativosCT.codigo_documento',array('label' => 'CPF','type' => 'text','class' => 'input-medium cpf', 'placeholder' => 'CPF')) ?>
           
                  <?php echo $this->BForm->input('DemonstrativosCT.data_baixa_inicio', array('id'=>'data_baixa_inicio','label' => 'Data Baixa Entre ', 'placeholder' => 'Data Inicial', 'type' => 'text', 'class' => 'data input-small')); ?>
                  <?php echo $this->BForm->input('DemonstrativosCT.data_baixa_fim', array('id'=>'data_baixa_fim','label' => '<br/>', 'placeholder' => 'Data Final', 'type' => 'text', 'class' => 'data input-small')); ?>
            </div>        
            <div class="row-fluid inline"> 
                  <?php echo $this->BForm->input('DemonstrativosCT.data_inclusao_inicio', array('id'=>'data_inclusao_inicio','label' => 'Cadastro/Atualização', 'placeholder' => 'Data Inicial', 'type' => 'text', 'class' => 'data input-small')); ?>
                  <?php echo $this->BForm->input('DemonstrativosCT.data_inclusao_fim', array('id'=>'data_inclusao_fim','label' => 'Entre', 'placeholder' => 'Data Final', 'type' => 'text', 'class' => 'data input-small')); ?>
            
                  
           </div>
            <div class="row-fluid inline">
                      <?php echo $this->BForm->input('DemonstrativosCT.codigo_documento_ct_inicial',array('id'=>'codigo_documento_ct_inicial' ,'label' => 'Número da CT Entre' ,'type' => 'text','class' => 'input-medium', 'placeholder' => 'Nº da CT Inicial')) ?> 
                   <?php echo $this->BForm->input('DemonstrativosCT.codigo_documento_ct_fim',array('id'=>'codigo_documento_ct_fim' ,'label' => '<br/>','type' => 'text','class' => 'input-medium', 'placeholder' => 'Nº da CT Final')) ?>
                   <?php echo $this->BForm->input('DemonstrativosCT.ano',array('label' => 'Ano','type' => 'text','class' => 'input-medium', 'placeholder' => 'Ano')) ?>
            </div>
                        <?php echo $this->BForm->submit('Buscar', array('id'=>'btn-filtrar','div' => false, 'class' => 'btn')) ?>
                        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
                       <?php echo $this->BForm->submit('Gerar CT', array('id'=>'btn-gerar','div' => false, 'class' => 'btn btn-primary','name'=>'liberar_ct')) ?>
                       <?php echo $this->BForm->submit('Excluir CT', array('id'=>'btn-excluir','div' => false, 'class' => 'btn btn-danger','name'=>'desabilitar_ct')) ?>  
                        
                    <?php echo $this->BForm->end() ?>
                </div> 
            </div> 

<script>
  $( "#btn-gerar" ).click(function() {
        $('#DemonstrativosCTGeraPdf').val("S");
   }); 
   $( "#btn-filtrar" ).click(function() {
        $('#DemonstrativosCTGeraPdf').val("N");
   }); 
  
    var erros = '';
    
    $('#DemonstrativosCTGeraPdf').val("N");
    if(erros != '') {
      alert(erros);
      return false;
     } else {
      return true;
    }
</script>
<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>
<?php echo $this->addScript($this->Buonny->link_js( array('fichas_scorecard', 'solicitacoes_monitoramento') )) ?>

<?php echo $this->Javascript->codeBlock('
    $(document).ready(function() {
        setup_datepicker();
        setup_mascaras();
        
    });', false);?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){ 
        //setup_codigo_cliente();
        atualizaListaDemostrativoCT(); 
        jQuery("#btn-filtrar").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        }); 
         
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:DemonstrativosCT/element_name:demonstrativosct/" + Math.random())
        });
        
    });', false); 
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>


