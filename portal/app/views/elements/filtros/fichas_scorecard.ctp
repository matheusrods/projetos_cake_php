<div class="well">
  <?php echo $bajax->form('FichaScorecard', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'FichaScorecard', 'element_name' => 'fichas_scorecard'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">      
      <?php //echo $this->BForm->input('codigo_ficha_scorecard',array('label' => false,'type' => 'text','disabled'=>'disabled' ,'class' => 'input-medium', 'placeholder' => 'Código Ficha')) ?>
      <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false,'FichaScorecard') ?>  
      <?php echo $this->BForm->input("Cliente.razao_social", array('label' => false, 'class' => 'input-xxlarge', 'readonly'=>true)) ?>    
      <?php echo $this->BForm->input('codigo_tipo_profissional',array('label' => false,'options' => $tipos_profissional,'class'=>'input-medium' ));?>
      <?php echo $this->BForm->hidden('pagina', array('value'=>1)); ?>
      <?php echo $this->BForm->input('codigo_documento',array('label' => false,'type' => 'text','class' => 'input-medium', 'placeholder' => 'CPF')) ?>
      <?php echo $this->BForm->input('tipos_prazo',array('label' => false, 'empty' => 'Tipo de Prazo','options' => array('Fichas Fora do Prazo','Fichas Dentro do Prazo'),'class'=>'input-large' ));?>
      <?php echo $this->BForm->input('origem_ficha',array('label' => false, 'empty' => 'Origem da Ficha','options' => array('W'=>'Web','E'=>'Interno'),'class'=>'input-medium' ));?>
      <?php echo $this->BForm->input('filtro_status_ficha',array('label' => false, 'empty' => 'Status da Ficha','options' => $status_ficha,'class'=>'input-medium' ));?>

      <?php echo $this->BForm->input('cliente_vip', array(
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
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->addScript($this->Buonny->link_js( array('fichas_scorecard', 'solicitacoes_monitoramento') )) ?>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		atualizaListaFichasScorecard();
    setup_codigo_cliente();
    $("#limpar-filtro").click(function(){
      bloquearDiv($(".form-procurar"));
      $(".form-procurar").load(baseUrl + "/filtros/limpar/model:FichaScorecard/element_name:fichas_scorecard/" + Math.random())
        
         $("#limpar-filtro").click(function(){
             $(".form-procurar :input").not(":button, :submit, :reset, :hidden").val("");
             $(".form-procurar form").submit();
          });   
		});
	});', false);?>
  