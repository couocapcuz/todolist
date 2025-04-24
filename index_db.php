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
  <title>Rahmat Hidayat - 40622200003</title>
  <link href="/todolist/assets/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h1 class="mb-4 text-center">To-Do List</h1>

  <?php if ($error): ?>
    <div class="alert alert-danger text-center"><?= $error ?></div>
  <?php endif; ?>

  <!-- Form Tambah -->
  <form method="POST" class="mb-4">
    <div class="input-group">
      <input type="text" name="title" class="form-control" placeholder="Tambahkan tugas baru..." required>
      <button class="btn btn-success" type="submit">Tambah</button>
    </div>
  </form>

  <!-- Daftar Tugas -->
  <?php if ($tasks->num_rows === 0): ?>
    <div class="alert alert-warning text-center">Belum ada tugas. Silakan tambahkan tugas baru!</div>
  <?php else: ?>
    <ul class="list-group">
      <?php while ($task = $tasks->fetch_assoc()): ?>
        <?php
          $checked = $task["status"] === "selesai" ? "checked" : "";
          $class = $task["status"] === "selesai" ? "text-decoration-line-through text-muted" : "";
          $statusText = $task["status"] === "selesai" ? "✅ Selesai" : "❗ Belum";
        ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <form method="POST" class="d-flex align-items-center gap-2 w-100">
            <input type="hidden" name="toggle" value="<?= $task['id'] ?>">
            <input type="checkbox" onChange="this.form.submit()" <?= $checked ?>>
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
</body>
</html>
