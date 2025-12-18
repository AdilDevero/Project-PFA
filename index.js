document.getElementById('contactForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const plane = document.querySelector('.paper-plane');
  const success = document.getElementById('successMessage');

  plane.classList.add('animate-plane');
  success.style.display = 'block';

  setTimeout(() => {
    plane.classList.remove('animate-plane');
    success.style.display = 'none';
  }, 3000);
});
document.getElementById('contactForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const inputs = this.querySelectorAll('input, textarea');
  let isValid = true;

  // Reset previous styles
  inputs.forEach(input => {
    input.style.border = '';
  });

  // Validate each input
  inputs.forEach(input => {
    if (!input.value.trim()) {
      input.style.border = '2px solid red';
      isValid = false;
    }

    // Email validation
    if (input.type === 'email') {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(input.value.trim())) {
        input.style.border = '2px solid red';
        isValid = false;
      }
    }

    // Phone validation (basic numeric check, 8-15 digits)
    if (input.type === 'tel') {
      const phoneRegex = /^\d{8,15}$/;
      if (!phoneRegex.test(input.value.trim())) {
        input.style.border = '2px solid red';
        isValid = false;
      }
    }
  });

  if (isValid) {
    const plane = document.querySelector('.paper-plane');
    const success = document.getElementById('successMessage');

    plane.classList.add('animate-plane');
    success.style.display = 'block';

    // Reset form after a short delay
    setTimeout(() => {
      plane.classList.remove('animate-plane');
      success.style.display = 'none';
      this.reset();
    }, 3000);
  }
});
document.getElementById('contactForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const inputs = this.querySelectorAll('input, textarea');
  let isValid = true;

  const firstName = document.getElementById('firstName');
  const lastName = document.getElementById('lastName');
  const nameError = document.getElementById('nameError');

  // Reset styles
  nameError.style.display = 'none';
  inputs.forEach(input => input.style.border = '');

  // Validate first & last name (only letters allowed, at least 2 characters)
  const nameRegex = /^[A-Za-zÀ-ÿ\s'-]{2,}$/;

  if (!nameRegex.test(firstName.value.trim()) || !nameRegex.test(lastName.value.trim())) {
    firstName.style.border = '2px solid red';
    lastName.style.border = '2px solid red';
    nameError.style.display = 'block';
    isValid = false;
  }

  // Validate other fields
  inputs.forEach(input => {
    if (!input.value.trim()) {
      input.style.border = '2px solid red';
      isValid = false;
    }

    // Email validation
    if (input.type === 'email') {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(input.value.trim())) {
        input.style.border = '2px solid red';
        isValid = false;
      }
    }

    // Phone validation
    if (input.type === 'tel') {
      const phoneRegex = /^\d{8,15}$/;
      if (!phoneRegex.test(input.value.trim())) {
        input.style.border = '2px solid red';
        isValid = false;
      }
    }
  });

  if (isValid) {
    const plane = document.querySelector('.paper-plane');
    const success = document.getElementById('successMessage');

    plane.classList.add('animate-plane');
    success.style.display = 'block';

    setTimeout(() => {
      plane.classList.remove('animate-plane');
      success.style.display = 'none';
      this.reset();
    }, 3000);
  }
});
