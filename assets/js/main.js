//
// main.js
//
document.addEventListener("DOMContentLoaded", function () {
  const filterIcon = document.querySelector("#filter-icon");
  const filterIconVisible = document.querySelector("#icon-filter");
  const closeFilterIconHidden = document.querySelector("#icon-close");
  const filterBox = document.querySelector(".filter-box");

  // Check if filterIcon exists before adding event listener
  if (filterIcon) {
      filterIcon.addEventListener("click", () => {
          if (filterIconVisible && closeFilterIconHidden && filterBox) {
              // Toggle visibility of icons based on filter box visibility
              if (filterBox.classList.contains("d-none")) {
                  // Filter box is hidden, so show the "filter" icon and hide the "close" icon
                  closeFilterIconHidden.classList.remove("d-none");
                  filterIconVisible.classList.add("d-none");
              } else {
                  // Filter box is visible, so hide the "filter" icon and show the "close" icon
                  closeFilterIconHidden.classList.add("d-none");
                  filterIconVisible.classList.add("d-none");
                  filterIconVisible.classList.remove("d-none");
              }
              // Toggle visibility of filter box
              filterBox.classList.toggle("d-none");
          }
      });
  }
});


$('.js_kl__countdown').countdown('2027/01/01', function (event) {
  $(this).html(event.strftime('%w weeks %d days %H:%M:%S'));
});


var swiper = new Swiper(".mySwiper", {
  slidesPerView: 1,
  spaceBetween: 30,
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
  breakpoints: {
    640: {
      slidesPerView: 2,
    },
    768: {
      slidesPerView: 3,
    },
    1024: {
      slidesPerView: 4,
    },
  },
});
var swiper = new Swiper(".saleSwiper", {
  slidesPerView: 1,
});

//tooltips
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

//popovers 
const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))


//jarallax
jarallax(document.querySelectorAll('.jarallax'), {
  speed: 0.2,
});


//header backdrop
const headerBackDrop = document.querySelector('.backdrop-shadow');
const navbarToggler = document.querySelector('.navbar-toggler');

if (navbarToggler) {
    navbarToggler.addEventListener('click', () => {
        if (headerBackDrop) {
            headerBackDrop.classList.remove("d-none");
        }
    });
}

headerBackDrop.addEventListener('click', () => {
  const navbarshow = document.querySelector('#navbarSupportedContent');
  headerBackDrop.classList.add("d-none");
  navbarshow.classList.remove("show");
});


let lastScrollTop = 0;

window.addEventListener("scroll", () => {
    let currentScroll = window.scrollY || document.documentElement.scrollTop;

    if (currentScroll < lastScrollTop) {
        // Downscroll code
        document.querySelector(".navbar")?.classList.add('headerFixed');
    } else {
        // Upscroll code
        document.querySelector(".navbar")?.classList.remove('headerFixed');
    }

    // Additional condition to remove 'headerFixed' class when scrolling up less than 100 pixels
    if (currentScroll < 100) {
        document.querySelector(".navbar")?.classList.remove('headerFixed');
    }

    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; // For Mobile or negative scrolling
}, false);

//product fixed bottom


// medial-swiper
var swiper = new Swiper(".medialSwiper", {
  slidesPerView: 1,
  spaceBetween: 30,
  keyboard: {
    enabled: true,
  },
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
  breakpoints: {
    576: {
      slidesPerView: 2,
    },
    640: {
      slidesPerView: 3,
    },
    1024: {
      slidesPerView: 5,
    },
  },
});

// Wait for window load
window.onload = function () {

    // Show popup only if it has not been closed before
    if (!localStorage.getItem('discountPopupClosed')) {

        setTimeout(function () {
            var discountModal = new bootstrap.Modal(document.getElementById('CODE15OFF'));
            discountModal.show();
        }, 5000);

    }

    // Save flag when popup is closed
    document.getElementById('CODE15OFF').addEventListener('hidden.bs.modal', function () {
        localStorage.setItem('discountPopupClosed', 'true');
    });

};


// Home-sport swiper
var featuredSwiper = new Swiper(".featuredSwiper", {
  loop: true,
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
  breakpoints: {
    300: {
      slidesPerView: 2,
      spaceBetween: 20,
    },
    768: {
      slidesPerView: 3,
      spaceBetween: 40,
    },
    1024: {
      slidesPerView: 4,
      spaceBetween: 50,
    },
  },
});
// Wait for window load
window.onload = function () {

    // Show popup only if it has not been closed before
    if (!localStorage.getItem('discountPopupClosed')) {

        setTimeout(function () {
            var discountModal = new bootstrap.Modal(document.getElementById('CODE15OFF'));
            discountModal.show();
        }, 3000);

    }

    // Save flag when popup is closed
    document.getElementById('CODE15OFF').addEventListener('hidden.bs.modal', function () {
        localStorage.setItem('discountPopupClosed', 'true');
    });

};

// price slider
document.addEventListener('DOMContentLoaded', function () {
  var snapSlider = document?.getElementById('slider-snap');

  if (snapSlider) {
      // Ensure snapValues is always defined
      var snapValues = [
          document?.getElementById('slider-snap-value-lower'),
          document?.getElementById('slider-snap-value-upper')
      ];

      if (typeof noUiSlider !== 'undefined' && noUiSlider !== null) {
          noUiSlider.create(snapSlider, {
              connect: true,
              behaviour: 'tap',
              start: [0, 100],
              range: {
                  'min': [3.50],
                  '10%': [10],
                  '50%': [50],
                  '80%': [80],
                  'max': [100]
              }
          });

          snapSlider.noUiSlider.on('update', function (values, handle) {
              snapValues[handle].innerHTML = values[handle];
          });

          // Convert range object to JSON string and display it
          var range = {
              'min': [3.50],
              '10%': [10],
              '50%': [50],
              '80%': [80],
              'max': [100]
          };
          document.getElementById('range').textContent = JSON.stringify(range, null, '\t');
      }
  }

  const sliders1 = document.querySelectorAll('.slider1');
if (sliders1) {
  
  sliders1.forEach(slider => {
      slider.addEventListener('input', function() {
          updateSliderColor(slider);
      });
      // Initial color fill
      updateSliderColor(slider);
  });

  function updateSliderColor(slider) {
      const value = slider.value;
      if (value == 0) {
          slider.style.background = '#d3d3d3';  // Default slider background color
      } else {
          const percentage = (value - slider.min) / (slider.max - slider.min) * 100;
          const color = `linear-gradient(90deg, #fdbc00 ${percentage}%, #d3d3d3 ${percentage}%)`;
          slider.style.background = color;
      }
  }
}
});
