<fieldset>
    <legend>Pesquisador de Fichas</legend>
    <table>
        <thead>
            <tr>
                <th>Pesquisa referente à <b><? echo $nome_categoria . ' (' . $nome_produto . ')'?></b></th>
                <th>Resultado</th>
            </tr>
            <style type="text/css">
                .aprovado{
                    color:#0a0;
                }
                .reprovado{
                    color:#f00;
                }
                .default{
                    color:#000;
                }
            </style>
        </thead>
       <?php
        function _formataSaida($estado) {
            switch ($estado) {
                case 'Concluído':
                    echo "<spam class='aprovado'>" . $estado . "</spam>";
                    break;
                case 'Pendente':
                    echo "<spam class='reprovado'>" . $estado . "</spam>";
                    break;
                default:
                    echo "<spam class='default'>" . $estado . "</spam>";
                    break;
            }
        }
        ?>
        <tbody>
            <tr>
                <td>Status Ficha Anterior (Profissional)</td>
                <td> <?php _formataSaida($status_ficha_anterior);  ?>  </td>

            </tr>
            <tr>
                <td>Status Ficha Anterior (Proprietário - Primeira tela)</td>
                <td> <?php _formataSaida($status_ficha_anterior_proprietario);  ?>  </td>
            </tr>
            <tr>
                <td>Status Profissional Negativado</td>
                <td> <?php _formataSaida($status_profissional_negativado);  ?>  </td>
            </tr>
             <tr>
                <td>Status CNH Válida</td>
                <td> <?php _formataSaida($status_cnh_vencida);  ?>  </td>
            </tr>
             <tr>
                <td>Status Veículo Ocorrências</td>
                <td> <?php _formataSaida($status_veiculo_ocorrencias);  ?>  </td>
            </tr>
            <tr>
                <td>Status Profissional (Cheque)</td>
                <td> <?php _formataSaida($status_profissional_cheque);  ?>  </td>
            </tr>
             <tr>
                <td>Status Proprietário (Cheque)</td>
                <td> <?php _formataSaida($status_proprietario_cheque);  ?>  </td>
            </tr>
             <tr>
                <td>Status Profissional (Valor)</td>
                <td> <?php _formataSaida($status_profissional_montante);  ?>  </td>
            </tr>
             <tr>
                <td>Status Proprietário (Valor)</td>
                <td> <?php _formataSaida($status_proprietario_montante);  ?>  </td>
            </tr>
            <tr>
                <td>Status Histórico Profissional</td>
                <td> <?php _formataSaida($status_historico_profissional);  ?>  </td>
            </tr>
            <tr>
                <td>Status Histórico Profissional(Renovação/Atualização)</td>
                <td> <?php _formataSaida($status_historico_profissional_ren_atu);  ?>  </td>
            </tr>
        </tbody>
    </table>
</fieldset>