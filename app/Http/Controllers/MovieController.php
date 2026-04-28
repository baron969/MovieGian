<?php

namespace App\Http\Controllers;

use App\Services\MovieService;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

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

    public function store(Request $request)
    {
        // Validasi data
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'string', 'max:255', Rule::unique('movies', 'id')],
            'judul' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'sinopsis' => 'required|string',
            'tahun' => 'required|integer',
            'pemain' => 'required|string',
            'foto_sampul' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Jika validasi gagal, kembali ke halaman input dengan pesan kesalahan
        if ($validator->fails()) {
            return redirect('movies/create')
                ->withErrors($validator)
                ->withInput();
        }

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

    public function update(Request $request, $id)
    {
        // Validasi data
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'sinopsis' => 'required|string',
            'tahun' => 'required|integer',
            'pemain' => 'required|string',
            'foto_sampul' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Jika validasi gagal, kembali ke halaman edit dengan pesan kesalahan
        if ($validator->fails()) {
            return redirect("/movies/edit/{$id}")
                ->withErrors($validator)
                ->withInput();
        }

        $this->movieService->updateMovie($id, $request->except('foto_sampul'), $request->file('foto_sampul'));

        return redirect('/movies/data')->with('success', 'Data berhasil diperbarui');
    }

    public function delete($id)
    {
        $this->movieService->deleteMovie($id);

        return redirect('/movies/data')->with('success', 'Data berhasil dihapus');
    }
}
