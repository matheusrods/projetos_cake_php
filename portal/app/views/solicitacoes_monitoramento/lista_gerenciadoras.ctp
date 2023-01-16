<option value="">Selecione uma Gerenciadora</option>
<?php
	foreach ($gerenciadoras as $key => $value) {
		echo '<option value="'.$key.'">'.$value.'</option>';
	}