<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


</head>

<body class="">
    <div class="container mt-5">
        <h4 class="mb-4 text-center">✅ Daftar Tugas (To-Do List)</h4>

        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">➕ Tambah Tugas Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('todos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group mb-2">
                        <input type="file" name="gambar" id="gambar" class="form-control">
                    </div>
                    <div class="input-group mb-2">
                        <input type="text" name="title" class="form-control"
                            placeholder="Tulis nama tugas di sini..." required>
                    </div>
                    <button type="submit" class="btn btn-success">Simpan Tugas</button>

                </form>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">📦 Tugas yang Tersedia</h5>
            </div>
            <ul class="list-group list-group-flush">
                {{-- Loop melalui setiap tugas (Ganti $todos dengan variabel dari controller Anda) --}}
                @forelse ($todos as $todo)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center 
                    @if ($todo->is_completed) list-group-item-success @endif">

                        {{-- Judul Tugas --}}
                        <span
                            class="flex-grow-1 d-flex align-items-center @if ($todo->is_completed) text-decoration-line-through text-muted @endif">
                            @if ($todo->gambar)
                                <img src="{{ asset('storage/' . $todo->gambar) }}" alt="Gambar tugas" class="me-3 rounded" style="width:48px;height:48px;object-fit:cover"> 
                            @endif
                            <span>{{ $todo->title }}</span>
                        </span>

                        {{-- Aksi (Tombol) --}}
                        <div>
                            {{-- Form Toggle Selesai/Belum Selesai (Update Status) --}}
                            <form action="{{ route('todos.update', $todo->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="is_completed" value="{{ $todo->is_completed ? 0 : 1 }}">
                                <button type="submit"
                                    class="btn btn-sm 
                                @if ($todo->is_completed) btn-warning @else btn-success @endif"
                                    title="{{ $todo->is_completed ? 'Tandai Belum Selesai' : 'Tandai Selesai' }}">
                                    <i
                                        class="bi @if ($todo->is_completed) bi-x-lg @else bi-check-lg @endif"></i>
                                </button>
                            </form>

                            {{-- Tombol Buka Modal Edit (Update Judul) --}}
                            <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal"
                                data-bs-target="#editModal{{ $todo->id }}" title="Edit Tugas">
                                <i class="bi bi-pencil"></i>
                            </button>

                            {{-- Form Delete --}}
                            <form action="{{ route('todos.destroy', $todo->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus Tugas">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </li>

                    <div class="modal fade" id="editModal{{ $todo->id }}" tabindex="-1"
                        aria-labelledby="editModalLabel{{ $todo->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-info text-white">
                                    <h5 class="modal-title" id="editModalLabel{{ $todo->id }}">✏️ Edit Tugas</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form action="{{ route('todos.update', $todo->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="title{{ $todo->id }}" class="form-label">Nama Tugas</label>
                                            <input type="text" name="title" class="form-control"
                                                id="title{{ $todo->id }}" value="{{ $todo->title }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-info text-white">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <li class="list-group-item text-center text-muted">🎉 Semua tugas selesai! Tambahkan tugas baru.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
