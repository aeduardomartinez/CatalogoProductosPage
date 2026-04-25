<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $stmt = $pdo->prepare("UPDATE categories SET name=?, slug=? WHERE id=?");
        $stmt->execute([$name, $slug, $_POST['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
        $stmt->execute([$name, $slug]);
    }
    header('Location: categories.php');
    exit;
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    header('Location: categories.php');
    exit;
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías | SneakerVault</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="glass">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <a href="index.php" class="logo">SNEAKER ADMIN</a>
            <nav>
                <ul>
                    <li><a href="index.php">Escritorio</a></li>
                    <li><a href="products.php">Productos</a></li>
                    <li><a href="categories.php">Categorías</a></li>
                </ul>
            </nav>
            <div>
                <a href="logout.php" class="btn glass" style="padding: 8px 16px;"><i class="fa-solid fa-right-from-bracket"></i></a>
            </div>
        </div>
    </header>

    <main class="container" style="padding: 50px 0;">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 40px;">
            <div>
                <h3 style="margin-bottom: 25px;">Añadir Categoría</h3>
                <form method="POST" class="glass" style="padding: 30px; border-radius: 20px;">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px;">Nombre</label>
                        <input type="text" name="name" required placeholder="Ej. Running">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Guardar</button>
                </form>
            </div>
            
            <div>
                <h3 style="margin-bottom: 25px;">Listado</h3>
                <div class="glass" style="border-radius: 20px; overflow: hidden;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: rgba(255,255,255,0.05);">
                            <tr>
                                <th style="padding: 20px; text-align: left;">ID</th>
                                <th style="padding: 20px; text-align: left;">Nombre</th>
                                <th style="padding: 20px; text-align: left;">Slug</th>
                                <th style="padding: 20px; text-align: right;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $c): ?>
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <td style="padding: 15px;"><?= $c['id'] ?></td>
                                    <td style="padding: 15px;"><?= $c['name'] ?></td>
                                    <td style="padding: 15px;"><?= $c['slug'] ?></td>
                                    <td style="padding: 15px; text-align: right;">
                                        <a href="categories.php?delete=<?= $c['id'] ?>" style="color: #ef4444;" onclick="return confirm('¿Eliminar?')"><i class="fa-solid fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
