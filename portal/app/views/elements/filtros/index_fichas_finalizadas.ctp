<?php echo $this->Buonny->link_css('fichas_scorecard'); ?>
<div class="well">
  <?php echo $bajax->form('FichaScorecard', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'FichaScorecard', 'element_name' => 'index_fichas_finalizadas'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
      <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente') ?>
      <?php echo $this->BForm->input("Cliente.razao_social", array('label' => false, 'class' => 'input-xxlarge', 'readonly'=>true)) ?>   
      <?php echo $this->BForm->input('FichaScorecard.classificacao', array('label' => false, 'empty' => 'Classificação - Todos','options' => $classificacao_tlc,'class'=>'input-xlarge' ));?>
    </div>        
    <div class="row-fluid inline">
		<?php echo $this->BForm->input('FichaScorecard.codigo_documento',array('label' => false,'type' => 'text','class' => 'input-medium formata-cpf', 'placeholder' => 'CPF')) ?>     
    <?php echo $this->BForm->input('FichaScorecard.numero_liberacao',array('label' => false,'type' => 'text','class' => 'input-medium', 'placeholder' => 'N° Liberação')) ?>
    <?php echo $this->BForm->input('FichaScorecard.usuario',array('label' => false,'type' => 'text','class' => 'input-medium', 'placeholder' => 'Usuário')) ?>
    <?php echo $this->BForm->input('FichaScorecard.cliente_vip', array(
          'type' => 'select',
          'label' => false,
          'multiple' => 'checkbox',
          'checked' => (isset($this->data['Cliente']['cliente_vip']) && $this->data['Cliente']['cliente_vip'] == 1) ? true : false,
          'options' => array(1 => 'Cliente Vip')
        ));
      ?>
      <?php echo $this->BForm->input('apenas_renovadas', array( 
          'type' => 'select',
          'label' => false,
          'multiple' => 'checkbox',
          'checked' => (isset($this->data['Cliente']['renovadas']) && $this->data['Cliente']['renovadas'] == 1) ? true : false,
          'options' => array(1 => 'Renovação Automática')
        ));
      ?>
    </div>
    <div class="row-fluid inline">
 <?php echo $this->Buonny->input_periodo($this,'FichaScorecard') ?>
</div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn', 'id' => 'filtrar')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->addScript($this->Buonny->link_js( array('fichas_scorecard', 'solicitacoes_monitoramento') )) ?>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
    setup_codigo_cliente();
    
    var div = jQuery("div.lista");
    bloquearDiv(div);
    div.load(baseUrl + "/fichas_scorecard/listagem_finalizadas/" + Math.random());
    $("#limpar-filtro").click(function(){
			bloquearDiv($(".form-procurar"));
			$(".form-procurar").load(baseUrl + "/filtros/limpar/model:FichaScorecard/element_name:index_fichas_finalizadas/" + Math.random())
		});
	});', false);?>