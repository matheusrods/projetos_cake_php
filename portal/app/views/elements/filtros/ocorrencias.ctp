<?php $filtrado = (isset($this->data['Ocorrencia']) && $this->data['Ocorrencia'] != null); ?>
<div class='well'>
    <h5><?= $this->Html->link(($filtrado ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros-salvos'>
        <?php foreach ($filtros_salvos as $filtro_salvo): ?>
            <div class='filtro-salvo'>
                <?= $html->link($filtro_salvo['Filtro']['nome_filtro'], array('controller' => 'filtros', 'action' => 'recuperar_filtro', $filtro_salvo['Filtro']['codigo'])) ?>
                <?= $html->link('X', array('controller' => 'filtros', 'action' => 'apagar_filtro', $filtro_salvo['Filtro']['codigo']), null, "Deseja realmente excluir o filtro {$filtro_salvo['Filtro']['nome_filtro']}?") ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div id='filtros'>
        <?php echo $bajax->form('Ocorrencia', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Ocorrencia', 'element_name' => 'ocorrencias'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo_sm', array('maxlength' => 10, 'label' => false, 'placeholder' => 'SM', 'class' => 'input-small')); ?>
            <?php echo $this->BForm->input('placa', array('size' => 7, 'label' => false, 'placeholder' => 'Placa', 'class' => 'input-small placa-veiculo')); ?>
            <div style="float:left">
                <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
                <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
                <label class="checkbox inline"><?php echo $this->BForm->input('Filtro.salvar_filtro', array('label' => false, 'type' => 'checkbox', 'div' => array('class' => 'checkbox-horizontal-alinhado-input'))); ?>Salvar Seleção</label>
            </div>
            <?php echo $this->BForm->input('Filtro.nome_filtro', array('label' => false, 'placeholder' => 'Nome do filtro', 'class' => 'input-medium', 'div' => array('style' => 'display:none'))); ?>
        </div>        
        <div class="row-fluid inline">
            <span class="label label-info">Status</span>
            <span class='pull-right'>
                <?= $html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("status")')) ?>
                <?= $html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("status")')) ?>
            </span>
            <div id='status'>
                <?php echo $this->BForm->input('codigo_status_ocorrencia', array('label' => false, 'class' => 'checkbox inline input-xlarge','options' => $tipoStatusSVizualizacao, 'multiple' => 'checkbox')); ?>
            </div>
        </div>
        <div class="row-fluid inline">
            <span class="label label-info">Prioridade</span>
            <div id='prioridade'>
                <?php echo $this->BForm->input('codigo_prioridade', array('label' => false, 'class' => 'checkbox inline input-small','options' => array('1' => 'Baixa', '2' => 'Média', '3' => 'Alta'), 'multiple' => 'checkbox')); ?>
            </div>
        </div>
        <div class="row-fluid inline">
            <span class="label label-info">Tecnologias</span>
            <span class='pull-right'>
                <?= $this->Html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("tecnologias")')) ?>
                <?= $this->Html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("tecnologias")')) ?>
            </span>
            <div id='tecnologias'>
                <?php echo $this->BForm->input('codigo_tecnologia', array('label' => false, 'class' => 'checkbox inline input-large', 'options' => $tecnologias, 'multiple' => 'checkbox')); ?>
            </div>
        </div>
        <div class="row-fluid inline">
            <span class="label label-info">Operações</span>
            <?= $this->BForm->input('tipo_filtro_operacoes', array('label' => array('class' => 'radio inline'), 'div' => false, 'legend' => false, 'options' => array('e', 'ou'), 'type' => 'radio', 'value' => (!isset($this->data['Ocorrencia']['tipo_filtro_operacoes']) ? '0' : $this->data['Ocorrencia']['tipo_filtro_operacoes']) )) ?>
            <span class='pull-right'>
                <?= $this->Html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("operacoes")')) ?>
                <?= $this->Html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("operacoes")')) ?>
            </span>
            <div id='operacoes'>
                <?php echo $this->BForm->input('codigo_operacao', array('label' => false, 'class' => 'checkbox inline input-large', 'options' => $operacoes, 'multiple' => 'checkbox')); ?>
            </div>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $this->BForm->end() ?>        
        </div>
</div>
<?php 
    echo $javascript->codeBlock('jQuery(document).ready(function(){
        $.placeholder.shim();
        atualizaListaOcorrencias();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Ocorrencia/element_name:ocorrencias/" + Math.random())
        });
        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });
    });');
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>