document.addEventListener('alpine:init', () => {
    Alpine.store('cart', {
        items: [],

        load() {
            try {
                this.items = JSON.parse(localStorage.getItem('kta_cart') || '[]');
            } catch (e) {
                this.items = [];
            }
        },

        save() {
            localStorage.setItem('kta_cart', JSON.stringify(this.items));
        },

        get total() {
            return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },

        get count() {
            return this.items.reduce((sum, item) => sum + item.quantity, 0);
        },

        addToCart(product, quantity = 1) {
            this.load();
            const existing = this.items.find(i => i.id === product.id);
            if (existing) {
                existing.quantity += quantity;
            } else {
                this.items.push({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    image: product.image || null,
                    quantity,
                });
            }
            this.save();
        },

        updateQuantity(id, quantity) {
            if (quantity <= 0) {
                this.removeFromCart(id);
                return;
            }
            const item = this.items.find(i => i.id === id);
            if (item) item.quantity = quantity;
            this.save();
        },

        removeFromCart(id) {
            this.items = this.items.filter(i => i.id !== id);
            this.save();
        },

        clearCart() {
            this.items = [];
            this.save();
        },
    });

    // Auto-load cart on init
    Alpine.store('cart').load();
});
