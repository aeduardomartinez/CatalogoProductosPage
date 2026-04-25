<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Get stats
$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$categoryCount = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | SneakerVault</title>
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
                <span style="margin-right: 15px; color: var(--text-muted);">Hola, <?= $_SESSION['admin_username'] ?></span>
                <a href="logout.php" class="btn glass" style="padding: 8px 16px;"><i class="fa-solid fa-right-from-bracket"></i></a>
            </div>
        </div>
    </header>

    <main class="container" style="padding: 50px 0;">
        <h2 style="margin-bottom: 40px;">Panel de Administración</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-bottom: 50px;">
            <div class="glass" style="padding: 30px; border-radius: 20px; display: flex; align-items: center; gap: 20px;">
                <div style="background: rgba(99, 102, 241, 0.2); color: var(--primary); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fa-solid fa-box"></i>
                </div>
                <div>
                    <h3 style="font-size: 2rem;"><?= $productCount ?></h3>
                    <p style="color: var(--text-muted);">Productos</p>
                </div>
            </div>
            <div class="glass" style="padding: 30px; border-radius: 20px; display: flex; align-items: center; gap: 20px;">
                <div style="background: rgba(16, 185, 129, 0.2); color: var(--secondary); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fa-solid fa-list"></i>
                </div>
                <div>
                    <h3 style="font-size: 2rem;"><?= $categoryCount ?></h3>
                    <p style="color: var(--text-muted);">Categorías</p>
                </div>
            </div>
        </div>

        <div class="glass" style="padding: 40px; border-radius: 30px;">
            <h3 style="margin-bottom: 20px;">Acciones Rápidas</h3>
            <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                <a href="products.php?action=add" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Añadir Producto</a>
                <a href="../index.php" class="btn glass" target="_blank"><i class="fa-solid fa-eye"></i> Ver Tienda</a>
            </div>
        </div>
    </main>
</body>
</html>
