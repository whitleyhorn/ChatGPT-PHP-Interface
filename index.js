const form = document.getElementById("input-form");
const output = document.getElementById("output");

const updateInstructions = () => {
  const type = document.getElementById("type").value;
  const emailContainer = document.getElementById("email-container");
  const contentContainer = document.getElementById("content-container");

  emailContainer.style.display = type === "email" ? "block" : "none";
  contentContainer.style.display = type === "content" ? "block" : "none";
};

// Show content section initially for testing
document.getElementById("type").value = "content";
updateInstructions();

form.addEventListener("submit", (e) => {
  e.preventDefault();

  const type = form.type.value;
  const input = form.input.value;
  const url = type === "email" ? "generate_email.php" : "generate_content.php";

  const data = new FormData();
  data.append("input", input);

  if (type === "content") {
    const { practiceArea, jurisdiction, county, keywords } = form;
    data.append("practice-area", practiceArea.value);
    data.append("jurisdiction", jurisdiction.value);
    data.append("county", county.value);
    data.append("keywords", keywords.value);
  }

  fetch(url, {
    method: "POST",
    body: data,
  })
    .then((response) => response[type === "content" ? "json" : "text"]())
    .then((content) => {
      let outputHTML = "";
      let caseNum = 1;
      let errors = [];

      if (type === "content") {
        // remove objects with errors from the content array
        content = content.filter((obj) => {
          if (obj.hasOwnProperty("error")) {
            errors.push(obj.error);
            return false;
          }
          return true;
        });

        // display any errors in the output element
        if (errors.length > 0) {
          const errorMessage = `The following errors occurred:\n${errors.join(
            "\n"
          )}`;
          output.innerHTML = errorMessage;
          return;
        }
        content.forEach((obj) => {
          outputHTML += `<div><h3>Case #${caseNum++}</h3><p>${
            obj.post
          }</p></div>`;
        });
      } else {
        outputHTML = content;
      }

      output.innerHTML = outputHTML;
    })
    .catch((error) => {
      console.error(error);
      output.innerHTML = `An error occurred: ${error.message}`;
    });
});
