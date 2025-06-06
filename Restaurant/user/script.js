function addFeedbackRow() {
    let table = document.getElementById("feedbackTable");
    let newRow = table.insertRow(-1);
    newRow.innerHTML = `
        <td>New</td>
        <td><input type="text" id="username" placeholder="Enter Username" style="font-size:20px;background:black;color:white;"></td>
        <td><input type="text" id="feedback" placeholder="Enter Feedback" style="font-size:20px;background:black;color:white;"></td>
        <td>
            <input type="number" id="rating" min="1" max="5" placeholder="1-5" style="font-size:20px;background:black;color:white;">
        </td>
        <td>Now</td>
        <td><button onclick="submitFeedback(this)">Submit</button></td>
    `;}
function submitFeedback(button) {
    let row = button.parentElement.parentElement;
    let username = row.cells[1].querySelector("input").value;
    let feedback = row.cells[2].querySelector("input").value;
    let rating = row.cells[3].querySelector("input").value;
    if (username && feedback && rating) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "submit_feedback.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                showPopup("Feedback Submitted Successfully!", true);
            }        };
        xhr.send("username=" + username + "&feedback=" + feedback + "&rating=" + rating);
    } else {
        showPopup("Please fill all fields.", false);    }}
// Function to show popup message
function showPopup(message, reloadPage) {
    let popup = document.createElement("div");
    popup.innerHTML = `
        <div style="
            position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
            background: black; color: white; padding: 20px; font-size: 20px;
            border-radius: 10px; text-align: center; width: 300px; z-index: 1000;">
            <p>${message}</p>
            <button onclick="closePopup(${reloadPage})" style="padding: 5px 15px; font-size: 18px;">OK</button>
        </div>
    `;
    popup.id = "popupMessage";
    document.body.appendChild(popup);}
// Function to close popup and refresh if needed
function closePopup(reloadPage) {
    let popup = document.getElementById("popupMessage");
    if (popup) popup.remove();
    if (reloadPage) location.reload();}
