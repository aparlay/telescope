<?php

namespace Aparlay\Core\Api\V1\Repositories;

interface RepositoryInterface
{
    public function __construct($model);

    public function all();

    public function create($data);

    public function update($data, $id);

    public function delete($id);

    public function find($id);
}
