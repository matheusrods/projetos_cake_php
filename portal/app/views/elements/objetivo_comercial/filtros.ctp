<div class="row-fluid inline">
    <?php echo $this->BForm->input('mes', array('type' => 'select', 'options' => $meses, 'class' => 'input-small', 'label' => 'Mês','empty' => 'Selecione o Mês')); ?>
    <?php echo $this->BForm->input('ano', array('type' => 'select', 'options' => $anos, 'class' => 'input-small', 'label' => 'Ano','empty' => 'Selecione o ano')); ?>
    <?php echo $this->BForm->input('codigo_endereco_regiao', array('type' => 'select', 'options' => $filiais, 'class' => 'input-medium', 'label' => 'Filial','empty' => 'Selecione a filial')); ?>
    <?php echo $this->BForm->input('codigo_gestor', array('type' => 'select', 'options' => $gestores, 'class' => 'input-medium', 'label' => 'Gestor','empty' => 'Selecione o gestor')); ?>
    <?php echo $this->BForm->input('codigo_produto', array('type' => 'select', 'options' => $produtos, 'class' => 'input-xlarge', 'label' => 'Produto','empty' => 'Selecione o produto')); ?>
    <?php echo $this->BForm->input('codigo_diretoria', array('type' => 'select', 'options' => $diretoria, 'class' => 'input-medium', 'label' => 'Diretoria','empty' => 'Selecione a diretoria')); ?>
    <?php echo $this->BForm->input('visualizacao', array('type' => 'hidden')); ?>
</div>
