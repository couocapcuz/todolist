<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "todolist");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$error = "";

// Fungsi: Tambah tugas
function tambahTugas($conn, $title) {
    $stmt = $conn->prepare("INSERT INTO tasks (title) VALUES (?)");
    $stmt->bind_param("s", $title);
    $stmt->execute();
    $stmt->close();
}

// Fungsi: Ubah status
function ubahStatus($conn, $id) {
    $result = $conn->query("SELECT status FROM tasks WHERE id = $id");
    if ($row = $result->fetch_assoc()) {
        $newStatus = ($row["status"] === "selesai") ? "belum" : "selesai";
        $conn->query("UPDATE tasks SET status = '$newStatus' WHERE id = $id");
    }
}

// Fungsi: Hapus tugas
function hapusTugas($conn, $id) {
    $conn->query("DELETE FROM tasks WHERE id = $id");
}

// Tangani form POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["title"])) {
        $title = trim($_POST["title"]);
        if ($title === "") {
            $error = "Judul tugas harus diisi.";
        } else {
            tambahTugas($conn, htmlspecialchars($title));
        }
    } elseif (isset($_POST["toggle"])) {
        ubahStatus($conn, $_POST["toggle"]);
    } elseif (isset($_POST["delete"])) {
        hapusTugas($conn, $_POST["delete"]);
    }
}

// Ambil data tugas
$tasks = $conn->query("SELECT * FROM tasks ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rahmat Hidayat - 40622200003</title>
    <link href="/todolist/assets/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/todolist/assets/animate.min.css"/>
    <style>
      /* Menambahkan animasi */
        .task-item {
            transition: transform 0.2s, background-color 0.2s;
        }
        .task-item:hover {
            transform: scale(1.02);
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-success, .btn-danger {
            transition: transform 0.2s;
        }
        .btn-success:hover, .btn-danger:hover {
            transform: translateY(-2px);
        }
        .alert {
            animation: fadeInDown 0.5s;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <!-- Header -->
        <div class="text-center mb-4">
            <h1 class="display-4 fw-bold animate__animated animate__bounceIn">To-Do List</h1>
            <p class="text-muted">Rahmat Hidayat - 40622200003</p>
        </div>

        <!-- Error Alert -->
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
                <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="card mb-4 animate__animated animate__fadeIn">
            <div class="card-body">
                <h5 class="card-title">Tambah Tugas Baru</h5>
                <form method="POST" class="input-group">
                    <input type="text" name="title" class="form-control" placeholder="Tambahkan tugas baru..." required>
                    <button class="btn btn-success" type="submit">Tambah</button>
                </form>
            </div>
        </div>

        <!-- Task List Card -->
        <div class="card animate__animated animate__fadeIn">
            <div class="card-body">
                <h5 class="card-title">Daftar Tugas</h5>
                <?php if ($tasks->num_rows === 0): ?>
                    <div class="alert alert-warning text-center animate__animated animate__fadeIn">
                        Belum ada tugas. Silakan tambahkan tugas baru!
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php while ($task = $tasks->fetch_assoc()): ?>
                            <?php
                            $checked = $task["status"] === "selesai" ? "checked" : "";
                            $class = $task["status"] === "selesai" ? "text-decoration-line-through text-muted" : "";
                            $statusText = $task["status"] === "selesai" ? "✅ Selesai" : "❗ Belum";
                            ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center animate__animated animate__fadeIn task-item">
                                <form method="POST" class="d-flex align-items-center gap-2 w-100">
                                    <input type="hidden" name="toggle" value="<?= $task['id'] ?>">
                                    <input type="checkbox" onChange="this.form.submit()" <?= $checked ?> class="form-check-input">
                                    <span class="flex-grow-1 <?= $class ?>"><?= htmlspecialchars($task['title']) ?></span>
                                    <span class="badge bg-secondary"><?= $statusText ?></span>
                                </form>
                                <?php if ($task["status"] === "selesai"): ?>
                                    <form method="POST">
                                        <input type="hidden" name="delete" value="<?= $task['id'] ?>">
                                        <button class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                <?php endif; ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="/todolist/assets/bootstrap.bundle.min.js" ></script>
</body>
</html>
<?php $conn->close(); ?>