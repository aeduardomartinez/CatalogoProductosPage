<?php
require_once 'config/db.php';

// Fetch featured products
$stmt = $pdo->query("SELECT * FROM products LIMIT 8");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SneakerVault | Exclusividad en cada paso</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
                    <li><a href="#catalogo">Catálogo</a></li>
                    <li><a href="#promociones">Ofertas</a></li>
                </ul>
            </nav>
            <div class="header-actions" style="display: flex; align-items: center; gap: 20px;">
                <div style="position: relative; cursor: pointer;" id="openCart">
                    <i class="fa-solid fa-cart-shopping" style="font-size: 1.2rem;"></i>
                    <span class="cart-badge" id="cartCount">0</span>
                </div>
                <a href="admin/login.php" class="btn btn-primary"
                    style="padding: 8px 16px; font-size: 0.9rem;">Admin</a>
            </div>
        </div>
    </header>

    <section class="hero"
        style="padding: 100px 0; background: radial-gradient(circle at top right, rgba(99, 102, 241, 0.15), transparent);">
        <div class="container" style="display: grid; grid-template-columns: 1fr 1fr; align-items: center; gap: 40px;">
            <div>
                <h1 style="font-size: 4rem; line-height: 1.1; margin-bottom: 20px;">Estilo que <span
                        style="color: var(--primary);">Define</span> Tu Camino.</h1>
                <p style="color: var(--text-muted); font-size: 1.2rem; margin-bottom: 30px;">Descubre la colección más
                    exclusiva de zapatillas urbanas y deportivas. Calidad premium garantizada.</p>
                <a href="#catalogo" class="btn btn-primary" style="font-size: 1.1rem; padding: 15px 35px;">Explorar
                    Catálogo <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div style="position: relative;">
                <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                    alt="Sneaker Hero"
                    style="width: 100%; border-radius: 30px; box-shadow: 0 30px 60px -12px rgba(0,0,0,0.5);">
                <div class="glass"
                    style="position: absolute; bottom: -20px; left: -20px; padding: 20px; border-radius: 20px; max-width: 200px;">
                    <p style="font-weight: 800; font-size: 1.5rem; color: var(--secondary);">20% OFF</p>
                    <p style="font-size: 0.9rem;">En tu primera compra</p>
                </div>
            </div>
        </div>
    </section>

    <main class="container" id="catalogo" style="padding: 80px 0;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 50px;">
            <div>
                <h2 style="font-size: 2.5rem; margin-bottom: 10px;">Catálogo Destacado</h2>
                <p style="color: var(--text-muted);">Los mas vendidos de la temporada</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <button class="btn glass">Todos</button>
                <button class="btn glass">Nike</button>
                <button class="btn glass">Adidas</button>
                <button class="btn glass">Jordan</button>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px;">
            <?php if (empty($products)): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 50px;" class="glass">
                    <i class="fa-solid fa-box-open"
                        style="font-size: 3rem; margin-bottom: 20px; color: var(--text-muted);"></i>
                    <h3>No hay productos disponibles por ahora.</h3>
                    <p>Vuelve pronto para ver nuestras novedades.</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card glass">
                        <a href="product.php?id=<?= $product['id'] ?>" style="text-decoration: none; color: inherit;">
                            <img src="<?= $product['image_url'] ? 'uploads/' . $product['image_url'] : 'https://via.placeholder.com/400x400' ?>"
                                alt="<?= $product['name'] ?>" class="product-image">
                        </a>
                        <div class="product-info">
                            <p style="color: var(--primary); font-weight: 600; font-size: 0.8rem; text-transform: uppercase;">
                                <?= $product['brand'] ?>
                            </p>
                            <a href="product.php?id=<?= $product['id'] ?>" style="text-decoration: none; color: inherit;">
                                <h3 style="margin: 5px 0 15px; font-size: 1.25rem;"><?= $product['name'] ?></h3>
                            </a>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div class="product-price">
                                    <?php if ($product['discount_price']): ?>
                                        <span class="old-price"><?= CURRENCY ?><?= number_format($product['price'], 2) ?></span>
                                        <?= CURRENCY ?>             <?= number_format($product['discount_price'], 2) ?>
                                    <?php else: ?>
                                        <?= CURRENCY ?>             <?= number_format($product['price'], 2) ?>
                                    <?php endif; ?>
                                </div>
                                <button class="btn btn-primary add-to-cart" data-id="<?= $product['id'] ?>"
                                    data-name="<?= $product['name'] ?>"
                                    data-price="<?= $product['discount_price'] ?? $product['price'] ?>"
                                    data-image="<?= $product['image_url'] ?>" style="padding: 10px;">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Shopping Cart Sidebar -->
    <div id="cartSidebar" class="glass"
        style="position: fixed; top: 0; right: -400px; width: 400px; height: 100vh; z-index: 2000; padding: 30px; transition: var(--transition); display: flex; flex-direction: column;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2>Tu Carrito</h2>
            <i class="fa-solid fa-xmark" id="closeCart" style="cursor: pointer; font-size: 1.5rem;"></i>
        </div>
        <div id="cartItems" style="flex: 1; overflow-y: auto;">
            <!-- Cart items will be injected here -->
        </div>
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--border);">
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <span style="font-size: 1.2rem; font-weight: 600;">Total:</span>
                <span id="cartTotal"
                    style="font-size: 1.5rem; font-weight: 800; color: var(--secondary);"><?= CURRENCY ?>0.00</span>
            </div>
            <button id="checkoutBtn" class="btn btn-primary"
                style="width: 100%; justify-content: center; padding: 15px;">
                Comprar por WhatsApp <i class="fa-brands fa-whatsapp"></i>
            </button>
        </div>
    </div>

    <footer style="padding: 60px 0; border-top: 1px solid var(--border); margin-top: 80px;">
        <div class="container" style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 40px;">
            <div>
                <a href="#" class="logo" style="margin-bottom: 20px; display: block;">SNEAKERVAULT</a>
                <p style="color: var(--text-muted);">Tu destino favorito para calzado exclusivo. Llevamos el estilo a tu
                    puerta con la seguridad de WhatsApp.</p>
            </div>
            <div>
                <h4>Enlaces</h4>
                <ul style="list-style: none; margin-top: 15px;">
                    <li><a href="#" style="color: var(--text-muted); text-decoration: none;">Inicio</a></li>
                    <li><a href="#" style="color: var(--text-muted); text-decoration: none;">Nosotros</a></li>
                    <li><a href="#" style="color: var(--text-muted); text-decoration: none;">Contacto</a></li>
                </ul>
            </div>
            <div>
                <h4>Social</h4>
                <div style="display: flex; gap: 15px; margin-top: 15px;">
                    <a href="#" class="glass"
                        style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: var(--text);"><i
                            class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="glass"
                        style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: var(--text);"><i
                            class="fa-brands fa-facebook"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="assets/js/cart.js"></script>
</body>

</html>