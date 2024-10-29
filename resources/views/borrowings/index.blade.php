@extends('layouts.app')

@section('title', 'Daftar Peminjaman')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>Daftar Peminjaman</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBorrowingModal">
                Pinjam Buku
            </button>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
{{--                    <th>ID</th>--}}
                    <th>Peminjam</th>
                    <th>Buku</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Status</th>
                    <th>Denda</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                @foreach($borrowings as $borrowing)
                    <tr>
{{--                        <td>{{ $borrowing->id }}</td>--}}
                        <td>{{ $borrowing->user->name }}</td>
                        <td>{{ $borrowing->book->title }}</td>
                        <td>{{ $borrowing->borrow_date }}</td>
                        <td>{{ $borrowing->due_date }}</td>
                        <td>
                            @if($borrowing->is_returned)
                                <span class="badge bg-success">Dikembalikan</span>
                            @else
                                <span class="badge bg-warning">Dipinjam</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($borrowing->fine_amount, 0, ',', '.') }}</td>
                        <td>
                            @if(!$borrowing->is_returned && (auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('petugas_perpus')))
                                <form action="{{ route('borrowings.return', $borrowing->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Kembalikan</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Borrowing Modal -->
    <div class="modal fade" id="addBorrowingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('borrowings.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Pinjam Buku</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if(auth()->user()->hasRole('siswa'))
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                        @else
                            <div class="mb-3">
                                <label class="form-label">Peminjam</label>
                                <select class="form-select select2" name="user_id" required>
                                    <option value="">Pilih Peminjam</option>
                                    @foreach($users->where('roles.name', 'siswa') as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Buku</label>
                            <select class="form-select select2" name="book_id" required>
                                <option value="">Pilih Buku</option>
                                @foreach($books->where('stock', '>', 0) as $book)
                                    <option value="{{ $book->id }}">
                                        {{ $book->title }} (Stok: {{ $book->stock }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Pinjam</label>
                            <input type="date" class="form-control" name="borrow_date"
                                   value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Kembali</label>
                            <input type="date" class="form-control" name="due_date"
                                   value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                        </div>

                        @if(auth()->user()->hasRole('siswa'))
                            <div class="alert alert-info">
                                <strong>Saldo Anda:</strong> Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}
                                <br>
                                <small>* Denda keterlambatan Rp 1.000/hari</small>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
