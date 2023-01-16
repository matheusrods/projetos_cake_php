<option value="">Todas empresas</option>
<?php foreach ($empresas as $key => $empresa): ?>
	<option value="<?= $key ?>"><?= $empresa ?></option>
<?php endforeach; ?>