const form = document.getElementById("input-form");
const output = document.getElementById("output");
let caseNum = 1;

form.addEventListener("submit", handleSubmit);

// *****
async function handleSubmit(e) {
  e.preventDefault();

  // clear the output div before appending new content
  caseNum = 1;
  output.innerHTML = "";

  const backendUrl = "backend.php";

  const formData = new FormData();
  formData.append("practice-area", form.practiceArea.value);
  formData.append("jurisdiction", form.jurisdiction.value);
  formData.append("county", form.county.value);
  formData.append("keywords", form.keywords.value);

  let caseMatches;

  try {
    caseMatches = await client(formData, backendUrl);
  } catch (error) {
    console.error(error);
    output.innerHTML = `An error occurred: ${error.message}`;
    return;
  }

  if (caseMatches.length === 0)
    output.innerHTML =
      "No data available that matches the given parameters. Try being less specific or using fewer keywords.";

  caseMatches.forEach(appendCase);
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
    // Allow user to re-generate summary
    // TODO: Fix, this isn't working
    // But it may not even be necessary.
    btn.disabled = false;

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
