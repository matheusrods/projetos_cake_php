<div class = 'form-procurar'>
	<?= $this->element('/filtros/index_metas') ?>
</div>

<div id="cadastrar_metas" class="btn btn-success pull-right" style="display: none; margin-bottom: 10px;">Cadastrar metas em massa</div>

<div class='lista'></div>

<div id="dialogMetas" title="Cadastrar metas em massa">
    <div class="well">
        <form id="metasEmMassa" style="margin: 0 !important;">
            <div class="row-fluid inline">
                <div class="control-group input text">
                    <label for="PosMetasValor">Meta (*)</label>
                    <input name="data[PosMetas][valor]" type="text" class="input-small numeric" maxlength="4" id="PosMetasValor">
                </div>
            </div>

            <div class="row-fluid inline">
                <div class="control-group input text">
                    <label for="PosMetasDiaFollowUp">Periodicidade da meta (em meses)(*)</label>
                    <input name="data[PosMetas][dia_follow_up]" type="text" class="input-large numeric" maxlength="2" id="PosMetasDiaFollowUp">
                </div>
            </div>
        </form>
    </div>

    <div class="form-actions" style="margin-top: 0; margin-bottom: 0">
        <button class="btn btn-primary" id="addSelecionadosIncluir">Salvar</button>
    </div>
</div>

<script>
    $(function(){
        $(".lista").css("position", "unset");
        $("#tabela_select_meta_container").css("position", "unset");

        $( "#dialogMetas" ).dialog({
            autoOpen: false,
            width: 900,
            resizable: false,
            height: "auto",
            modal: true,
        });

        $(".metas_select_all").on("change", function(){
            if ($(this).is(":checked")) {
                $('.tabela_select_meta tbody tr input:checkbox').prop('checked','checked');
            } else {
                $('.tabela_select_meta tbody tr input:checkbox').removeProp('checked');
            }
        });

        $("#cadastrar_metas").click(function(e) {
            $("#dialogMetas").dialog('open');
        });

        $("#addSelecionadosIncluir").click(function(){
            const posMetas = [];

            $(".lista .tabela_select_meta .checkbox").each(function() {
                if ($(this).is(":checked")) {
                    posMetas.push({
                        codigo_cliente: $(this).data("unidade"),
                        codigo_setor: $(this).data("setor"),
                        codigo_cliente_bu: $(this).data("bu") || null,
                        codigo_cliente_opco: $(this).data("opco") || null
                    });
                }
            });

            if (posMetas.length <= 0) {
                alert("Selecione ao menos 1 configuração para alterar e/ou inserir.");
            } else {
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

                const objeto = {
                    valor: $("#PosMetasValor").val(),
                    dia_follow_up: $("#PosMetasDiaFollowUp").val(),
                    pos_metas: posMetas
                };

                var dados = {
                    dados : objeto
                };

                const div = jQuery("div#dialogMetas");
                bloquearDiv(div);

                $.ajax({
                    type: "POST",
                    url: baseUrl + "swt/inserir_em_massa",
                    data: dados,
                    dataType: "json",
                    success: function(data) {
                        $("#dialogMetas").dialog("close");
                        desbloquearDiv(div);

                        if (data == 1) {
                            const div2 = jQuery(".lista");
                            bloquearDiv(div2);
                            
                            div2.load(baseUrl + "swt/listagem_metas/" + Math.random());
                        }
                    },
                    error: function(data){
                        desbloquearDiv(div);
                    }
                });   
            }
        });

        $(document).on("input", ".numeric", function() {
            this.value = this.value.replace(/\D/g,'');
        });
    });
</script>
