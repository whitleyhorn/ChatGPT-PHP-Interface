<!DOCTYPE html>
<html>
<head>
  <title>ChatGPT-PHP-Interface</title>
</head>
<body>
  <h1>ChatGPT-PHP-Interface</h1>
  <form id="input-form">
    <label for="type">Select Type:</label>
    <select name="type" id="type" onchange="updateInstructions()">
      <option value="email">Generate Email</option>
      <option value="content">Generate Content</option>
    </select>
    <br/>
    <p>Instructions:</p>
    <div id="email-container" style="display: block;">
      <p>Please enter some notes from a sales call or meeting, and the API will generate a professional sales email based on your notes.</p>
      <p>EXAMPLE NOTES: Spoke with John, he's interested but needs more details. Asked about features and pricing. Mentioned budget constraints but open to negotiation. Mentioned timeline for decision is next week. Follow up call scheduled for Friday at 3pm.</p>
      <div id="email-inputs">
        <label for="input">Enter sales call notes:</label>
        <br>
        <textarea name="input" id="input" rows="10" cols="50"></textarea>
        <br>
      </div>
    </div>
    <div id="content-container" style="display: none;">
      <p>Please enter the following information, and the API will generate 100 word descriptions of 3 interesting cases based on your keywords.</p>
      <div id="content-inputs">
        <label for="practice-area">Practice Area:</label>
        <input type="text" name="practice-area" id="practice-area">
        <br>
        <label for="location">Location (County, State):</label>
        <input type="text" name="location" id="location">
        <br>
        <label for="keywords">Keywords (up to 3, comma-separated):</label>
        <input type="text" name="keywords" id="keywords">
      </div>
    </div>
    <input type="submit" value="Generate">
  </form>
  <div id="output-container">
    <h2>Output</h2>
    <div id="output"></div>
  </div>

  <script>
    const form = document.getElementById('input-form');
    const output = document.getElementById('output');

    form.addEventListener('submit', e => {
      e.preventDefault();

      const type = form.type.value;
      const input = form.input.value;

      const data = new FormData();
      data.append('input', input);

      if (type === 'email') {
        const url = 'generate_email.php';

        fetch(url, {
          method: 'POST',
          body: data
        })
        .then(response => response.text())
        .then(text => {
          output.innerHTML = text;
        })
        .catch(error => console.error(error));

      } else if (type === 'content') {
        const practiceArea = document.getElementById('practice-area').value;
        const location = document.getElementById('location').value;
        const keywords = document.getElementById('keywords').value;

        const data = new FormData();
        data.append('practice-area', practiceArea);
        data.append('location', location);
        data.append('keywords', keywords);

        const url = 'generate_content.php';

        fetch(url, {
          method: 'POST',
          body: data
        })
        .then(response => response.text())
        .then(text => {
          output.innerHTML = text;
        })
        .catch(error => console.error(error));
      }
    });

    function updateInstructions() {
      const type = document.getElementById('type').value;
      const emailContainer = document.getElementById('email-container');
      const emailInputs = document.getElementById('email-inputs');
      const contentContainer = document.getElementById('content-container');
      const contentInputs = document.getElementById('content-inputs');

      if (type === 'email') {
        emailContainer.style.display = 'block';
        contentContainer.style.display = 'none';
      } else if (type === 'content') {
        emailContainer.style.display = 'none';
        contentContainer.style.display = 'block';
      }
    }
  </script>
</body>
</html>

      

