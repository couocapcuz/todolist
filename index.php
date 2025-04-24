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

        echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
        
        echo <<<HTML
        <form method="POST" class="d-flex align-items-center gap-2 w-100">
            <input type="hidden" name="toggle" value="{$task['id']}">
            <input type="checkbox" onChange="this.form.submit()" $checked>
            <span class="flex-grow-1 {$statusClass}">{$task['title']}</span>
            <span class="badge bg-secondary" style="margin-right:10px;">{$statusText}</span>
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
  <title>Rahmat Hidayat - 40622200003</title>
  <link href="/todolist/assets/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h1 class="mb-4 text-center">To-Do List</h1>

  <!-- Alert Error -->
  <?php if ($error): ?>
    <div class="alert alert-danger text-center"><?= $error ?></div>
  <?php endif; ?>

  <!-- Form Tambah Tugas -->
  <form method="POST" class="mb-4">
    <div class="input-group">
      <input type="text" name="title" class="form-control" placeholder="Tambahkan tugas baru..." required>
      <button class="btn btn-success" type="submit">Tambah</button>
    </div>
  </form>

  <!-- Daftar Tugas -->
  <?php if (empty($tasks)): ?>
    <div class="alert alert-warning text-center">Belum ada tugas. Silakan tambahkan tugas baru!</div>
  <?php else: ?>
    <ul class="list-group">
      <?php tampilkanDaftar($tasks); ?>
    </ul>
  <?php endif; ?>
</div>
</body>
</html>
