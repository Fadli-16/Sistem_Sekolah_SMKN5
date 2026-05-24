<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Status Peminjaman</title>
    <style>
    /* Gaya CSS sederhana */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    label {
        font-size: 16px;
        margin-bottom: 8px;
        display: block;
    }

    select {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    button {
        width: 100%;
        padding: 10px;
        background-color: #004080;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    button:hover {
        background-color: #0056b3;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Ubah Status Peminjaman</h2>

        <form action="{{ route('admin.kelola.inv.status.post', ['id' => request()->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <label for="status">Pilih Status Baru</label>
            <select name="status" id="status" required>
                <option value="Menunggu" <?php echo (isset($status) && $status == 'Menunggu') ? 'selected' : ''; ?>>
                    Menunggu</option>
                <option value="Disetujui" <?php echo (isset($status) && $status == 'Disetujui') ? 'selected' : ''; ?>>
                    Disetujui</option>
                <option value="Ditolak" <?php echo (isset($status) && $status == 'Ditolak') ? 'selected' : ''; ?>>
                    Ditolak</option>
            </select>

            <button type="submit">Ubah Status</button>
        </form>
    </div>
</body>

</html>