//MINE
document.addEventListener('DOMContentLoaded', () => {
  const wrapper = document.querySelector('.wrapper');
  const btnLogin = document.querySelector('.button_login');
  const loginForm = document.querySelector('.form-box.login');
  const registerForm = document.querySelector('.form-box.register');
  const iconClose = document.querySelector('.icon-close');
  const registerLink = document.querySelector('.login-register p a');

  // Default height for login form
  const defaultHeight = '420px';

  // Function to show login form
  function showLoginForm() {
      // Hide register form and show login form
      registerForm.style.transform = 'translateX(100%)';
      loginForm.style.transform = 'translateX(0)';

      // Show wrapper with login form
      wrapper.classList.add('active-popup');
      wrapper.style.zIndex = '9999'; // Ensure wrapper is above other content
      loginForm.style.zIndex = '10000'; // Ensure form is above other content
      wrapper.style.height = defaultHeight;
  }

  // Function to show register form
  function showRegisterForm() {
      // Hide login form and show register form
      loginForm.style.transform = 'translateX(-100%)';
      registerForm.style.transform = 'translateX(0)';

      // Show wrapper with register form
      wrapper.classList.add('active-popup');
      wrapper.style.zIndex = '9999'; // Ensure wrapper is above other content
      registerForm.style.zIndex = '10000'; // Ensure form is above other content
      wrapper.style.height = '480px';
  }

  // Function to close forms
  function closeForms() {
      // Reset form positions
      loginForm.style.transform = 'translateX(0)';
      registerForm.style.transform = 'translateX(100%)';

      // Hide wrapper
      wrapper.classList.remove('active-popup');
      wrapper.style.height = defaultHeight;
  }

  // Add event listener to the login button
  btnLogin.addEventListener('click', showLoginForm);

  // Add event listener to the register link within the login form
  registerLink.addEventListener('click', (event) => {
      event.preventDefault(); // Prevent default link behavior
      showRegisterForm();
  });

  // Add event listener to the login link within the register form
  const loginLink = document.querySelector('.form-box.register .login-register p a');
  loginLink.addEventListener('click', (event) => {
      event.preventDefault(); // Prevent default link behavior
      showLoginForm();
  });

  // Add event listener to the close icon
  iconClose.addEventListener('click', () => {
      closeForms(); // Call the closeForms function
  });
});


//Register form at HOMEPAGE
document.getElementById("registerForm").addEventListener("submit", function(event) {
  event.preventDefault(); // Prevent form submission
  
  // Check if the required fields are filled
  var email = document.getElementById("email").value;
  var password = document.getElementById("password").value;
  var confirmPassword = document.getElementById("confirmPassword").value;
  var terms = document.getElementById("terms").checked;
  
  // Regular expression to check if email contains '@' character
  var emailPattern = /\S+@\S+/;
  
  if (email && emailPattern.test(email) && password && confirmPassword && terms && password === confirmPassword) {
      // If all fields are filled, email contains '@' character, passwords match, and terms are accepted,
      // redirect to the RegisterForm.html page with email and password as URL parameters
      window.location.href = "RegisterForm.php?email=" + encodeURIComponent(email) + "&password=" + encodeURIComponent(password);
  } else {
      // If any required field is empty, email format is incorrect, passwords don't match, or terms are not accepted, show an alert
      alert("Please fill in all required fields correctly, ensure passwords match, and accept the terms and conditions.");
  }
});


(function() {
  "use strict";

  /**
   * Easy selector helper function
   */
  const select = (el, all = false) => {
    el = el.trim()
    if (all) {
      return [...document.querySelectorAll(el)]
    } else {
      return document.querySelector(el)
    }
  }

  /**
   * Easy event listener function
   */
  const on = (type, el, listener, all = false) => {
    let selectEl = select(el, all)
    if (selectEl) {
      if (all) {
        selectEl.forEach(e => e.addEventListener(type, listener))
      } else {
        selectEl.addEventListener(type, listener)
      }
    }
  }

  /**
   * Easy on scroll event listener 
   */
  const onscroll = (el, listener) => {
    el.addEventListener('scroll', listener)
  }

  /**
   * Toggle .navbar-reduce
   */
  let selectHNavbar = select('.navbar-default')
  if (selectHNavbar) {
    onscroll(document, () => {
      if (window.scrollY > 100) {
        selectHNavbar.classList.add('navbar-reduce')
        selectHNavbar.classList.remove('navbar-trans')
      } else {
        selectHNavbar.classList.remove('navbar-reduce')
        selectHNavbar.classList.add('navbar-trans')
      }
    })
  }

  /**
   * Back to top button
   */
  let backtotop = select('.back-to-top')
  if (backtotop) {
    const toggleBacktotop = () => {
      if (window.scrollY > 100) {
        backtotop.classList.add('active')
      } else {
        backtotop.classList.remove('active')
      }
    }
    window.addEventListener('load', toggleBacktotop)
    onscroll(document, toggleBacktotop)
  }

  /**
   * Preloader
   */
  let preloader = select('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.remove()
    });
  }

  /**
   * Search window open/close
   */
  let body = select('body');
  on('click', '.navbar-toggle-box', function(e) {
    e.preventDefault()
    body.classList.add('box-collapse-open')
    body.classList.remove('box-collapse-closed')
  })

  on('click', '.close-box-collapse', function(e) {
    e.preventDefault()
    body.classList.remove('box-collapse-open')
    body.classList.add('box-collapse-closed')
  })

  /**
   * Intro Carousel
   */
  new Swiper('.intro-carousel', {
    speed: 600,
    loop: true,
    autoplay: {
      delay: 2000,
      disableOnInteraction: false
    },
    slidesPerView: 'auto',
    pagination: {
      el: '.swiper-pagination',
      type: 'bullets',
      clickable: true
    }
  });

  /**
   * Property carousel
   */
  new Swiper('#property-carousel', {
    speed: 600,
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false
    },
    slidesPerView: 'auto',
    pagination: {
      el: '.propery-carousel-pagination',
      type: 'bullets',
      clickable: true
    },
    breakpoints: {
      320: {
        slidesPerView: 1,
        spaceBetween: 20
      },

      1200: {
        slidesPerView: 3,
        spaceBetween: 20
      }
    }
  });

  /**
   * News carousel
   */
  new Swiper('#news-carousel', {
    speed: 600,
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false
    },
    slidesPerView: 'auto',
    pagination: {
      el: '.news-carousel-pagination',
      type: 'bullets',
      clickable: true
    },
    breakpoints: {
      320: {
        slidesPerView: 1,
        spaceBetween: 20
      },

      1200: {
        slidesPerView: 3,
        spaceBetween: 20
      }
    }
  });

  /**
   * Testimonial carousel
   */
  new Swiper('#testimonial-carousel', {
    speed: 600,
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false
    },
    slidesPerView: 'auto',
    pagination: {
      el: '.testimonial-carousel-pagination',
      type: 'bullets',
      clickable: true
    }
  });

  /**
   * Property Single carousel
   */
  new Swiper('#property-single-carousel', {
    speed: 600,
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false
    },
    pagination: {
      el: '.property-single-carousel-pagination',
      type: 'bullets',
      clickable: true
    }
  });

})()