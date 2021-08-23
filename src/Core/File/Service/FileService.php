<?php

declare(strict_types=1);

namespace Lea\Core\File\Service;

use Lea\Core\Exception\FileNotExistsException;
use Lea\Core\File\Entity\File;
use Lea\Core\Service\ServiceInterface;

class FileService implements ServiceInterface
{
    public function previewFile(string $file_name)
    {
        $file_name = urldecode($file_name);
        $file = $this->getFileName($file_name);
        if (!file_exists($file))
            throw new FileNotExistsException();

        header("Content-Type: " . mime_content_type($file));
        header("Content-Length: " . filesize($file));
        echo file_get_contents($file);
        if (ob_get_contents())
            ob_end_clean();
        flush();
        readfile($file);
    }

    private function getFileName(string $file)
    {
        return File::PATH . base64_decode($file);
    }
}
