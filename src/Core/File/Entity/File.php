<?php

namespace Lea\Core\File\Entity;

use Lea\Core\Entity\Entity;
use Lea\Core\Entity\FileInterface;
use Lea\Core\Exception\FileNotExistsException;
use Lea\Core\Exception\FileSaveFailedException;

abstract class File extends Entity implements FileInterface
{
    public const PATH = __DIR__ . '/../../../../files/';
    /**
     * @var string
     */
    protected $server_name;

    /**
     * @var string
     */
    protected $file_name;

    
    public function set(array $data): void
    {
        if ($this->id === null && !isset($_FILES[$data['file_key'] ?? false])) /* TODO - check if does have sense */
            throw new FileNotExistsException($data['file_key']);
        $this->file = $_FILES[$data['file_key']] ?? null;
    }

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

    public static function getPath(): string
    {
        return self::PATH;
    }

    public function moveUploadedFile(): void
    {
        $ext = pathinfo($this->file["name"]);
        $name = $this->file['tmp_name'];
        $dir = self::PATH;
        $this->file_name = $this->file['name'];
        $this->server_name = md5(microtime()).md5($this->file["name"]).'.'.$ext['extension'];
        if (!move_uploaded_file($name, $dir . $this->server_name))
            throw new FileSaveFailedException($this->file['name']);

    }

    public function get(array $specific_fields = null): array
    {
        $result = parent::get($specific_fields);
        $result['server_name'] = base64_encode($result['server_name']);

        return $result;
    }
}
