// Function to toggle password visibility
function togglePasswordVisibility(inputId) {
    const passwordInput = document.getElementById(inputId);
    const eyeIcon = document.querySelector(`[onclick="togglePasswordVisibility('${inputId}')"] .fas`);

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}

// Function to validate password
function validatePassword() {
    const newPassword = document.getElementById('new-password').value;
    const confirmPassword = document.getElementById('confirm-password').value;

    if (newPassword !== confirmPassword) {
        alert('New password and confirm password do not match.');
        return false;
    }
    return true;
}
// Function to check password strength
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

// Attach event listener to new password input
document.getElementById('new-password').addEventListener('input', function (e) {
    updatePasswordStrengthMessage(e.target.value, 'password-strength-message');
});

// Auto-close toasts after 5 seconds
document.addEventListener('DOMContentLoaded', function () {
    const toasts = document.querySelectorAll('.toast');
    toasts.forEach(toast => {
        setTimeout(() => {
            toast.remove();
        }, 5000); // 5 seconds
    });
});

// Function to toggle Checking Account balance field 
function toggleCheckingAccount() {
    const checkingBalanceGroup = document.getElementById('checking_balance_group');
    const addCheckingAccount = document.getElementById('add_checking_account');
    checkingBalanceGroup.style.display = addCheckingAccount.checked ? 'block' : 'none';
}

function toggleSavingsAccount() {
    const savingsBalanceGroup = document.getElementById('savings_balance_group');
    const addSavingsAccount = document.getElementById('add_savings_account');
    savingsBalanceGroup.style.display = addSavingsAccount.checked ? 'block' : 'none';
}

// Function to validate the "Add Client" form

function validateAddClientForm() {
    const addChecking = document.getElementById('add_checking_account').checked;
    const addSavings = document.getElementById('add_savings_account').checked;
    
    if (!addChecking && !addSavings) {
        alert('Please select at least one account type.');
        return false;
    }

    if (addChecking) {
        const checkingBalance = parseFloat(document.getElementById('checking_balance').value);
        if (isNaN(checkingBalance) || checkingBalance <= 0) {
            alert('Checking account balance must be greater than 0.');
            return false;
        }
    }

    if (addSavings) {
        const savingsBalance = parseFloat(document.getElementById('savings_balance').value);
        if (isNaN(savingsBalance) || savingsBalance <= 0) {
            alert('Savings account balance must be greater than 0.');
            return false;
        }
    }

    return true;
}
// Attach the validation function to the form's submit event
document.querySelector('form').addEventListener('submit', function (e) {
    if (!validateAddClientForm()) {
        e.preventDefault(); // Prevent form submission if validation fails
    }
});

function openEditClientModal(clientId, username, email, phoneNumber, address, status, checkingBalance, savingsBalance) {
    // Populate the form fields
    document.getElementById('edit_client_id').value = clientId;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_phone_number').value = phoneNumber;
    document.getElementById('edit_address').value = address;
    document.getElementById('edit_status').value = status;
    document.getElementById('edit_checking_balance').value = checkingBalance || '';
    document.getElementById('edit_savings_balance').value = savingsBalance || '';

    // Show the modal
    document.getElementById('editClientModal').style.display = 'block';
}

function closeEditClientModal() {
    document.getElementById('editClientModal').style.display = 'none';
}

// Close the modal if the user clicks outside of it
window.onclick = function(event) {
    const modal = document.getElementById('editClientModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
};

// Function to edit a deposit
function editDeposit(depositId) {
    // Redirect to the edit deposit page or open a modal
    window.location.href = `/PHPLearning/NovaBank/public/admin/edit-deposit/${depositId}`;
}

// Function to delete a deposit
function deleteDeposit(depositId) {
    if (confirm('Are you sure you want to delete this deposit?')) {
        fetch(`/PHPLearning/NovaBank/public/admin/delete-deposit/${depositId}`, {
            method: 'DELETE',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Deposit deleted successfully.');
                location.reload(); // Reload the page to reflect changes
            } else {
                alert('Failed to delete deposit.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

// Function to validate the deposit form
function validateDepositForm() {
    const accountId = document.getElementById('account_id').value;
    const amount = document.getElementById('amount').value;

    if (!accountId || !amount) {
        alert('Please fill in all fields.');
        return false;
    }

    if (amount <= 0) {
        alert('Amount must be greater than 0.');
        return false;
    }

    return true;
}