document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.formulario-trabaja');
    const inputs = form.querySelectorAll('input, select, textarea');
    
    // **Configuración de validaciones**
    const validationRules = {
        nombre: {
            required: true,
            minLength: 2,
            pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/,
            message: 'El nombre debe contener solo letras y tener al menos 2 caracteres'
        },
        apellidos: {
            required: true,
            minLength: 2,
            pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/,
            message: 'Los apellidos deben contener solo letras y tener al menos 2 caracteres'
        },
        email: {
            required: true,
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            message: 'Ingresa un correo electrónico válido'
        },
        telefono: {
            required: true,
            pattern: /^[+]?[\d\s\-\(\)]{9,15}$/,
            message: 'El teléfono debe tener entre 9 y 15 dígitos'
        },
        'nivel-academico': {
            required: true,
            message: 'Selecciona tu nivel académico'
        },
        'area-trabajo': {
            required: true,
            message: 'Selecciona el área de trabajo'
        },
        'carta-presentacion': {
            required: false,
            minLength: 50,
            maxLength: 1000,
            message: 'La carta debe tener entre 50 y 1000 caracteres'
        },
        curriculum: {
            required: true,
            fileTypes: ['pdf', 'doc', 'docx'],
            maxSize: 5 * 1024 * 1024, // 5MB
            message: 'Adjunta un archivo PDF, DOC o DOCX (máximo 5MB)'
        },
        politica: {
            required: true,
            message: 'Debes aceptar la política de privacidad'
        }
    };

    // **Función principal de validación**
    function validateField(field) {
        const fieldName = field.name || field.id;
        const rules = validationRules[fieldName];
        
        if (!rules) return true;

        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        // Validación requerido
        if (rules.required && (!value || (field.type === 'checkbox' && !field.checked))) {
            isValid = false;
            errorMessage = rules.message;
        }
        
        // Validación de longitud mínima
        else if (rules.minLength && value.length < rules.minLength) {
            isValid = false;
            errorMessage = rules.message;
        }
        
        // Validación de longitud máxima
        else if (rules.maxLength && value.length > rules.maxLength) {
            isValid = false;
            errorMessage = rules.message;
        }
        
        // Validación de patrón
        else if (rules.pattern && value && !rules.pattern.test(value)) {
            isValid = false;
            errorMessage = rules.message;
        }
        
        // Validación de archivos
        else if (field.type === 'file' && field.files.length > 0) {
            const file = field.files[0];
            const fileExtension = file.name.split('.').pop().toLowerCase();
            
            if (!rules.fileTypes.includes(fileExtension)) {
                isValid = false;
                errorMessage = 'Formato de archivo no válido. Usa PDF, DOC o DOCX';
            } else if (file.size > rules.maxSize) {
                isValid = false;
                errorMessage = 'El archivo es demasiado grande. Máximo 5MB';
            }
        }

        showFieldError(field, isValid, errorMessage);
        return isValid;
    }

    // **Función para mostrar errores**
    function showFieldError(field, isValid, message) {
        const container = field.closest('.form-group') || field.closest('.form-group-full') || field.closest('.form-group-half') || field.closest('.checkbox-container');
        
        const existingError = container.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        
        if (isValid) {
            field.classList.remove('error');
            field.classList.add('valid');
        } else {
            field.classList.remove('valid');
            field.classList.add('error');
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = message;
            container.appendChild(errorDiv);
        }
    }

    // **Validación en tiempo real**
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        if (input.type === 'text' || input.type === 'email' || input.type === 'tel' || input.tagName === 'TEXTAREA') {
            input.addEventListener('input', function() {
                // Validar solo si ya había un error
                if (this.classList.contains('error')) {
                    validateField(this);
                }
            });
        }
        
        // Validar selects al cambiar
        if (input.tagName === 'SELECT') {
            input.addEventListener('change', function() {
                validateField(this);
            });
        }
    });

    // **Validación al enviar el formulario**
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let allValid = true;
        
        inputs.forEach(input => {
            if (!validateField(input)) {
                allValid = false;
            }
        });
        
        if (allValid) {
            showSuccessMessage();
            // form.submit(); // Descomenta para envío real
        } else {
            // Scroll al primer error
            const firstError = form.querySelector('.error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    });

    // **Mensaje de éxito**
    function showSuccessMessage() {
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message';
        successDiv.innerHTML = `
            <h3>¡Formulario enviado correctamente!</h3>
            <p>Gracias por tu interés. Nos pondremos en contacto contigo pronto.</p>
        `;
        
        form.style.display = 'none';
        form.parentNode.insertBefore(successDiv, form);
        
        successDiv.scrollIntoView({ behavior: 'smooth' });
    }
});