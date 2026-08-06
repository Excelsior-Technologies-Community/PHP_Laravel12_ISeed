@extends('layouts.app')

@section('content')

<div class="container">

    <h2 class="mb-4">
        🌱 Seeder Management Dashboard
    </h2>

    <div class="card mb-4">

        <div class="card-header bg-primary text-white">
            Generate Seeder Backup
        </div>

        <div class="card-body">

            <form method="POST" action="{{ route('seeder.generate') }}">
                @csrf

                <select name="table" class="form-control mb-3" required>

                    <option value="">
                        Select Table
                    </option>

                    @foreach($tables as $table)

                        <option value="{{ $table }}">
                            {{ $table }}
                        </option>

                    @endforeach

                </select>

                <button class="btn btn-success">
                    Generate Seeder
                </button>

            </form>

        </div>

    </div>

    <div class="card">

        <div class="card-header bg-dark text-white">
            Seeder Backup History
        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>Table</th>
                        <th>Seeder</th>
                        <th>Records</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($histories as $history)

                        <tr>

                            <td>{{ $history->table_name }}</td>

                            <td>{{ $history->seeder_name }}</td>

                            <td>{{ $history->records }}</td>

                            <td>
                                <span class="badge bg-success">
                                    {{ $history->status }}
                                </span>
                            </td>

                            <td>

                                <a href="{{ route('seeder.download', $history->seeder_name) }}"
                                    class="btn btn-sm btn-info">
                                    Download
                                </a>

                                <form method="POST"
                                    action="{{ route('seeder.run') }}"
                                    style="display:inline">

                                    @csrf

                                    <input type="hidden"
                                        name="seeder"
                                        value="{{ $history->seeder_name }}">

                                    <button class="btn btn-sm btn-warning">
                                        Run
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="text-center">
                                No Seeder History Found
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection