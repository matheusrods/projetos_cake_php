<?php //debug($cliente); ?>

<div class='well'>

    <div class="row-fluid inline">
        <?php
            echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => "{$codigo_cliente}"));
        ?>

        <?php echo $this->BForm->input('codigo_pos_ferramenta', array('id' => 'pos_ferramenta', 'label' => 'Tipo da ferramenta (*)','class' => 'input-medium', 'options'=> $combo_pos_ferramenta, 'empty' => 'Selecione', 'default' => $cliente['Cliente']['codigo_pos_ferramenta'])); ?>

    </div>

    <div class="row-fluid inline criticidade1">
        <?php echo $this->BForm->input('PosCriticidade[][0][descricao]', array('class' => 'input-mini', 'placeholder' => 'Descrição', 'label' => 'Descrição', 'value' => 'Baixo', 'readonly' => 'readonly')) ?>
        <?php echo $this->BForm->input('PosCriticidade[][0][cor]', array('class' => 'input-mini jscolor', 'placeholder' => 'Cor', 'label' => 'Cor (*)', 'value' => "".$cliente['Cliente']['PosCriticidade'][0]['cor']."" )) ?>
        <?php echo $this->BForm->input('PosCriticidade[][0][valor_inicio]', array('class' => 'input-mini numeric valor_inicio', 'label' => 'De', 'value' => "".$cliente['Cliente']['PosCriticidade'][0]['valor_inicio']."")) ?>
        <?php echo $this->BForm->input('PosCriticidade[][0][valor_fim]', array('class' => 'input-mini numeric valor_fim', 'label' => 'Até', 'value' => "".$cliente['Cliente']['PosCriticidade'][0]['valor_fim']."")) ?>
        <?php echo $this->BForm->input('PosCriticidade[][0][codigo]', array('type' => 'hidden', 'value' => "".$cliente['Cliente']['PosCriticidade'][0]['codigo']."")) ?>
    </div>

    <div class="row-fluid inline criticidade2">
        <?php echo $this->BForm->input('PosCriticidade[][1][descricao]', array('class' => 'input-mini', 'placeholder' => 'Descrição', 'label' => 'Descrição', 'value' => 'Média', 'readonly' => 'readonly' )) ?>
        <?php echo $this->BForm->input('PosCriticidade[][1][cor]', array('class' => 'input-mini jscolor', 'placeholder' => 'Cor', 'label' => 'Cor (*)', 'value' => "".$cliente['Cliente']['PosCriticidade'][1]['cor']."")) ?>
        <?php echo $this->BForm->input('PosCriticidade[][1][valor_inicio]', array('class' => 'input-mini numeric valor_inicio', 'label' => 'De', 'value' => "".$cliente['Cliente']['PosCriticidade'][1]['valor_inicio']."")) ?>
        <?php echo $this->BForm->input('PosCriticidade[][1][valor_fim]', array('class' => 'input-mini numeric valor_fim', 'label' => 'Até', 'value' => "".$cliente['Cliente']['PosCriticidade'][1]['valor_fim']."")) ?>
        <?php echo $this->BForm->input('PosCriticidade[][1][codigo]', array('type' => 'hidden', 'value' => "".$cliente['Cliente']['PosCriticidade'][1]['codigo']."")) ?>
    </div>

    <div class="row-fluid inline criticidade3">
        <?php echo $this->BForm->input('PosCriticidade[][2][descricao]', array('class' => 'input-mini', 'placeholder' => 'Descrição', 'label' => 'Descrição', 'value' => 'Alta', 'readonly' => 'readonly')) ?>
        <?php echo $this->BForm->input('PosCriticidade[][2][cor]', array('class' => 'input-mini jscolor', 'placeholder' => 'Cor', 'label' => 'Cor (*)', 'value' => "".$cliente['Cliente']['PosCriticidade'][2]['cor']."")) ?>
        <?php echo $this->BForm->input('PosCriticidade[][2][valor_inicio]', array('class' => 'input-mini numeric valor_inicio', 'label' => 'De', 'value' => "".$cliente['Cliente']['PosCriticidade'][2]['valor_inicio']."")) ?>
        <?php echo $this->BForm->input('PosCriticidade[][2][valor_fim]', array('class' => 'input-mini numeric valor_fim', 'label' => 'Até', 'value' => "".$cliente['Cliente']['PosCriticidade'][2]['valor_fim']."")) ?>
        <?php echo $this->BForm->input('PosCriticidade[][2][codigo]', array('type' => 'hidden', 'value' => "".$cliente['Cliente']['PosCriticidade'][2]['codigo']."")) ?>
    </div>

    <div class="row-fluid inline criticidade4">
        <?php echo $this->BForm->input('PosCriticidade[][3][descricao]', array('class' => 'input-mini', 'placeholder' => 'Descrição', 'label' => 'Descrição', 'value' => 'Maior', 'readonly' => 'readonly')) ?>
        <?php echo $this->BForm->input('PosCriticidade[][3][cor]', array('class' => 'input-mini jscolor', 'placeholder' => 'Cor', 'label' => 'Cor (*)', 'value' => "".$cliente['Cliente']['PosCriticidade'][3]['cor']."")) ?>
        <?php echo $this->BForm->input('PosCriticidade[][3][valor_inicio]', array('class' => 'input-mini numeric valor_inicio', 'label' => 'De', 'value' => "".$cliente['Cliente']['PosCriticidade'][3]['valor_inicio']."")) ?>
        <?php echo $this->BForm->input('PosCriticidade[][3][valor_fim]', array('class' => 'input-mini numeric valor_fim', 'label' => 'Até', 'value' => "".$cliente['Cliente']['PosCriticidade'][3]['valor_fim']."")) ?>
        <?php echo $this->BForm->input('PosCriticidade[][3][codigo]', array('type' => 'hidden', 'value' => "".$cliente['Cliente']['PosCriticidade'][3]['codigo']."")) ?>
    </div>

