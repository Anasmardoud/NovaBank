// Function to toggle password visibility
function togglePasswordVisibility(inputId) {
    const passwordInput = document.getElementById(inputId);
    const eyeIcon = document.querySelector(`[data-input="${inputId}"] .eye-icon`);

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye'); // Open eye icon
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash'); // Closed eye icon
    }
}

// Reusable Password Strength Checker Function
function checkPasswordStrength(password) {
    const strength = {
        score: 0,
        hasLength: false,
        hasUppercase: false,
        hasLowercase: false,
        hasNumber: false,
        hasSpecialChar: false,
    };

    // Check password length
    if (password.length >= 8) {
        strength.score += 1;
        strength.hasLength = true;
    }

    // Check for uppercase letters
    if (/[A-Z]/.test(password)) {
        strength.score += 1;
        strength.hasUppercase = true;
    }

    // Check for lowercase letters
    if (/[a-z]/.test(password)) {
        strength.score += 1;
        strength.hasLowercase = true;
    }

    // Check for numbers
    if (/\d/.test(password)) {
        strength.score += 1;
        strength.hasNumber = true;
    }

    // Check for special characters
    if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
        strength.score += 1;
        strength.hasSpecialChar = true;
    }

    return strength;
}

// Function to update password strength message
function updatePasswordStrengthMessage(password, messageElementId) {
    const strength = checkPasswordStrength(password);
    const messageElement = document.getElementById(messageElementId);

    // Clear previous classes and messages
    messageElement.className = 'password-strength-message';
    messageElement.textContent = '';

    if (password.length === 0) {
        messageElement.textContent = ''; // No message if password is empty
        return;
    }

    // Update message and color based on strength
    if (strength.score <= 2) {
        messageElement.classList.add('weak');
        messageElement.textContent = 'Weak: Add more characters, numbers, or special symbols.';
    } else if (strength.score === 3) {
        messageElement.classList.add('medium');
        messageElement.textContent = 'Medium: Good, but could be stronger.';
    } else if (strength.score >= 4) {
        messageElement.classList.add('strong');
        messageElement.textContent = 'Strong: Great job!';
    }
}

// Attach event listener to password input
document.getElementById('client-password').addEventListener('input', function (e) {
    updatePasswordStrengthMessage(e.target.value, 'password-strength-message');
});