<?php

namespace App\Interfaces;

interface MovieRepositoryInterface
{
    public function getAllPaginated($perPage);
    public function getLatestPaginated($perPage);
    public function searchPaginated($keyword, $perPage);
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
