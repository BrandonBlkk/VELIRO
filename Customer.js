let moveRight = document.getElementById("move-right");

window.addEventListener('scroll', function() {
    let scrollableHeight = document.documentElement.scrollHeight - window.innerHeight;
    let scrollPercentage = (window.scrollY / scrollableHeight) * 100; 

    if (scrollPercentage >= 100) {
        moveRight.style.width = '100%';
    } else {
        moveRight.style.width = scrollPercentage + '%';
    }
});

// SearchBox display
const searchBtn = document.getElementById('searchBtn');
const searchBox = document.getElementById('searchBox');
const searchInput = document.getElementById('searchInput');

searchBtn.addEventListener('click', () => {
    searchBtn.classList.toggle('font-semibold')
    searchBox.classList.toggle('top-20');
    searchInput.classList.toggle('ring-2')
    searchInput.classList.toggle('ring-indigo-400')
    searchInput.value = '';
    searchInput.focus();
})

// Shopping cart display
const aside = document.getElementById('aside');
const shoppingCart = document.getElementById('shoppingCart');
const closeBtn = document.getElementById('closeBtn');
const darkOverlay = document.getElementById('darkOverlay');

shoppingCart.addEventListener('click', () => {
    aside.style.right = '0%';
    darkOverlay.classList.remove('hidden');
    darkOverlay.classList.add('flex');
})

closeBtn.addEventListener('click', () => {
    aside.style.right = '-100%';
    darkOverlay.classList.add('hidden');
    darkOverlay.classList.remove('flex');
})

darkOverlay.addEventListener('click', () => {
    aside.style.right = '-100%';
    darkOverlay.classList.add('hidden');
    darkOverlay.classList.remove('flex');
});

// Profile box display
const profile = document.getElementById('profile');
const profileBox = document.getElementById('profileBox');
const profileCloseBtn = document.getElementById('profileCloseBtn');
const darkOverlay2 = document.getElementById('darkOverlay2');

profile.addEventListener('click', () => {
    profileBox.style.right = '0%';
    darkOverlay2.classList.remove('hidden');
    darkOverlay2.classList.add('flex');
})

profileCloseBtn.addEventListener('click', () => {
    profileBox.style.right = '-100%';
    darkOverlay2.classList.add('hidden');
    darkOverlay2.classList.remove('flex');
})

darkOverlay2.addEventListener('click', () => {
    profileBox.style.right = '-100%';
    darkOverlay2.classList.add('hidden');
    darkOverlay2.classList.remove('flex');
});

// Aside nav display
const nav = document.getElementById('nav');
const navClose = document.getElementById('navClose');
const hamburgerIcon = document.getElementById('hamburgerIcon');
const darkOverlay3 = document.getElementById('darkOverlay3');

hamburgerIcon.addEventListener('click', () => {
    nav.style.left = '0%';
    darkOverlay3.classList.remove('hidden');
    darkOverlay3.classList.add('flex');
})

navClose.addEventListener('click', () => {
    nav.style.left = '-100%';
    shopCategory.classList.add('h-10');
    shopCategory.classList.remove('h-full');
    darkOverlay3.classList.add('hidden');
    darkOverlay3.classList.remove('flex');
})

darkOverlay3.addEventListener('click', () => {
    nav.style.left = '-100%';
    darkOverlay3.classList.add('hidden');
    darkOverlay3.classList.remove('flex');
});

// Shop category display
const shopCategory = document.getElementById('shopCategory');
const category = document.getElementById('category');

shopCategory.addEventListener('click', () => {
    shopCategory.classList.toggle('h-10');
    shopCategory.classList.toggle('h-full');
})

//Change image when click the img
function changeImage(newImageUrl) {
    let mainImage = document.getElementById('mainImage');
    mainImage.src = newImageUrl;
}

//Add to favorite
const toggleFavorite = (icon) => {
    icon.classList.toggle('bg-indigo-400/25');
    icon.classList.toggle('bg-black/5');
}

// Displaying product details
document.addEventListener('DOMContentLoaded', () => {
    const accordion = document.getElementById('accordion');
    let expandedSection = null;

    accordion.addEventListener('click', (e) => {
        const target = e.target.closest('[data-target]');
        if (target) {
            const targetId = target.getAttribute('data-target');
            const content = document.getElementById(targetId);

            if (expandedSection && expandedSection !== content) {
                expandedSection.style.height = 0;
                const prevIcon = expandedSection.previousElementSibling.querySelector('.ri-subtract-line');
                if (prevIcon) {
                    prevIcon.classList.replace('ri-subtract-line', 'ri-add-line');
                }
                expandedSection = null;
            }

            if (content.style.height === '0px' || !content.style.height) {
                content.style.height = content.scrollHeight + 'px';

                const currentIcon = target.querySelector('.ri-add-line');
                if (currentIcon) {
                    currentIcon.classList.replace('ri-add-line', 'ri-subtract-line');
                }
                expandedSection = content;
            } else {
                content.style.height = 0;
                const currentIcon = target.querySelector('.ri-subtract-line');
                if (currentIcon) {
                    currentIcon.classList.replace('ri-subtract-line', 'ri-add-line');
                }
            }
        }
    });
});

