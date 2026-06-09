<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        // Mengambil semua data buku dari database
        // Jika tabel kosong, $books akan berisi array kosong []
        $books = Buku::all();

        // Mengirim data ke file resources/views/books.blade.php
        return view('books', compact('books'));
    }
}