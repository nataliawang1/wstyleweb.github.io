async function fetchJson(url) {
    try {
        const response = await fetch(url, {cache: 'no-store'});
        return await response.json();
    } catch (error) {
        console.error('Error fetching JSON:', error);
        return {success: false, error: String(error)};
    }
}

function getQueryParam(name) {
    const params = new URLSearchParams(window.location.search);
    return params.get(name);
}

function showMessage(type, text) {
    const logoPath = 'images/LOGO WANG RED.jpg';
    const container = document.createElement('div');
    container.className = `form-message form-message-${type}`;
    container.innerHTML = `
        <div class="form-message-brand">
            <img src="${logoPath}" alt="W-Style" />
        </div>
        <div class="form-message-text">${text}</div>
        <button class="form-message-close" type="button" aria-label="Cerrar mensaje">×</button>
    `;

    container.querySelector('.form-message-close').addEventListener('click', () => {
        container.remove();
    });

    document.body.insertBefore(container, document.body.firstChild);
    setTimeout(() => {
        container.remove();
    }, 7000);
}

async function renderHomepage() {
    const portfolioData = await fetchJson('php/obtenerPortafolio.php');
    const testimonialData = await fetchJson('php/obtenerClientes.php');

    const portfolioGrid = document.getElementById('homepage-portfolio-grid');
    const testimonialsTrack = document.querySelector('.testimonials-track');

    console.log('Portfolio data:', portfolioData);

    if (portfolioGrid && portfolioData.success) {
        portfolioGrid.innerHTML = '';
        if (portfolioData.items.length === 0) {
            portfolioGrid.innerHTML = '<p>No hay items en el portafolio.</p>';
        } else {
            portfolioData.items.slice(0, 6).forEach(item => {
                portfolioGrid.innerHTML += `
                    <div class="portfolio-card">
                        <a href="portafolio.html?id=${item.id}">
                            <img src="images/${item.imagen}" alt="${item.titulo}">
                            <div class="portfolio-info">
                                <h3>${item.titulo}</h3>
                                <p>${item.descripcion || 'Autor'}</p>
                            </div>
                        </a>
                    </div>
                `;
            });
        }
    } else if (portfolioGrid) {
        portfolioGrid.innerHTML = '<p>Error al cargar el portafolio.</p>';
    }

    if (testimonialsTrack && testimonialData.success) {
        testimonialsTrack.innerHTML = '';
        testimonialData.items.filter(item => item.testimonio).slice(0, 6).forEach(item => {
            testimonialsTrack.innerHTML += `
                <div class="testimonial-item">
                    <p>"${item.testimonio}"</p>
                    <cite>- ${item.nombre}, ${item.marca}</cite>
                </div>
            `;
        });
    }
}

async function renderPortfolioPage() {
    const listSection = document.getElementById('portfolio-list');
    const detailSection = document.getElementById('portfolio-detail');
    const pageTitle = document.getElementById('portfolio-page-title');
    const pageDescription = document.getElementById('portfolio-page-description');
    const selectedId = getQueryParam('id');

    if (selectedId) {
        listSection.classList.add('hidden');
        detailSection.classList.remove('hidden');
        pageTitle.textContent = 'Sesión de Portafolio';
        pageDescription.textContent = 'Descubre la galería completa y los colaboradores de esta sesión.';

        const result = await fetchJson(`php/obtenerPortafolioDetalle.php?id=${encodeURIComponent(selectedId)}`);
        if (result.success && result.item) {
            renderPortfolioDetail(result.item);
        } else {
            detailSection.innerHTML = `<p class="error-text">No se encontró la sesión solicitada. <a href="portafolio.html">Volver al portafolio</a></p>`;
        }
        return;
    }

    detailSection.classList.add('hidden');
    listSection.classList.remove('hidden');
    pageTitle.textContent = 'Portafolio W';
    pageDescription.textContent = 'Nuestras mejores creaciones';

    const data = await fetchJson('php/obtenerPortafolio.php');
    if (data.success) {
        listSection.innerHTML = data.items.map(item => `
            <div class="portfolio-item" data-id="${item.id}">
                <img src="images/${item.imagen}" alt="${item.titulo}">
                <div class="portfolio-overlay">
                    <h3>${item.titulo}</h3>
                    <p>${item.descripcion}</p>
                </div>
            </div>
        `).join('');

        listSection.querySelectorAll('.portfolio-item').forEach(card => {
            card.addEventListener('click', () => {
                window.location.href = `portafolio.html?id=${card.dataset.id}`;
            });
        });
    }
}

