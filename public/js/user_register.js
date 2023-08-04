const $password_input = document.querySelector('#user_password');
const $password_error = document.querySelector('#password-error')

$password_input.addEventListener('focus', () => {
  $password_error.style.display = 'none'
})