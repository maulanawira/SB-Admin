<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SB Admin</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Font -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Link CSS -->
    <link rel="stylesheet" href="styleindex.css">
</head>
<body>

<!-- Background & Centered Content -->
<div class="bg d-flex justify-content-center align-items-center">
    <div class="text-center">
        <!-- Welcome Text -->
        <div class="welcome-container">
            <h1 class="welcome-text">SB Admin</h1>
        </div>

        <!-- Form Login -->
        <div class="login-form mt-4 p-4">
            <form action="login.php" method="post">
                <!-- Username Input -->
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <!-- Password Input -->
                <div class="form-group">
                    <label for="password">Password:</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="input-group-append">
                            <!-- Toggle Password Visibility -->
                            <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    </div>
</div>

<!-- jQuery & Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
    // Error Alert
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error')) {
        alert('Username atau password salah. Silakan coba lagi.');
    }

    // Toggle Password
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>

</body>
</html>