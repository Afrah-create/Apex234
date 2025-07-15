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
            sidebar.classList.remove('open');
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

// Load cart from backend on page load
function loadCartFromServer() {
    fetch('/api/cart')
      .then(res => res.json())
      .then(data => {
        cart = data.cart || [];
        updateCartCount();
        updateCartSidebar();
      });
}

// Save cart to backend after every change
function saveCartToServer() {
    fetch('/api/cart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ cart })
    })
    .then(res => res.json())
    .then(data => {
        console.log('Cart save response:', data);
    })
    .catch(err => {
        console.error('Cart save error:', err);
    });
}

function updateCartCount() {
    document.getElementById('cart-count').textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
}

function updateCartSidebar() {
    const cartSidebar = document.getElementById('cart-sidebar');
    const cartItemsList = document.getElementById('cart-items-list');
    const cartTotal = document.getElementById('cart-total');
    cartItemsList.innerHTML = '';
    let total = 0;
    cart.forEach(item => {
        const div = document.createElement('div');
        div.className = 'flex justify-between items-center mb-3 p-2 rounded-lg bg-gray-50 shadow-sm';
        div.innerHTML = `
            <div class='flex items-center gap-2'>
                <span class='font-bold text-lg text-blue-700'>${item.name}</span>
                <span class='inline-block bg-green-600 text-white text-xs font-bold rounded-full px-3 py-1 ml-1 shadow'>x${item.quantity}</span>
            </div>
            <div class='text-right'>
                <div class='text-blue-700 font-bold'>UGX ${(item.price * item.quantity).toLocaleString(undefined, {minimumFractionDigits:2})}</div>
                <button class='ml-2 text-red-600 font-bold remove-cart-item-btn' data-id='${item.id}' title='Remove'>&times;</button>
            </div>
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
            saveCartToServer(); // Save after remove
        });
    });
    showCheckoutForm();
}

function showCheckoutForm() {
    const form = document.getElementById('checkout-form');
    if (cart.length > 0) {
        form.style.display = '';
    } else {
        form.style.display = 'none';
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
    // Load cart from backend on page load
    loadCartFromServer();

    // Add to Cart
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.product-card');
            const id = parseInt(card.getAttribute('data-product-id'));
            const name = card.querySelector('.font-bold.text-lg').textContent;
            const price = parseFloat(card.querySelector('.text-blue-600.font-bold').textContent.replace('UGX','').replace(/,/g,''));
            const qtyInput = card.querySelector('.quantity-input');
            let quantity = 1;
            if (qtyInput) {
                quantity = parseInt(qtyInput.value) || 1;
                if (quantity < 1) quantity = 1;
            }
            let item = cart.find(i => i.id === id);
            if (item) {
                item.quantity += quantity;
            } else {
                cart.push({id, name, price, quantity});
            }
            updateCartCount();
            updateCartSidebar();
            saveCartToServer(); // Save after add
            showNotification('Added to cart!');
            if (qtyInput) qtyInput.value = 1;
        });
    });

    // Save to Cart
    document.querySelectorAll('.save-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.product-card');
            const id = parseInt(card.getAttribute('data-product-id'));
            const name = card.querySelector('.font-bold.text-lg').textContent;
            const price = parseFloat(card.querySelector('.text-blue-600.font-bold').textContent.replace('UGX','').replace(/,/g,''));
            let item = cart.find(i => i.id === id);
            if (item) {
                showNotification('Already saved to cart!');
            } else {
                cart.push({id, name, price, quantity: 1});
                updateCartCount();
                updateCartSidebar();
                saveCartToServer();
                showNotification('Saved to cart!');
            }
        });
    });

    // Cart sidebar toggle
    const cartSidebar = document.getElementById('cart-sidebar');
    const cartToggleBtn = document.getElementById('cart-toggle-btn');
    if (cartToggleBtn) {
        cartToggleBtn.addEventListener('click', function() {
            cartSidebar.style.display = 'block';
            setTimeout(() => cartSidebar.classList.remove('translate-x-full'), 10);
            updateCartSidebar();
        });
    }
    const cartCloseBtn = document.getElementById('cart-close-btn');
    if (cartCloseBtn) {
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
                const res = await fetch('/retailer/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                    saveCartToServer(); // Save after clear
                    document.getElementById('cart-sidebar').classList.add('translate-x-full');
                    setTimeout(() => document.getElementById('cart-sidebar').style.display = 'none', 300);
                } else {
                    showNotification(data.message || 'Order failed.', 'error');
                }
            } catch (e) {
                alert('Order failed. Please try again.');
            }
        });
    }
}); 