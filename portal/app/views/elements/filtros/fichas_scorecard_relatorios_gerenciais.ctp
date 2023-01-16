<div class="well">
  <?php echo $bajax->form('FichaScorecard', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'FichaScorecard', 'element_name' => 'fichas_scorecard_relatorios_gerenciais'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
      <?php echo $this->BForm->create('FichaScorecard', array('autocomplete' => 'off', 'url' => array('controller' => 'fichas_scorecard', 'action' => 'relatorios_gerenciais_scorecard'))) ?>
      <?php echo $this->BForm->input('tipo_busca', array( 'type' => 'hidden', 'value' => $tipo_busca, 'label' => false)) ?>
      <?php if( $tipo_busca == 3 ): ?>
      <?php echo $this->BForm->input('data', array('class' => 'data input-small', 'placeholder' => 'Data', 'label' => 'Data')) ?>
      <?php echo $this->BForm->input('hora_inicio', array('label' => 'Hora Início', 'class' => 'hora input-mini')) ?>
      <?php echo $this->BForm->input('hora_termino', array('label' => 'Hora Fim', 'class' => 'hora input-mini')) ?>
      <?php echo $this->BForm->input('tipo_origem', array('class' => 'input-small', 'options' => array(0 => 'Todos',1 => 'Web', 2 => 'Interno'), 'label' => 'Origem')) ?>
      <?php else: ?>
        <?php echo $this->BForm->input('tipo_mes', array('class' => 'input-medium', 'options' => $meses, 'label' => 'Mês')) ?>
        <?php echo $this->BForm->input('ano', array('class' => 'input-medium', 'options' => $anos, 'label' => 'Ano')) ?>
        <?php echo $this->BForm->input('tipo_profissional', array('class' => 'input-medium', 'options' => $tipo_profissional ,'empty'=>'Todos', 'label' => 'Tipo Profissional')) ?>
        <?php if($tipo_busca == 2): ?>
        <?php echo $this->BForm->input('tipo_origem', array('class' => 'input-small', 'options' => array(0 => 'Todos',1 => 'Web', 2 => 'Interno'), 'label' => 'Origem')) ?>
        <?php endif; ?>
        <?php echo $this->BForm->input('usuario',array('label' => 'Usuario','type' => 'text','class' => 'input-medium', 'placeholder' => 'Usuario')) ?>        
      <?php endif; ?>
    </div>        
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
    setup_datepicker();
    setup_time();
    var div = jQuery("div.lista");
    bloquearDiv(div);
    div.load(baseUrl + "/fichas_scorecard/relatorios_gerenciais_scorecard/" + Math.random());		
    $("#limpar-filtro").click(function(){
      bloquearDiv($(".form-procurar"));
      $(".form-procurar").load(baseUrl + "/filtros/limpar/model:FichaScorecard/element_name:fichas_scorecard_relatorios_gerenciais/'.$tipo_busca.'/" + Math.random())
      $("#limpar-filtro").click(function(){
        $(".form-procurar :input").not(":button, :submit, :reset, :hidden").val("");
        $(".form-procurar form").submit();
      });   
		});
});', false);?>  