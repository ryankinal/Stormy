<?php
include_once('lib/DBRecord.php');

class File extends DBRecord
{
	protected $columns = array(
		'file_id',
		'name',
		'location',
		'mime_type',
		'size',
		'_user_id',
		'created'
	);
	protected $IDColumn = 'file_id';
	protected $table = 'files';
}
?>