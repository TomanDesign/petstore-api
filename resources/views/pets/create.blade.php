<!DOCTYPE html>
<html>
<head>
    <title>Dodaj zwierzaka</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Dodaj zwierzaka</h1>
        <form action="{{ route('pets.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>ID</label>
                <input type="number" name="id" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Nazwa</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="available">Dostępny</option>
                    <option value="pending">Oczekujący</option>
                    <option value="sold">Sprzedany</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Dodaj</button>
            <a href="{{ route('pets.index') }}" class="btn btn-secondary">Wróć</a>
        </form>
    </div>
</body>
</html>