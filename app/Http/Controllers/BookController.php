<?php

namespace App\Http\Controllers;

use App\Models\Book; // Pastikan kamu sudah punya model Book
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        // Mengambil semua data buku dari database
        // Jika tabel kosong, $books akan berisi array kosong []
        $books = Book::all(); 

        // Mengirim data ke file resources/views/books.blade.php
        return view('books', compact('books'));
    }
}