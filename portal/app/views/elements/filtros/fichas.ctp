<div class='formulario'>
    <h1>Alteração de Dados/Status</h1>
    <?php echo $bajax->form('Ficha', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Ficha', 'element_name' => 'fichas'), 'divupdate' => '.form-procurar')) ?>
    <div class='acao'>
        <div class="fullwide">
            <?php echo $this->BForm->input('codigo_documento', array('label' => 'CPF', 'type' => 'text', 'class' => 'cpf text-small2')) ?>
            <?php echo $this->BForm->input('profissional', array('label' => 'Nome do Profissional', 'type' => 'text', 'readonly' => true, 'tab-index' => '-1', 'class' => 'text-large')) ?>
            <?php echo $this->BForm->input('codigo_produto', array('label' => 'Produto', 'options' => $produtos, 'empty' => 'Selecione uma opção')); ?>
        </div>        

        <div class="fullwide">
            <?php echo $this->BForm->input('codigo_cliente', array('label' => 'Código', 'class' => 'text-small')) ?>
            <?php echo $this->BForm->input('razao_social', array('label' => 'Razão Social', 'readonly' => true, 'class' => 'text-large')) ?>
        </div>
        <div class="fullwide">
            <?php echo $this->BForm->input('data_inclusao_inicio', array('label' => 'Data inicial', 'class' => 'data text-small')) ?>
            <?php echo $this->BForm->input('data_inclusao_fim', array('label' => 'Data final', 'class' => 'data text-small')) ?>
        </div>
        
        <?php echo $this->BForm->submit('Buscar', array('div' => array('class' => 'acao-filtro'))) ?>
        <?php echo $this->BForm->end() ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro')) ;?>
    </div>
</div>

<script type="text/javascript">
//<![CDATA[
    (function($){
        atualizaListaFichas("fichas");
        setup_mascaras();
        setup_datepicker();
        var inputProfissional = $("#FichaProfissional");
        var inputCodigoCliente = $("#FichaCodigoCliente");
        var inputRazaoSocial = $("#FichaRazaoSocial");

        inputProfissional.val("");
        inputRazaoSocial.val("");

        var carregaNomeProfissional = function() {
            var self = this;
            var documento = $(this).val().replace(/\D/g, "");
            if ($(this).val() == "")  {
                inputProfissional.val("");
                return false;
            }
            $.ajax(baseUrl + '/profissionais/carrega_profissionalnome/' + documento, {
                success: function(r, status, e) {
                    if (r) {
                        inputProfissional.val(r.Profissional.nome);
                        inputCodigoCliente.focus();
                    } else {
                        inputProfissional.val('');
                        self.select();
                    }
                }
            });
        }

        $("#FichaCodigoDocumento").change(carregaNomeProfissional).keyup(function(e) {
            if (e.keyCode == 13) {
                $(this).blur();
                return false;
            }
        }).trigger('change');
        
        var carregaRazaoSocial = function() {
            var self = this;
            var codigo = $(this).val();
            if (codigo == "")  {
                inputRazaoSocial.val("");
                return false;
            }
            $.ajax(baseUrl + '/clientes/carrega_cliente/' + codigo, {
                success: function(r, status, e) {
                    if (r) {
                        inputRazaoSocial.val(r.Cliente.razao_social);
                    } else {
                        inputRazaoSocial.val("");
                        self.select();
                    }
                }
            });
        }
        
        inputCodigoCliente.change(carregaRazaoSocial).keyup(function(e) {
            if (e.keyCode == 13) {
                $(this).blur();
                return false;
            }
        }).trigger('change');
        
        jQuery('#limpar-filtro').click(function(){
            bloquearDiv(jQuery('.form-procurar'));
            jQuery(".form-procurar").load(baseUrl + '/filtros/limpar/model:Ficha/element_name:fichas/' + Math.random())
        });

    })(jQuery);
    //]]>
</script>