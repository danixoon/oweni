function onFormSubmit(form, redirect) {
  const data = new FormData(form);
  const url = form.getAttribute("action");
  const method = form.getAttribute("method") || "GET";

  const query = new URLSearchParams(data).toString();

  console.log(data);

  fetch(`${url}/?${query}`, { method, body: method !== "GET" ? data : undefined })
    .then(async (response) => {
      if (response.ok) {
        console.log(await response.text());
        if (redirect) window.location.href = redirect
        else window.location.reload();
      }
      else {
        let responseData = "";
        try {
          responseData = await response.json();
        } catch (err) {
          console.error(err);
        }
        let errorMessage = `Ошибка ${response.status}: ` + responseData || "";
        console.error(errorMessage);
        alert(`Ошибка ${response.status}: ` + responseData || "");
      }
    })
    .catch((response) => console.error(response));

  return false;
}
