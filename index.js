const form = document.getElementById("input-form");
const output = document.getElementById("output");
let caseNum = 1;

const updateInstructions = () => {
  const type = document.getElementById("type").value;
  const emailContainer = document.getElementById("email-container");
  const contentContainer = document.getElementById("content-container");

  emailContainer.style.display = type === "email" ? "block" : "none";
  contentContainer.style.display = type === "content" ? "block" : "none";
};

document.getElementById("type").value = "content";
updateInstructions();

form.addEventListener("submit", handleSubmit);

// *****
async function handleSubmit(e) {
  e.preventDefault();

  const type = form.type.value;
  const input = form.input.value;
  const url = type === "email" ? "generate_email.php" : "generate_content.php";

  const formData = new FormData();
  formData.append("input", input);

  if (type === "content") {
    const { practiceArea, jurisdiction, county, keywords } = form;
    formData.append("practice-area", practiceArea.value);
    formData.append("jurisdiction", jurisdiction.value);
    formData.append("county", county.value);
    formData.append("keywords", keywords.value);
  }

  let data;

  try {
    data = await client(formData, url);
    if (type === "email") {
      output.innerHTML = data;
      return;
    }
  } catch (error) {
    console.error(error);
    output.innerHTML = `An error occurred: ${error.message}`;
    return;
  }

  data.forEach(appendCase);
}

function appendCase(caseInfo) {
  const caseDiv = document.createElement("div");
  caseDiv.innerHTML = `<h3>Case #${caseNum++}</h3>
      <h4><a href="${caseInfo.frontend_url}">${caseInfo.name}</a></h4>
      <p>Decision Date: ${caseInfo.decision_date}</p>
      <p>Court: ${caseInfo.court}</p>
      <p>Opinion: ${caseInfo.opinion.substring(0, 1000)}</p>
      <button class="get-summary">Get summary</button>`;
  output.appendChild(caseDiv);

  const getSummaryBtn = caseDiv.querySelector(".get-summary");
  getSummaryBtn.addEventListener(
    "click",
    getSummary.bind(null, getSummaryBtn, caseInfo.opinion, caseDiv)
  );
}

async function getSummary(btn, opinion, caseDiv) {
  btn.disabled = true;
  try {
    const summary = await client(
      { opinion },
      "functions/get_case_extraction.php"
    );
    caseDiv.innerHTML += `<div><h4>Summary</h4><p>${summary}</p></div>
        <button class="get-blog-post">Get Blog Post</button>`;
    btn.remove();

    const getBlogPostBtn = caseDiv.querySelector(".get-blog-post");
    getBlogPostBtn.addEventListener(
      "click",
      getBlogPost.bind(null, getBlogPostBtn, summary, caseDiv)
    );
  } catch (error) {
    console.error(error);
    caseDiv.innerHTML += `<div><h4>Error</h4><p>${error.message}</p></div>`;
  }
}

async function getBlogPost(btn, summary, caseDiv) {
  btn.disabled = true;
  try {
    const blogPost = await client({ summary }, "functions/get_blog_post.php");
    caseDiv.innerHTML += `<div><h4>Blog Post</h4><p>${blogPost}</p></div>`;
    btn.disabled = false;
  } catch (error) {
    console.error(error);
    caseDiv.innerHTML += `<div><h4>Error</h4><p>${error.message}</p></div>`;
  }
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
