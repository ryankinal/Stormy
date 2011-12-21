<?php
include_once('lib/DBRecord.php');

class Share extends DBRecord
{
    protected $columns = array(
        'share_id',
        '_sharer_id',
        '_sharee_id',
        'created',
        'expires'
    );
    protected $table = 'shares';
    protected $IDColumn = 'share_id';
}
?>