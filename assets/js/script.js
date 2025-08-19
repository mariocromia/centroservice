// Centro Service - Modern JavaScript with Animations and Interactions

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

// Initialize All App Functions
function initializeApp() {
    handleLoadingScreen();
    initializeNavbar();
    initializeHero();
    initializeAnimations();
    initializeCounters();
    initializeScrollEffects();
    initializeForms();
    initializeModalSystem();
    initializeWhatsApp();
    initializeMobileMenu();
    initializeParallax();
}

// Loading Screen Handler
function handleLoadingScreen() {
    const loadingScreen = document.getElementById('loadingScreen');
    
    window.addEventListener('load', () => {
        setTimeout(() => {
            loadingScreen.classList.add('hidden');
            document.body.style.overflow = 'auto';
            
            // Trigger entrance animations
            triggerEntranceAnimations();
        }, 1500);
    });
}

// Entrance Animations
function triggerEntranceAnimations() {
    const heroElements = document.querySelectorAll('.hero-content > *');
    
    heroElements.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            element.style.transition = 'all 0.8s ease';
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, index * 200);
    });
}

// Navbar Handler
function initializeNavbar() {
    const navbar = document.getElementById('navbar');
    const navLinks = document.querySelectorAll('.nav-link');
    
    // Scroll effect
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
    
    // Smooth scroll for nav links
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {
                scrollToSection(targetId.substring(1));
            }
        });
    });
}

// Mobile Menu
function initializeMobileMenu() {
    const hamburger = document.getElementById('navHamburger');
    const navMenu = document.getElementById('navMenu');
    
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');
    });
    
    // Close menu when clicking on links
    navMenu.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
        });
    });
}

// Hero Section Animations
function initializeHero() {
    const floatingCards = document.querySelectorAll('.service-card-mini');
    
    // Staggered animation for floating cards
    floatingCards.forEach((card, index) => {
        const delay = card.getAttribute('data-delay') || index * 100;
        card.style.opacity = '0';
        card.style.transform = 'translateY(50px)';
        
        setTimeout(() => {
            card.style.transition = 'all 1s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 2000 + parseInt(delay));
    });
    
    // Stats animation trigger
    const statsSection = document.querySelector('.hero-stats');
    if (statsSection) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        });
        observer.observe(statsSection);
    }
}

// Counter Animation
function initializeCounters() {
    // This will be triggered by the hero intersection observer
}

function animateCounters() {
    const counters = document.querySelectorAll('.stat-number');
    
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 2000;
        const increment = target / (duration / 16); // 60 FPS
        let current = 0;
        
        const updateCounter = () => {
            if (current < target) {
                current += increment;
                counter.textContent = Math.floor(current);
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target;
            }
        };
        
        updateCounter();
    });
}

// Advanced Scroll Animations (AOS Alternative)
function initializeAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const delay = entry.target.getAttribute('data-aos-delay') || 0;
                
                setTimeout(() => {
                    entry.target.classList.add('aos-animate');
                }, parseInt(delay));
                
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe all elements with data-aos attribute
    document.querySelectorAll('[data-aos]').forEach(element => {
        observer.observe(element);
    });
}

// Scroll Effects
function initializeScrollEffects() {
    // Service cards hover effect enhancement
    const serviceCards = document.querySelectorAll('.service-card');
    
    serviceCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-15px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Testimonial cards parallax effect
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const testimonialCards = document.querySelectorAll('.testimonial-card');
        
        testimonialCards.forEach((card, index) => {
            const speed = 0.1 + (index * 0.05);
            card.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });
}

// Parallax Effects
function initializeParallax() {
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        
        // Hero parallax
        const heroPattern = document.querySelector('.hero-pattern');
        if (heroPattern) {
            heroPattern.style.transform = `translateY(${scrolled * 0.3}px)`;
        }
        
        // Visual cards parallax in about section
        const visualCards = document.querySelectorAll('.visual-card');
        visualCards.forEach((card, index) => {
            const speed = 0.1 + (index * 0.03);
            card.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });
}

