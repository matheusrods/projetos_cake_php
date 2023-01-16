<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('codigo') ?>
    <?php echo $this->BForm->hidden('codigo_regulador',array('value'=>$this->data['ReguladorRegiao']['codigo_regulador'])) ?>
    <?php echo $this->BForm->input('cidade', array('label' => 'Cidade', 'class' => 'text-medium')) ?>
    <?php echo $this->BForm->input('latitude', array('label' => 'Latitude','class' => 'text-medium')) ?>
    <?php echo $this->BForm->input('longitude', array('label' => 'Longitude','class' => 'text-medium')) ?>
    <?php echo $this->BForm->input('raio', array('label' => 'Raio','class' => 'text-medium')) ?>
    <?php echo $this->BForm->input('prioridade', array('label' => 'Prioridade','class' => 'text-small')) ?>
</div>