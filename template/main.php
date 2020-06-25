<?php


function render_auth()
{
?>
  <div class="plus shadow" style="margin-left:50vh; margin-top: 20vh;">
    <form action="/api/auth.php" onsubmit="return onFormSubmit(this)" class="form">
      <input name="username">
      <input name="password" type="password">
      <button type="submit"> Войти </button>
    </form>
  </div>
<?php
}
