<?php
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto | SneakerVault</title>
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
        </div>
    </header>

    <main class="container" style="padding: 100px 0;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center;">
            <div>
                <h1 style="font-size: 3.5rem; margin-bottom: 20px;">¿Tienes alguna <span style="color: var(--primary);">pregunta?</span></h1>
                <p style="color: var(--text-muted); font-size: 1.2rem; margin-bottom: 40px;">Estamos aquí para ayudarte. Si necesitas información sobre tallas, envíos o modelos específicos, no dudes en escribirnos.</p>
                
                <div style="display: flex; flex-direction: column; gap: 30px;">
                    <div style="display: flex; gap: 20px; align-items: center;">
                        <div class="glass" style="width: 60px; height: 60px; border-radius: 20px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.5rem;">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <p style="font-weight: 600;">Llámanos</p>
                            <p style="color: var(--text-muted);">+57 300 000 0000</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 20px; align-items: center;">
                        <div class="glass" style="width: 60px; height: 60px; border-radius: 20px; display: flex; align-items: center; justify-content: center; color: var(--secondary); font-size: 1.5rem;">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div>
                            <p style="font-weight: 600;">Escríbenos</p>
                            <p style="color: var(--text-muted);">info@sneakervault.com</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass" style="padding: 50px; border-radius: 40px;">
                <form id="contactForm">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px;">Nombre Completo</label>
                        <input type="text" id="contactName" required placeholder="Tu nombre">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px;">Mensaje</label>
                        <textarea id="contactMessage" rows="5" required placeholder="¿En qué podemos ayudarte?"></textarea>
                    </div>
                    <button type="button" id="sendContactBtn" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 18px;">
                        Enviar a WhatsApp <i class="fa-brands fa-whatsapp"></i>
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('sendContactBtn').addEventListener('click', () => {
            const name = document.getElementById('contactName').value;
            const msg = document.getElementById('contactMessage').value;
            
            if(!name || !msg) return alert('Por favor completa todos los campos');

            const text = `Hola, mi nombre es ${name}. Tengo la siguiente consulta:\n\n${msg}`;
            const encodedText = encodeURIComponent(text);
            const phoneNumber = '573000000000';
            window.open(`https://wa.me/${phoneNumber}?text=${encodedText}`, '_blank');
        });
    </script>
</body>
</html>
