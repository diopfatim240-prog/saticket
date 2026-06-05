// Admin JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {

  // Highlight active menu item based on current page
  const currentPage = new URLSearchParams(window.location.search).get('page') || 'dashboard';
  const currentPagination = new URLSearchParams(window.location.search).get('pagination') || '1';
  const menuItems = document.querySelectorAll('.menu-item');

  menuItems.forEach(item => {
    item.classList.remove('active');
    const link = item.querySelector('a');
    if (link) {
      const href = link.getAttribute('href');
      if (href.includes(`page=${currentPage}`) || (currentPage === 'dashboard' && href === 'admin.php')) {
        item.classList.add('active');
      }
    }
  });

  // Update page title based on current page
  const pageTitles = {
    'dashboard': 'SaticketAdmin',
    'events': 'Gestion des Événements',
    'tickets': 'Gestion des Tickets',
    'users': 'Gestion des Utilisateurs',
    'payments': 'Gestion des Paiements',
    'orders': 'Gestion des Commandes',
    'comments': 'Gestion des Commentaires',
    'promotions': 'Gestion des Promotions',
    'reports': 'Rapports et Analyses',
    'settings': 'Paramètres Système'
  };

  const headerTitle = document.querySelector('.admin-header h1');
  if (headerTitle && pageTitles[currentPage]) {
    headerTitle.textContent = pageTitles[currentPage];
  }

  // Handle pagination links
  const paginationLinks = document.querySelectorAll('.pagination .page-link');
  paginationLinks.forEach(link => {
    const href = link.getAttribute('href');
    if (href && href.includes('pagination=')) {
      const paginationParam = new URL(link.href, window.location.origin).searchParams.get('pagination');
      
      // Mark active page
      if (paginationParam === currentPagination || 
          (paginationParam === '1' && !currentPagination)) {
        link.parentElement.classList.add('active');
      } else {
        link.parentElement.classList.remove('active');
      }
      
      // Handle navigation on click
      link.addEventListener('click', function(e) {
        // Allow normal navigation
        // e.preventDefault() removed to let links work naturally
      });
    }
  });

  // Add loading states for buttons
  const actionButtons = document.querySelectorAll('.btn');
  actionButtons.forEach(button => {
    button.addEventListener('click', function() {
      if (this.classList.contains('btn-success') || this.classList.contains('btn-primary')) {
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Traitement...';
        this.disabled = true;

        // Reset after 2 seconds (for demo purposes)
        setTimeout(() => {
          this.innerHTML = originalText;
          this.disabled = false;
        }, 2000);
      }
    });
  });

  // Auto-hide alerts after 5 seconds
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 300);
    }, 5000);
  });

  // Confirm delete actions
  const deleteButtons = document.querySelectorAll('[data-action="delete"]');
  deleteButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
        e.preventDefault();
      }
    });
  });

  // Initialize tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Handle form submissions with loading states
  const forms = document.querySelectorAll('form');
  forms.forEach(form => {
    form.addEventListener('submit', function() {
      const submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Enregistrement...';
      }
    });
  });

  // Search functionality for tables
  const searchInputs = document.querySelectorAll('.table-search');
  searchInputs.forEach(input => {
    input.addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase();
      const table = this.closest('.admin-table').querySelector('table');
      const rows = table.querySelectorAll('tbody tr');

      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
      });
    });
  });

  // Pagination for tables (basic implementation)
  // Handled by href links in HTML - no JavaScript needed

  // Modal enhancements
  const modals = document.querySelectorAll('.modal');
  modals.forEach(modal => {
    modal.addEventListener('shown.bs.modal', function() {
      // Focus on first input when modal opens
      const firstInput = modal.querySelector('input, select, textarea');
      if (firstInput) {
        firstInput.focus();
      }
    });
  });

  // Settings tabs persistence
  const settingsTabs = document.querySelectorAll('#settingsTabs .nav-link');
  settingsTabs.forEach(tab => {
    tab.addEventListener('click', function() {
      localStorage.setItem('activeSettingsTab', this.id);
    });
  });

  // Restore active settings tab
  const activeTab = localStorage.getItem('activeSettingsTab');
  if (activeTab) {
    const tabElement = document.getElementById(activeTab);
    if (tabElement) {
      tabElement.click();
    }
  }

  // Real-time validation for forms
  const emailInputs = document.querySelectorAll('input[type="email"]');
  emailInputs.forEach(input => {
    input.addEventListener('blur', function() {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (this.value && !emailRegex.test(this.value)) {
        this.classList.add('is-invalid');
      } else {
        this.classList.remove('is-invalid');
      }
    });
  });

  // Number input validation
  const numberInputs = document.querySelectorAll('input[type="number"]');
  numberInputs.forEach(input => {
    input.addEventListener('input', function() {
      const min = parseFloat(this.min);
      const max = parseFloat(this.max);
      const value = parseFloat(this.value);

      if ((min && value < min) || (max && value > max)) {
        this.classList.add('is-invalid');
      } else {
        this.classList.remove('is-invalid');
      }
    });
  });

  // Interactive buttons without existing onclick
  const allButtons = document.querySelectorAll('.btn:not([onclick]):not([data-bs-toggle])');
  allButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      const title = this.getAttribute('title') || this.textContent.trim();
      const btnClass = this.className;
      
      // Ignore buttons that already have functionality
      if (btnClass.includes('btn-secondary') && btnClass.includes('btn-sm')) return;
      
      // Handle success buttons (Export, Rapport, Download)
      if (btnClass.includes('btn-success')) {
        e.preventDefault();
        alert('Action: ' + title + ' - Fonctionnalité en cours d\'implémentation');
      }
      // Handle primary buttons
      else if (btnClass.includes('btn-primary') && !btnClass.includes('btn-sm')) {
        // Main action buttons
        const text = this.textContent.trim();
        if (text.includes('Générer') || text.includes('Créer')) {
          e.preventDefault();
          alert('Création en cours... ' + text);
        }
      }
      // Handle icon buttons in actions
      else if (btnClass.includes('btn-sm')) {
        const icon = this.querySelector('i');
        if (icon) {
          if (icon.className.includes('bi-eye')) {
            e.preventDefault();
            alert('Affichage des détails...');
          } else if (icon.className.includes('bi-pencil')) {
            e.preventDefault();
            alert('Modification en cours...');
          } else if (icon.className.includes('bi-trash')) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
              alert('Élément supprimé avec succès');
            }
          } else if (icon.className.includes('bi-printer')) {
            e.preventDefault();
            alert('Impression en cours...');
          } else if (icon.className.includes('bi-arrow-counterclockwise')) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir effectuer un remboursement ?')) {
              alert('Remboursement en cours...');
            }
          } else if (icon.className.includes('bi-check')) {
            e.preventDefault();
            alert('Validation en cours...');
          } else if (icon.className.includes('bi-x')) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir annuler ?')) {
              alert('Annulation effectuée');
            }
          } else if (icon.className.includes('bi-receipt')) {
            e.preventDefault();
            alert('Récupération du reçu...');
          } else if (icon.className.includes('bi-pause')) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir suspendre cet utilisateur ?')) {
              alert('Utilisateur suspendu');
            }
          } else if (icon.className.includes('bi-box-seam')) {
            e.preventDefault();
            if (confirm('Marquer cette commande comme expédiée ?')) {
              alert('Commande marquée comme expédiée');
            }
          } else if (icon.className.includes('bi-check-circle')) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de valider cet élément ?')) {
              alert('Validation effectuée');
            }
          } else if (icon.className.includes('bi-flag')) {
            e.preventDefault();
            alert('Élément signalé pour révision');
          } else if (icon.className.includes('bi-ban')) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir bloquer cet utilisateur ?')) {
              alert('Utilisateur bloqué');
            }
          } else if (icon.className.includes('bi-toggle-on')) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de désactiver cette promotion ?')) {
              alert('Promotion désactivée');
            }
          } else if (icon.className.includes('bi-toggle-off')) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr d\'activer cette promotion ?')) {
              alert('Promotion activée');
            }
          } else if (icon.className.includes('bi-clipboard')) {
            e.preventDefault();
            alert('Code copié dans le presse-papiers !');
          }
        }
      }
    });
  });

});