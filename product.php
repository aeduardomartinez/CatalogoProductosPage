<?php
require_once 'config/db.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

// Fetch product
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: index.php');
    exit;
}

// Fetch reviews
$stmt = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC");
$stmt->execute([$id]);
$reviews = $stmt->fetchAll();

// Fetch recommendations (same category or brand)
$stmt = $pdo->prepare("SELECT * FROM products WHERE (category_id = ? OR brand = ?) AND id != ? LIMIT 4");
$stmt->execute([$product['category_id'], $product['brand'], $id]);
$recommendations = $stmt->fetchAll();

// Handle Review Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $name = $_POST['customer_name'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    
    $stmt = $pdo->prepare("INSERT INTO reviews (product_id, customer_name, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id, $name, $rating, $comment]);
    header("Location: product.php?id=$id&rev=ok");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $product['name'] ?> | SneakerVault</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="glass">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <a href="index.php" class="logo">SNEAKERVAULT</a>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="index.php#catalogo">Catálogo</a></li>
                </ul>
            </nav>
            <div class="header-actions" style="display: flex; align-items: center; gap: 20px;">
                <div style="position: relative; cursor: pointer;" id="openCart">
                    <i class="fa-solid fa-cart-shopping" style="font-size: 1.2rem;"></i>
                    <span class="cart-badge" id="cartCount">0</span>
                </div>
            </div>
        </div>
    </header>

    <main class="container" style="padding: 60px 0;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; margin-bottom: 80px;">
            <div>
                <img src="<?= $product['image_url'] ? 'uploads/'.$product['image_url'] : 'https://via.placeholder.com/600x600' ?>" alt="<?= $product['name'] ?>" style="width: 100%; border-radius: 40px; box-shadow: var(--shadow);">
            </div>
            <div>
                <p style="color: var(--primary); font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px;"><?= $product['brand'] ?> • <?= $product['category_name'] ?></p>
                <h1 style="font-size: 3.5rem; line-height: 1.1; margin-bottom: 20px;"><?= $product['name'] ?></h1>
                
                <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px;">
                    <div style="font-size: 2.5rem; font-weight: 800; color: var(--secondary);">
                        <?php if ($product['discount_price']): ?>
                            <span style="text-decoration: line-through; color: var(--text-muted); font-size: 1.5rem; margin-right: 15px;"><?= CURRENCY ?><?= number_format($product['price'], 2) ?></span>
                            <?= CURRENCY ?><?= number_format($product['discount_price'], 2) ?>
                        <?php else: ?>
                            <?= CURRENCY ?><?= number_format($product['price'], 2) ?>
                        <?php endif; ?>
                    </div>
                    <?php if ($product['discount_price']): ?>
                        <span class="glass" style="padding: 5px 15px; border-radius: 50px; color: var(--accent); font-weight: 600;">OFERTA</span>
                    <?php endif; ?>
                </div>

                <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 40px;"><?= nl2br($product['description']) ?></p>

                <div style="display: flex; gap: 20px; margin-bottom: 40px;">
                    <button class="btn btn-primary add-to-cart" 
                        data-id="<?= $product['id'] ?>" 
                        data-name="<?= $product['name'] ?>" 
                        data-price="<?= $product['discount_price'] ?? $product['price'] ?>"
                        data-image="<?= $product['image_url'] ?>"
                        style="flex: 1; justify-content: center; padding: 18px; font-size: 1.1rem;">
                        <i class="fa-solid fa-cart-plus"></i> Añadir al Carrito
                    </button>
                </div>

                <div class="glass" style="padding: 20px; border-radius: 20px; display: flex; align-items: center; gap: 15px;">
                    <i class="fa-brands fa-whatsapp" style="font-size: 2rem; color: #25d366;"></i>
                    <div>
                        <p style="font-weight: 600;">Compra Segura por WhatsApp</p>
                        <p style="font-size: 0.8rem; color: var(--text-muted);">Asesoría personalizada y pagos rápidos.</p>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 60px;">
            <!-- Reviews Section -->
            <section>
                <h3 style="font-size: 2rem; margin-bottom: 30px;">Opiniones de Clientes</h3>
                <?php if (empty($reviews)): ?>
                    <p style="color: var(--text-muted);">Aún no hay opiniones para este producto. ¡Sé el primero!</p>
                <?php else: ?>
                    <?php foreach ($reviews as $rev): ?>
                        <div class="glass" style="padding: 25px; border-radius: 20px; margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <h4 style="font-weight: 600;"><?= $rev['customer_name'] ?></h4>
                                <div style="color: var(--accent);">
                                    <?php for($i=0; $i<$rev['rating']; $i++): ?> <i class="fa-solid fa-star"></i> <?php endfor; ?>
                                </div>
                            </div>
                            <p style="color: var(--text-muted);"><?= nl2br($rev['comment']) ?></p>
                            <small style="color: rgba(255,255,255,0.2); display: block; margin-top: 10px;"><?= date('d/m/Y', strtotime($rev['created_at'])) ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="glass" style="padding: 30px; border-radius: 25px; margin-top: 40px;">
                    <h4 style="margin-bottom: 20px;">Deja tu Valoración</h4>
                    <form method="POST">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <input type="text" name="customer_name" placeholder="Tu Nombre" required>
                            <select name="rating" required>
                                <option value="5">⭐⭐⭐⭐⭐ (Excelente)</option>
                                <option value="4">⭐⭐⭐⭐ (Muy Bueno)</option>
                                <option value="3">⭐⭐⭐ (Bueno)</option>
                                <option value="2">⭐⭐ (Regular)</option>
                                <option value="1">⭐ (Pobre)</option>
                            </select>
                        </div>
                        <textarea name="comment" rows="4" placeholder="¿Qué te parecieron estas zapatillas?" style="margin-bottom: 20px;"></textarea>
                        <button type="submit" name="submit_review" class="btn btn-primary">Enviar Opinión</button>
                    </form>
                </div>
            </section>

            <!-- Recommendations Section -->
            <section>
                <h3 style="font-size: 2rem; margin-bottom: 30px;">Recomendados</h3>
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <?php foreach ($recommendations as $rec): ?>
                        <a href="product.php?id=<?= $rec['id'] ?>" style="text-decoration: none; color: inherit;">
                            <div class="glass" style="padding: 15px; border-radius: 15px; display: flex; gap: 15px; align-items: center; transition: var(--transition);" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                                <img src="<?= $rec['image_url'] ? 'uploads/'.$rec['image_url'] : 'https://via.placeholder.com/80' ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 10px;">
                                <div>
                                    <h4 style="font-size: 1rem;"><?= $rec['name'] ?></h4>
                                    <p style="color: var(--secondary); font-weight: 700;"><?= CURRENCY ?><?= number_format($rec['discount_price'] ?? $rec['price'], 2) ?></p>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </main>

    <!-- Shopping Cart Sidebar (Same as index) -->
    <div id="cartSidebar" class="glass" style="position: fixed; top: 0; right: -400px; width: 400px; height: 100vh; z-index: 2000; padding: 30px; transition: var(--transition); display: flex; flex-direction: column;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2>Tu Carrito</h2>
            <i class="fa-solid fa-xmark" id="closeCart" style="cursor: pointer; font-size: 1.5rem;"></i>
        </div>
        <div id="cartItems" style="flex: 1; overflow-y: auto;"></div>
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--border);">
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <span style="font-size: 1.2rem; font-weight: 600;">Total:</span>
                <span id="cartTotal" style="font-size: 1.5rem; font-weight: 800; color: var(--secondary);"><?= CURRENCY ?>0.00</span>
            </div>
            <button id="checkoutBtn" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 15px;">
                Comprar por WhatsApp <i class="fa-brands fa-whatsapp"></i>
            </button>
        </div>
    </div>

    <script src="assets/js/cart.js"></script>
</body>
</html>
