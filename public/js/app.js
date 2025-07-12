// Sidebar toggle for mobile (no Alpine.js required)
document.addEventListener('DOMContentLoaded', function() {
    var menuBtn = document.querySelector('.navbar-menu button');
    var sidebar = document.querySelector('.sidebar');
    var overlay = document.querySelector('.sidebar-overlay');

    if (menuBtn && sidebar) {
        menuBtn.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.toggle('open');
            if (overlay) overlay.classList.toggle('open');
        });
    }
    if (overlay) {
        overlay.addEventListener('click', function() {
            if (sidebar) sidebar.classList.remove('open');
            overlay.classList.remove('open');
        });
    }
    // Hide sidebar on resize to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024 && sidebar) {
            sidebar.classList.remove('open');
            if (overlay) overlay.classList.remove('open');
        }
    });
});

// Retailer Cart Logic
let cart = [];

function updateCartCount() {
    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        cartCount.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
    }
}

function updateCartSidebar() {
    const cartSidebar = document.getElementById('cart-sidebar');
    const cartItemsList = document.getElementById('cart-items-list');
    const cartTotal = document.getElementById('cart-total');
    
    if (!cartSidebar || !cartItemsList || !cartTotal) return;
    
    cartItemsList.innerHTML = '';
    let total = 0;
    cart.forEach(item => {
        const div = document.createElement('div');
        div.className = 'flex justify-between items-center mb-2';
        div.innerHTML = `
            <div>
                <span class='font-bold'>${item.name}</span> x <span>${item.quantity}</span>
            </div>
            <div>UGX ${(item.price * item.quantity).toLocaleString(undefined, {minimumFractionDigits:2})}</div>
            <button class='ml-2 text-red-600 font-bold remove-cart-item-btn' data-id='${item.id}'>&times;</button>
        `;
        cartItemsList.appendChild(div);
        total += item.price * item.quantity;
    });
    cartTotal.textContent = `UGX ${total.toLocaleString(undefined, {minimumFractionDigits:2})}`;
    // Remove item event
    cartItemsList.querySelectorAll('.remove-cart-item-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = parseInt(this.getAttribute('data-id'));
            cart = cart.filter(item => item.id !== id);
            updateCartCount();
            updateCartSidebar();
        });
    });
    showCheckoutForm();
}

function showCheckoutForm() {
    const form = document.getElementById('checkout-form');
    if (form) {
    if (cart.length > 0) {
        form.style.display = '';
    } else {
        form.style.display = 'none';
        }
    }
}

function showNotification(message, type = 'success') {
    let notif = document.getElementById('retailer-toast');
    if (!notif) {
        notif = document.createElement('div');
        notif.id = 'retailer-toast';
        notif.style.position = 'fixed';
        notif.style.top = '1.5rem';
        notif.style.right = '1.5rem';
        notif.style.zIndex = 9999;
        notif.style.padding = '1rem 2rem';
        notif.style.borderRadius = '0.5rem';
        notif.style.fontWeight = 'bold';
        notif.style.boxShadow = '0 2px 8px rgba(0,0,0,0.15)';
        document.body.appendChild(notif);
    }
    notif.textContent = message;
    notif.style.background = type === 'success' ? '#22c55e' : '#ef4444';
    notif.style.color = '#fff';
    notif.style.display = 'block';
    setTimeout(() => { notif.style.display = 'none'; }, 2000);
}

document.addEventListener('DOMContentLoaded', function() {
    // Add to Cart
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.product-card');
            if (!card) return;
            
            const id = parseInt(card.getAttribute('data-product-id'));
            const nameElement = card.querySelector('.font-bold.text-lg');
            const priceElement = card.querySelector('.text-blue-600.font-bold');
            
            if (!nameElement || !priceElement) return;
            
            const name = nameElement.textContent;
            const price = parseFloat(priceElement.textContent.replace('UGX','').replace(/,/g,''));
            let item = cart.find(i => i.id === id);
            if (item) {
                item.quantity += 1;
            } else {
                cart.push({id, name, price, quantity: 1});
            }
            updateCartCount();
            updateCartSidebar();
            showNotification('Added to cart!');
        });
    });
    
    // Cart sidebar toggle
    const cartSidebar = document.getElementById('cart-sidebar');
    const cartToggleBtn = document.getElementById('cart-toggle-btn');
    if (cartToggleBtn && cartSidebar) {
        cartToggleBtn.addEventListener('click', function() {
            cartSidebar.style.display = 'block';
            setTimeout(() => cartSidebar.classList.remove('translate-x-full'), 10);
            updateCartSidebar();
        });
    }
    
    const cartCloseBtn = document.getElementById('cart-close-btn');
    if (cartCloseBtn && cartSidebar) {
        cartCloseBtn.addEventListener('click', function() {
            cartSidebar.classList.add('translate-x-full');
            setTimeout(() => cartSidebar.style.display = 'none', 300);
        });
    }
    
    // Place Order (frontend only)
    const placeOrderBtn = document.getElementById('place-order-btn');
    if (placeOrderBtn) {
        placeOrderBtn.addEventListener('click', async function() {
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }
            // Send order to backend (no delivery details needed)
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    throw new Error('CSRF token not found');
                }
                
                const res = await fetch('/retailer/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                    },
                    body: JSON.stringify({
                        cart: cart
                    })
                });
                const data = await res.json();
                if (data.success) {
                    showNotification('Order placed successfully!');
                    cart = [];
                    updateCartCount();
                    updateCartSidebar();
                    const cartSidebar = document.getElementById('cart-sidebar');
                    if (cartSidebar) {
                        cartSidebar.classList.add('translate-x-full');
                        setTimeout(() => cartSidebar.style.display = 'none', 300);
                    }
                } else {
                    showNotification(data.message || 'Order failed.', 'error');
                }
            } catch (e) {
                alert('Order failed. Please try again.');
            }
        });
    }
}); 