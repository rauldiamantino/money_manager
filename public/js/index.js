// exibe alertas de sucesso ou erro nas telas do painel do usuário
const alert_create_account = document.querySelector('#alert_create_account')
const alert_create_category = document.querySelector('#alert_create_category')
const alert_add_transaction = document.querySelector('#alert_add_transaction')

if (alert_create_account) {
  display_alert(alert_create_account)
}

if (alert_create_category) {
  display_alert(alert_create_category)
}

if (alert_add_transaction) {
  display_alert(alert_add_transaction)
}

function display_alert(alert_div) {
  setTimeout(() => {
    alert(alert_div.innerText)
  }, 100)
}

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