function checkLogin(event) {
    event.preventDefault(); // Prevent default link action

    // Check if user is logged in
    let isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    
    if (isLoggedIn) {
        window.location.href = "feedback.php"; // Redirect to Feedback Page
    } else {
        document.getElementById("loginPopup").style.display = "flex"; // Show Popup
    }
}

function closePopup() {
    document.getElementById("loginPopup").style.display = "none"; // Hide Popup
}

function redirectToLogin() {
    window.location.href = "login.html"; // Redirect to Login Page
}
