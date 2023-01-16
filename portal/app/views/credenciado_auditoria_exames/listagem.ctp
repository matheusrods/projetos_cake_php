<?php
/**
 * Listagem:
    • Código Credenciado
    • Nome Fantasia Credenciado
    • Status
        ◦ Pendente, Pagamento Bloqueado, Liberado para Pagamento
            ▪ Quando o status estiver “Pagamento Bloqueado”, deve apresentar com tolltip o motivo do mesmo 
    • Código Pedido Exame
    • Exame
    • Data Baixa
    • Anexo Exame
        ◦ Link para o anexo do exame
    • Anexo Ficha Clínica
        ◦ Link para o anexo da ficha clínica
    • Valor
    • Ação
        ◦ Auditar
            ▪ Quando acionar a ação Auditar, deve abrir uma modal com os dados do Exame onde poderá alterar o Status do mesmo, caso o status seja “Pagamento Bloqueado”, incluir o motivo do bloqueio. 
            ▪ Neste modal deve existir um botão salvar onde registrará a auditoria.
 */
?>
<?php if(!empty($listagem)):?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini">Código do Credenciado</th>
            <th>Nome Fantasia Credenciado</th>
            <th>Status</th>
            <th>Código Pedido Exame</th>
            <th>Exame</th>
            <th>Data Baixa</th>
            <th>Anexo Exame</th>
            <th>Anexo Ficha Clínica</th>
            <th>Valor</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($listagem as $linha): ?>
        <tr>
            <td><?php debug($linha); ?></td>
            <td>
                <?php // Quando o status estiver “Pagamento Bloqueado”, deve apresentar com tolltip o motivo do mesmo
                // echo $html->link('', array(
                //     'action' => 'imprimir_laudo_pcd', 
                //     $funcionario['ClienteFuncionario']['codigo']), 
                //     array('class' => 'icon-print', 'data-toggle' => 'tooltip', 'title' => 'Colocar aqui motivo ')) 

                // Link para o anexo do exame
                // Link para o anexo da ficha clínica
                ?>
            </td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
</table>

<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 

<div class="modal fade" id="modal-auditar-detalhes" 
        data-backdrop="static" 
        style="width: 85%; left: 8%; top: 15%; margin: 0 auto;">
    <div class="modal-dialog modal-sm" style="position: static;">
        <div class="modal-content" id="modal_data">
            <div class="modal-header" style="text-align: center;">
                <h3>Dados do Exame</h3>
            </div>

            <div class="modal-body" style="max-height: 360px;font-size: 15px;">


            </div>

            <div class="modal-footer">
                <center><a href="javascript:void(0);" id="modal-auditar-salvar-auditoria" class="btn btn-danger">FECHAR</a></center>
            </div>

        </div>
    </div>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        
        function manipula_modal(id, mostra) {
            if(mostra) {
                $("#" + id).css("z-index", "1050");
                $("#" + id).modal("show");
            } else {
                $(".modal").css("z-index", "-1");
                $("#" + id).modal("hide");
            }
        }

        $(document).ready(function() {
            $("[data-toggle=\"tooltip\"]").tooltip();
        });

        jQuery("#abre-auditar-detalhes").click(function(){
          var id = this.id;
          manipula_modal("modal-auditar-detalhes", 0);

        });

        jQuery("#salva-auditar-detalhes").click(function(){
          var id = this.id;

        });

    });', false);