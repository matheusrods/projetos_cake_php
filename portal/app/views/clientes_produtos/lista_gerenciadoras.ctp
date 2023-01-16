<?php
	if($gerenciadoras){
		echo '<option value = ""></option>';
	} else {
		foreach ($gerenciadoras as $key => $value) {
			echo '<option value = "'.$key.'">'.$value.'</option>';
		}
	}