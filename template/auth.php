<?php


function render_auth()
{
?>
  <div class="plus shadow">
    <form action="/api/auth.php" onsubmit="return onFormSubmit(this)" class="form">
      <input name="username">
      <input name="password" type="password">
      <button type="submit"> Войти </button>
    </form>
  </div>
<?php
}
