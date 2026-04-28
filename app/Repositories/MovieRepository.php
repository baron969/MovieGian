<?php

namespace App\Repositories;

use App\Interfaces\MovieRepositoryInterface;
use App\Models\Movie;

class MovieRepository implements MovieRepositoryInterface
{
    public function getAllPaginated($perPage)
    {
        return Movie::paginate($perPage);
    }

    public function getLatestPaginated($perPage)
    {
        return Movie::latest()->paginate($perPage);
    }

    public function searchPaginated($keyword, $perPage)
    {
        return Movie::latest()
            ->where('judul', 'like', '%' . $keyword . '%')
            ->orWhere('sinopsis', 'like', '%' . $keyword . '%')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById($id)
    {
        return Movie::findOrFail($id);
    }

    public function create(array $data)
    {
        return Movie::create($data);
    }

    public function update($id, array $data)
    {
        $movie = $this->findById($id);
        $movie->update($data);
        return $movie;
    }

    public function delete($id)
    {
        $movie = $this->findById($id);
        return $movie->delete();
    }
}
