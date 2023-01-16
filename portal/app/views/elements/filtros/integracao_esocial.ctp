<div class='well'>
  	<?php echo $bajax->form('LogIntegracao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'LogIntegracao', 'element_name' => 'integracao_esocial'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
        <?php echo $this->Buonny->input_grupo_economico($this, 'LogIntegracao', $unidades, $setores, $cargos); ?>
    </div>
    <div class="row-fluid inline">
         <?php echo $this->BForm->input('matricula', array('label' => 'Matricula', 'placeholder' => false, 'class' => 'input-medium')); ?>
          <?php echo $this->BForm->input('empresa', array('options' => $multi_empresas, 'empty' => 'Selecione a empresa', 'class' => 'input-xlarge',  'label' => 'Empresa')); ?>
          <?php echo $this->BForm->input('tabela', array('options' => $tabelas_esocial, 'empty' => 'Selecione a tabela', 'class' => 'input-medium',  'label' => 'Tabela')); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('arquivo', array('label' => 'Nome do Arquivo', 'type' => 'text')); ?>
    	<?php echo $this->BForm->input('conteudo', array('label' => 'Parte do Conteúdo', 'type' => 'text')); ?>
        <?php echo $this->BForm->input('data_inicio', array('label' => 'Data Inicial', 'placeholder' => 'Início', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
       	<?php echo $this->BForm->input('data_fim', array('label' => 'Data Final', 'placeholder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  	<?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		setup_datepicker();
        setup_mascaras();
		
        var div = jQuery(".lista");
        bloquearDiv(div);
        div.load(baseUrl + "logs_integracoes/lista_integracao_esocial/" + Math.random());
		
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:LogIntegracao/element_name:integracao_esocial/" + Math.random())
        });
    });', false);
?>