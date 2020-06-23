function onFormSubmit(form) {
  const data = new FormData(form);
  const url = form.getAttribute("action");
  const method = form.getAttribute("method") || "GET";

  const query = new URLSearchParams(data).toString();

  fetch(`${url}/?${query}`, { method })
    .then(async (response) => {
      if (response.ok) window.location.reload();
      else {
        let responseData = "";
        try {
          responseData = await response.json();
        } catch (err) {}
        alert(`Ошибка ${response.status}: ` + responseData || "");
      }
    })
    .catch((response) => console.error(response));

  return false;
}
