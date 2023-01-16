<p>Para gerar os arquivos de negativação primerio selecione o tipo e depois gere o PROREDE e o CONVEM.</p>
<p><strong>Total a Negativar: </strong><?php echo $total_inclusoes; ?></p>
<p><strong>Total a Baixar: </strong><?php echo $total_exclusoes; ?></p>
<div class="form-actions">
    <?php echo $this->BForm->create('Negativacao', array('url' => array('controller' => 'negativacoes', 'action' => 'gerar_arquivos'))); ?>
        <?php echo $this->BForm->input('codigo_operacao', array('label' => array('class' => 'radio inline'), 'div' => false, 'legend' => false, 'options' => array('I' => 'Inclusão', 'E' => 'Exclusão'), 'type' => 'radio', 'value' => 'I')) ?>
        <?php echo $this->BForm->hidden('arquivo', array('value' => '')) ?>
        <?php echo $html->link('Gerar PROREDE', 'javascript:void(0)', array('id' => 'prorede', 'class' => 'prorede btn btn-primary')); ?>
        <?php echo $html->link('Gerar CONVEM', 'javascript:void(0)', array('id' => 'convem', 'class' => 'convem btn btn-primary')); ?>
    <?php echo $this->BForm->end() ?>
</div>
<script type="text/javascript">
    $('.prorede').click(function() {
        $('#NegativacaoArquivo').val('prorede');
        $('#NegativacaoIndexForm').submit();
    })
    $('.convem').click(function() {
        $('#NegativacaoArquivo').val('convem');
        $('#NegativacaoIndexForm').submit();
    })
</script>