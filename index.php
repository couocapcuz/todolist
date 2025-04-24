<?php
session_start();

// Inisialisasi data awal
if (!isset($_SESSION["tasks"])) {
    $_SESSION["tasks"] = [
        ["id" => 1, "title" => "Belajar PHP", "status" => "belum"],
        ["id" => 2, "title" => "Kerjakan Tugas UX", "status" => "selesai"]
    ];
}

$tasks = &$_SESSION["tasks"];
$error = "";

// Fungsi
function tambahTugas(&$tasks, $title) {
    $idBaru = empty($tasks) ? 1 : (end($tasks)["id"] + 1);
    $tasks[] = ["id" => $idBaru, "title" => htmlspecialchars($title), "status" => "belum"];
}

function ubahStatusTugas(&$tasks, $id) {
    foreach ($tasks as &$task) {
        if ($task["id"] == $id) {
            $task["status"] = ($task["status"] === "selesai") ? "belum" : "selesai";
            break;
        }
    }
}

function hapusTugas(&$tasks, $id) {
    foreach ($tasks as $i => $task) {
        if ($task["id"] == $id) {
            array_splice($tasks, $i, 1);
            break;
        }
    }
}

// Handle POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["title"])) {
        $title = trim($_POST["title"]);
        if ($title === "") {
            $error = "Judul tugas harus diisi.";
        } else {
            tambahTugas($tasks, $title);
        }
    } elseif (isset($_POST["toggle"])) {
        ubahStatusTugas($tasks, $_POST["toggle"]);
    } elseif (isset($_POST["delete"])) {
        hapusTugas($tasks, $_POST["delete"]);
    }
}

// Fungsi tampilan
function tampilkanDaftar($tasks) {
    foreach ($tasks as $task) {
        $checked = $task["status"] === "selesai" ? "checked" : "";
        $statusClass = $task["status"] === "selesai" ? "text-decoration-line-through text-muted" : "";
        $statusText = $task["status"] === "selesai" ? "✅ Selesai" : "❗ Belum";

        echo '<li class="list-group-item d-flex justify-content-between align-items-center animate__animated animate__fadeIn task-item">';
        
        echo <<<HTML
        <form method="POST" class="d-flex align-items-center gap-2 w-100">
            <input type="hidden" name="toggle" value="{$task['id']}">
            <input type="checkbox" onChange="this.form.submit()" $checked class="form-check-input">
            <span class="flex-grow-1 $statusClass">{$task['title']}</span>
            <span class="badge bg-secondary">$statusText</span>
        </form>
        HTML;

        if ($task["status"] === "selesai") {
            echo <<<HTML
            <form method="POST" style="margin:0;">
                <input type="hidden" name="delete" value="{$task['id']}">
                <button class="btn btn-sm btn-danger">Hapus</button>
            </form>
            HTML;
        }

        echo '</li>';
    }
}
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
                <?php if (empty($tasks)): ?>
                    <div class="alert alert-warning text-center animate__animated animate__fadeIn">
                        Belum ada tugas. Silakan tambahkan tugas baru!
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php tampilkanDaftar($tasks); ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="/todolist/assets/bootstrap.bundle.min.js"></script>
</body>
</html>