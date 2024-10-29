<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    public function index()
    {
        $borrowings = Borrowing::with(['user', 'book'])->latest();

        // Jika user adalah siswa, hanya tampilkan peminjaman miliknya
//        if (auth()->user()->hasRole('siswa')) {
//            $borrowings = $borrowings->where('user_id', auth()->id());
//        }

        $borrowings = $borrowings->get();

        // Ambil data buku yang stoknya tersedia
        $books = Book::where('stock', '>', 0)->get();

        // Ambil data siswa untuk dropdown peminjam
        $users = User::role('siswa')->get();

        return view('borrowings.index', compact('borrowings', 'books', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'borrow_date' => 'required|date',
            'due_date' => 'required|date|after:borrow_date'
        ]);

        // Jika user adalah siswa, gunakan ID nya sendiri
        if (auth()->user()->hasRole('siswa')) {
            $validated['user_id'] = auth()->id();
        } else {
            $request->validate([
                'user_id' => 'required|exists:users,id'
            ]);
            $validated['user_id'] = $request->user_id;
        }

        $book = Book::findOrFail($validated['book_id']);
        if ($book->stock <= 0) {
            return back()->with('error', 'Maaf, stok buku tidak tersedia.');
        }

        // Cek peminjaman aktif untuk user ini
        $activeLoans = Borrowing::where('user_id', $validated['user_id'])
            ->where('is_returned', false)
            ->count();

        if ($activeLoans >= 2) {
            return back()->with('error', 'Maaf, maksimal peminjaman adalah 2 buku.');
        }

        // Cek apakah ada denda yang belum dibayar
        $unpaidFines = Borrowing::where('user_id', $validated['user_id'])
            ->where('fine_amount', '>', 0)
            ->where('is_returned', true)
            ->sum('fine_amount');

        if ($unpaidFines > 0) {
            return back()->with('error', 'Maaf, ada denda yang belum dibayar sebesar Rp ' . number_format($unpaidFines, 0, ',', '.'));
        }

        // Buat peminjaman
        DB::transaction(function () use ($validated, $book) {
            Borrowing::create([
                'user_id' => $validated['user_id'],
                'book_id' => $validated['book_id'],
                'borrow_date' => $validated['borrow_date'],
                'due_date' => $validated['due_date'],
                'is_returned' => false
            ]);

            // Kurangi stok buku
            $book->decrement('stock');
        });

        return redirect()->route('borrowings.index')
            ->with('success', 'Peminjaman berhasil ditambahkan.');
    }

    public function return(Borrowing $borrowing)
    {
        if (!$borrowing->is_returned) {
            $today = Carbon::now();
            $dueDate = Carbon::parse($borrowing->due_date);

            if ($today->gt($dueDate)) {
                $daysLate = $today->diffInDays($dueDate);
                $fineAmount = $daysLate * 1000; // Rp 1.000 per hari

                $user = $borrowing->user;
                if ($user->balance >= $fineAmount) {
                    $user->decrement('balance', $fineAmount);
                    $borrowing->fine_amount = $fineAmount;
                }
            }

            DB::transaction(function () use ($borrowing, $today) {
                $borrowing->update([
                    'return_date' => $today,
                    'is_returned' => true
                ]);

                // Kembalikan stok buku
                $borrowing->book->increment('stock');
            });

            return redirect()->route('borrowings.index')
                ->with('success', 'Buku berhasil dikembalikan.');
        }

        return redirect()->route('borrowings.index')
            ->with('error', 'Buku sudah dikembalikan sebelumnya.');
    }
}
