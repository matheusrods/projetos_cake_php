
<div class='row-fluid inline parent'>
    <?php echo $this->BForm->input('TCdisCriterioDistribuicao.cdis_nivel', array('class' => 'input-mini', 'label'=> 'Ordem', 'readonly'=>TRUE)); ?>
    <?php echo $this->BForm->input('TCdisCriterioDistribuicao.cdis_max_sm', array('class' => 'input-mini', 'label'=> 'Max SM', 'readonly'=>TRUE)); ?>
    <?php echo $this->BForm->input('TAatuAreaAtuacao.aatu_descricao', array('class' => 'input-medium', 'label'=> 'A. Atuação', 'readonly'=>TRUE)); ?>
</div>    

<div class='row-fluid inline parent'>
    <?php echo $this->BForm->input('TTtraTipoTransporte.ttra_descricao', array('class' => 'input-medium', 'label'=> 'Tipo Transporte', 'readonly'=>TRUE)); ?>
    <?php echo $this->BForm->input('TTecnTecnologia.tecn_descricao', array('class' => 'input-medium', 'label'=> 'Tecnologia', 'readonly'=>TRUE)); ?>
    
</div>    

<div class='row-fluid inline parent'>
    <?php echo $this->BForm->input('TProdProduto.prod_descricao', array('class' => 'input-medium', 'label'=> 'Produto', 'readonly'=>TRUE)); ?>
    <?php echo $this->BForm->input('TCdfvCriterioFaixaValor.cdfv_valor_minimo', array('class' => 'input-small', 'label'=> 'Valor Min', 'readonly'=>TRUE)); ?>
    <?php echo $this->BForm->input('TCdfvCriterioFaixaValor.cdfv_valor_maximo', array('class' => 'input-small', 'label'=> 'Valor Max', 'readonly'=>TRUE)); ?>
</div> 

<div class='row-fluid inline parent'>        
    <?php echo $this->BForm->input('ClienteEmbarcador.codigo', array('class' => 'input-small', 'label'=> 'Cod Embarcador', 'readonly'=>TRUE)); ?>
    <?php echo $this->BForm->input('ClienteEmbarcador.razao_social', array('class' => 'input-xlarge', 'label'=> 'Razão Social', 'readonly'=>TRUE)); ?>
</div>
<div class='row-fluid inline parent'>        
    <?php echo $this->BForm->input('ClienteTransportador.codigo', array('class' => 'input-small', 'label'=> 'Cod Transportador', 'readonly'=>TRUE)); ?>
    <?php echo $this->BForm->input('ClienteTransportador.razao_social', array('class' => 'input-xlarge', 'label'=> 'Razão Social', 'readonly'=>TRUE)); ?>
</div>

<div class="form-actions">
      <?php echo $html->link('Fechar','javascript:void(0)', array('class' => 'btn', 'id'=> "fechar-dialog")) ;?>
</div>
<?php echo $this->Javascript->codeBlock('
    $(function(){
        $("#fechar-dialog").click(function() {
            $( "#dialog-criterio" ).dialog("close");            
        });
    });');
?>