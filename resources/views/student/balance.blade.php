@extends('layouts.app')

@section('title', 'Saldo')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Informasi Saldo</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th>Nama</th>
                            <td>: {{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>: {{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Saldo Saat Ini</th>
                            <td>: Rp {{ number_format($user->balance, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

{{--            <div class="mt-4">--}}
{{--                <h4>Riwayat Denda</h4>--}}
{{--                <table class="table table-bordered">--}}
{{--                    <thead>--}}
{{--                    <tr>--}}
{{--                        <th>No</th>--}}
{{--                        <th>Buku</th>--}}
{{--                        <th>Tanggal Pinjam</th>--}}
{{--                        <th>Tanggal Kembali</th>--}}
{{--                        <th>Keterlambatan</th>--}}
{{--                        <th>Denda</th>--}}
{{--                    </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}
{{--                    @forelse($user->borrowings->where('fine_amount', '>', 0) as $borrowing)--}}
{{--                        <tr>--}}
{{--                            <td>{{ $loop->iteration }}</td>--}}
{{--                            <td>{{ $borrowing->book->title }}</td>--}}
{{--                            <td>{{ $borrowing->borrow_date }}</td>--}}
{{--                            <td>{{ $borrowing->return_date }}</td>--}}
{{--                            <td>--}}
{{--                                @php--}}
{{--                                    $dueDate = \Carbon\Carbon::parse($borrowing->due_date);--}}
{{--                                    $returnDate = \Carbon\Carbon::parse($borrowing->return_date);--}}
{{--                                    $daysLate = $returnDate->diffInDays($dueDate);--}}
{{--                                @endphp--}}
{{--                                {{ $daysLate }} hari--}}
{{--                            </td>--}}
{{--                            <td>Rp {{ number_format($borrowing->fine_amount, 0, ',', '.') }}</td>--}}
{{--                        </tr>--}}
{{--                    @empty--}}
{{--                        <tr>--}}
{{--                            <td colspan="6" class="text-center">Tidak ada riwayat denda</td>--}}
{{--                        </tr>--}}
{{--                    @endforelse--}}
{{--                    </tbody>--}}
{{--                    <tfoot>--}}
{{--                    <tr>--}}
{{--                        <th colspan="5" class="text-end">Total Denda:</th>--}}
{{--                        <th>Rp {{ number_format($user->borrowings->sum('fine_amount'), 0, ',', '.') }}</th>--}}
{{--                    </tr>--}}
{{--                    </tfoot>--}}
{{--                </table>--}}
{{--            </div>--}}

            <div class="mt-4">
                <h4>Riwayat Peminjaman</h4>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Buku</th>
                        <th>Tanggal Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                        <th>Denda</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($borrowingHistory as $borrowing)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $borrowing->book->title }}</td>
                            <td>{{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($borrowing->due_date)->format('d/m/Y') }}</td>
                            <td>
                                @if($borrowing->return_date)
                                    {{ \Carbon\Carbon::parse($borrowing->return_date)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @php
                                    $dueDate = \Carbon\Carbon::parse($borrowing->due_date);
                                    $now = \Carbon\Carbon::now();
                                    $isOverdue = $now->gt($dueDate);
                                @endphp
                                @if($borrowing->is_returned)
                                    @if($borrowing->fine_amount > 0)
                                        <span class="badge bg-warning">Terlambat Dikembalikan</span>
                                    @else
                                        <span class="badge bg-success">Sudah Dikembalikan</span>
                                    @endif
                                @else
                                    @if($isOverdue)
                                        <span class="badge bg-danger">Terlambat</span>
                                    @else
                                        <span class="badge bg-primary">Sedang Dipinjam</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($borrowing->fine_amount > 0)
                                    Rp {{ number_format($borrowing->fine_amount, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada riwayat peminjaman</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
