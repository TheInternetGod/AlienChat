function CheckDateForWipeOut() {
  setInterval(function () {
    let xhr = new XMLHttpRequest();
    xhr.open(
      "GET",
      "../ServerSidePageOperations/CheckDateForWipeOut.php",
      true
    );
    xhr.onload = function () {
      if (this.status == 200) {
        console.log("Cleanup check completed");
      } else {
        console.error("Cleanup check failed:", this.status);
      }
    };
    xhr.onerror = function() {
      console.error("Cleanup request failed");
    };
    xhr.send();
  }, 5000);
}

function RefreshChat() {
  setInterval(function () {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "../ServerSidePageOperations/ReloadChat.php", true);
    xhr.onload = function () {
      if (this.status == 200) {
        document.getElementById("result").innerHTML = this.responseText;
      } else {
        console.error("Chat refresh failed:", this.status);
      }
    };
    xhr.onerror = function() {
      console.error("Chat refresh failed");
    };
    xhr.send();
  }, 2000);
}

function RefreshFiles() {
  setInterval(function () {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "../ServerSidePageOperations/ReloadFiles.php", true);
    xhr.onload = function () {
      if (this.status == 200) {
        document.getElementById("files").innerHTML = this.responseText;
      } else {
        console.error("File refresh failed:", this.status);
      }
    };
    xhr.onerror = function() {
      console.error("File refresh failed");
    };
    xhr.send();
  }, 2000);
}

