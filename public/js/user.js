// oculta alertas de sucesso ou erro nas telas de login e registro
const $email_input = document.querySelector('#user_email')
const $password_input = document.querySelector('#user_password')
const $password_error = document.querySelector('#alert_error_password')
const $register_error = document.querySelector('#alert_error_register')
const $login_error = document.querySelector('#alert_login_error')

if ($register_error) {
  $email_input.addEventListener('focus', () => {
    $register_error.style.display = 'none'
  })
}

if ($login_error) {
  $email_input.addEventListener('focus', () => {
    $login_error.style.display = 'none'
  })

  $password_input.addEventListener('focus', () => {
    $login_error.style.display = 'none'
  })
}

if ($password_error) {
  $password_input.addEventListener('focus', () => {
  $password_error.style.display = 'none'
})
}