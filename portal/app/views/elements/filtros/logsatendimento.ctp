<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $bajax->form('LogAtendimento', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'LogAtendimento', 'element_name' => 'logsatendimento'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
        <?php echo $this->BForm->input('usuario', array('class' => 'input-medium', 'label'=>'Usuário')) ?>
        <?php echo $this->BForm->input('codigo_profissional_tipo', array('label' => false,'label'=>'Categoria de Profissional','empty' => 'Selecione uma Categoria','options' => $tipos_profissional,'class'=>'input-large' ));?>
        <?php echo $this->BForm->input('codigo_tipo_operacao', array('label' => false,'label'=>'Tipo de Operação', 'empty' => 'Selecione um Tipo de Operação','options' => $tipos_operacoes,'class'=>'input-xxlarge' ));?>
        </div>
        <div class="row-fluid inline">
        <?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium cpf', 'label'=>'CPF')) ?>
        <?php echo $this->BForm->input('placa', array('class' => 'input-small placa-veiculo', 'label'=>'Placa')) ?>
        <?php echo $this->BForm->input('data_inicial', array('id'=>'data_inicial' ,'label' => false,'label'=>'Data Inicial', 'placeholder' => 'Data Inicial', 'type' => 'text', 'class' => 'data input-small')); ?>
        <?php echo $this->BForm->input('data_final', array('id'=>'data_final','label' => false,'label'=>'Data Final','placeholder' => 'Data Final', 'type' => 'text', 'class' => 'data input-small')); ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'id'=>'btn-filtrar','class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div> 
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
       setup_datepicker();   
        setup_mascaras();
        atualizaListaLogsAtendimento();
        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:LogAtendimento/element_name:logsatendimento/" + Math.random()) 
        });
    });', false);?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>