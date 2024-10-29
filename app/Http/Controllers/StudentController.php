<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function showBalance()
    {
        $user = auth()->user();
        // Data denda
        $borrowings = Borrowing::with('book')
            ->where('user_id', $user->id)
            ->where('fine_amount', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        // Riwayat peminjaman (semua)
        $borrowingHistory = Borrowing::with('book')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalFine = $borrowings->sum('fine_amount');

        return view('student.balance', compact('user', 'borrowings', 'borrowingHistory', 'totalFine'));
    }
}
