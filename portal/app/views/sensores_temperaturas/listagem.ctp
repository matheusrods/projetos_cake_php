<?php if(isset($filtros['codigo_cliente']) && $filtros['codigo_cliente']): ?>
    <?php foreach($dados_sensores as $codigo_sm => $dados ): ?>
    <?if($codigo_sm):?>
        <div class='row-fluid'>
            <table class='table table-striped'>
                <thead>
                    <tr>
                        <th>SM</th>
                        <th>Data Início</th>
                        <th>Data Fim</th>
                        <th>Mínima</th>
                        <th>Máxima</th>
                        <th>Transportador</th>
                        <th>Embarcador</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $this->Buonny->codigo_sm( $codigo_sm  ); ?></td>
                        <td><?php echo preg_replace("/(\d{2,4})\-(\d{2})\-(\d{2})(\w*)/", "$3/$2/$1$4", $dados['dados_sm']['viag_data_inicio']); ?></td>
                        <td><?php echo preg_replace("/(\d{2,4})\-(\d{2})\-(\d{2})(\w*)/", "$3/$2/$1$4", $dados['dados_sm']['viag_data_fim']); ?></td>
                        <td><?php echo $dados['dados_sm']['vtem_valor_minimo']; ?></td>
                        <td><?php echo $dados['dados_sm']['vtem_valor_maximo']; ?></td>
                        <td><?php echo $dados['dados_sm']['transportador_pjur_razao_social']; ?></td>
                        <td><?php echo $dados['dados_sm']['embarcador_pjur_razao_social']; ?></td>
                    </tr>
                </tbody>            
                <table class='table table-striped'>
                    <thead>
                        <tr>
                            <th>De</th>
                            <th>Até</th>
                            <th>Temperatura Média</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                <?php foreach( $dados['dados_sensores'] as $dados_temperaturas  ): ?>
                    <tbody>
                        <tr>
                            <td><?php echo preg_replace("/(\d{2,4})\-(\d{2})\-(\d{2})(\w*)/", "$3/$2/$1$4", $dados_temperaturas['stem_data_cadastro_de']); ?></td>
                            <td><?php echo preg_replace("/(\d{2,4})\-(\d{2})\-(\d{2})(\w*)/", "$3/$2/$1$4", $dados_temperaturas['stem_data_cadastro_ate']); ?></td>
                            <td><?php echo $dados_temperaturas['stem_media_sensores']; ?></td>
                            <td class="">
                                <?php if( $dados_temperaturas['na_faixa'] == 0 ): ?>
                                    <span class="badge badge-empty badge-important" title="Anormal"></span>
                                <?php else: ?>
                                    <span class="badge badge-empty badge-success" title="Normal"></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                <?php endforeach; ?>
                </table>
            </table>
        </div>
    <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>