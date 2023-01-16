<div class="well">  
  <div id='filtros'>
    <?php echo $bajax->form('FichaScorecard', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'FichaScorecard', 'element_name' => 'fichas_scorecard_relatorio_vinculo_excluido'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">      
      <?php echo $this->Buonny->input_codigo_cliente_base($this, 'codigo_cliente', 'Cliente', false, 'FichaScorecard' );?>
      <?php echo $this->BForm->input("Cliente.razao_social", array('label' => false, 'class' => 'input-xxlarge', 'readonly'=>true)) ?>  
    </div> 
    <div class="row-fluid inline">
      <?php echo $this->BForm->input('codigo_documento',array('label' => false,'type' => 'text','class' => 'input-medium cpf', 'placeholder' => 'CPF')) ?>
      <?php echo $this->BForm->input('data_alteracao_inicial', array('label' => false, 'placeholder' => 'Data Inicial', 'type' => 'text', 'class' => 'data input-small')); ?>
      <?php echo $this->BForm->input('data_alteracao_final', array('label' => false, 'placeholder' => 'Data Final', 'type' => 'text', 'class' => 'data input-small')); ?>
    </div>    
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>  
</div>
<?php echo $this->addScript($this->Buonny->link_js( array('fichas_scorecard', 'solicitacoes_monitoramento') )) ?>
<?php echo $this->Javascript->codeBlock('
jQuery(document).ready(function(){
    $(".btn").click(function(){
      var div = jQuery("div.lista");
      bloquearDiv(div);
      div.load(baseUrl + "fichas_scorecard/listagem_relatorio_vinculo_excluido/" + Math.random());
    });        
    setup_mascaras();
    setup_codigo_cliente();
    setup_datepicker();
    jQuery("#limpar-filtro").click(function(){
        bloquearDiv(jQuery(".form-procurar"));           
        jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:FichaScorecard/element_name:fichas_scorecard_relatorio_vinculo_excluido/" + Math.random())
    });
});', false);?>