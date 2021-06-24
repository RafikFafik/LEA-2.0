<?php

namespace Lea\Core\Entity;

use Lea\Core\Entity\Entity;

abstract class File extends Entity implements FileInterface
{
    /**
     * @var string
     */
    private $server_name;

    /**
     * @var string
     */
    private $file_name;



    public function getServerName(): string
    {
        return $this->server_name;
    }

    public function setServerName(string $server_name): void
    {
        $this->server_name = $server_name;
    }

    public function getFileName()
    {
        return $this->file_name;
    }

    public function setFileName(string $file_name): void
    {
        $this->file_name = $file_name;
    }
}
