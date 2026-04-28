<?php

namespace App\Services;

use App\Interfaces\MovieRepositoryInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MovieService
{
    protected $movieRepository;

    public function __construct(MovieRepositoryInterface $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    public function getMoviesList($search = null)
    {
        if ($search) {
            return $this->movieRepository->searchPaginated($search, 6);
        }
        return $this->movieRepository->getLatestPaginated(6);
    }

    public function getMoviesDataPaginated()
    {
        return $this->movieRepository->getLatestPaginated(10);
    }

    public function getMovieById($id)
    {
        return $this->movieRepository->findById($id);
    }

    public function createMovie(array $data, $imageFile)
    {
        $fileName = $this->uploadImage($imageFile);
        $data['foto_sampul'] = $fileName;
        
        return $this->movieRepository->create($data);
    }

    public function updateMovie($id, array $data, $imageFile = null)
    {
        $movie = $this->movieRepository->findById($id);

        if ($imageFile) {
            $data['foto_sampul'] = $this->uploadImage($imageFile);
            $this->deleteImage($movie->foto_sampul);
        }

        return $this->movieRepository->update($id, $data);
    }

    public function deleteMovie($id)
    {
        $movie = $this->movieRepository->findById($id);
        $this->deleteImage($movie->foto_sampul);
        return $this->movieRepository->delete($id);
    }

    private function uploadImage($file)
    {
        $randomName = Str::uuid()->toString();
        $fileExtension = $file->getClientOriginalExtension();
        $fileName = $randomName . '.' . $fileExtension;

        $file->move(public_path('images'), $fileName);
        return $fileName;
    }

    private function deleteImage($fileName)
    {
        $filePath = public_path('images/' . $fileName);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
    }
}
