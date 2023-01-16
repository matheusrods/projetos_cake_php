<div class='well'>
  <div class="row-fluid inline">
      <?php echo $this->BForm->hidden('codigo'); ?>
      <?php echo $this->BForm->input('descricao', array('class' => 'input-xxlarge', 'label' => 'Pergunta', 'required'=>true)); ?>
      <?php echo $this->BForm->input('codigo_tipo_campo', array('class' => 'input-medium', 'label' => 'Tipo Campo', 'options'=>$tipos_campo, 'empty'=>'--', 'required'=>true)); ?>
  </div>
  <div class="row-fluid inline">
      <?php echo $this->BForm->hidden('casas_decimais', array('value' => 2)); ?>
      <?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => 'Ativo', 'options'=>$arraySimNao, 'disabled'=>true)); ?>
      <?php echo $this->BForm->input('obrigatorio', array('class' => 'input-small', 'label' => 'Obrigatório', 'options'=>$arraySimNao, 'required'=>true)); ?>
      <div class='row-fluid inline valores'>
      <?php echo $this->BForm->input("valor_min", array('class' => "input-small $classe_valor", 'label' => 'Valor Mínimo')); ?>
      <?php echo $this->BForm->input("valor_max", array('class' => "input-small $classe_valor", 'label' => 'Valor Máximo')); ?>
      </div>
      <div class='row-fluid inline tamanho'>
      <?php echo $this->BForm->input('tamanho_maximo', array('class' => 'input-small just-number', 'label' => 'Tamanho Máximo')); ?>
      </div>
  </div>
</div>
<div id='divRespostas'>
  <?php echo $this->element('proposta_perguntas/lista_respostas', array(
    'titulo'=>'Respostas', 
    'respostas'=>isset($this->data['PropostaPerguntaResposta']) ? $this->data['PropostaPerguntaResposta'] : array(), 
    'index'=>'proposta_pergunta_resposta', 
    'model'=>'PropostaPerguntaResposta'
  ))?>
</div>
<div class='well'>
  <div class='row-fluid inline' id="checkboxes_produtos">

    <span class="label label-info">Produtos</span>
    <span class='pull-right'>
      <?= $html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("checkboxes_produtos")')) ?>
      <?= $html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("checkboxes_produtos")')) ?>
    </span>
    <?php echo $this->BForm->input('PropostaPerguntaProduto.codigo_produto', 
      array('label'=>false, 
      'options'=>$produtos, 
      'multiple'=>'checkbox', 
      'class' => 'checkbox inline input-xlarge')); ?>
  </div>
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>

</div>    
    
  <?php echo $this->Javascript->codeBlock('
    function exibe_parametros(codigo_tipo_campo) {
      if (codigo_tipo_campo==3) {
        $(".tamanho").show();
      } else {
        $(".tamanho").hide();
      }
      if (codigo_tipo_campo==1 || codigo_tipo_campo==2) {
        $(".valores").show();
      } else {
        $(".valores").hide();
      }
      if (codigo_tipo_campo==4 || codigo_tipo_campo==5) {
        $("#divRespostas").show();
      } else {
        $("#divRespostas").hide();
      }
      if (codigo_tipo_campo==2) {
        if ($("#PropostaPerguntaValorMin").hasClass("just-number")) {
          $("#PropostaPerguntaValorMin").removeClass("just-number");
          $("#PropostaPerguntaValorMax").removeClass("just-number");
        }
        if (!($("#PropostaPerguntaValorMin").hasClass("moeda")) ) {
          $("#PropostaPerguntaValorMin").addClass("moeda");
          $("#PropostaPerguntaValorMax").addClass("moeda");
        }
      } else {
        if ($("#PropostaPerguntaValorMin").hasClass("moeda")) {
          $("#PropostaPerguntaValorMin").removeClass("moeda");
          $("#PropostaPerguntaValorMax").removeClass("moeda");
        }
        if (!($("#PropostaPerguntaValorMin").hasClass("just-number")) ) {
          $("#PropostaPerguntaValorMin").addClass("just-number");
          $("#PropostaPerguntaValorMax").addClass("just-number");
        }
      }
      setup_mascaras();
    }

    jQuery(document).ready(function(){
       
       $("#PropostaPerguntaCodigoTipoCampo").change(function () {
          exibe_parametros(this.value);
       });

       setup_mascaras();
       exibe_parametros('.(isset($this->data['PropostaPergunta']['codigo_tipo_campo']) ? $this->data['PropostaPergunta']['codigo_tipo_campo'] : 'null').');
    });', false); 
    ?>    
