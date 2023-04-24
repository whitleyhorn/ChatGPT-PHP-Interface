const form = document.getElementById("email-form");
const output = document.getElementById("output");
const generateBtn = document.querySelector('input[type="submit"]');

form.addEventListener("submit", handleSubmit);

async function handleSubmit(e) {
  e.preventDefault();

  // Disable submit button
  generateBtn.disabled = true;

  const input = form.input.value;

  const formData = new FormData();
  formData.append("input", input);

  let data;

  try {
    data = await client(formData, "backend.php");
  } catch (error) {
    console.error(error);
    output.innerHTML = `An error occurred: ${error.message}`;
    // Enable submit button
    generateBtn.disabled = false;
    return;
  }

  output.innerHTML = data;

  // Enable submit button
  generateBtn.disabled = false;
}

async function client(data, endpoint) {
  let headers = {};

  if (!(data instanceof FormData)) {
    headers = {
      "Content-Type": "application/json",
    };
    data = JSON.stringify(data);
  }

  const response = await fetch(endpoint, {
    method: "POST",
    body: data,
    headers,
  });

  const responseData = await response.json();

  if (response.status >= 400) {
    throw new Error(responseData.error);
  }

  return responseData;
}