let portfolioLightboxImages = [];
let lightboxCurrentIndex = 0;

function renderPortfolioDetail(item) {
    const mainImage = document.getElementById('portfolio-main-image');
    const thumbnailGrid = document.getElementById('portfolio-thumbnail-grid');
    const title = document.getElementById('detail-title');
    const description = document.getElementById('detail-description');
    const category = document.getElementById('detail-category');
    const collaboratorsList = document.getElementById('collaborators-list');

    const images = (item.imagenes && item.imagenes.length > 0) ? item.imagenes : [{ imagen: item.imagen }];
    const firstImage = images[0] || { imagen: item.imagen };
    portfolioLightboxImages = images.map(image => `images/${image.imagen}`);
    lightboxCurrentIndex = 0;

    mainImage.src = portfolioLightboxImages[0];
    mainImage.alt = item.titulo;
    title.textContent = item.titulo;
    description.textContent = item.descripcion || 'Sin descripción disponible para esta sesión.';
    category.textContent = item.categoria ? item.categoria.toUpperCase() : 'Sesión exclusiva';

    thumbnailGrid.innerHTML = images.map((image, index) => `
        <div class="thumbnail-item ${index === 0 ? 'active' : ''}" data-index="${index}">
            <img src="images/${image.imagen}" alt="${item.titulo} - ${index + 1}">
        </div>
    `).join('');

    thumbnailGrid.querySelectorAll('.thumbnail-item').forEach(itemEl => {
        itemEl.addEventListener('click', () => {
            const index = Number(itemEl.dataset.index);
            lightboxCurrentIndex = index;
            const imageSrc = portfolioLightboxImages[index];
            mainImage.src = imageSrc;
            thumbnailGrid.querySelectorAll('.thumbnail-item').forEach(el => el.classList.remove('active'));
            itemEl.classList.add('active');
        });
    });

    mainImage.addEventListener('click', () => {
        openLightbox(lightboxCurrentIndex, item.titulo);
    });

    setupLightboxEvents(item.titulo);

    if (item.colaboradores && item.colaboradores.length > 0) {
        collaboratorsList.innerHTML = item.colaboradores.map(collab => `
            <div class="collaborator-card">
                <strong>${collab.rol}</strong>
                <span>${collab.nombre}</span>
            </div>
        `).join('');
    } else {
        collaboratorsList.innerHTML = '<p class="error-text">No hay colaboradores registrados para esta sesión.</p>';
    }
}

function openLightbox(index, title) {
    const lightbox = document.getElementById('portfolio-lightbox');
    const lightboxImage = document.getElementById('lightbox-image');
    const lightboxCaption = document.getElementById('lightbox-caption');

    lightboxCurrentIndex = index;
    lightboxImage.src = portfolioLightboxImages[lightboxCurrentIndex];
    lightboxImage.alt = `${title} - imagen ${lightboxCurrentIndex + 1}`;
    lightboxCaption.textContent = `${title} · ${lightboxCurrentIndex + 1} / ${portfolioLightboxImages.length}`;
    lightbox.classList.remove('hidden');
    lightbox.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const lightbox = document.getElementById('portfolio-lightbox');
    lightbox.classList.add('hidden');
    lightbox.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
}

function showLightboxImage(title) {
    const lightboxImage = document.getElementById('lightbox-image');
    const lightboxCaption = document.getElementById('lightbox-caption');

    lightboxImage.src = portfolioLightboxImages[lightboxCurrentIndex];
    lightboxImage.alt = `${title} - imagen ${lightboxCurrentIndex + 1}`;
    lightboxCaption.textContent = `${title} · ${lightboxCurrentIndex + 1} / ${portfolioLightboxImages.length}`;
}

function setupLightboxEvents(title) {
    const lightbox = document.getElementById('portfolio-lightbox');
    const closeButton = lightbox.querySelector('.lightbox-close');
    const backdrop = lightbox.querySelector('.lightbox-backdrop');
    const prevButton = lightbox.querySelector('.lightbox-nav.prev');
    const nextButton = lightbox.querySelector('.lightbox-nav.next');

    closeButton.onclick = closeLightbox;
    backdrop.onclick = closeLightbox;
    prevButton.onclick = () => {
        lightboxCurrentIndex = (lightboxCurrentIndex - 1 + portfolioLightboxImages.length) % portfolioLightboxImages.length;
        showLightboxImage(title);
    };
    nextButton.onclick = () => {
        lightboxCurrentIndex = (lightboxCurrentIndex + 1) % portfolioLightboxImages.length;
        showLightboxImage(title);
    };

    document.onkeydown = (event) => {
        if (lightbox.classList.contains('hidden')) return;
        if (event.key === 'Escape') {
            closeLightbox();
        } else if (event.key === 'ArrowLeft') {
            lightboxCurrentIndex = (lightboxCurrentIndex - 1 + portfolioLightboxImages.length) % portfolioLightboxImages.length;
            showLightboxImage(title);
        } else if (event.key === 'ArrowRight') {
            lightboxCurrentIndex = (lightboxCurrentIndex + 1) % portfolioLightboxImages.length;
            showLightboxImage(title);
        }
    };
}

