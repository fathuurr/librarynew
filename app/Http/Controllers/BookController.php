<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $books = Book::when($search, function($query) use ($search) {
            return $query->where('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%");
        })->orderBy('created_at', 'desc')->paginate(10);

        return view('books.index', compact('books', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'author' => 'required',
            'stock' => 'required|integer|min:0'
        ]);

        Book::create($validated);
        return redirect()->route('books.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required',
            'author' => 'required',
            'stock' => 'required|integer|min:0'
        ]);

        $book->update($validated);
        return redirect()->route('books.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $activeBorrowings = Borrowing::where('book_id', $book->id)
            ->where('is_returned', false)
            ->exists();

        if ($activeBorrowings) {
            return redirect()->route('books.index')
                ->with('error', 'Buku tidak dapat dihapus karena sedang dipinjam');
        }

        $hasBorrowingHistory = Borrowing::where('book_id', $book->id)->exists();

        if ($hasBorrowingHistory) {
            return redirect()->route('books.index')
                ->with('error', 'Buku tidak dapat dihapus karena memiliki riwayat peminjaman');
        }

        try {
            $book->delete();
            return redirect()->route('books.index')
                ->with('success', 'Buku berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('books.index')
                ->with('error', 'Terjadi kesalahan saat menghapus buku');
        }
    }
}
