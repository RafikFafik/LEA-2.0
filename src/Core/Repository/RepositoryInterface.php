<?php

namespace Lea\Core\Repository;
interface RepositoryInterface {
    public function getById(int $id);
    public function post();
    public function update();
}