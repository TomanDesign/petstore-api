<!DOCTYPE html>
<html>
<head>
    <title>Edytuj zwierzaka</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Edytuj zwierzaka</h1>
        <form action="{{ route('pets.update', $pet['id']) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>ID</label>
                <input type="number" name="id" class="form-control" value="{{ $pet['id'] }}" readonly>
            </div>
            <div class="form-group">
                <label>Nazwa</label>
                <input type="text" name="name" class="form-control" value="{{ $pet['name'] }}" required>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="available" {{ $pet['status'] == 'available' ? 'selected' : '' }}>Dostępny</option>
                    <option value="pending" {{ $pet['status'] == 'pending' ? 'selected' : '' }}>Oczekujący</option>
                    <option value="sold" {{ $pet['status'] == 'sold' ? 'selected' : '' }}>Sprzedany</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Zapisz</button>
            <a href="{{ route('pets.index') }}" class="btn btn-secondary">Wróć</a>
        </form>
    </div>
</body>
</html>