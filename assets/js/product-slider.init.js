// Product detail layout 01 swiper
var productSmall = new Swiper(".productSmall", {
    spaceBetween: 10,
    slidesPerView: 6,
    direction: "vertical",
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    breakpoints: {
        100: {
            direction: "horizontal",
            slidesPerView: 4,
        },
        1200: {
            direction: "vertical",

        }
    },
});
var productMain = new Swiper(".productMain", {
    slidesPerView: 1,
	spaceBetween: 10,
    direction: 'horizontal',
    autoHeight: true,
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    thumbs: {
        swiper: productSmall,
    },
});

var kidsProductSmall = new Swiper(".kidsProductSmall", {
    spaceBetween: 10,
    slidesPerView: 4,
    direction: "horizontal",
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    }
});
var kidsProductMain = new Swiper(".kidsProductMain", {
    slidesPerView: 1,
	spaceBetween: 10,
    direction: 'horizontal',
    autoHeight: true,
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    thumbs: {
        swiper: kidsProductSmall,
    },
});

function isData() {
    var t = document.getElementsByClassName("plus"),
        e = document.getElementsByClassName("minus"),
        n = document.getElementsByClassName("product");
    t && Array.from(t).forEach(function (t) {
        t.addEventListener("click", function (e) {
            parseInt(t.previousElementSibling.value) < e.target.previousElementSibling.getAttribute("max") && (e.target.previousElementSibling.value++, n) && Array.from(n).forEach(function (t) {
                updateQuantity(e.target)
            })
        })
    }), e && Array.from(e).forEach(function (t) {
        t.addEventListener("click", function (e) {
            parseInt(t.nextElementSibling.value) > e.target.nextElementSibling.getAttribute("min") && (e.target.nextElementSibling.value--, n) && Array.from(n).forEach(function (t) {
                updateQuantity(e.target)
            })
        })
    })
} isData();


// Product detail thumb bottom swiper
var swiper = new Swiper(".productSm", {
    loop: true,
    spaceBetween: 10,
    slidesPerView: 4,
    freeMode: true,
    watchSlidesProgress: true,
});
var swiper2 = new Swiper(".productLg", {
    loop: true,
    spaceBetween: 10,
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    thumbs: {
        swiper: swiper,
    },
});

// Product detail without Thumbnail
var swiper = new Swiper(".productJewellry", {
    loop: true,
    spaceBetween: 10,
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    pagination: {
        el: ".swiper-pagination",
    },
});

//
window.onscroll = () => {
    if(window.innerWidth > 575){
        let maxScrollHeight = 3300; // Default value
        if (window.innerWidth <= 1224) {
            maxScrollHeight = 2750;
        }
        if (window.scrollY > window.innerHeight && window.scrollY <= maxScrollHeight) {
            document.querySelector(".productCart")?.classList.remove("d-none");
        } else {
            var cartProduct = document.querySelector(".productCart")?.classList;
            if (!cartProduct?.contains("d-none")) {
                cartProduct?.add("d-none");
            }
        }
    }else{
        let maxScrollHeight = 3300; // Default value
        if (window.innerWidth <= 1224) {
            maxScrollHeight = 2450;
        }
        if (window.scrollY > window.innerHeight && window.scrollY <= maxScrollHeight) {
            document.querySelector(".productCart")?.classList.remove("d-none");
        } else {
            var cartProduct = document.querySelector(".productCart")?.classList;
            if (!cartProduct?.contains("d-none")) {
                cartProduct?.add("d-none");
            }
        }

    }
};

// Product Single View Zoom Effect

// document.addEventListener('DOMContentLoaded', function() {
//     var pIZoomContainer = document.querySelector('.p_i_zoom');
//     var paneContainer = pIZoomContainer.querySelector('.detail');