// Forms Handler
function initializeForms() {
    const contactForm = document.getElementById('contactForm');
    const modalForm = document.getElementById('modalForm');
    
    // Main contact form
    if (contactForm) {
        contactForm.addEventListener('submit', handleFormSubmit);
        initializeFormValidation(contactForm);
    }
    
    // Modal form
    if (modalForm) {
        modalForm.addEventListener('submit', handleModalFormSubmit);
        initializeFormValidation(modalForm);
    }
    
    // Phone mask
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', applyPhoneMask);
    });
}

// Form Validation
function initializeFormValidation(form) {
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('blur', () => validateField(input));
        input.addEventListener('input', () => clearFieldError(input));
    });
}

function validateField(field) {
    const value = field.value.trim();
    const fieldType = field.type;
    const fieldName = field.name;
    
    clearFieldError(field);
    
    // Required validation
    if (field.hasAttribute('required') && !value) {
        showFieldError(field, 'Este campo é obrigatório');
        return false;
    }
    
    // Email validation
    if (fieldType === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            showFieldError(field, 'Digite um e-mail válido');
            return false;
        }
    }
    
    // Phone validation
    if (fieldType === 'tel' && value) {
        const phoneRegex = /^\(\d{2}\)\s\d{4,5}-\d{4}$/;
        if (!phoneRegex.test(value)) {
            showFieldError(field, 'Digite um telefone válido');
            return false;
        }
    }
    
    return true;
}

function showFieldError(field, message) {
    const formGroup = field.closest('.form-group');
    formGroup.classList.add('error');
    
    // Remove existing error message
    const existingError = formGroup.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    // Add error message
    const errorElement = document.createElement('span');
    errorElement.className = 'error-message';
    errorElement.textContent = message;
    errorElement.style.color = 'var(--error)';
    errorElement.style.fontSize = 'var(--font-size-sm)';
    errorElement.style.marginTop = 'var(--spacing-xs)';
    errorElement.style.display = 'block';
    
    formGroup.appendChild(errorElement);
    
    field.style.borderColor = 'var(--error)';
}

function clearFieldError(field) {
    const formGroup = field.closest('.form-group');
    formGroup.classList.remove('error');
    
    const errorMessage = formGroup.querySelector('.error-message');
    if (errorMessage) {
        errorMessage.remove();
    }
    
    field.style.borderColor = '';
}

// Phone Mask
function applyPhoneMask(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    if (value.length <= 11) {
        value = value.replace(/(\d{2})(\d{4,5})(\d{4})/, '($1) $2-$3');
        if (value.length === 14) {
            value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
        }
    }
    
    e.target.value = value;
}

// Form Submit Handlers
async function handleFormSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('.form-submit');
    
    // Validate all fields
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    if (!isValid) {
        showNotification('Por favor, corrija os erros no formulário', 'error');
        return;
    }
    
    // Show loading state
    showButtonLoading(submitBtn);
    
    try {
        // Send to PHP handler
        const response = await fetch('assets/php/contact.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Mensagem enviada com sucesso! Entraremos em contato em breve.', 'success');
            form.reset();
        } else {
            throw new Error(result.message || 'Erro ao enviar mensagem');
        }
    } catch (error) {
        console.error('Form submission error:', error);
        showNotification('Erro ao enviar mensagem. Tente novamente ou entre em contato via WhatsApp.', 'error');
    } finally {
        hideButtonLoading(submitBtn);
    }
}

async function handleModalFormSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('.btn-primary');
    
    // Validate all fields
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    if (!isValid) {
        showNotification('Por favor, corrija os erros no formulário', 'error');
        return;
    }
    
    // Show loading state
    showButtonLoading(submitBtn);
    
    try {
        // Send to PHP handler
        const response = await fetch('assets/php/contact.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Mensagem enviada com sucesso! Entraremos em contato em breve.', 'success');
            form.reset();
            closeContactModal();
        } else {
            throw new Error(result.message || 'Erro ao enviar mensagem');
        }
    } catch (error) {
        console.error('Modal form submission error:', error);
        showNotification('Erro ao enviar mensagem. Tente novamente ou entre em contato via WhatsApp.', 'error');
    } finally {
        hideButtonLoading(submitBtn);
    }
}

// Button Loading States
function showButtonLoading(button) {
    button.classList.add('loading');
    button.disabled = true;
}

function hideButtonLoading(button) {
    button.classList.remove('loading');
    button.disabled = false;
}

