<!DOCTYPE html>
<html>
<head>
    <title>Pet Store</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Lista zwierząt</h1>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <a href="{{ route('pets.create') }}" class="btn btn-primary">Dodaj zwierzaka</a>
        @if (empty($pets))
            <p>Brak zwierząt do wyświetlenia.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nazwa</th>
                        <th>Status</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pets as $pet)
                        <tr>
                            <td>{{ $pet['id'] ?? 'Brak ID' }}</td>
                            <td>{{ $pet['name'] ?? 'Brak nazwy' }}</td>
                            <td>{{ $pet['status'] ?? 'Brak statusu' }}</td>
                            <td>
                                <a href="{{ route('pets.edit', $pet['id'] ?? 0) }}" class="btn btn-sm btn-warning">Edytuj</a>
                                <form action="{{ route('pets.destroy', $pet['id'] ?? 0) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno?')">Usuń</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>