<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini">Habilita:</th>
            <th>Empresa:</th>
        </tr>
    </thead>
    <tbody>
		<?php foreach($todas_empresas as $k => $empresa) : ?>
	        <tr>
	            <td class="input-mini" style="text-align: center;"><?php echo $this->BForm->input('Usuario.multi_empresa.' . $empresa['MultiEmpresa']['codigo'],   array('type' => 'checkbox',  'label' => false, 'class' => 'checkbox-multiempresa', 'checked' => isset($empresas_marcadas[$empresa['MultiEmpresa']['codigo']]) ? true : false)); ?></td>
	            <td><?php echo $empresa['MultiEmpresa']['razao_social']; ?></td>
	        </tr>
        <?php endforeach; ?>    		
    </tbody>
</table>