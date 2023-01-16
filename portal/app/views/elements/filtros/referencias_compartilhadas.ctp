<div class='well'>
	<?php echo $bajax->form('Referencia', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Referencia', 'element_name' => 'referencias_compartilhadas'), 'divupdate' => '.form-procurar')) ?>
    <div class='row-fluid inline'>
        <?php echo $this->BForm->input('descricao', array('label' => false, 'placeholder' => 'Descrição','type' => 'text', 'class' => 'input-xlarge')) ?>
        <?php echo $this->BForm->input('classe', array('label' => false, 'empty' => 'Classe','options' => $classes, 'class' => 'medium')) ?>
        <?php if( isset($paises) ) : ?>
        <?php echo $this->BForm->input('codigo_pais', array('label' => false,'class' => 'input-medium pais', 'empty' => 'Pais', 'options' => $paises)) ?>
        <?php endif;?>
        <?php echo $this->BForm->input('estado', array('label' => false, 'empty' => 'Estado','options' => $estados, 'class' => 'input-small')) ?>
        <?php echo $this->BForm->input('cidade', array('label' => false, 'placeholder' => 'Cidade','type' => 'text', 'class' => 'input-large')) ?>
    </div>
    <div class='row-fluid inline'>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    </div>
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    $(document).ready(function(){
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "referencias/listagem_compartilhados/" + Math.random());
        $("#limpar-filtro").click(function(){
            bloquearDiv($(".form-procurar"));
            $(".form-procurar").load(baseUrl + "/filtros/limpar/model:Referencia/element_name:referencias_compartilhadas/" + Math.random())
        });
        $("#ReferenciaCodigoPais").change(function(){
            buscar_t_estado("#ReferenciaCodigoPais", "#ReferenciaEstado");
        });

    });', false);?>