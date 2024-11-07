<?php

class BackWorkModel
{
    protected $tblbackworks = 'backworks'; // The table where data is stored

    protected $tblbackworks_approvals = 'backworks_approvals';

    protected $tblbackworks_attachment = 'backworks_attachments';

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo; // Inject the global PDO object
    }
}