// Alert message
document.addEventListener('DOMContentLoaded', () => {
    const alertBox = document.getElementById('alertBox');
    const loader = document.getElementById('loader');

    // Show the alert box with a transition
    if (alertBox) {
        setTimeout(() => {
            alertBox.classList.remove('opacity-0', '-bottom-full');
            alertBox.classList.add('opacity-100', 'bottom-3');
        }, 100); 
    }

    // Automatically remove loader and alert box after a delay
    setTimeout(() => {
        alertBox.classList.remove('opacity-100', 'bottom-3');
        alertBox.classList.add('opacity-0', '-bottom-full');

        // Optionally, remove the alert box after hiding
        setTimeout(() => {
            alertBox.remove();
        }, 300);
    }, 2000);
});

document.addEventListener('DOMContentLoaded', () => {
    const addToBagForm = document.getElementById('addToBagForm'); 
    const loader = document.getElementById('loader');
    const alertBox = document.getElementById('alertBox');

    // Show loader on form submit
    if (addToBagForm) {
        addToBagForm.addEventListener('submit', (e) => {
            loader.classList.remove('hidden');
        });
    }

    // Show the alert box and loader with a transition
    if (alertBox) {
        setTimeout(() => {
            loader.classList.remove('hidden'); 
            alertBox.classList.remove('opacity-0', '-bottom-full');
            alertBox.classList.add('opacity-100', 'bottom-3');
        }, 100); 
    }

    // Automatically remove loader and alert box after a delay
    setTimeout(() => {
        loader.classList.add('hidden'); 
        alertBox.classList.remove('opacity-100', 'bottom-3');
        alertBox.classList.add('opacity-0', '-bottom-full');

        // Optionally, remove the alert box after hiding
        setTimeout(() => {
            alertBox.remove();
        }, 300);
    }, 2000);
});

// Close button for request login form
document.addEventListener('DOMContentLoaded', () => {
    const closeRequestLogin = document.getElementById('closeRequestLogin');
    const requestLogin = document.getElementById('requestLogin');

    if (closeRequestLogin) {
        closeRequestLogin.addEventListener('click', () => {
            // Add transition classes for fading out
            requestLogin.classList.add('opacity-0');
            requestLogin.classList.remove('opacity-100');

            // Remove the modal after the transition
            setTimeout(() => {
                requestLogin.remove();
            }, 300); 
        });
    }
});

// Product See More Button
document.addEventListener('DOMContentLoaded', function() {
    const seeMoreBtn = document.getElementById('see-more-btn');
    const productItems = document.querySelectorAll('.product-item');
    const viewedCount = document.getElementById('viewed-count');
    const progressBar = document.getElementById('progress-bar');
    let itemsToShow = 16;
    const totalItems = productItems.length;

    seeMoreBtn.addEventListener('click', function() {
        const hiddenItems = Array.from(productItems).filter(item => item.classList.contains('hidden'));
        hiddenItems.slice(0, 16).forEach(item => item.classList.remove('hidden'));

        itemsToShow += 16;
        if (itemsToShow >= totalItems) {
            seeMoreBtn.style.display = 'none';
        }

        // Update the viewed count and progress bar
        viewedCount.textContent = `You've viewed ${Math.min(itemsToShow, totalItems)} of ${totalItems} products`;
        progressBar.style.width = `${(Math.min(itemsToShow, totalItems) / totalItems) * 100}%`;
    });
});

// Truncate sentence
document.addEventListener('DOMContentLoaded', function() {
    const readMoreLinks = document.querySelectorAll('.read-more');
    const readLessLinks = document.querySelectorAll('.read-less');

    readMoreLinks.forEach(link => {
        link.addEventListener('click', function() {
            const container = this.closest('.bg-white'); // Find the closest container
            const truncatedComment = container.querySelector('.truncated-comment');
            const fullComment = container.querySelector('.full-comment');
            const readMore = container.querySelector('.read-more');
            const readLess = container.querySelector('.read-less');
            
            truncatedComment.classList.add('hidden');
            fullComment.classList.remove('hidden');
            readMore.classList.add('hidden');
            readLess.classList.remove('hidden');
        });
    });

    readLessLinks.forEach(link => {
        link.addEventListener('click', function() {
            const container = this.closest('.bg-white'); // Find the closest container
            const truncatedComment = container.querySelector('.truncated-comment');
            const fullComment = container.querySelector('.full-comment');
            const readMore = container.querySelector('.read-more');
            const readLess = container.querySelector('.read-less');
            
            truncatedComment.classList.remove('hidden');
            fullComment.classList.add('hidden');
            readMore.classList.remove('hidden');
            readLess.classList.add('hidden');
        });
    });
});

// Size guide modal
const sizeGuideLink = document.getElementById('sizeGuideLink');
const sizeGuideModal = document.getElementById('sizeGuideModal');
const closeModal = document.getElementById('closeModal');

// Show the modal when clicking the "Size Guide" link
sizeGuideLink.addEventListener('click', function(e) {
    e.preventDefault();
    sizeGuideModal.style.display = 'flex';
});

// Close the modal when clicking the close button (X)
closeModal.addEventListener('click', function() {
    sizeGuideModal.style.display = 'none';
});

// Close the modal when clicking outside of the modal content
window.addEventListener('click', function(e) {
    if (e.target === sizeGuideModal) {
        sizeGuideModal.style.display = 'none';
    }
});