function parseServiceDescription(descripcion) {
    if (!descripcion) {
        return { intro: '', items: [] };
    }

    const parts = descripcion.split(/\n\s*\n/);
    if (parts.length >= 2) {
        return {
            intro: parts[0].trim(),
            items: parts.slice(1).join('\n').split('\n').map(line => line.trim()).filter(line => line && !/^incluye:?$/i.test(line))
        };
    }

    const lines = descripcion.split('\n').map(line => line.trim()).filter(Boolean);
    if (lines.length > 1) {
        return {
            intro: lines[0],
            items: lines.slice(1).filter(line => !/^incluye:?$/i.test(line))
        };
    }

    return { intro: descripcion.trim(), items: [] };
}

async function renderServicesPage() {
    const data = await fetchJson('php/obtenerServicios.php');
    const list = document.getElementById('services-list');
    if (!list) return;
    if (data.success) {
        list.innerHTML = '';
        data.items.forEach(item => {
            const { intro, items } = parseServiceDescription(item.descripcion);
            const includesHtml = items.length
                ? `<p class="service-includes-label">Incluye:</p>
                   <ul class="service-includes">
                       ${items.map(entry => `<li>${entry}</li>`).join('')}
                   </ul>`
                : '';

            list.innerHTML += `
                <div class="service-item">
                    <div class="service-content">
                        <h3>${item.titulo}</h3>
                        <p>${intro}</p>
                        ${includesHtml}
                        <a href="https://www.instagram.com/wangstyle.co/" target="_blank" class="service-btn">Ver más</a>
                    </div>
                    <div class="service-image">
                        <img src="images/${item.imagen}" alt="${item.titulo}" class="lightbox-trigger">
                    </div>
                </div>
            `;
        });

        list.querySelectorAll('.lightbox-trigger').forEach(img => {
            img.addEventListener('click', () => {
                openImageLightbox(img.src);
            });
        });
    }
}

async function renderClientsPage() {
    const data = await fetchJson('php/obtenerGaleriaBTS.php');
    const gallery = document.getElementById('bts-gallery');
    
    if (!gallery || !data.success || !data.items) return;

    gallery.innerHTML = data.items.map((item, index) => {
        if (item.tipo === 'video' && item.url_video) {
            const videoId = item.url_video.includes('youtube') ? 
                item.url_video.split('v=')[1] || item.url_video.split('/')[3] : item.url_video;
            return `
                <div class="gallery-item video-item" data-id="${item.id}" data-index="${index}">
                    <div class="video-thumbnail">
                        <img src="https://img.youtube.com/vi/${videoId}/maxresdefault.jpg" alt="${item.nombre}">
                        <div class="play-button">▶</div>
                    </div>
                    <div class="gallery-item-caption">
                        <h3>${item.nombre}</h3>
                        <p>${item.descripcion || ''}</p>
                    </div>
                </div>
            `;
        } else {
            return `
                <div class="gallery-item photo-item" data-id="${item.id}" data-index="${index}">
                    <img src="images/${item.archivo}" alt="${item.nombre}">
                    <div class="gallery-item-caption">
                        <h3>${item.nombre}</h3>
                        <p>${item.descripcion || ''}</p>
                    </div>
                </div>
            `;
        }
    }).join('');

    gallery.querySelectorAll('.gallery-item').forEach(item => {
        item.addEventListener('click', () => {
            const videoItem = item.classList.contains('video-item');
            if (videoItem) {
                const urlVideo = data.items[parseInt(item.dataset.index)].url_video;
                openVideoModal(urlVideo);
            } else {
                const imageSrc = item.querySelector('img').src;
                openImageLightbox(imageSrc);
            }
        });
    });
}

function openImageLightbox(imageSrc) {
    const modal = document.createElement('div');
    modal.className = 'lightbox-modal';
    modal.innerHTML = `
        <div class="lightbox-backdrop"></div>
        <div class="lightbox-content">
            <button class="lightbox-close">×</button>
            <img src="${imageSrc}" alt="Galería">
        </div>
    `;
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';
    modal.querySelector('.lightbox-close').addEventListener('click', () => {
        modal.remove();
        document.body.style.overflow = '';
    });
    modal.querySelector('.lightbox-backdrop').addEventListener('click', () => {
        modal.remove();
        document.body.style.overflow = '';
    });
}

