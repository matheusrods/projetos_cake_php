<?php 
    echo $this->BForm->create(
        'PosMetas', 
        array(
            'url' => array(
                'controller' => 'swt',
                'action' => 'incluir_metas', 
                $codigo_cliente, 
                $codigo_setor, 
                $codigo_cliente_bu, 
                $codigo_cliente_opco
            )
        )
    ); 
?>
    <div class='well'>	
		<?php
            echo $this->BForm->hidden('codigo_cliente', array('value' => $codigo_cliente));
            echo $this->BForm->hidden('codigo_setor', array('value' => $codigo_setor));
            echo $this->BForm->hidden('codigo_cliente_bu', array('value' => $codigo_cliente_bu));
            echo $this->BForm->hidden('codigo_cliente_opco', array('value' => $codigo_cliente_opco));
            echo $this->BForm->hidden('codigo', array('value' => !empty($this->data['PosMetas']['codigo'])? $this->data['PosMetas']['codigo'] : '') );
		?>

        <div class='row-fluid inline'>
            <?php echo $this->BForm->input('Cliente.codigo', array('label' => 'Código', 'class' => 'input-mini', 'disabled' => true)); ?>
            <?php echo $this->BForm->input('Cliente.razao_social', array('label' => 'Razão social', 'class' => 'input-large', 'disabled' => true)); ?>
            <?php echo $this->BForm->input('Cliente.nome_fantasia', array('label' => 'Nome fantasia', 'class' => 'input-large', 'disabled' => true)); ?>
        </div>

        <div class='row-fluid inline'>
            <?php echo $this->BForm->input('ClienteOpco.descricao', array('label' => 'Opco', 'class' => 'input-large', 'disabled' => true)); ?>
            <?php echo $this->BForm->input('ClienteBu.descricao', array('label' => 'Business Unit', 'class' => 'input-large', 'disabled' => true)); ?>
        </div>

        <div class='row-fluid inline'>
            <?php echo $this->BForm->input('Setores.descricao', array('label' => 'Setor', 'class' => 'input-large', 'disabled' => true)); ?>
        </div>

        <div class='row-fluid inline'>
            <?php echo $this->BForm->input('valor', array('label' => 'Meta (*)', 'class' => 'input-small numeric')); ?>
        </div>

        <div class='row-fluid inline'>
            <?php echo $this->BForm->input('dia_follow_up', array('label' => 'Periodicidade da meta (em meses)(*)', 'class' => 'input-large numeric', 'maxlength' => 2)); ?>
        </div>
	</div>

    <div class='form-actions'>
        <div id="salvar_meta" class="btn btn-primary">Salvar</div>
        <?= $html->link('Voltar', array('controller' => 'swt', 'action' => 'index_metas'), array('class' => 'btn')); ?>
    </div>

<?php echo $this->BForm->end(); ?>

<script>
    $(function(){
        $(document).on("input", ".numeric", function() {
            this.value = this.value.replace(/\D/g,'');
        });

        $("#salvar_meta").on('click', function(e) {
            const metasValor = $("#PosMetasValor").val();
            const diaFollowUp = $("#PosMetasDiaFollowUp").val();

            if (!(diaFollowUp >= 1 && diaFollowUp <= 12)) {
                alert("O campo 'periodicidade' deve possuir valores de 1 a 12.");
                return;
            }

            if (!(metasValor > 0)) {
                alert("O campo 'Meta' tem que ser um valor inteiro maior que 0.");
                return;
            }
            
            $("#PosMetasIncluirMetasForm").submit();
        });
    });
</script>
