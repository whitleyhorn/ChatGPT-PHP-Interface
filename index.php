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
        <b><label for="practiceArea">Practice Area:</label></b>
        <input type="text" name="practiceArea" id="practiceArea">
        <br>
          <b><label for="jurisdiction">Jurisdiction:</label></b>
          <select name="jurisdiction" id="jurisdiction">
          <option value="">Please Select</option><option value="ala" style="display: none;">Alabama</option><option value="alaska" style="display: none;">Alaska</option><option value="am-samoa" style="display: none;">AmericanSamoa</option><option value="ariz" style="display: none;">Arizona</option><option value="ark">Arkansas</option><option value="cal" style="display: none;">California</option><option value="colo" style="display: none;">Colorado</option><option value="conn" style="display: none;">Connecticut</option><option value="dakota-territory" style="display: none;">DakotaTerritory</option><option value="dc" style="display: none;">DistrictofColumbia</option><option value="del" style="display: none;">Delaware</option><option value="fla" style="display: none;">Florida</option><option value="ga" style="display: none;">Georgia</option><option value="guam" style="display: none;">Guam</option><option value="haw" style="display: none;">Hawaii</option><option value="idaho" style="display: none;">Idaho</option><option value="ill">Illinois</option><option value="ind" style="display: none;">Indiana</option><option value="iowa" style="display: none;">Iowa</option><option value="kan" style="display: none;">Kansas</option><option value="ky" style="display: none;">Kentucky</option><option value="la" style="display: none;">Louisiana</option><option value="mass" style="display: none;">Massachusetts</option><option value="md" style="display: none;">Maryland</option><option value="me" style="display: none;">Maine</option><option value="mich" style="display: none;">Michigan</option><option value="minn" style="display: none;">Minnesota</option><option value="miss" style="display: none;">Mississippi</option><option value="mo" style="display: none;">Missouri</option><option value="mont" style="display: none;">Montana</option><option value="native-american" style="display: none;">NativeAmerican</option><option value="navajo-nation" style="display: none;">NavajoNation</option><option value="nc">NorthCarolina</option><option value="nd" style="display: none;">NorthDakota</option><option value="neb" style="display: none;">Nebraska</option><option value="nev" style="display: none;">Nevada</option><option value="nh" style="display: none;">NewHampshire</option><option value="nj" style="display: none;">NewJersey</option><option value="nm">NewMexico</option><option value="n-mar-i" style="display: none;">NorthernMarianaIslands</option><option value="ny" style="display: none;">NewYork</option><option value="ohio" style="display: none;">Ohio</option><option value="okla" style="display: none;">Oklahoma</option><option value="or" style="display: none;">Oregon</option><option value="pa" style="display: none;">Pennsylvania</option><option value="pr" style="display: none;">PuertoRico</option><option value="regional" style="display: none;">Regional</option><option value="ri" style="display: none;">RhodeIsland</option><option value="sc" style="display: none;">SouthCarolina</option><option value="sd" style="display: none;">SouthDakota</option><option value="tenn" style="display: none;">Tennessee</option><option value="tex" style="display: none;">Texas</option><option value="tribal" style="display: none;">TribalJurisdictions</option><option value="uk" style="display: none;">UnitedKingdom</option><option value="us" style="display: none;">UnitedStates</option><option value="utah" style="display: none;">Utah</option><option value="va" style="display: none;">Virginia</option><option value="vi" style="display: none;">VirginIslands</option><option value="vt" style="display: none;">Vermont</option><option value="wash" style="display: none;">Washington</option><option value="wis" style="display: none;">Wisconsin</option><option value="w-va" style="display: none;">WestVirginia</option><option value="wyo" style="display: none;">Wyoming</option></select>
        
        <br>
        <b><label for="county">County (optional):</label></b>
        <div>Some examples:
          <div>
            Illinois:
            Cook County,
            DuPage County,
            Lake County,
            McHenry County,
            Winnebago County,
          </div>

          <div>
            Arkansas:
            Pulaski County,
            Washington County,
            Benton County,
            Sebastian County,
            Faulkner County,
          </div>

          <div>
            New Mexico:
            Bernalillo County,
            Dona Ana County,
            Santa Fe County,
            Sandoval County,
            San Juan County,
          </div>

          <div>
            North Carolina:
            Mecklenburg County,
            Wake County,
            Guilford County,
            Forsyth County,
            Durham County,
          </div>
        </div>

        <input type="text" name="county" id="county" placeholder="Cook County">
        <br>
        <b><label for="keywords">Keywords (optional, up to 3, comma-separated):</label></b>
        <input type="text" name="keywords" id="keywords">
      </div>
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

      

