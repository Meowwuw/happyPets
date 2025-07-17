document.addEventListener("DOMContentLoaded", function () {
  const contactForm = document.querySelector(".formulario-contacto");

  if (!contactForm) return;

  const contactInputs = contactForm.querySelectorAll("input, textarea");

  const contactValidationRules = {
    nombre: {
      required: true,
      minLength: 2,
      maxLength: 50,
      pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/,
      message: "El nombre debe contener solo letras (2-50 caracteres)",
    },
    telefono: {
      required: true,
      pattern: /^[+]?[\d\s\-\(\)]{9,15}$/,
      message: "Ingresa un teléfono válido (9-15 dígitos)",
    },
    correo: {
      required: true,
      pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
      message: "Ingresa un correo electrónico válido",
    },
    sede: {
      required: true,
      message: "La sede es obligatoria",
    },
    mensaje: {
      required: true,
      minLength: 10,
      maxLength: 500,
      message: "El mensaje debe tener entre 10 y 500 caracteres",
    },
    politica: {
      required: true,
      message: "Debes aceptar la política de privacidad",
    },
  };

  // **Función principal de validación**
  function validateContactField(field) {
    const fieldName = field.name || field.id;
    const rules = contactValidationRules[fieldName];

    if (!rules) return true;

    const value = field.value.trim();
    let isValid = true;
    let errorMessage = "";

    // Validación campo requerido
    if (
      rules.required &&
      (!value || (field.type === "checkbox" && !field.checked))
    ) {
      isValid = false;
      errorMessage = rules.message;
    }

    // Validación longitud mínima
    else if (
      rules.minLength &&
      value.length > 0 &&
      value.length < rules.minLength
    ) {
      isValid = false;
      errorMessage = rules.message;
    }

    // Validación longitud máxima
    else if (rules.maxLength && value.length > rules.maxLength) {
      isValid = false;
      errorMessage = rules.message;
    }

    // Validación patrón
    else if (rules.pattern && value && !rules.pattern.test(value)) {
      isValid = false;
      errorMessage = rules.message;
    }

    // **Validaciones específicas adicionales**
    if (isValid && value) {
      switch (fieldName) {
        case "correo":
          const commonDomains = [
            "gmail.com",
            "hotmail.com",
            "yahoo.com",
            "outlook.com",
          ];
          const domain = value.split("@")[1];
          if (
            domain &&
            !commonDomains.includes(domain) &&
            !domain.includes(".pe") &&
            !domain.includes(".com")
          ) {
            // Advertencia, pero no error
            showFieldWarning(
              field,
              "Verifica que el dominio del correo sea correcto"
            );
          }
          break;

        case "telefono":
          const cleanPhone = value.replace(/[\s\-\(\)]/g, "");
          if (cleanPhone.length === 9 && !cleanPhone.startsWith("9")) {
            showFieldWarning(
              field,
              "Los teléfonos móviles en Perú suelen empezar con 9"
            );
          }
          break;
      }
    }

    showContactFieldError(field, isValid, errorMessage);
    return isValid;
  }

  function showContactFieldError(field, isValid, message) {
    let container;
    if (field.type === "checkbox") {
      container = field.closest(".politica");
    } else {
      container = field.parentElement;
    }

    const existingError = container.querySelector(".contact-error-message");
    const existingWarning = container.querySelector(".contact-warning-message");

    if (existingError) existingError.remove();
    if (existingWarning) existingWarning.remove();

    if (isValid) {
      field.classList.remove("contact-error");
      field.classList.add("contact-valid");
    } else {
      field.classList.remove("contact-valid");
      field.classList.add("contact-error");

      const errorDiv = document.createElement("div");
      errorDiv.className = "contact-error-message";
      errorDiv.textContent = message;

      if (field.type === "checkbox") {
        container.appendChild(errorDiv);
      } else {
        field.parentNode.insertBefore(errorDiv, field.nextSibling);
      }
    }
  }

  // **Función para mostrar advertencias**
  function showFieldWarning(field, message) {
    const container = field.parentElement;

    const existingWarning = container.querySelector(".contact-warning-message");
    if (existingWarning) existingWarning.remove();

    const warningDiv = document.createElement("div");
    warningDiv.className = "contact-warning-message";
    warningDiv.textContent = message;

    field.parentNode.insertBefore(warningDiv, field.nextSibling);

    setTimeout(() => {
      if (warningDiv.parentElement) {
        warningDiv.remove();
      }
    }, 4000);
  }

  // **Contador de caracteres para el mensaje**
  const mensajeField = contactForm.querySelector("#mensaje");
  if (mensajeField) {
    const maxLength = contactValidationRules.mensaje.maxLength;

    const counterDiv = document.createElement("div");
    counterDiv.className = "contact-char-counter";
    mensajeField.parentElement.appendChild(counterDiv);

    function updateCounter() {
      const currentLength = mensajeField.value.length;
      const remaining = maxLength - currentLength;

      counterDiv.textContent = `${currentLength}/${maxLength} caracteres`;

      if (remaining < 50) {
        counterDiv.classList.add("contact-warning");
      } else {
        counterDiv.classList.remove("contact-warning");
      }

      if (remaining < 0) {
        counterDiv.classList.add("contact-error");
      } else {
        counterDiv.classList.remove("contact-error");
      }
    }

    mensajeField.addEventListener("input", updateCounter);
    updateCounter();
  }

  contactInputs.forEach((input) => {
    input.addEventListener("blur", function () {
      validateContactField(this);
    });

    if (
      input.type === "text" ||
      input.type === "email" ||
      input.type === "tel" ||
      input.tagName === "TEXTAREA"
    ) {
      input.addEventListener("input", function () {
        if (this.classList.contains("contact-error")) {
          validateContactField(this);
        }
      });
    }
  });

  // **Validación al enviar formulario**
  contactForm.addEventListener("submit", function (e) {
    e.preventDefault();

    let allContactValid = true;

    contactInputs.forEach((input) => {
      if (!validateContactField(input)) {
        allContactValid = false;
      }
    });

    if (allContactValid) {
      showContactLoading();

      setTimeout(() => {
        hideContactLoading();
        showContactSuccess();
      }, 2000);

      fetch(contactForm.action, {
        method: "POST",
        body: new FormData(contactForm),
      })
        .then((response) => {
          if (response.ok) {
            showContactSuccess();
          } else {
            showContactGeneralError();
          }
        })
        .catch(() => {
          showContactGeneralError();
        })
        .finally(() => {
          hideContactLoading();
        });
    } else {
      const firstContactError = contactForm.querySelector(".contact-error");
      if (firstContactError) {
        firstContactError.scrollIntoView({
          behavior: "smooth",
          block: "center",
        });
        firstContactError.focus();
      }

      showContactGeneralError();
    }
  });

  // **Funciones de feedback visual**
  function showContactLoading() {
    const submitBtn = contactForm.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="loading-spinner"></span> ENVIANDO...';
    submitBtn.classList.add("loading");
  }

  function hideContactLoading() {
    const submitBtn = contactForm.querySelector('button[type="submit"]');
    submitBtn.disabled = false;
    submitBtn.innerHTML = "ENVIAR";
    submitBtn.classList.remove("loading");
  }

  function showContactSuccess() {
    const successDiv = document.createElement("div");
    successDiv.className = "contact-success-message";
    successDiv.innerHTML = `
            <div class="success-icon">✓</div>
            <h3>¡Mensaje enviado correctamente!</h3>
            <p>Gracias por contactarnos. Te responderemos a la brevedad.</p>
        `;

    contactForm.style.display = "none";
    contactForm.parentNode.insertBefore(successDiv, contactForm);

    successDiv.scrollIntoView({ behavior: "smooth" });

    setTimeout(() => {
      contactForm.reset();
      contactForm.style.display = "block";
      successDiv.remove();

      contactInputs.forEach((input) => {
        input.classList.remove("contact-valid", "contact-error");
      });
    }, 5000);
  }

  function showContactGeneralError() {
    const existingError = contactForm.querySelector(".contact-general-error");
    if (existingError) {
      existingError.remove();
    }

    const errorDiv = document.createElement("div");
    errorDiv.className = "contact-general-error";
    errorDiv.textContent = "Por favor, corrige los errores marcados en rojo";

    const submitBtn = contactForm.querySelector('button[type="submit"]');
    submitBtn.parentElement.insertBefore(errorDiv, submitBtn);

    setTimeout(() => {
      if (errorDiv.parentElement) {
        errorDiv.remove();
      }
    }, 4000);
  }
});