// Modal System
function initializeModalSystem() {
    const modal = document.getElementById('contactModal');
    
    // Close modal when clicking outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeContactModal();
        }
    });
    
    // Close modal on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeContactModal();
        }
    });
}

function openContactModal(service = '') {
    const modal = document.getElementById('contactModal');
    const serviceSelect = modal.querySelector('select[name="service"]');
    
    if (service && serviceSelect) {
        serviceSelect.value = service;
    }
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // Focus first input
    setTimeout(() => {
        const firstInput = modal.querySelector('input');
        if (firstInput) {
            firstInput.focus();
        }
    }, 300);
}

function closeContactModal() {
    const modal = document.getElementById('contactModal');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// WhatsApp Integration
function initializeWhatsApp() {
    // Enhanced WhatsApp button with message generation
}

function openWhatsApp() {
    const phoneNumber = '5521965982113';
    const message = encodeURIComponent('Olá! Vim pelo site da Centro Service e gostaria de solicitar um orçamento para serviço de manutenção. Podemos coversar?');
    const whatsappUrl = `https://wa.me/${phoneNumber}?text=${message}`;
    
    // Forçar abertura sem cache
    window.open(whatsappUrl, '_blank', 'noopener,noreferrer');
}

// Utility Functions
function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        const offsetTop = section.offsetTop - 80; // Account for fixed navbar
        
        window.scrollTo({
            top: offsetTop,
            behavior: 'smooth'
        });
    }
}

function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-icon">
                ${type === 'success' ? '✓' : type === 'error' ? '✗' : 'ℹ'}
            </span>
            <span class="notification-message">${message}</span>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        z-index: 10000;
        max-width: 400px;
        background: ${type === 'success' ? '#1e40af' : type === 'error' ? '#EF4444' : '#6B7280'};
        color: white;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    notification.querySelector('.notification-content').style.cssText = `
        display: flex;
        align-items: center;
        gap: 12px;
    `;
    
    notification.querySelector('.notification-close').style.cssText = `
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        margin-left: auto;
    `;
    
    // Add to DOM
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 300);
    }, 5000);
}

// Enhanced Visual Effects
function initializeVisualEffects() {
    // Cursor trail effect (optional)
    createCursorTrail();
    
    // Card tilt effect
    initializeCardTilt();
}

function createCursorTrail() {
    const trail = [];
    const maxTrail = 10;
    
    document.addEventListener('mousemove', (e) => {
        trail.push({ x: e.clientX, y: e.clientY, opacity: 1 });
        
        if (trail.length > maxTrail) {
            trail.shift();
        }
        
        // Update trail elements (implementation depends on design requirements)
    });
}

function initializeCardTilt() {
    const cards = document.querySelectorAll('.service-card, .testimonial-card');
    
    cards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;
            
            const deltaX = (e.clientX - centerX) / rect.width;
            const deltaY = (e.clientY - centerY) / rect.height;
            
            const tiltX = deltaY * 10;
            const tiltY = deltaX * -10;
            
            card.style.transform = `perspective(1000px) rotateX(${tiltX}deg) rotateY(${tiltY}deg) translateZ(20px)`;
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });
}

// Performance Optimization
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Optimize scroll events
window.addEventListener('scroll', throttle(() => {
    // Scroll-dependent code here
}, 16)); // 60 FPS

// Video Demo Function
function playVideoDemo() {
    // Aqui você pode implementar a lógica para reproduzir um vídeo demo
    // Exemplo: abrir modal com vídeo, redirecionar para YouTube, etc.
    
    // Simulação de abertura de vídeo
    showNotification('Funcionalidade de vídeo em desenvolvimento! Entre em contato para mais informações.', 'info');
    
    // Alternativa: abrir link do YouTube (descomente se tiver um vídeo)
    // window.open('https://youtube.com/watch?v=SEU_VIDEO_ID', '_blank');
}

// Initialize visual effects after DOM load
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(initializeVisualEffects, 1000);
});

// Global Functions (for HTML onclick handlers)
window.openContactModal = openContactModal;
window.closeContactModal = closeContactModal;
window.openWhatsApp = openWhatsApp;
window.scrollToSection = scrollToSection;
window.playVideoDemo = playVideoDemo;