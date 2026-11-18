/**
 * Dress Point Basic - Vanilla JavaScript (ES6)
 * Handles Mobile Menu, Cart Notification Toast, and Image Gallery Switching
 */

document.addEventListener('DOMContentLoaded', function() {
    initMobileNav();
    initGallerySwitcher();
    updateCartBadge();
});

/* ==============================================================
   1. Toast Notification Utility
   Shows a temporary pop-up message when an item is added to the cart
   ============================================================== */
function showToast(message, type = 'success') {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.innerHTML = `
        <span style="font-size: 1.2rem; color: var(--accent);">&#10004;</span>
        <span>${message}</span>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3200);
}

/* ==============================================================
   2. Cart Operations
   Sends an AJAX request to cart_action.php to add an item to the bag
   ============================================================== */
function addToCart(productId, name, price, size, image, quantity = 1) {
    if (!size) {
        showToast('Please select a size first!', 'warning');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_id', productId);
    formData.append('name', name);
    formData.append('price', price);
    formData.append('size', size);
    formData.append('image', image);
    formData.append('quantity', quantity);

    fetch('/dresspoint_basic/cart_action.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            updateCartBadge(data.cart_count);
            showToast(`Added <b>${name}</b> (${size}) to your bag!`);
        } else {
            showToast(data.message || 'Error adding item to bag', 'error');
        }
    })
    .catch(err => {
        console.warn('Cart update note:', err);
    });
}

/**
 * Updates the item counter badge in the header
 */
function updateCartBadge(count) {
    const badges = document.querySelectorAll('.cart-badge');
    badges.forEach(badge => {
        if (count !== undefined) {
            badge.textContent = count;
        }
    });
}

/* ==============================================================
   3. Mobile Navigation Toggle
   Expands or collapses the mobile menu on small screens
   ============================================================== */
function initMobileNav() {
    const toggleBtn = document.getElementById('mobileNavToggle');
    const mainNav = document.getElementById('mainNav');

    if (!toggleBtn || !mainNav) return;

    toggleBtn.addEventListener('click', function() {
        mainNav.classList.toggle('open');
        this.innerHTML = mainNav.classList.contains('open') ? '&times;' : '&#9776;';
    });

    document.addEventListener('click', function(e) {
        if (!mainNav.contains(e.target) && !toggleBtn.contains(e.target)) {
            mainNav.classList.remove('open');
            toggleBtn.innerHTML = '&#9776;';
        }
    });
}

/* ==============================================================
   4. Product Gallery Thumbnail Switcher
   Switches the large photo on product.php when a thumbnail is clicked
   ============================================================== */
function initGallerySwitcher() {
    const mainImg = document.getElementById('mainProductImg');
    const thumbs = document.querySelectorAll('.thumb-item');

    if (mainImg && thumbs.length > 0) {
        thumbs.forEach(thumb => {
            thumb.addEventListener('click', function() {
                thumbs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                const newSrc = this.querySelector('img').getAttribute('src');
                mainImg.setAttribute('src', newSrc);
            });
        });
    }
}
