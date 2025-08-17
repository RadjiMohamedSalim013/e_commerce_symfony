class SearchManager {
    constructor() {
        this.searchInput = document.getElementById('search-input');
        this.searchResults = document.getElementById('search-results');
        this.debounceTimer = null;
        
        this.init();
    }

    init() {
        if (!this.searchInput || !this.searchResults) return;

        this.searchInput.addEventListener('input', (e) => this.handleSearch(e));
        this.searchInput.addEventListener('focus', () => this.showResults());
        
        // Fermer les résultats quand on clique ailleurs
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-container')) {
                this.hideResults();
            }
        });
    }

    handleSearch(event) {
        const query = event.target.value.trim();
        
        if (query.length < 2) {
            this.hideResults();
            return;
        }

        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => {
            this.performSearch(query);
        }, 300);
    }

    async performSearch(query) {
        try {
            this.showLoading();
            
            const response = await fetch(`/search?q=${encodeURIComponent(query)}`);
            
            if (!response.ok) {
                throw new Error('Erreur de réseau');
            }
            
            const products = await response.json();
            
            this.displayResults(products, query);
            
        } catch (error) {
            console.error('Erreur lors de la recherche:', error);
            this.showError();
        }
    }

    displayResults(products, query) {
        this.searchResults.innerHTML = '';
        
        if (products.length === 0) {
            this.showNoResults(query);
            return;
        }

        products.forEach(product => {
            const item = this.createResultItem(product);
            this.searchResults.appendChild(item);
        });

        this.showResults();
    }

    createResultItem(product) {
        const div = document.createElement('div');
        div.className = 'search-result-item';
        
        const imageUrl = product.image 
            ? `/uploads/products/${product.image}` 
            : '/images/no-product.png';
        
        div.innerHTML = `
            <img src="${imageUrl}" alt="${product.name}" class="search-result-image">
            <div class="search-result-info">
                <div class="search-result-name">${product.name}</div>
                <div class="search-result-price">${product.price} FCFA</div>
                <div class="search-result-description">${product.description}</div>
            </div>
        `;
        
        div.addEventListener('click', () => {
            window.location.href = product.url;
        });
        
        return div;
    }

    showLoading() {
        this.searchResults.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Recherche en cours...</div>';
        this.showResults();
    }

    showNoResults(query) {
        this.searchResults.innerHTML = `
            <div class="no-results">
                <i class="fas fa-search" style="font-size: 2rem; color: #ccc; margin-bottom: 10px;"></i>
                <div>Aucun produit trouvé pour "${query}"</div>
                <small style="color: #666;">Essayez avec d'autres termes</small>
            </div>
        `;
        this.showResults();
    }

    showError() {
        this.searchResults.innerHTML = `
            <div class="no-results">
                <i class="fas fa-exclamation-triangle" style="color: #ff6b6b; margin-bottom: 10px;"></i>
                <div>Erreur lors de la recherche</div>
                <small style="color: #666;">Veuillez réessayer</small>
            </div>
        `;
        this.showResults();
    }

    showResults() {
        this.searchResults.style.display = 'block';
    }

    hideResults() {
        this.searchResults.style.display = 'none';
    }
}

// Initialiser la recherche quand le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    new SearchManager();
});
