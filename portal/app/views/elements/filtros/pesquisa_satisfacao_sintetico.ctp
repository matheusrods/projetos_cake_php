<div class='well'>
    <div id='filtros'>
        <?php echo $this->Bajax->form('PesquisaSatisfacao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PesquisaSatisfacao', 'element_name' => 'pesquisa_satisfacao_sintetico'), 'divupdate' => '.form-procurar')) ?>
            <div class="row-fluid inline">                
                <?php echo $this->Buonny->input_codigo_cliente($this,'codigo_cliente','Cliente',true,'PesquisaSatisfacao'); ?>
                <?php echo $this->Buonny->input_periodo($this,'PesquisaSatisfacao','data_inicial', 'data_final', TRUE) ?>
                <?php echo $this->BForm->input('codigo_usuario_pesquisa', array('class' => 'input-medium','label' => 'Operador', 'options' => $usuarios_pesquisa, 'empty'=>'Operador' )) ?>
                <?php echo $this->BForm->input('codigo_produto', array('class' => 'input-medium','label' => 'Produto','options' => array('Todos os produtos','1' => 'Teleconsult','82' => 'BuonnySat'))) ?>
                <?php echo $this->BForm->input('codigo_status_pesquisa', array('class' => 'input-medium','label' => 'Status da Pesquisa','options' => $status_pesquisa , 'empty'=>'Status Pesquisa' )) ?>
                <?php echo $this->BForm->input('status_pesquisa', array('class' => 'input-medium','label' => 'Pesquisa','options' => array('Todas Pesquisas','Pendente','Realizada'))) ?>
                <?php if($gestor_logado == FALSE): ?>
                    <?php echo $this->BForm->input('codigo_gestor', array('label' => 'Gestor Comercial', 'class' => 'input-medium', 'options' => $gestores_com, 'empty' => 'Gestor Comercial')); ?>
                    <?php echo $this->BForm->input('codigo_gestor_npe', array('label' => 'Gestor NPE','class' => 'input-medium', 'options' => $gestores_npe, 'empty' => 'Selecione')); ?> 
                <?php endif; ?>
             </div>
            <?php if( isset($sintetico) ):?>
            <div class="row-fluid inline">
                <span class="label label-info">Agrupar por:</span>
                <div id='agrupamento'>
                    <?php echo $this->BForm->input('agrupamento', array('type' => 'radio', 'options' => $agrupamento, 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-medium'))) ?>
                </div>
            </div>                
            <?php endif;?>
            <div class="row-fluid inline">
                <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
                <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
                <?php echo $this->BForm->end() ?>
            </div>
		<?php echo $this->BForm->end() ?>
	</div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "pesquisas_satisfacao/listagem_pesquisa_satisfacao_sintetico/" + Math.random());
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PesquisaSatisfacao/element_name:pesquisa_satisfacao_sintetico/" + Math.random())
            jQuery(".lista").empty();           
        });  
        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });
    });', false);
?>