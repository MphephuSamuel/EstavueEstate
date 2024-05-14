//validating seller login using ajax
document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission
  
    var formData = new FormData(this);
  
    fetch('seller_login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            
            window.location.href = data.redirectUrl;
        } else {

            alert('Login failed. Please check your username and password.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
  });
  





