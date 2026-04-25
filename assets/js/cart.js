document.addEventListener('DOMContentLoaded', () => {
    const cartSidebar = document.getElementById('cartSidebar');
    const openCartBtn = document.getElementById('openCart');
    const closeCartBtn = document.getElementById('closeCart');
    const cartItemsContainer = document.getElementById('cartItems');
    const cartCount = document.getElementById('cartCount');
    const cartTotal = document.getElementById('cartTotal');
    const checkoutBtn = document.getElementById('checkoutBtn');

    let cart = JSON.parse(localStorage.getItem('sneakerCart')) || [];

    const updateCart = () => {
        localStorage.setItem('sneakerCart', JSON.stringify(cart));
        renderCart();
    };

    const renderCart = () => {
        cartItemsContainer.innerHTML = '';
        let total = 0;
        let count = 0;

        cart.forEach((item, index) => {
            total += item.price * item.quantity;
            count += item.quantity;

            const itemEl = document.createElement('div');
            itemEl.className = 'glass';
            itemEl.style.display = 'flex';
            itemEl.style.gap = '15px';
            itemEl.style.padding = '15px';
            itemEl.style.borderRadius = '15px';
            itemEl.style.marginBottom = '15px';
            itemEl.style.alignItems = 'center';

            itemEl.innerHTML = `
                <img src="${item.image ? 'uploads/' + item.image : 'https://via.placeholder.com/80'}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 10px;">
                <div style="flex: 1;">
                    <h4 style="font-size: 0.9rem; margin-bottom: 5px;">${item.name}</h4>
                    <p style="font-size: 0.8rem; color: var(--secondary); font-weight: 600;">$${item.price.toLocaleString()}</p>
                    <div style="display: flex; align-items: center; gap: 10px; margin-top: 5px;">
                        <button class="qty-btn" onclick="changeQty(${index}, -1)" style="background: none; border: 1px solid var(--border); color: white; border-radius: 4px; cursor: pointer; width: 20px; height: 20px;">-</button>
                        <span style="font-size: 0.8rem;">${item.quantity}</span>
                        <button class="qty-btn" onclick="changeQty(${index}, 1)" style="background: none; border: 1px solid var(--border); color: white; border-radius: 4px; cursor: pointer; width: 20px; height: 20px;">+</button>
                    </div>
                </div>
                <i class="fa-solid fa-trash" onclick="removeItem(${index})" style="color: #ef4444; cursor: pointer; font-size: 0.8rem;"></i>
            `;
            cartItemsContainer.appendChild(itemEl);
        });

        cartCount.innerText = count;
        cartTotal.innerText = `$${total.toLocaleString()}`;

        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<p style="text-align: center; color: var(--text-muted); margin-top: 50px;">Tu carrito está vacío</p>';
        }
    };

    window.changeQty = (index, delta) => {
        cart[index].quantity += delta;
        if (cart[index].quantity < 1) cart[index].quantity = 1;
        updateCart();
    };

    window.removeItem = (index) => {
        cart.splice(index, 1);
        updateCart();
    };

    // Open/Close Sidebar
    openCartBtn.addEventListener('click', () => {
        cartSidebar.style.right = '0';
    });

    closeCartBtn.addEventListener('click', () => {
        cartSidebar.style.right = '-400px';
    });

    // Add to cart
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const price = parseFloat(btn.getAttribute('data-price'));
            const image = btn.getAttribute('data-image');

            const existing = cart.find(item => item.id === id);
            if (existing) {
                existing.quantity += 1;
            } else {
                cart.push({ id, name, price, image, quantity: 1 });
            }
            updateCart();
            cartSidebar.style.right = '0';
        });
    });

    // WhatsApp Checkout
    checkoutBtn.addEventListener('click', () => {
        if (cart.length === 0) return alert('El carrito está vacío');

        let text = '¡Hola! Me gustaría realizar un pedido:\n\n';
        let total = 0;

        cart.forEach(item => {
            text += `• ${item.name} (x${item.quantity}) - $${(item.price * item.quantity).toLocaleString()}\n`;
            total += item.price * item.quantity;
        });

        text += `\n*Total a pagar: $${total.toLocaleString()}*\n\nPor favor, confírmame disponibilidad.`;
        
        const encodedText = encodeURIComponent(text);
        const phoneNumber = '573000000000'; // Same as in config/db.php
        const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodedText}`;

        window.open(whatsappUrl, '_blank');
    });

    renderCart();
});
