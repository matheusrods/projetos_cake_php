<?php $filtrado = (isset($this->data['Recebsm']) && $this->data['Recebsm'] != null); ?>
<div class='well'>
    <h5><?= $this->Html->link(($filtrado ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $bajax->form('Recebsm', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Recebsm', 'element_name' => 'solicitacoes_monitoramento'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <div class="row-fluid inline">
                <?php echo $this->BForm->input('sm', array('class' => 'input-small', 'placeholder' => 'SM', 'label' => false, 'type' => 'text')) ?>
                <?php echo $this->BForm->input('cod_operador', array('class' => 'input-small', 'placeholder' => 'Cod. Operador', 'label' => false, 'type' => 'text')) ?>
                <?php echo $this->BForm->input('operador', array('class' => 'input-mediun', 'placeholder' => 'Nome Operador', 'label' => false, 'type' => 'text')) ?>      
                <?php echo $this->Buonny->input_periodo($this, 'Recebsm') ?>
            </div>
            <div class="row-fluid inline">
                
                <?php
                if(!isset($_SESSION['Auth']['Usuario']['codigo_cliente'])){
                    
                    echo $this->Buonny->input_cliente_tipo($this, 1, $clientes_embarcadores, 'Recebsm');
                    echo $this->Buonny->input_cliente_tipo($this, 4, $clientes_transportadores, 'Recebsm');
                    
                }
                else
                {
                    if(isset($clientes_embarcadores) && !empty($clientes_embarcadores))
                        echo $this->Buonny->input_cliente_tipo($this, 1, $clientes_embarcadores, 'Recebsm');
                    
                    if(isset($clientes_transportadores) && !empty($clientes_transportadores))
                        echo $this->Buonny->input_cliente_tipo($this, 4, $clientes_transportadores, 'Recebsm');
                } 
                ?>
                
                <?php echo $this->BForm->input('descricao_cidade',    array('class' => 'input-large ui-autocomplete-input', 'placeholder' => 'Informe uma Cidade', 'empty' => 'Cidade', 'label' => false)) ?>
                <?php echo $this->BForm->input('codigo_cidade',    array('class' => 'input-large', 'type' => 'hidden', 'empty' => 'Cidade', 'label' => false)) ?>
                <?php echo $this->BForm->input('tipo_estatistica', array('label' => array('class' => 'radio inline'), 'div' => false, 'legend' => false, 'type' => 'radio', 'options' => array(1 => 'Origem', 2 => 'Destino'))); ?>
            </div>
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('ValSmDe', array('class' => 'input-small numeric moeda', 'label' => 'Valor de:')) ?>
                <?php echo $this->BForm->input('ValSmAte', array('class' => 'input-small numeric moeda', 'label' => 'Até')) ?>
				<?php echo $this->BForm->input('codigo_seguradora', array('class' => 'input-large', 'label' => 'Seguradora','options' => $seguradoras,'empty' => 'Selecione')) ?>
			</div>
            <div class="row-fluid inline">
                <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
                <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
            </div>
            <div class="row-fluid inline">
                <span class="label label-info">Status</span>
                <div id='status'>
                    <?php echo $this->BForm->input('status', array('label' => false, 'class' => 'checkbox inline input-large', 'options' => array(1 => 'Em Aberto', 2 => 'Em Viagem', 3 => 'Encerradas'), 'multiple' => 'checkbox')); ?>
                </div>
            </div>
            <div class="row-fluid inline">
                <span class="label label-info">Tecnologias</span>
                <span class='pull-right'>
                    <?= $this->Html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("tecnologias")')) ?>
                    <?= $this->Html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("tecnologias")')) ?>
                </span>
                <div id='tecnologias'>
                    <?php echo $this->BForm->input('codequipamento', array('label' => false, 'class' => 'checkbox inline input-large', 'options' => $tecnologias, 'multiple' => 'checkbox')); ?>
                </div>
            </div>
            <div class="row-fluid inline">
                <span class="label label-info">Operações</span>
                <?= $this->BForm->input('tipo_filtro_operacoes', array('label' => array('class' => 'radio inline'), 'div' => false, 'legend' => false, 'options' => array('e', 'ou'), 'type' => 'radio', 'value' => (!isset($this->data['Recebsm']['tipo_filtro_operacoes']) ? '0' : $this->data['Recebsm']['tipo_filtro_operacoes']) )) ?>
                <span class='pull-right'>
                    <?= $this->Html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("operacoes")')) ?>
                    <?= $this->Html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("operacoes")')) ?>
                </span>
                <div id='operacoes'>
                    <?php echo $this->BForm->input('cod_operacao', array('label' => false, 'class' => 'checkbox inline input-large', 'options' => $operacoes, 'multiple' => 'checkbox')); ?>
                </div>
            </div>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
        <?php echo $this->BForm->end() ?>
    </div>
</div>

<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        $.placeholder.shim();
        atualizaListaSolicitacoesMonitoramento();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Recebsm/element_name:solicitacoes_monitoramento/" + Math.random())
        });      
        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        
		setup_mascaras();
        
        // inicia função de busca por cidades
        var cidade = $("#RecebsmDescricaoCidade");
        $("#RecebsmDescricaoCidade").attr("autocomplete", "off");
        function split(val){
            return val.split( /,\s*/ );
        }
        function extractLast(term){
            return split(term).pop();
        }
        cidade.autocomplete({
            minLength:	3,
            source:	"busca_cidades",
            focus: function(){return false;},
            select: function( event, ui ){
                var terms = split( this.value );
                terms.pop();
                terms.push( ui.item.label );
                terms.push( "" );
                this.value = terms.join("");
                $("#RecebsmCodigoCidade").val(ui.item.value);
                return false;
            }
	});
        
        // limpa código da cidade do campo hidden
        jQuery("#RecebsmDescricaoCidade").focusout(function(){
            if(jQuery("#RecebsmDescricaoCidade").val() == ""){
                jQuery("#RecebsmCodigoCidade").attr("value","");
            }
        });
        
    });', false);
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
<?php endif; ?>