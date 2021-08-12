<?php

namespace Aparlay\Core\Repositories;

interface RepositoryInterface
{
    public function __construct($model);

    public function all();

    public function create(array $data);

    public function update(array $data, $id);

    public function delete($id);

    public function find($id);
}