</div>

<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
<?php echo $html->link('Voltar', array('action' => 'config_criticidade_cliente', $this->passedArgs[0]), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();
	});
'); ?>
<?php echo $this->Buonny->link_js('jscolor'); ?>

<script>
    $(function(){

        $(document).on("input", ".numeric", function() {
            this.value = this.value.replace(/\D/g,'');
        });

        <?php if ($cliente['Cliente']['codigo_pos_ferramenta'] == 1) { ?>
            $('.numeric').closest('.text').addClass('hidden')
        <?php } ?>

        $("#pos_ferramenta").on("change", function(){

            if ($(this).val() == 2 || $(this).val() == 3) {
                $(".numeric").closest(".text").removeClass("hidden");
                $(".numeric").val('');
                $(".numeric").not(":first").attr("readonly", "readonly")
            } else {
                $(".numeric").closest(".text").addClass("hidden");
                $(".numeric").val('');
                $(".numeric").not(":first").attr("readonly", "readonly")
            }
        });

        $(".numeric").keyup(function(){

            var atual = parseInt($(this).val());

            if ($(this).hasClass('valor_inicio')) {
                var prev = $(this).closest(".inline").prev().find(".valor_fim").val();
                var next = $(this).closest(".input").next().find(".valor_fim").val();

            } else {
                var prev = $(this).closest(".input").prev().find(".valor_inicio").val();
                var next = $(this).closest(".inline").next().find(".valor_inicio").val();
            }

            if (parseInt(atual) > parseInt(prev) && parseInt(atual) < parseInt(next)) {
                $(this).closest(".inline").nextAll().find(".numeric").removeAttr('readonly')

                if ($(this).hasClass('valor_inicio')) {
                    $(this).closest(".input").next().find(".valor_fim").removeAttr('readonly');
                }
            } else {
                $(this).closest(".inline").nextAll().find(".numeric").attr('readonly', 'readonly');

                if ($(this).hasClass('valor_inicio')) {
                    console.log('valor_inicio')
                    $(this).closest(".input").next().find(".valor_fim").attr('readonly', 'readonly');
                }
            }
        });

        $(".criticidade1 .valor_inicio").keyup(function(){

            $(".criticidade1 .valor_fim").removeAttr("readonly")
        })

        $(".criticidade1 .valor_fim").keyup(function(){

            var inicio1 = $(".criticidade1 .valor_inicio").val();
            var fim1 = $(this).val();

            if ( parseInt(inicio1) < parseInt(fim1)) {
                $(".criticidade2 .valor_inicio").removeAttr("readonly")
            }
        })

        $(".criticidade2 .valor_inicio").keyup(function(){

            var inicio2 = $(this).val();
            var fim1 = $(".criticidade1 .valor_fim").val();

            if ( parseInt(inicio2) > parseInt(fim1)) {
                $(".criticidade2 .valor_fim").removeAttr("readonly")
            }
        })

        $(".criticidade2 .valor_fim").keyup(function(){

            var inicio2 = $(".criticidade2 .valor_inicio").val();
            var fim2 = $(this).val();

            if ( parseInt(fim2) > parseInt(inicio2)) {
                $(".criticidade3 .valor_inicio").removeAttr("readonly")
            }
        })

        $(".criticidade3 .valor_inicio").keyup(function(){

            var inicio3 = $(this).val();
            var fim2 = $(".criticidade2 .valor_fim").val();

            if ( parseInt(inicio3) > parseInt(fim2)) {
                $(".criticidade3 .valor_fim").removeAttr("readonly")
            }
        })

        $(".criticidade3 .valor_fim").keyup(function(){

            var inicio3 = $(".criticidade3 .valor_inicio").val();
            var fim3 = $(this).val();

            if ( parseInt(fim3) > parseInt(inicio3)) {
                $(".criticidade4 .valor_inicio").removeAttr("readonly")
            }
        })

        $(".criticidade4 .valor_inicio").keyup(function(){

            var inicio4 = $(this).val();
            var fim3 = $(".criticidade3 .valor_fim").val();

            if ( parseInt(inicio4) > parseInt(fim3)) {
                $(".criticidade4 .valor_fim").removeAttr("readonly")
            }
        })
    })
</script>
