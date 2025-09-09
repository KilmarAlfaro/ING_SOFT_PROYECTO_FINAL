import './bootstrap';


<script>
const togglePassword = document.getElementById('togglePassword');
const password = document.getElementById('password');
togglePassword.addEventListener('click', () => {
  const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
  password.setAttribute('type', type);
});

const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
const passwordConfirm = document.getElementById('password_confirmation');
togglePasswordConfirm.addEventListener('click', () => {
  const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
  passwordConfirm.setAttribute('type', type);
});
</script>

