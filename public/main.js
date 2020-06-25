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
        alert("Операция успешно выполнена.");
        if (redirect) window.location.href = redirect;
        else window.location.reload();
      } else {
        let responseData = "";
        try {
          responseData = await response.json();
        } catch (err) {
          console.error(err);
        }
        let errorMessage = `Ошибка ${response.status}: ` + responseData.message || "";
        console.error(errorMessage, response);
        alert(`Ошибка ${response.status}: ` + responseData.message || "");
      }
    })
    .catch((response) => console.error(response));

  return false;
}

function toggleCardEditModal(profileId) {
  $(`#card-edit__modal-${profileId}`).toggleClass("visible");
}

function toggleCardRemoveModal(profileId) {
  $(`#card-remove__modal-${profileId}`).toggleClass("visible");
}

function toggleFormModal(profileId) {
  $(`#form-edit__modal-${profileId}`).toggleClass("visible");
}
