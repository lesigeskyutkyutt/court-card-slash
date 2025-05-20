// Toggle between login and signup forms
const toggleForm = document.getElementById('toggleForm');
const loginForm = document.getElementById('loginForm');
const signupForm = document.getElementById('signupForm');
const downloadBtn = document.getElementById('downloadBtn');

toggleForm.addEventListener('click', (e) => {
    e.preventDefault();
    if (loginForm.style.display === 'none') {
        loginForm.style.display = 'block';
        signupForm.style.display = 'none';
        toggleForm.textContent = "Don't have an account? Sign up";
    } else {
        loginForm.style.display = 'none';
        signupForm.style.display = 'block';
        toggleForm.textContent = 'Already have an account? Login';
    }
})