<?php

namespace App\Http\Controllers;

use App\Services\MovieService;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;

class MovieController extends Controller
{
    protected $movieService;

    public function __construct(MovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    public function index(Request $request)
    {
        $movies = $this->movieService->getMoviesList($request->input('search'));
        return view('homepage', compact('movies'));
    }

    public function detail($id)
    {
        $movie = $this->movieService->getMovieById($id);
        return view('detail', compact('movie'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('input', compact('categories'));
    }

    public function store(StoreMovieRequest $request)
    {
        $this->movieService->createMovie($request->except('foto_sampul'), $request->file('foto_sampul'));

        return redirect('/')->with('success', 'Data berhasil disimpan');
    }

    public function data()
    {
        $movies = $this->movieService->getMoviesDataPaginated();
        return view('data-movies', compact('movies'));
    }

    public function form_edit($id)
    {
        $movie = $this->movieService->getMovieById($id);
        $categories = Category::all();
        return view('form-edit', compact('movie', 'categories'));
    }

    public function update(UpdateMovieRequest $request, $id)
    {
        $this->movieService->updateMovie($id, $request->except('foto_sampul'), $request->file('foto_sampul'));

        return redirect('/movies/data')->with('success', 'Data berhasil diperbarui');
    }

    public function delete($id)
    {
        $this->movieService->deleteMovie($id);

        return redirect('/movies/data')->with('success', 'Data berhasil dihapus');
    }
}
