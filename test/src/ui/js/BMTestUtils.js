// Test utilities belong to the BMTestUtils module
var BMTestUtils = {};

// Utility to get all elements in the document DOM and all javascript
// variables used by buttonmen code
// This is used to detect whether modules are erroneously modifying
// the DOM or other modules, and to make sure we're correctly
// cleaning up everything between tests.
BMTestUtils.getAllElements = function() {

  // Populate DOM element info
  var elementInfo = [];
  var allElements = document.getElementsByTagName("*");
  for (var i=0, max=allElements.length; i < max; i++) {
    var elemNode = allElements[i].nodeName;
    var elemId = allElements[i].id;
    var elemClass = allElements[i].className;

    // Skip module-name and test-name SPAN elements created by QUnit itself
    if ((elemNode == "SPAN") && (elemId == "") &&
        ((elemClass == "module-name") || (elemClass == "test-name") ||
         (elemClass == "passed") || (elemClass == "total") ||
         (elemClass == "failed"))) {
      continue;
    }

    elementInfo.push(
      "node=" + elemNode + ", id=" + elemId + ", class=" + elemClass
    );
  }

  // Populate javascript variable info
  var jsInfo = {
    'Api':      JSON.stringify(Api, null, "  "),
    'Env':      JSON.stringify(Env, null, "  "),
    'Game':     JSON.stringify(Game, null, "  "),
    'Login':    JSON.stringify(Login, null, "  "),
    'Newgame':  JSON.stringify(Newgame, null, "  "),
    'Newuser':  JSON.stringify(Newuser, null, "  "),
    'Overview': JSON.stringify(Overview, null, "  "),
  };
  
  return {
    'DOM': elementInfo,
    'JS': jsInfo
  };
}

// Other modules may set Env.message, so have a central test utility
// to clean it up
BMTestUtils.deleteEnvMessage = function() {
  delete Env.message;
  $('#env_message').remove();
  $('#env_message').empty();
}

// Fake player login information for other functions to use
BMTestUtils.setupFakeLogin = function() {
  BMTestUtils.OverviewOldLoginPlayer = Login.player;
  BMTestUtils.OverviewOldLoginLoggedin = Login.logged_in;
  Login.player = 'tester1';
  Login.logged_in = true;
}

BMTestUtils.cleanupFakeLogin = function() {
  Login.player = BMTestUtils.OverviewOldLoginPlayer;
  Login.logged_in = BMTestUtils.OverviewOldLoginLoggedin;
}

// We don't currently test reading the URL bar contents, because
// that's hard to do within QUnit, but rather override those contents
// with hardcoded values that we want to test.
//
// Note that, in general, these values need to be synchronized with
// the fake test data returned by DummyResponder in order for good
// things to happen.
BMTestUtils.overrideGetParameterByName = function() {
  Env.getParameterByName = function(name) {
    if (name == 'game') {
      if (BMTestUtils.GameType == 'newgame') { return '1'; }
      if (BMTestUtils.GameType == 'swingset') { return '2'; }
      if (BMTestUtils.GameType == 'turn_active') { return '3'; }
      if (BMTestUtils.GameType == 'turn_inactive') { return '4'; }
      if (BMTestUtils.GameType == 'finished') { return '5'; }
      if (BMTestUtils.GameType == 'newgame_twin') { return '6'; }
      if (BMTestUtils.GameType == 'focus') { return '7'; }
      if (BMTestUtils.GameType == 'chance_active') { return '8'; }
      if (BMTestUtils.GameType == 'chance_inactive') { return '9'; }
      if (BMTestUtils.GameType == 'newgame_nonplayer') { return '10'; }
      if (BMTestUtils.GameType == 'turn_nonplayer') { return '11'; }
      if (BMTestUtils.GameType == 'chance_nonplayer') { return '12'; }
      if (BMTestUtils.GameType == 'chooseaux_active') { return '13'; }
      if (BMTestUtils.GameType == 'chooseaux_inactive') { return '14'; }
      if (BMTestUtils.GameType == 'chooseaux_nonplayer') { return '15'; }
      if (BMTestUtils.GameType == 'reserve_active') { return '16'; }
      if (BMTestUtils.GameType == 'reserve_inactive') { return '17'; }
      if (BMTestUtils.GameType == 'reserve_nonplayer') { return '18'; }
    }

    // always return the userid associated with tester1 in the fake data
    if (name == 'id') {
      return '1';
    }

    // Syntactically valid but fake verification key
    if (name == 'key') {
      return 'facadefacadefacadefacadefacade12';
    }
  }
}