function openVideoModal(urlVideo) {
    let embedUrl = urlVideo;
    if (urlVideo.includes('youtube.com')) {
        const videoId = urlVideo.split('v=')[1];
        embedUrl = `https://www.youtube.com/embed/${videoId}`;
    } else if (urlVideo.includes('youtu.be')) {
        const videoId = urlVideo.split('/')[3];
        embedUrl = `https://www.youtube.com/embed/${videoId}`;
    }

    const modal = document.createElement('div');
    modal.className = 'lightbox-modal';
    modal.innerHTML = `
        <div class="lightbox-backdrop"></div>
        <div class="lightbox-content">
            <button class="lightbox-close">×</button>
            <iframe width="100%" height="600" src="${embedUrl}" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
        </div>
    `;
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';
    modal.querySelector('.lightbox-close').addEventListener('click', () => {
        modal.remove();
        document.body.style.overflow = '';
    });
    modal.querySelector('.lightbox-backdrop').addEventListener('click', () => {
        modal.remove();
        document.body.style.overflow = '';
    });
}

async function renderWClubPage() {
    const data = await fetchJson('php/obtenerWClub.php');
    const countEl = document.querySelector('.wclub-member-count');
    const listEl = document.querySelector('.wclub-members-list');
    if (countEl && data.success) {
        countEl.textContent = data.count;
    }
    if (listEl && data.success) {
        listEl.innerHTML = '';
        data.members.slice(0, 6).forEach(member => {
            listEl.innerHTML += `
                <div class="benefit-card">
                    <h3>${member.nombre}</h3>
                    <p>${member.email}</p>
                </div>
            `;
        });
    }
}

function renderMessageFromQuery() {
    const success = getQueryParam('success');
    const error = getQueryParam('error');
    if (success) {
        showMessage('success', success === '1' ? 'Registro enviado correctamente.' : success);
    } else if (error) {
        showMessage('error', decodeURIComponent(error));
    }
}

function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input, textarea');
    
    inputs.forEach(input => {
        const formGroup = input.closest('.form-group');
        const errorSpan = formGroup.querySelector('.error-message');
        
        input.classList.remove('error');
        errorSpan.textContent = '';
        
        if (input.value.trim() === '') {
            input.classList.add('error');
            errorSpan.textContent = 'Requerido';
            isValid = false;
        } else if (input.type === 'email') {
            if (!isValidEmail(input.value)) {
                input.classList.add('error');
                errorSpan.textContent = 'Correo inválido';
                isValid = false;
            } else if (!input.value.includes('@')) {
                input.classList.add('error');
                errorSpan.textContent = 'Falta @';
                isValid = false;
            } else if (!input.value.includes('.')) {
                input.classList.add('error');
                errorSpan.textContent = 'Falta dominio';
                isValid = false;
            }
        } else if (input.type === 'tel' && !isValidPhone(input.value)) {
            input.classList.add('error');
            errorSpan.textContent = 'Teléfono inválido';
            isValid = false;
        }
    });
    
    return isValid;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    const phoneRegex = /^[0-9+\s\-()]{10,}$/;
    return phoneRegex.test(phone);
}

function setupFormValidation() {
    const contactForm = document.getElementById('contact-form');
    const contactPageForm = document.getElementById('contact-page-form');
    const wclubForm = document.getElementById('wclub-form');
    
    [contactForm, contactPageForm, wclubForm].forEach(form => {
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!validateForm(form)) {
                    e.preventDefault();
                }
            });
        }
    });
}

function setupGlobalLightbox() {
    document.querySelectorAll('.lightbox-trigger').forEach(img => {
        img.addEventListener('click', (e) => {
            e.stopPropagation();
            openImageLightbox(img.src);
        });
    });
}

function init() {
    renderMessageFromQuery();
    setupFormValidation();
    setupGlobalLightbox();
    const path = window.location.pathname.split('/').pop();
    if (path === 'index.html' || path === '' || path === 'index.php') {
        renderHomepage();
    }
    if (path === 'portafolio.html') {
        renderPortfolioPage();
    }
    if (path === 'servicios.html') {
        renderServicesPage();
    }
    if (path === 'clientes.html') {
        renderClientsPage();
    }
    if (path === 'wclub.html') {
        renderWClubPage();
    }
}

document.addEventListener('DOMContentLoaded', init);
