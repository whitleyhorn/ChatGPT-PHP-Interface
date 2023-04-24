<!DOCTYPE html>
<html>
<head>
  <title>ChatGPT-PHP-Interface - Generate Email</title>
</head>
<body>
  <h1>ChatGPT-PHP-Interface - Generate Email</h1>
  <form id="email-form">
    <p>Please enter some notes from a sales call or meeting, and the API will generate a professional sales email based on your notes.</p>
    <p>EXAMPLE NOTES: Spoke with John, he's interested but needs more details. Asked about features and pricing. Mentioned budget constraints but open to negotiation. Mentioned timeline for decision is next week. Follow up call scheduled for Friday at 3pm.</p>
    <div id="email-inputs">
      <label for="input">Enter sales call notes:</label>
      <br>
      <textarea name="input" id="input" rows="10" cols="50"></textarea>
      <br>
    </div>
    <input type="submit" value="Generate">
  </form>
  <div id="output-container">
    <h2>Output</h2>
    <div id="output"></div>
  </div>
  <script src="index.js" type="text/javascript"></script>
</body>
</html>

