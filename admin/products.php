<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$action = $_GET['action'] ?? 'list';
$msg = '';

// Delete Product
if ($action === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    if ($stmt->execute([$id])) {
        header('Location: products.php?msg=deleted');
        exit;
    }
}

// Save Product (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $discount_price = !empty($_POST['discount_price']) ? $_POST['discount_price'] : null;
    $category_id = $_POST['category_id'];
    $cropped_image = $_POST['cropped_image']; // Base64 data from Cropper.js

    $image_name = '';
    if (!empty($cropped_image)) {
        // Handle Base64 image
        $image_parts = explode(";base64,", $cropped_image);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $image_name = 'prod_' . time() . '.' . $image_type;
        file_put_contents('../uploads/' . $image_name, $image_base64);
    }

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $id = $_POST['id'];
        $sql = "UPDATE products SET name=?, brand=?, description=?, price=?, discount_price=?, category_id=?";
        $params = [$name, $brand, $description, $price, $discount_price, $category_id];
        
        if ($image_name) {
            $sql .= ", image_url=?";
            $params[] = $image_name;
        }
        
        $sql .= " WHERE id=?";
        $params[] = $id;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $msg = 'updated';
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO products (name, brand, description, price, discount_price, category_id, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $brand, $description, $price, $discount_price, $category_id, $image_name]);
        $msg = 'added';
    }
    
    header("Location: products.php?msg=$msg");
    exit;
}

// Fetch categories for the form
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// Fetch product if editing
$product = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch();
}

// Fetch all products for the list
$products = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos | SneakerVault</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
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
        <?php if ($action === 'list'): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
                <h2>Gestión de Productos</h2>
                <a href="products.php?action=add" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Añadir Nuevo</a>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <div class="glass" style="padding: 15px; border-radius: 12px; margin-bottom: 30px; border-left: 4px solid var(--secondary); color: var(--secondary);">
                    ¡Operación realizada con éxito!
                </div>
            <?php endif; ?>

            <div class="glass" style="border-radius: 20px; overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead style="background: rgba(255,255,255,0.05);">
                        <tr>
                            <th style="padding: 20px;">Imagen</th>
                            <th style="padding: 20px;">Nombre</th>
                            <th style="padding: 20px;">Marca</th>
                            <th style="padding: 20px;">Precio</th>
                            <th style="padding: 20px;">Categoría</th>
                            <th style="padding: 20px; text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 15px;">
                                    <img src="<?= $p['image_url'] ? '../uploads/'.$p['image_url'] : 'https://via.placeholder.com/50' ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                </td>
                                <td style="padding: 15px;"><?= $p['name'] ?></td>
                                <td style="padding: 15px;"><?= $p['brand'] ?></td>
                                <td style="padding: 15px;">$<?= number_format($p['price'], 2) ?></td>
                                <td style="padding: 15px;"><?= $p['category_name'] ?></td>
                                <td style="padding: 15px; text-align: right;">
                                    <a href="products.php?action=edit&id=<?= $p['id'] ?>" style="color: var(--primary); margin-right: 15px;"><i class="fa-solid fa-pen"></i></a>
                                    <a href="products.php?action=delete&id=<?= $p['id'] ?>" style="color: #ef4444;" onclick="return confirm('¿Estás seguro?')"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($action === 'add' || $action === 'edit'): ?>
            <div style="max-width: 800px; margin: 0 auto;">
                <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 40px;">
                    <a href="products.php" style="color: var(--text-muted); font-size: 1.5rem;"><i class="fa-solid fa-arrow-left"></i></a>
                    <h2><?= $action === 'edit' ? 'Editar Producto' : 'Nuevo Producto' ?></h2>
                </div>

                <form method="POST" id="productForm" class="glass" style="padding: 40px; border-radius: 30px;">
                    <input type="hidden" name="id" value="<?= $product['id'] ?? '' ?>">
                    <input type="hidden" name="cropped_image" id="croppedImageInput">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px;">Nombre del Producto</label>
                            <input type="text" name="name" value="<?= $product['name'] ?? '' ?>" required>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px;">Marca</label>
                            <input type="text" name="brand" value="<?= $product['brand'] ?? '' ?>">
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px;">Descripción</label>
                        <textarea name="description" rows="4"><?= $product['description'] ?? '' ?></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px;">Precio</label>
                            <input type="number" name="price" step="0.01" value="<?= $product['price'] ?? '' ?>" required>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px;">Precio Oferta (Opcional)</label>
                            <input type="number" name="discount_price" step="0.01" value="<?= $product['discount_price'] ?? '' ?>">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px;">Categoría</label>
                            <select name="category_id">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (isset($product['category_id']) && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= $cat['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 30px;">
                        <label style="display: block; margin-bottom: 8px;">Imagen del Producto</label>
                        <input type="file" id="imageInput" accept="image/*" style="margin-bottom: 20px;">
                        
                        <div id="cropperContainer" style="display: none; max-width: 100%; height: 400px; background: #000; border-radius: 15px; overflow: hidden; margin-bottom: 20px;">
                            <img id="imageToCrop" src="">
                        </div>
                        
                        <?php if (isset($product['image_url']) && !empty($product['image_url'])): ?>
                            <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 10px;">Imagen actual:</p>
                            <img src="../uploads/<?= $product['image_url'] ?>" style="width: 100px; height: 100px; object-fit: cover; border-radius: 10px; margin-bottom: 20px;">
                        <?php endif; ?>
                    </div>

                    <button type="button" id="saveProductBtn" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 15px;">
                        <?= $action === 'edit' ? 'Guardar Cambios' : 'Crear Producto' ?>
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        let cropper;
        const imageInput = document.getElementById('imageInput');
        const imageToCrop = document.getElementById('imageToCrop');
        const cropperContainer = document.getElementById('cropperContainer');
        const saveBtn = document.getElementById('saveProductBtn');
        const productForm = document.getElementById('productForm');
        const croppedImageInput = document.getElementById('croppedImageInput');

        imageInput?.addEventListener('change', (e) => {
            const files = e.target.files;
            if (files && files.length > 0) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    imageToCrop.src = event.target.result;
                    cropperContainer.style.display = 'block';
                    if (cropper) cropper.destroy();
                    cropper = new Cropper(imageToCrop, {
                        aspectRatio: 1, // Square for sneakers
                        viewMode: 2,
                        autoCropArea: 1,
                    });
                };
                reader.readAsDataURL(files[0]);
            }
        });

        saveBtn?.addEventListener('click', () => {
            if (cropper) {
                // Get cropped canvas and convert to Base64
                const canvas = cropper.getCroppedCanvas({
                    width: 800,
                    height: 800
                });
                croppedImageInput.value = canvas.toDataURL('image/jpeg', 0.8); // Optimization
            }
            productForm.submit();
        });
    </script>
</body>
</html>
