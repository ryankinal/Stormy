<?php
function smarty_modifier_filesize($bytes, $truncate = 1)
{
	$sz = 'BKMGTP';
	$factor = floor((strlen($bytes) - 1) / 3);
	$str = sprintf("%.{$decimals}f", $bytes / pow(1024, $factor));
	return substr($str, 0, strpos($str, '.') + ($truncate + 1)).@$sz[$factor];
}
?>