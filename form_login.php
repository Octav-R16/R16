<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card p-4 shadow" style="width: 350px;">
        <h2 class="text-center mb-3">Alert</h2>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>
        <p class="mt-3 text-center text-muted">Terimakasih, report komplain Anda sudah kami teruskan ke Tim IT, status pengerjaan akan di informasikan ke Telegram Cabang.</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
