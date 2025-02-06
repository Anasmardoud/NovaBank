// Toast Notification for Transaction Details
document.querySelectorAll('.notification-icon').forEach(icon => {
  icon.addEventListener('click', () => {
    const transaction = icon.closest('li');
    const date = transaction.querySelector('.date').textContent;
    const from = transaction.querySelector('.from').textContent;
    const to = transaction.querySelector('.to').textContent;
    const amount = transaction.querySelector('.amount').textContent;

    const toast = document.createElement('div');
    toast.className = 'toast success';
    toast.innerHTML = `
      <span>Transaction Details:</span>
      <ul>
        <li>Date: ${date}</li>
        <li>From: ${from}</li>
        <li>To: ${to}</li>
        <li>Amount: ${amount}</li>
      </ul>
      <button class="toast-close">×</button>
    `;

    document.getElementById('toast-container').appendChild(toast);
    setTimeout(() => toast.remove(), 5000); // Auto-close after 5 seconds
  });
});
// Function to toggle password visibility
        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling;
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    