<?php
switch ($var->v0) {
	case 'run':
		$id = $var->v1;
		if ($id) {
			$id = qscms::authcode($id, false);
			if (is_numeric($id)) {
				task::run($id);
			}
		}
	break;
}
?>