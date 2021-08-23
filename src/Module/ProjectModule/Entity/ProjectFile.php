<?php

declare(strict_types=1);

namespace Lea\Module\ProjectModule\Entity;

use Lea\Core\File\Entity\File;

class ProjectFile extends File
{
    /**
     * @var int
     */
    private $project_id;

    /**
     * Get the value of project_id
     *
     * @return  int
     */ 
    public function getProjectId()
    {
        return $this->project_id;
    }

    /**
     * Set the value of project_id
     *
     * @param  int  $project_id
     *
     * @return  self
     */ 
    public function setProjectId(int $project_id)
    {
        $this->project_id = $project_id;

        return $this;
    }
}