//     if (!paneContainer || !(paneContainer instanceof Element) || window.innerWidth <= 786) {
//         console.error('paneContainer must be a valid DOM element.');
//     } else {
//         document.querySelectorAll('.thumbnail-container').forEach(function(thumbnailContainer) {
//             var triggerElement = thumbnailContainer.querySelector('.drift-demo-trigger');
//             var driftOptions = {
//                 paneContainer: paneContainer,
//                 inlinePane: 769,
//                 inlineOffsetY: -85,
//                 containInline: true,
//                 hoverBoundingBox: false
//             };
//             new Drift(triggerElement, driftOptions);

//             thumbnailContainer.addEventListener('mouseover', function() {
//                 pIZoomContainer.classList.add('active');
//             });

//             thumbnailContainer.addEventListener('mouseout', function() {
//                 pIZoomContainer.classList.remove('active');
//             });
//         });
//     }
// });

// Product Single View Zoom Effect Overlay
const productContainers = document.querySelectorAll('.product-container');
const details = document.querySelectorAll('.product-details');

productContainers.forEach(container => {
    container.addEventListener('mouseover', event => {
        const index = Array.from(productContainers).indexOf(container);
        details[index].classList.add('hovered');
    });
    container.addEventListener('mouseout', event => {
        const index = Array.from(productContainers).indexOf(container);
        details[index].classList.remove('hovered');
    });
});


// Product Single View Zoom Effect Cursor Design
var customCursor = document.getElementsByClassName("cursor");
if(customCursor.length > 0){
    document.addEventListener('DOMContentLoaded', function() {
        // Select all product containers
        var productContainers = document.querySelectorAll('.product-container');
    
        // Add event listeners to each product container
        productContainers.forEach(function(container) {
            // Add mousemove event listener
            container.addEventListener('mousemove', function(event) {
                // Get the cursor element inside the current product container
                var cursor = container.querySelector('.cursor');
                // Calculate the cursor position relative to the product container
                var offsetX = event.clientX - container.getBoundingClientRect().left - cursor.offsetWidth / 2;
                var offsetY = event.clientY - container.getBoundingClientRect().top - cursor.offsetHeight / 2;
                // Update the cursor position
                cursor.style.left = offsetX + 'px';
                cursor.style.top = offsetY + 'px';
                // Add class to show cursor
                cursor.classList.add('active');
            });
    
            // Add mouseout event listener
            container.addEventListener('mouseout', function() {
                // Get the cursor element inside the current product container
                var cursor = container.querySelector('.cursor');
                // Remove class to hide cursor
                cursor.classList.remove('active');
            });
        });
    });
} 

document.querySelectorAll('.product-img--main').forEach(function(element) {
    var dataImage = element.getAttribute('data-image');
    var defaultScale = 1;
    var hoverScale = 1.6;

    var imageContainer = document.createElement('div');
    imageContainer.className = 'product-img--main__image';
    imageContainer.style.backgroundImage = 'url(' + dataImage + ')';
    imageContainer.style.transform = 'scale(' + defaultScale + ')';
    element.appendChild(imageContainer);

    element.addEventListener('mouseover', function() {
        var rect = this.getBoundingClientRect();
        var offsetX = (event.clientX - rect.left) / rect.width * 100;
        var offsetY = (event.clientY - rect.top) / rect.height * 100;
        imageContainer.style.transformOrigin = offsetX + '% ' + offsetY + '%';
        imageContainer.style.transform = 'scale(' + hoverScale + ')';
    });
    
    element.addEventListener('mouseout', function() {
        imageContainer.style.transformOrigin = 'initial';
        imageContainer.style.transform = 'scale(' + defaultScale + ')';
    });

    element.addEventListener('mousemove', function(e) {
        var rect = this.getBoundingClientRect();
        var offsetX = (e.clientX - rect.left) / rect.width * 100;
        var offsetY = (e.clientY - rect.top) / rect.height * 100;
        imageContainer.style.transformOrigin = offsetX + '% ' + offsetY + '%';
    });
});
