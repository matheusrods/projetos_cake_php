<h4>Pendentes</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                  <th>Documento</th>
                </tr>
            </thead>
            <tbody>
            <?php 
                if(!empty($documentos_pendentes)): 
                    foreach ($documentos_pendentes as $pendentes): ?>
                      <tr>
                            <td><?php echo $pendentes['TipoDocumento']['descricao'] ?></td>
                      </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td><div>Nenhum dado foi encontrado.</div></td>
                    </tr>
                <?php endif; ?>
            <tbody>
        </table>